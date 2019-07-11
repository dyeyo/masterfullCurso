<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    protected $table='categorias';
    protected $fillable=['nombre'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
