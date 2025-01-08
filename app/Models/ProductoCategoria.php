<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoCategoria extends Model
{
    use HasFactory;

    // Si prefieres que Laravel no gestione la tabla de forma automática, especifica el nombre
    // de la tabla y desactiva la convención de las claves primarias auto incrementales:
    protected $table = 'producto_categoria';
    public $timestamps = false;  
}
