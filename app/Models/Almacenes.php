<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacenes extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'ubicacion', 'descripcion'
    ];

    public function productos()
    {
        return $this->belongsToMany(Productos::class, 'productos_almacenes')->withPivot('cantidad');
    }
}
