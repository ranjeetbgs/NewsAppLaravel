<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \App\Models\Blog;

class UserDevice extends Model
{
    use HasFactory;
    protected $fillable = ['uuid','token','name','language','meta','user_id'];


     public function blogs()
    {
        return $this->belongsToMany(Blog::class);
    }
}
