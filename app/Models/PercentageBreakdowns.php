<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PercentageBreakdowns extends Model
{
    protected $table = 'percentage_breakdowns';

    protected $fillable = [
        'user_id',
        'percentage_type_id',
        'total_earning'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(PercentageType::class, 'percentage_type_id');
    }
}
