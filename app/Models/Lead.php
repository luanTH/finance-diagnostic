<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'type', // 'pf' ou 'pj'
    ];

    /**
     * Um Lead pode ter várias respostas no diagnóstico.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function diagnoses(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }
}
