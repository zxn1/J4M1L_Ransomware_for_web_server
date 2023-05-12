<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class keyAndPass extends Model
{
    use HasFactory;
    protected $table = 'key_and_passes';
    protected $fillable = [
        'id',
        'key',
        'password',
    ];
}
