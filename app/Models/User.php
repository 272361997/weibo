<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *防止批量赋值安全漏洞的字段白名单
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *当使用$user->toArray()或$user->toJson()时隐藏这些字段
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *指定模型属性的数据类型
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function gravatar($size = '100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));

        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }
}
