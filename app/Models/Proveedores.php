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

    public function movimientos()
    {
        return $this->hasMany(MovimientosInventario::class);
    }
}
