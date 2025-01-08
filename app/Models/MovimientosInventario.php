<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientosInventario extends Model
{
    use HasFactory;

    protected $fillable = [
        'producto_id', 'tipo_movimiento', 'cantidad', 'fecha', 'proveedor_id'
    ];

    public function producto()
    {
        return $this->belongsTo(Productos::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedores::class);
    }
}
