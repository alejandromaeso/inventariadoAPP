<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'descripcion', 'precio', 'cantidad', 'categoria_id'
    ];

    public function categorias()
    {
        return $this->belongsToMany(Categorias::class, 'producto_categoria');
    }

    public function movimientos()
    {
        return $this->hasMany(MovimientosInventario::class);
    }

    public function almacenes()
    {
        return $this->belongsToMany(Almacenes::class, 'productos_almacenes')->withPivot('cantidad');
    }
}
