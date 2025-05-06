<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productos extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'categoria_id'
    ];

    public function categoria()
    {
        return $this->belongsTo(Categorias::class, 'categoria_id');
    }


    public function movimientos()
    {
        return $this->hasMany(MovimientosInventario::class, 'producto_id');
    }

    public function almacenes()
    {

        $nombreTablaPivote = 'productos_almacenes';
        $claveForaneaEsteModelo = 'producto_id';
        $claveForaneaOtroModelo = 'almacen_id';

        return $this->belongsToMany(
                Almacenes::class,
                $nombreTablaPivote,
                $claveForaneaEsteModelo,
                $claveForaneaOtroModelo
            )
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    public function proveedores()
    {
        $nombreTablaPivote = 'productos_proveedores';
        $claveForaneaEsteModelo = 'producto_id';
        $claveForaneaOtroModelo = 'proveedor_id';

        return $this->belongsToMany(
                Proveedores::class,
                $nombreTablaPivote,
                $claveForaneaEsteModelo,
                $claveForaneaOtroModelo
            )
            ->withPivot('precio_proveedor')
            ->withTimestamps();
    }
}
