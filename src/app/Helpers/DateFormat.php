<?php

use Carbon\Carbon;

if (!function_exists('japanese_date_format')) {
    /**
     * @param string $date
     */
    function japanese_date_format($date): string
    {
        return Carbon::parse($date)->format('Y年m月d日');
    }
}

if (!function_exists('japanese_date_format_add_days')) {
    /**
     * @param string $date
     * @param int    $days
     */
    function japanese_date_format_add_days($date, $days): string
    {
        return Carbon::parse($date)->addDays($days)->format('Y年m月d日');
    }
}

if (!function_exists('date_time_slash_format')) {
    /**
     * @param string $date
     */
    function date_time_slash_format($date): string
    {
        return Carbon::parse($date)->format('Y/m/d H:i');
    }
}
