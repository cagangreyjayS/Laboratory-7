<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'cn',
        'category',
        'name',
        'status',
        'score',
    ];

    public function isLowStock(): bool
    {
        return !is_null($this->score) && $this->score < 90;
    }
}