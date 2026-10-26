<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'number', 'email', 'status', 'delivery_address',
        'special_instructions', 'submitted_at', 'total', 'items',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'total' => 'decimal:2',
            'items' => 'array',
        ];
    }
}
