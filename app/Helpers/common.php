<?php

use App\Models\GeneralSettings;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Site Information
 */
if (! function_exists('settings')) {
    function settings()
    {
        return GeneralSettings::first();
    }
}

/**
 * DATE FORMAT eg. March 15, 2024
 */
if (! function_exists('date_formatter')) {
    function date_formatter($value, $format = 'long')
    {
        try {
            if ($format === 'long') {
                $format = 'F j, Y';
            } elseif ($format === 'short') {
                $format = 'M j, Y';
            } else {
                $format = 'F j, Y'; // Default to long format if an unrecognized format is provided
            }

            return Carbon::parse($value)->translatedFormat($format);
        } catch (\Exception $e) {
            return null; // or return $value;
        }
    }
}

/**
 * STRIP WORDS
 */
if (! function_exists('strip_words')) {
    function strip_words($value, $words = 15, $end = '...')
    {
        return Str::words(strip_tags($value), $words, $end);
    }
}

/**
 * CALCULATE POST READING TIME
 */
if (! function_exists('readingDuration')) {
    function readingDuration(...$text)
    {
        Str::macro('timeCounter', function ($text) {
            $totalWords = str_word_count(implode(' ', $text));
            $minutesToRead = round($totalWords / 200); // Assuming an average reading speed of 200 words per minute

            return (int) max(1, $minutesToRead); // Ensure at least 1 minute
        });

        return Str::timeCounter($text);
    }
}
