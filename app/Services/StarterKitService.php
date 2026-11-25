<?php

namespace App\Services;

/**
 * Pizza Business Starter Kit Builder.
 *
 * Each kit: list of [productId, quantity]. Totals are computed from PRODUCTS prices.
 * Ported 1:1 from js/starter-kit.js
 */
class StarterKitService
{
    public const KITS = [
        'home' => [
            3000  => [
                'name'  => 'Home Business Starter',
                'note'  => 'Enough to test your menu and take your first neighborhood orders.',
                'items' => [['p1', 2], ['p4', 1], ['p6', 1], ['p8', 1], ['p10', 1]],
            ],
            5000  => [
                'name'  => 'Home Business Growth',
                'note'  => 'Add flavor variety and a bigger box supply for weekend selling.',
                'items' => [['p1', 3], ['p3', 1], ['p4', 2], ['p6', 2], ['p8', 1], ['p10', 1]],
            ],
            10000 => [
                'name'  => 'Home Business Pro',
                'note'  => 'A full setup with multiple dough types, both box sizes and a paddle.',
                'items' => [['p1', 4], ['p2', 2], ['p3', 2], ['p5', 2], ['p6', 2], ['p7', 1], ['p8', 1], ['p9', 1], ['p10', 1]],
            ],
        ],
        'stall' => [
            3000  => [
                'name'  => 'Food Stall Starter',
                'note'  => 'Fast-moving basics for a small stall or pop-up table.',
                'items' => [['p1', 3], ['p6', 1], ['p8', 1], ['p10', 1]],
            ],
            5000  => [
                'name'  => 'Food Stall Rush',
                'note'  => 'Extra 12" boxes and dough for busy days and events.',
                'items' => [['p1', 4], ['p3', 1], ['p7', 2], ['p8', 1], ['p10', 1]],
            ],
            10000 => [
                'name'  => 'Food Stall Max',
                'note'  => 'High volume supplies for a full day of selling plus pro tools.',
                'items' => [['p1', 6], ['p2', 3], ['p5', 2], ['p7', 3], ['p8', 1], ['p9', 1], ['p10', 1]],
            ],
        ],
        'shop' => [
            3000  => [
                'name'  => 'Small Shop Starter',
                'note'  => 'Core supplies to open the doors with a limited menu.',
                'items' => [['p1', 3], ['p7', 1], ['p8', 1], ['p10', 1]],
            ],
            5000  => [
                'name'  => 'Small Shop Standard',
                'note'  => 'Dough, packaging and a proper paddle for daily service.',
                'items' => [['p1', 4], ['p2', 1], ['p7', 2], ['p9', 1]],
            ],
            10000 => [
                'name'  => 'Small Shop Complete',
                'note'  => 'Everything for a full menu: three doughs, boxes and all equipment.',
                'items' => [['p1', 6], ['p2', 4], ['p3', 2], ['p7', 3], ['p8', 1], ['p9', 1], ['p10', 1]],
            ],
        ],
    ];

    public const BUSINESS_LABELS = [
        'home'  => 'Home Business',
        'stall' => 'Food Stall',
        'shop'  => 'Small Shop',
    ];

    public function kits(): array
    {
        return self::KITS;
    }

    public function businessLabels(): array
    {
        return self::BUSINESS_LABELS;
    }

    public function get(string $type, int $budget): ?array
    {
        return self::KITS[$type][$budget] ?? null;
    }

    public function isValidChoice(?string $type, ?int $budget): bool
    {
        if (!$type || !$budget) {
            return false;
        }
        return isset(self::KITS[$type][$budget]);
    }
}
