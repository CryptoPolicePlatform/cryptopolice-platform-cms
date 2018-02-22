<?php

namespace CryptoPolice\Platform\Classes;

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
}