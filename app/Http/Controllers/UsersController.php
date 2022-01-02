<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\Gate;

class UsersController extends Controller
{
    protected function validator(int $id)
    {
        return  [
            'fullname' => 'string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore(User::find($id))],
            'username' => ['required', 'string', 'min:4', Rule::unique('users')->ignore(User::find($id))]
        ];
    }

    protected function passValidator()
    {
        return  [
            'password' => 'required|string|min:6|confirmed'
        ];
    }

    static function getUser(int $id)
    {
        return  User::find($id);
    }

    static function getUsers()
    {
        return (new User())->where('is_admin', '=', false)->get();
    }

    public function showProfile(int $id)
    {
        Gate::authorize('show' ,User::find($id));
        $notifications = NotificationsController::getNotifications(Auth::id());

        $user = self::getUser($id);
        $projects = $user->projects->sortByDesc('created_at');
        $fav_projects = $user->favorites->sortByDesc('created_at');
        return view('pages.user', ['user' => $user, 'projects' => $projects, 'fav_projects' => $fav_projects, 'notifications' => $notifications]);
    }

    public function update(int $id, Request $request)
    {
        Gate::authorize('update',User::find($id));

        $request->validate($this->validator($id));

        $user = User::find($id);
        $user->fullname = $request->fullname;
        $user->username = $request->username;
        $user->email = $request->email;
        if ($request->hasFile('img_url')) {
            $request->img_url->storeAs('images/users', 'img_user_' . $id . '.png', 'public_files');
            $user->img_url = 'images/users/img_user_' . $id . '.png';
        }
        $user->save();

        return redirect()->back();
    }

    function updatePassword(int $id, Request $request)
    {
        Gate::authorize('update',User::find($id));

        $validator = $request->validate($this->passValidator());

        $user = User::find($id);
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->back();
    }

    public static function getUsersSearch(Request $request)
    {
        $users = User::where('is_admin', '=', false)
            ->whereRaw('(username like \'%' . $request->search . '%\' or email like \'%' . $request->search . '%\')')
            ->get();

        return  $users;
    }

    static public function createUser(Request $data)
    {
        return User::create([
            'fullname' => $data['fullname'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
