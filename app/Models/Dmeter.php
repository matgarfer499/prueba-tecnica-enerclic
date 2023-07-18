<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dmeter extends Model
{
    use HasFactory;

    protected $fillable = [
        'datatime',
        'power',
        'energy'
    ];
}
