<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pack extends Model
{
    use HasFactory;

    protected $fillable = [
        'size',
        'name',
        'description',
    ];

    /**
     * Get the orders for the pack.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get orders for a specific month and year.
     */
    public function ordersByMonth(int $month, int $year): HasMany
    {
        return $this->orders()
            ->whereMonth('order_date', $month)
            ->whereYear('order_date', $year);
    }
}
