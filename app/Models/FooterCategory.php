<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FooterCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category1',
        'category2',
        'category3',
        'category4'
    ];

    
}
