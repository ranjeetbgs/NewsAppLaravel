<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogImage extends Model
{
    use HasFactory;
    public $fillable = ['image','is_file'];

    public function blog()
{
    return $this->belongsTo('App\Models\Blog');
}
}
