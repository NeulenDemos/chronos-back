<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UsersCalendars;
use App\Models\Events;
use App\Models\UsersEvents;
use App\Filters\EventFilters;
use Carbon\Carbon;

class EventsController extends Controller
{
    public function getAll(Request $request)
    {
        $user_id = auth()->user()->id;
        $filters = new EventFilters($request);
        $events = $filters->apply(Events::query())->get();
        $result = [];
        if ($events)
            foreach ($events as $event) {
                $check1 = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $event['calendar_id']])->where('permissions', '>=', 1)->first();
                $check2 = UsersEvents::where(['user_id' => $user_id, 'event_id' => $event['id']])->first();
                if ($check1 || $check2)
                    array_push($result, $event);
            }
        return $result;
    }
    public function get($id)
    {
        $user_id = auth()->user()->id;
        $calendar = Events::whereKey($id)->first(['calendar_id']);
        if (!$calendar)
            return response('', 400);
        $check1 = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $calendar['calendar_id']])->where('permissions', '>=', 1)->first();
        $check2 = UsersEvents::where(['user_id' => $user_id, 'event_id' => $id])->first();
        if (!$check1 && !$check2)
            return response('0', 403);
        $result = Events::whereKey($id)->first();
        $users = UsersEvents::where(['event_id' => $id])->get(['user_id'])->all();
        $users_arr = [];
        if ($users)
            foreach ($users as $u)
                array_push($users_arr, User::whereKey($u['user_id'])->first());
        $result['users'] = $users_arr;
        return $result;
    }
    public function addUser($id, Request $request)
    {
        $user_id = auth()->user()->id;
        $calendar = Events::whereKey($id)->first(['calendar_id']);
        if (!$calendar)
            return response('', 400);
        $check = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $calendar['calendar_id']])->where('permissions', '>=', 2)->first();
        if (!$check)
            return response('0', 403);
        $data = $request->all();
        $data['event_id'] = $id;
        $result = UsersEvents::where(['user_id' => $data['user_id'], 'event_id' => $data['event_id']])->first();
        if (!$result)
            $result = UsersEvents::create($data);
        return $result;
    }
    public function update($id, Request $request)
    {
        $user_id = auth()->user()->id;
        $calendar = Events::whereKey($id)->first(['calendar_id']);
        if (!$calendar)
            return response('', 400);
        $check = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $calendar['calendar_id']])->where('permissions', '>=', 2)->first();
        if (!$check)
            return response('0', 403);
        $data = $request->all();
        $data['start_dt'] = Carbon::parse($data['start_dt'])->setTimezone('UTC');
        if (isset($data['end_dt']))
            $data['end_dt'] = Carbon::parse($data['end_dt'])->setTimezone('UTC');
        $result = Events::whereKey($id)->update($data);
        return $result;
    }
    public function delete($id)
    {
        $user_id = auth()->user()->id;
        $calendar = Events::whereKey($id)->first(['calendar_id']);
        if (!$calendar)
            return response('', 400);
        $check = UsersCalendars::where(['user_id' => $user_id, 'calendar_id' => $calendar['calendar_id']])->where('permissions', '>=', 3)->first();
        if (!$check)
            return response('0', 403);
        $result = Events::whereKey($id)->delete();
        return $result;
    }
}
