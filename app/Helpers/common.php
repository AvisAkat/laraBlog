<?php

use App\Models\GeneralSettings;

/**
 * Site Information
 */
if (! function_exists('settings')) {
    function settings()
    {
        return GeneralSettings::first();
    }
}