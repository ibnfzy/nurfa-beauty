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

if (!function_exists('get_monthly_behavior_reward')) {
    /**
     * Dapatkan reward promo perilaku pelanggan berdasarkan jumlah produk yang dibeli per bulan.
     *
     * Tingkat reward:
     * - Beli >= 10 produk / bulan: Reward Super Fan (Diskon 15% / Rp 50.000 + 100 Poin)
     * - Beli >= 5 produk / bulan:  Reward Loyal Shopper (Diskon 10% / Rp 25.000 + 50 Poin)
     * - Beli >= 3 produk / bulan:  Reward Smart Buyer (Diskon 5% / Rp 10.000 + 20 Poin)
     *
     * @param int $monthlyProductCount Total produk dibeli bulan berjalan
     * @return array
     */
    function get_monthly_behavior_reward(int $monthlyProductCount): array
    {
        $tiers = [
            [
                'target'       => 10,
                'title'        => 'Super Fan Reward',
                'badge'        => '10+ Produk / Bulan',
                'discount_pct' => 15,
                'max_discount' => 50000,
                'bonus_points' => 100,
                'description'  => 'Diskon 15% (maks Rp 50.000) + Bonus 100 Poin Loyalitas',
            ],
            [
                'target'       => 5,
                'title'        => 'Loyal Shopper Reward',
                'badge'        => '5+ Produk / Bulan',
                'discount_pct' => 10,
                'max_discount' => 25000,
                'bonus_points' => 50,
                'description'  => 'Diskon 10% (maks Rp 25.000) + Bonus 50 Poin Loyalitas',
            ],
            [
                'target'       => 3,
                'title'        => 'Smart Buyer Reward',
                'badge'        => '3+ Produk / Bulan',
                'discount_pct' => 5,
                'max_discount' => 10000,
                'bonus_points' => 20,
                'description'  => 'Diskon 5% (maks Rp 10.000) + Bonus 20 Poin Loyalitas',
            ],
        ];

        $currentReward = null;
        $nextTarget    = null;

        foreach ($tiers as $tier) {
            if ($monthlyProductCount >= $tier['target']) {
                $currentReward = $tier;
                break;
            }
        }

        // Cari target tier berikutnya
        $reversed = array_reverse($tiers);
        foreach ($reversed as $tier) {
            if ($monthlyProductCount < $tier['target']) {
                $nextTarget = [
                    'target'    => $tier['target'],
                    'title'     => $tier['title'],
                    'remaining' => $tier['target'] - $monthlyProductCount,
                    'progress'  => round(($monthlyProductCount / $tier['target']) * 100, 1),
                ];
                break;
            }
        }

        return [
            'monthly_product_count' => $monthlyProductCount,
            'active_reward'         => $currentReward,
            'next_target'           => $nextTarget,
            'is_qualified'          => $currentReward !== null,
        ];
    }
}
