<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre'
    ];

    public function productos()
    {
        return $this->hasMany(Productos::class);
    }

    public function productosRelacionados()
    {
        return $this->belongsToMany(Productos::class, 'producto_categoria');
    }
}
