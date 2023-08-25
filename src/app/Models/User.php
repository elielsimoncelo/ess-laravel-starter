<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $table = 'user';

    // Campos preenchíveis em massa
    protected $fillable = [
        'id',
        'username',
        'password',
        'email',
        'created_at',
        'updated_at',
    ];
}
