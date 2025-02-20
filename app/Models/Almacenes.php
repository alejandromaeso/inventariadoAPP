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

    // RELACIÓN MUCHOS A MUCHOS CON PRODUCTOS
    public function productos()
    {
        return $this->belongsToMany(Productos::class, 'productos_almacenes')->withPivot('cantidad');
    }

    // GETTERS
    public function getNombre($value)
    {
        return ucfirst($value); // Primera letra en mayúscula
    }

    public function getUbicacion($value)
    {
        return strtoupper($value); // Convierte en mayúsculas
    }

    // SETTERS
    public function setNombre($value)
    {
        $this->attributes['nombre'] = strtolower($value); // Guarda en minúsculas
    }

    public function setUbicacion($value)
    {
        $this->attributes['ubicacion'] = strtolower($value);
    }

    // MÉTODO PARA OBTENER TODOS LOS PRODUCTOS DEL ALMACÉN
    public function obtenerProductos()
    {
        return $this->productos()->with('categoria')->get(); // Trae productos con su categoría (si tiene relación)
    }

    // MÉTODO PARA CONTAR PRODUCTOS EN EL ALMACÉN
    public function contarProductos()
    {
        return $this->productos()->count();
    }

    // MÉTODO PARA OBTENER LA CANTIDAD TOTAL DE PRODUCTOS
    public function cantidadTotalProductos()
    {
        return $this->productos()->sum('pivot.cantidad'); // Suma la cantidad en la tabla pivot
    }
}
