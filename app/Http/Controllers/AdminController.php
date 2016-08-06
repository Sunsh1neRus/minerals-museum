<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Illuminate\Http\Request;

use App\Http\Requests;
use Validator;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function index()
    {
        return view('admin/index');
    }

    public function getUsersIndex()
    {
        $users = User::with('role')->simplePaginate(10);
        return view('admin/usersIndex', [
            'users' => $users
        ]);
    }

    public function postChangeUserRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|min:1',
            'role_id' => 'required|integer|min:1|exists:roles,id',
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => 0, 'msgs' => ['error' => $validator->errors()->all(), 'success' => [], 'warning' => [], 'info' => []]], 406);
        }

        $user = User::where('id', $request->user_id)->first();
        if (is_null($user)) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Такого пользователя у нас нет.'], 'success' => [], 'warning' => [], 'info' => []]], 406);
        }
        if (\Auth::user()->getAuthIdentifier() === $user->id) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Вы не можете изменить свою роль.'], 'success' => [], 'warning' => [], 'info' => []]], 403);
        }
        if ($user->role_id === 1) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Вы не можете изменить роль другого администратора.'], 'success' => [], 'warning' => [], 'info' => []]], 403);
        }
        $user->role_id = $request->role_id;
        $user->save();
        return response()->json(['success' => 1, 'msgs' => ['error' => [], 'success' => ['Роль изменена.'], 'warning' => [], 'info' => []]], 200);
    }

    public function postDeleteUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|min:1'
        ]);
        if ($validator->fails()) {
            return response()->json(['success' => 0, 'msgs' => ['error' => $validator->errors()->all(), 'success' => [], 'warning' => [], 'info' => []]], 406);
        }

        $user = User::where('id', $request->user_id)->first();
        if (is_null($user)) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Такого пользователя у нас нет.'], 'success' => [], 'warning' => [], 'info' => []]], 406);
        }
        if (\Auth::user()->getAuthIdentifier() === $user->id) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Вы не можете удалить сами себя.'], 'success' => [], 'warning' => [], 'info' => []]], 403);
        }
        if ($user->role_id === 1) {
            return response()->json(['success' => 0, 'msgs' => ['error' => ['Вы не можете удалить другого администратора.'], 'success' => [], 'warning' => [], 'info' => []]], 403);
        }
        $user->delete();
        return response()->json(['success' => 1, 'msgs' => ['error' => [], 'success' => ['Пользователь удалён.'], 'warning' => [], 'info' => []]], 200);
    }
}
