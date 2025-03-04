<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_id',
        'quantity',
        'order_date',
        'notes',
    ];

    protected $casts = [
        'order_date' => 'date',
    ];

    /**
     * Get the pack that owns the order.
     */
    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }

    /**
     * Scope a query to only include orders from a specific month and year.
     */
    public function scopeByMonth($query, int $month, int $year)
    {
        return $query->whereMonth('order_date', $month)
                     ->whereYear('order_date', $year);
    }
}
