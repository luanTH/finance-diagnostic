<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Diagnosis extends Model
{
    use HasFactory;

    protected $table = 'diagnostics';

    protected $fillable = [
        'lead_id',
        'results',
        'total_score',
    ];

    protected $casts = [
        'analysis_data' => 'array',
        'total_score' => 'float',
    ];

    /**
     * O diagnóstico pertence a um Lead.
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }
}
