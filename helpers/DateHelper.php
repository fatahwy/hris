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

    public static function getHumanRangeDate($startDate, $endDate, $isHour = false)
    {
        if ($isHour) {
            return date('H:i', strtotime($startDate)) . ' - ' . date('H:i', strtotime($endDate));
        }

        $startTime = '00:00:00';
        $endTime = '23:59:59';
        $intStartDate = strtotime($startDate);
        $intEndDate = strtotime($endDate);

        $withHour = date('H:i:s', $intStartDate) != $startTime || date('H:i:s', $intEndDate) != $endTime;
        $isSameDay = date('Ymd', $intStartDate) === date('Ymd', $intEndDate);
        $isSameYear = date('Y', $intStartDate) === date('Y', $intEndDate);
        $isSameMonth = date('Ym', $intStartDate) === date('Ym', $intEndDate);

        if ($withHour) {
            return date('d M Y H:i', $intStartDate) . ' - ' . date('d M Y H:i', $intEndDate);
        } else {
            if ($isSameDay) {
                return date('d M Y', $intStartDate);
            } else if ($isSameMonth) {
                return date('d', $intStartDate) . ' - ' . date('d M Y', $intEndDate);
            } else if ($isSameYear) {
                return date('d M', $intStartDate) . ' - ' . date('d M Y', $intEndDate);
            }

            return date('d M Y', $intStartDate) . ' - ' . date('d M Y', $intEndDate);
        }
    }
}