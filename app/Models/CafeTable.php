<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['table_number', 'capacity', 'location', 'status', 'is_active'])]
class CafeTable extends Model
{
    protected function casts(): array
    {
        return ['capacity' => 'integer', 'is_active' => 'boolean'];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
