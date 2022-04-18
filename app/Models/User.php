<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use mysql_xdevapi\Collection;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Статус пользователя
     */
    public function status()
    {
        return $this->belongsTo(UserStatus::class,'status_id');
    }


    /**
     * Upload avatar
     * @param $request object
     * @return string
     */
    public function uploadAvatar($request) {
        if(!empty($request->file('avatar'))) {
            $avatar = $request->file('avatar');
            $avatar_path = $avatar->store('uploads');
            return $avatar_path;
        }
    }

    /**
     * Check and delete avatar if it exist
     * @return string
     */
    public function checkAndDeleteAvatar() {
        if(!empty($this->avatar)) {
            Storage::delete($this->avatar);
        }
    }

    /**
     * Get avatar path
     * @param $avatar string
     * @return string
     */
    public function getAvatarPath($avatar) {
        if(empty($avatar)) {
            return 'uploads/avatar-m.png';
        } else {
            return $avatar;
        }
    }

    /**
     * Get user list with avatar path
     * @return Collection
     */
    public static function getUsersWithAvatars() {
        $users = self::with('status')->get();
        foreach($users as $user) {
            $user->avatar_path = $user->getAvatarPath($user->avatar);
        }
        return $users;
    }

    /**
     * Check permissions
     * @return Redirect
     */
    public function checkPermissions() {
        if(auth()->user()->id != $this->id && !auth()->user()->hasRole('admin')) {
            Redirect::to('users')->withErrors(['У вас нет прав редактировать данного пользователя'])->send();
        }
    }
}
