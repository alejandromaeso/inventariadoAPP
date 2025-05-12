<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedores extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'direccion', 'telefono', 'email'
    ];

    public function productos()
    {
        $nombreTablaPivote = 'productos_proveedores';
        $claveForaneaEsteModelo = 'proveedor_id';
        $claveForaneaOtroModelo = 'producto_id';

        return $this->belongsToMany(
            // Modelo relacionado
                Productos::class,
                $nombreTablaPivote,
                $claveForaneaEsteModelo,
                $claveForaneaOtroModelo
            )
            // Añadimos la columa "precio_proveedor" a la tabla pivote
            ->withPivot('precio_proveedor')
            ->withTimestamps();
    }

    public function obtenerProductos()
    {
        // Accemos los los productos accesibles a $producto->pivot->precio_proveedor
        return $this->productos()->with('categoria')->get();
    }

    public function contarProductos()
    {
        return $this->productos()->count();
    }


     // GETTERS
    public function getNombreAttribute($value)
    {
        return ucfirst($value);
    }

    public function getDireccionAttribute($value)
    {
        return $value ? ucfirst($value) : null;
    }

    // SETTERS
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtolower($value);
    }

    public function setDireccionAttribute($value)
    {
         $this->attributes['direccion'] = $value ? strtolower($value) : null;
    }
}
