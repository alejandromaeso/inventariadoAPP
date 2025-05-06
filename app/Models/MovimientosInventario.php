<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientosInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    protected $fillable = [
        'producto_id',
        'almacen_id',
        'tipo',
        'cantidad',
        'user_id',
        'descripcion',
    ];

    // Relaciones
    public function producto()
    {
        return $this->belongsTo(Productos::class, 'producto_id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacenes::class, 'almacen_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
