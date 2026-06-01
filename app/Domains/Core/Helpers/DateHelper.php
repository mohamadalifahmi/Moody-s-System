<?php

namespace App\Domains\Core\Helpers;

use Carbon\Carbon;

class DateHelper
{
    public static function format(mixed $date): string
    {
        return Carbon::parse($date)->format('Y-m-d');
    }

    public static function formatArabic(mixed $date): string
    {
        $days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
        $months = [
            'يناير', 'فبراير', 'مارس', 'إبريل', 'مايو', 'يونيو',
            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر',
        ];

        $carbon = Carbon::parse($date);

        $dayName = $days[$carbon->dayOfWeek];
        $monthName = $months[$carbon->month - 1];

        return $dayName . '، ' . $carbon->day . ' ' . $monthName . ' ' . $carbon->year;
    }
}
