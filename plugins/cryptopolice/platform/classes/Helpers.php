<?php

namespace CryptoPolice\Platform\Classes;

use Carbon\Carbon;
use CryptoPolice\Academy\Models\Settings;

class Helpers
{
    public function setFacebookShare()
    {
        return 'https://www.facebook.com/sharer/sharer.php?' . http_build_query(['u' => url()->current()]);
    }

    public function setTwitterShare($text)
    {
        return 'https://twitter.com/share?' . http_build_query(['url' => url()->current(), 'text' => $text]);
    }

    public function setRedditShare($title)
    {
        return 'https://reddit.com/submit?' . http_build_query(['url' => url()->current(), 'title' => $title]);
    }

    public function checkLinks($value)
    {
        preg_match_all('/\b(?:(?:https?|ftp|file):\/\/|www\.|ftp\.)[-A-Z0-9+&@#\/%=~_|$?!:,.]*[A-Z0-9+&@#\/%=~_|$]/i', $value, $result, PREG_PATTERN_ORDER);
        return $result[0];
    }
    public function setStatus($createdAt, $views, $comments)
    {
        $hours = Carbon::now()->diffInHours(Carbon::parse($createdAt));
        if ($hours >= Settings::get('hot_post_min_hours') && $hours <= Settings::get('hot_post_max_hours') && $views >= Settings::get('hot_post_views') && $comments >= Settings::get('hot_post_min_comments'))
            return 3;
        if ($hours >= Settings::get('med_post_min_hours') && $hours <= Settings::get('med_post_max_hours') && $views >= Settings::get('med_post_views') && $comments >= Settings::get('med_post_min_comments'))
            return 2;
        if ($hours >= Settings::get('new_post_min_hours') && $hours <= Settings::get('new_post_max_hours') && $views >= Settings::get('new_post_views') && $comments >= Settings::get('new_post_min_comments'))
            return 1;
        return 0;
    }
}