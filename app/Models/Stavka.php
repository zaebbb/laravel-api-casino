<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stavka extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'price',
        'company',
        'settings',
    ];
    protected $table = 'stavka';
}
