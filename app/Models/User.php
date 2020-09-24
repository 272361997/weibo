<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use App\Models\Status;

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

    public static function boot()
    {
        parent::boot();

        static::creating(function($user){
            $user->activation_token = Str::random(10);
        });
    }

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

    public function statuses()
    {
        // 一个用户拥有多条微博
        return $this->hasMany(Status::class);
    }


    public function feed()
    {
        return $this->statuses()
                    ->orderBy('created_at','desc');
    }

    public function followers()
    {
        return $this->belongsToMany(User::Class,'followers','user_id','follower_id');
    }

    public function followings()
    {
        return $this->belongsToMany(User::Class,'followers','follower_id','user_id');
    }

    public function follow($user_ids)
    {
        if(! is_array($user_ids)){
            $user_ids = compact('user_ids');
        }

        $this->followings()->sync($user_ids,false);
    }

    public function unfollow($user_ids)
    {
        if(! is_array($user_ids)){
            $user_ids = compact('user_ids');
        }

        $this->followings()->detach($user_ids);
    }

    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }

    public function feed()
    {
        $user_ids = $this->followings->pluck('id')->toArray();
        array_push($user_ids,$this->id);

        return Status::whereIn('user_id',$user_ids)
                        ->with('user')
                        ->orderBy('created_at','desc');
    }




}
