<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSettings extends Model
{
    use HasFactory;
    protected $fillable = [
        'site_title',
        'site_email',
        'site_phone',
        'site_meta_keywords',
        'site_meta_description',
        'site_logo',
        'site_favicon'
    ];
}
