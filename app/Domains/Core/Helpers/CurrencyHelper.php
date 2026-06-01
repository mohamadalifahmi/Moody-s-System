<?php

namespace App\Domains\Core\Helpers;

class CurrencyHelper
{
    public static function orderTypeLabel(string $orderType, string $businessType = 'general'): string
    {
        $labels = config("business.order_types.{$businessType}", config('business.order_types.general', []));
        return $labels[$orderType] ?? $orderType;
    }
    public static function formatDual(float|int|string|null $amountUsd, float|int|string $rate = 89500): string
    {
        $amountUsd = (float) ($amountUsd ?? 0);
        $usd = number_format($amountUsd, 2, '.', ',');
        $lbp = number_format($amountUsd * (float) $rate, 0, '', ',');
        return "$usd \$ / $lbp ل.ل";
    }

    public static function formatUsd(float|int|string $amount): string
    {
        return '$ ' . number_format((float) $amount, 2, '.', ',');
    }

    public static function formatLbp(float|int|string $amountUsd, float|int|string $rate = 89500): string
    {
        return number_format((float) $amountUsd * (float) $rate, 0, '', ',') . ' ل.ل';
    }
}
