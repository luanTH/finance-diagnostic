<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'category_id',
        'order',
        'is_Active',
    ];

    /**
     * Cast de tipos: o campo 'options' será tratado como array,
     * e 'is_active' como booleano.
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
