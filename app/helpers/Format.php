<?php

namespace App\Helpers;

class Format
{
    /**
     * Format a peso amount, e.g. 450 -> "₱450"
     */
    public static function peso(int $amount): string
    {
        return '₱' . number_format($amount);
    }

    /**
     * Format star string, e.g. 5 -> "★★★★★"
     */
    public static function stars(int $rating): string
    {
        $r = max(0, min(5, $rating));
        return str_repeat('★', $r) . str_repeat('☆', 5 - $r);
    }

    /**
     * Build a shop page URL preserving the current query parameters
     * but updating the page number.
     */
    public static function shopPageUrl(int $page, array $query = []): string
    {
        $params = array_filter($query, function ($value, $key) {
            // Drop CSRF token and the page param itself
            return $key !== '_token' && $key !== 'page' && $value !== '' && $value !== null;
        }, ARRAY_FILTER_USE_BOTH);

        $params['page'] = $page;

        return route('shop') . '?' . http_build_query($params);
    }
}
