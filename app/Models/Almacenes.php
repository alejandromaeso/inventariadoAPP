<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacenes extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'ubicacion'
    ];

    public function productos()
    {
        // Nombres de las columnas en la tabla pivote 'productos_almacenes'
        $nombreTablaPivote = 'productos_almacenes';
        $claveForaneaEsteModelo = 'almacen_id';
        $claveForaneaOtroModelo = 'producto_id';

        return $this->belongsToMany(
                Productos::class,
                $nombreTablaPivote,
                $claveForaneaEsteModelo,
                $claveForaneaOtroModelo
            )
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    // GETTERS
    public function getNombreAttribute($value)
    {
        // Primera letra en mayúscula
        return ucfirst($value);
    }

    public function getUbicacionAttribute($value)
    {
        // Primera letra en mayúscula
        return ucfirst($value);
    }

    // SETTERS
    public function setNombreAttribute($value)
    {
        // Guardamos en minúsculas para evitar problemas con la BD
        $this->attributes['nombre'] = strtolower($value);
    }

    public function setUbicacionAttribute($value)
    {
        $this->attributes['ubicacion'] = strtolower($value);
    }

    public function obtenerProductos()
    {
        // Obtenemos los productos con su categoría
        return $this->productos()->with('categoria')->get();
    }

    public function contarProductos()
    {
        return $this->productos()->count();
    }

    public function cantidadTotalStock()
    {
        return $this->productos()->sum('productos_almacenes.cantidad');
    }
}
