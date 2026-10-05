<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['invoice', 'user_id', 'cafe_table_id', 'payment_method', 'subtotal', 'total', 'amount_paid', 'change_amount', 'note', 'status', 'paid_at'])]
class Transaction extends Model
{
    protected function casts(): array
    {
        return [
            'subtotal' => 'integer',
            'total' => 'integer',
            'amount_paid' => 'integer',
            'change_amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cafeTable(): BelongsTo
    {
        return $this->belongsTo(CafeTable::class);
    }
}
