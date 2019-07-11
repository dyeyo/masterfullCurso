<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $table='posts';
    protected $fillable=['titulo', 'contenido', 'imagen', 'user_id','categoria_id'];

    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function categorias()
    {
        return $this->belongsTo(Categorias::class);
    }
}
