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
                Productos::class, // Modelo relacionado
                $nombreTablaPivote,
                $claveForaneaEsteModelo,
                $claveForaneaOtroModelo
            )
            // Si añadiste columnas extra a la tabla pivote (ej: costo), añádelas aquí:
            ->withPivot('precio_proveedor')
            ->withTimestamps(); // Para usar created_at/updated_at de la tabla pivote
    }

   /**
     * Obtiene todos los productos asociados a este proveedor.
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerProductos()
    {
        // Ahora, al obtener los productos, cada uno tendrá accesible $producto->pivot->precio_proveedor
        return $this->productos()->with('categoria')->get();
    }

     /**
     * Cuenta cuántos productos diferentes suministra este proveedor.
     * @return int
     */
    public function contarProductos()
    {
        return $this->productos()->count();
    }


     // --- GETTERS (Accesors) ---
    public function getNombreAttribute($value)
    {
        return ucfirst($value);
    }

    public function getDireccionAttribute($value)
    {
        return $value ? ucfirst($value) : null;
    }

    // --- SETTERS (Mutators) ---
    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] = strtolower($value);
    }

    public function setDireccionAttribute($value)
    {
         $this->attributes['direccion'] = $value ? strtolower($value) : null;
    }
}
