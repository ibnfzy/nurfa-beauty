<?php

if (!function_exists('calculate_loyalty_points')) {
    /**
     * Hitung poin loyalitas berdasarkan amount dan level membership
     * Base: 1 poin per Rp 10.000
     * Multiplier: bronze=1x, silver=1.5x, gold=2x, platinum=3x
     */
    function calculate_loyalty_points(float $amount, string $level = 'bronze'): int
    {
        $multipliers = [
            'bronze'   => 1,
            'silver'   => 1.5,
            'gold'     => 2,
            'platinum' => 3,
        ];
        $multiplier = $multipliers[$level] ?? 1;
        $basePoints = (int) floor($amount / 10000);
        return (int) floor($basePoints * $multiplier);
    }
}

if (!function_exists('get_membership_level')) {
    /**
     * Tentukan level membership berdasarkan total_spending
     */
    function get_membership_level(float $totalSpending): string
    {
        if ($totalSpending >= 10000000) return 'platinum';
        if ($totalSpending >= 5000000) return 'gold';
        if ($totalSpending >= 2000000) return 'silver';
        return 'bronze';
    }
}

if (!function_exists('get_membership_benefits')) {
    /**
     * Dapatkan benefit membership berdasarkan level
     */
    function get_membership_benefits(string $level): array
    {
        $benefits = [
            'bronze' => [
                'label'    => 'Bronze',
                'multiplier' => 1,
                'discount' => 0,
                'perks'    => ['Poin 1x per belanja'],
            ],
            'silver' => [
                'label'    => 'Silver',
                'multiplier' => 1.5,
                'discount' => 5,
                'perks'    => ['Poin 1.5x per belanja', 'Diskon 5%'],
            ],
            'gold' => [
                'label'    => 'Gold',
                'multiplier' => 2,
                'discount' => 10,
                'perks'    => ['Poin 2x per belanja', 'Diskon 10%', 'Voucher Bulanan'],
            ],
            'platinum' => [
                'label'    => 'Platinum',
                'multiplier' => 3,
                'discount' => 15,
                'perks'    => ['Poin 3x per belanja', 'Diskon 15%', 'Voucher Mingguan'],
            ],
        ];
        return $benefits[$level] ?? $benefits['bronze'];
    }
}

if (!function_exists('get_next_membership_level')) {
    /**
     * Dapatkan level membership berikutnya dan spending yang dibutuhkan
     */
    function get_next_membership_level(float $totalSpending): ?array
    {
        $thresholds = [
            ['level' => 'silver', 'spending' => 2000000],
            ['level' => 'gold', 'spending' => 5000000],
            ['level' => 'platinum', 'spending' => 10000000],
        ];

        foreach ($thresholds as $threshold) {
            if ($totalSpending < $threshold['spending']) {
                return [
                    'level'       => $threshold['level'],
                    'target'      => $threshold['spending'],
                    'remaining'   => $threshold['spending'] - $totalSpending,
                    'progress'    => round(($totalSpending / $threshold['spending']) * 100, 1),
                ];
            }
        }

        return null; // Already at max level
    }
}

if (!function_exists('calculate_bonus_points')) {
    /**
     * Hitung bonus poin untuk belanja di atas minimum spend
     * > Rp 500.000 → bonus 50 poin
     * > Rp 1.000.000 → bonus 100 poin
     * > Rp 2.000.000 → bonus 250 poin
     */
    function calculate_bonus_points(float $amount): int
    {
        if ($amount >= 2000000) return 250;
        if ($amount >= 1000000) return 100;
        if ($amount >= 500000) return 50;
        return 0;
    }
}
