<?php

if (!function_exists('mapDate')) {
    /**
     * Chuẩn Hóa Ngày.
     * @param  string  $needles
     * @return string
     */
    function mapDate($needles)
    {
        if (preg_match('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)$/', $needles)) {
            return preg_replace('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)$/', '$3-$2-$1 $4:$5:$6', $needles);
        }

        if ($needles == '0000-00-00') {
            return null;
        }

        return preg_replace('/(\d+)-(\d+)-(\d+)$/', '$3-$2-$1', $needles);
    }
}

if (!function_exists('getSundays')) {
    /**
     * Get Week Days.
     * @param  string  $startDate
     * @param  string  $endDate
     * @param  int  $weekdayNumber
     * @return dateArr
     */
    function getSundays($startDate = null, $endDate = null, $weekdayNumber = 0)
    {
        $startDate = $startDate ? strtotime($startDate) : strtotime('-3day');
        $endDate   = $endDate ? strtotime($endDate) : strtotime('+6day', $startDate);
        $dateArr   = [];
        do {
            if (date('w', $startDate) != $weekdayNumber) {
                $startDate += (24 * 3600);// add 1 day
            }
        } while (date('w', $startDate) != $weekdayNumber);
        while ($startDate <= $endDate) {
            $dateArr[] = date('Y-m-d', $startDate);
            $startDate += (7 * 24 * 3600);// add 7 days
        }

        return $dateArr;
    }
}
