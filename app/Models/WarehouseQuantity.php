<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseQuantity extends Model
{
    protected $table = 'warehouse_quantity';
    
    protected $fillable = [
        'total_quantity',
        'notes'
    ];
}
