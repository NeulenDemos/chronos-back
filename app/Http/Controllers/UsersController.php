<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Calendars;
use App\Models\UsersCalendars;
use App\Models\Events;
use App\Models\UsersEvents;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function get($id)
    {
        if (is_numeric($id))
            $result = User::whereKey($id)->get();
        else
            $result = User::where(['email' => $id])->first();
        return $result;
    }
    public function getMe()
    {
        $user_id = auth()->user()->id;
        $result = User::whereKey($user_id)->first();
        return $result;
    }
    public function create(Request $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $result = User::create($data);
        return $result;
    }
    public function avatar(Request $request)
    {
        $user_id = auth()->user()->id;
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $imageName = time().'.'.$request->image->extension();
        $request->image->move(public_path('storage/images'), $imageName);
        $result = User::whereKey($user_id)->update(['picture' => $imageName]);
        return $result;
    }
    public function update(Request $request)
    {
        $user_id = auth()->user()->id;
        $data = $request->all();
        if (isset($data['password']))
            $data['password'] = Hash::make($data['password']);
        $result = User::whereKey($user_id)->update($data);
        foreach ($result as $key)
            if ($key == 0)
                return response('0', 400);
        return response('1', 200);
    }
    public function delete(Request $request)
    {
        $user_id = auth()->user()->id;
        $result = User::whereKey($user_id)->delete();
        return $result;
    }
}
