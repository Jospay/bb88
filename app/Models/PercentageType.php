<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PercentageType extends Model
{
    use HasFactory;

    protected $table = 'percentage_types';

    protected $fillable = [
        'name',
        'type',
        'value'
    ];
}
