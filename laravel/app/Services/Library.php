<?php
namespace App\Services;

class Library
{
    /**
     * Chuẩn Hóa Ngày.
     * @param string $needles
     * @return string
     */
    public function chuanHoaNgay($needles)
    {
        if (preg_match('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)$/', $needles)) {
            return preg_replace('/(\d+)-(\d+)-(\d+) (\d+):(\d+):(\d+)$/', '$3-$2-$1 $4:$5:$6', $needles);
        }

        if ($needles == '0000-00-00') {
            return null;
        }

        return preg_replace('/(\d+)-(\d+)-(\d+)$/', '$3-$2-$1', $needles);
    }

    public function base64ToImage($base64_string, $output_file)
    {
        $ifp = fopen($output_file, 'wb');
        $data = explode(',', $base64_string);
        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        return $output_file;
    }

    public function getProfileImage($ID = '')
    {
        $type = 'png';
        $imagePath = $this->getProfilePath() . "/$ID.$type";
        if (!file_exists($imagePath)) {
            $imagePath = $this->getProfilePath() . '/default.png';
        }

        return \File::get($imagePath);
    }

    /**
     * Get the path to the profile image folder.
     * @param string $path
     * @return string
     */
    public function getProfilePath($path = '')
    {
        return public_path('profile-image') . ($path ? '/' . $path : $path);
    }

    /**
     * Get Week Days.
     * @param string $startDate
     * @param string $endDate
     * @param int $weekdayNumber
     * @return dateArr
     */
    public function SpecificDayBetweenDates($startDate = null, $endDate = null, $weekdayNumber = 0)
    {
        $startDate = $startDate ? strtotime($startDate) : strtotime('-3day');
        $endDate = $endDate ? strtotime($endDate) : strtotime('+6day', $startDate);
        $dateArr = [];
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
