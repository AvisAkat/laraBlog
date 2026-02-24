<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSocialLink extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'facebook_url',
        'youtube_url',
        'instagram_url',
        'x_url',
        'linkedin_url',
        'github_url',
    ];
}
