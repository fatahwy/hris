<?php

namespace app\helpers;

class DateHelper
{
    public static function getTotalWeek($month, $year)
    {
        $ttlWeek = 0;

        $ttlDays = date('t', strtotime("$year-$month-01"));

        for ($day = 1; $day <= $ttlDays; $day++) {
            $dayOfWeek = date('w', strtotime("$year-$month-$day"));

            if ($dayOfWeek == 0) {
                $ttlWeek++;
            }
        }

        return $ttlWeek;
    }
}