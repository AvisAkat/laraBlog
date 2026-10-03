<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'x_url',
        'youtube_url'
    ];
}
