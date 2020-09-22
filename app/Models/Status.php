<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{

    protected $fillable = ['content'];

    public function user()
    {
        // 一条微博属于一个用户
        return $this->belongsTo(User::class);
    }
}
