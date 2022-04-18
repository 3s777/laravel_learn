<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateAvatarRequest;
use App\Http\Requests\UserUpdateCredentialsRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Requests\UserUpdateStatusRequest;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware(['role:admin'],['only' => ['create','store']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index()
    {
        $users = User::getUsersWithAvatars();
        return view('index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        $user_statuses = UserStatus::get();
        return view('users.create', compact('user_statuses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  UserCreateRequest  $request
     * @return Redirect
     */
    public function store(UserCreateRequest $request)
    {
        $input = $request->all();
        $user = new User();
        $input['avatar'] = $user->uploadAvatar($request);
        $input['password'] = Hash::make($request->password);
        $user->create($input);
        return redirect()
                ->route('users.index')
                ->with(['success'=>"Пользователь успешно добавлен"]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function show($id)
    {
        $user = User::find($id);
        $user->avatar_path = $user->getAvatarPath($user->avatar);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return View
     */
    public function edit($id)
    {
        $user = User::find($id);
        $user->checkPermissions();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return View
     */
    public function update(UserUpdateRequest $request, $id)
    {
        $input = $request->only(['name', 'address', 'job', 'phone']);
        $user = User::find($id);
        $user->checkPermissions();
        $user->update($input);
        return redirect()
            ->route('users.index')
            ->with(['success'=>"Пользователь успешно отредактирован"]);
    }

    /**
     * Show the form for editing the specified resource credentials.
     *
     * @param  int  $id
     * @return View
     */
    public function edit_credentials($id)
    {
        $user = User::find($id);
        $user->checkPermissions();
        return view('users.edit_credentials', compact('user'));
    }

    /**
     * Update the specified resource credentials in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Redirect
     */
    public function update_credentials(UserUpdateCredentialsRequest $request, $id)
    {
        $input = $request->only(['email', 'password']);
        $user = User::find($id);
        $user->checkPermissions();
        $user->update($input);
        return redirect()
            ->route('users.index')
            ->with(['success'=>"Пользователь успешно отредактирован"]);
    }

    /**
     * Show the form for editing the specified resource status.
     *
     * @param  int  $id
     * @return View
     */
    public function edit_status($id)
    {
        $user = User::find($id);
        $user->checkPermissions();
        $user_statuses = UserStatus::get();
        return view('users.edit_status', compact('user','user_statuses'));
    }

    /**
     * Update the specified resource status in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Redirect
     */
    public function update_status(UserUpdateStatusRequest $request, $id)
    {
        $input = $request->only(['status_id']);
        $user = User::find($id);
        $user->checkPermissions();
        $user->update($input);
        return redirect()
            ->route('users.index')
            ->with(['success'=>"Статус успешно установлен"]);
    }

    /**
     * Show the form for editing the specified resource avatar.
     *
     * @param  int  $id
     * @return View
     */
    public function edit_avatar($id)
    {
        $user = User::find($id);
        $user->checkPermissions();
        $user->avatar_path = $user->getAvatarPath($user->avatar);
        return view('users.edit_avatar', compact('user'));
    }

    /**
     * Update the specified resource avatar in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Redirect
     */
    public function update_avatar(UserUpdateAvatarRequest $request, $id)
    {
        $user = User::find($id);
        $user->checkPermissions();
        $user->checkAndDeleteAvatar();
        $user->avatar = $user->uploadAvatar($request);
        $user->save();
        return redirect()
            ->route('users.index')
            ->with(['success'=>"Аватар успешно установлен"]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->checkPermissions();
        $user->checkAndDeleteAvatar();
        $user->delete();
        return redirect()
            ->route('users.index')
            ->with(['success'=>"Пользователь успешно удален"]);
    }
}
