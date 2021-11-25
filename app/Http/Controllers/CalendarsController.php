<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Calendars;
use App\Models\UsersCalendars;
use App\Models\Events;
use App\Models\User;
use App\Misc\DefaultCalendar;
use Carbon\Carbon;

class CalendarsController extends Controller
{
    // Permissions:
    // 0 - None
    // 1 - Read
    // 2 - Write
    // 3 - Delete
    // 4 - Creator (Edit other users permissions)
    public function getAll(Request $request)
    {
        $user_id = auth()->user()->id;
        $calendars_id = UsersCalendars::where('user_id', $user_id)->get(['calendar_id'])->all();
        $result = [];
        if ($calendars_id)
            foreach ($calendars_id as $id) {
                $calendar = Calendars::whereKey($id['calendar_id'])->first();
                $calendar['events_count'] = Events::where('calendar_id', $id['calendar_id'])->count();
                array_push($result, $calendar);
            }
        return $result;
    }
    public function get($id)
    {
        $user_id = auth()->user()->id;
        $check = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $id])->where('permissions', '>=', 1)->first();
        if (!$check)
            return response('0', 403);
        $result = Calendars::whereKey($id)->first();
        if ($result) {
            $result['permissions'] = $check['permissions'];
            $users = [];
            $users_id = UsersCalendars::where('calendar_id', $id)->get(['user_id', 'permissions'])->all();
            if ($users_id)
                foreach ($users_id as $u_id) {
                    $user = User::whereKey($u_id['user_id'])->first()->makeVisible(['permissions']);
                    $user['permissions'] = $u_id['permissions'];
                    array_push($users, $user);
                }
            $result['users'] = $users;
            $events = [];
            $events_id = Events::where('calendar_id', $id)->get(['id'])->all();
            if ($events_id)
                foreach ($events_id as $event_id)
                    array_push($events, Events::whereKey($event_id['id'])
                    ->first(['id','title','description','all_day','color','start_dt','end_dt','type']));
            $result['events'] = $events;
        }
        return $result;
    }
    public function create(Request $request)
    {
        $user_id = auth()->user()->id;
        $data = $request->all();
        $data['user_id'] = $user_id;
        if ($data['primary']) {
            $calendars = UsersCalendars::where(['user_id' => $user_id, 'permissions' => 4])->get(['calendar_id'])->all();
            if ($calendars) {
                $calendars = array_map(fn($value) => $value['calendar_id'], $calendars);
                $exists = Calendars::whereIn('id', $calendars)->where(['primary' => 1])->first();
                if ($exists)
                    return response('Primary for user already exists', 403);
            }
            $dc = new DefaultCalendar();
            $events = $dc->GetEvents($request->ip());
            $result = Calendars::create($data);
            UsersCalendars::create(['user_id' => $user_id, 'calendar_id' => $result['id'], 'permissions' => 4]);
            foreach ($events as $event) {
                $event['calendar_id'] = $result['id'];
                $event['type'] = 'reminder';
                $event['all_day'] = 1;
                Events::create($event);
            }
        }
        else {
            $result = Calendars::create($data);
            UsersCalendars::create(['user_id' => $user_id, 'calendar_id' => $result['id'], 'permissions' => 4]);
        }
        return $result;
    }
    public function createEvent($id, Request $request)
    {
        $user_id = auth()->user()->id;
        $check = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $id])->where('permissions', '>=', 2)->first();
        if (!$check)
            return response('0', 403);
        $data = $request->all();
        $data['calendar_id'] = $id;
        $data['start_dt'] = Carbon::parse($data['start_dt'])->setTimezone('UTC');
        if (isset($data['end_dt']))
            $data['end_dt'] = Carbon::parse($data['end_dt'])->setTimezone('UTC');
        $result = Events::create($data);
        return $result;
    }
    public function update($id, Request $request)
    {
        $user_id = auth()->user()->id;
        $check = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $id])->where('permissions', '>=', 2)->first();
        if (!$check)
            return response('0', 403);
        $data = $request->all();
        $result = Calendars::whereKey($id)->update($data);
        return $result;
    }
    public function delete($id)
    {
        $user_id = auth()->user()->id;
        $check = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $id])->where('permissions', '>=', 3)->first();
        if (!$check)
            return response('0', 403);
        $result = Calendars::whereKey($id)->delete();
        return $result;
    }
    public function addUser($id, Request $request)
    {
        $user_id = auth()->user()->id;
        $check = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $id])->where('permissions', '>=', 4)->first();
        if (!$check)
            return response('0', 403);
        $data = $request->all();
        $data['calendar_id'] = $id;
        $result = UsersCalendars::create($data);
        return $result;
    }
    public function editUser($id, Request $request)
    {
        $user_id = auth()->user()->id;
        $data = $request->all();
        $edit_user = $data['user_id'];
        if ($user_id == $edit_user)
            return response('0', 403);
        $check = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $id])->where('permissions', '>=', 4)->first();
        if (!$check)
            return response('0', 403);
        $result = UsersCalendars::where(['user_id' => $edit_user, 'calendar_id' => $id])->update(['permissions' => $data['permissions']]);
        return $result;
    }
}
