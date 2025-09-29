<?php

namespace App\Services;


use App\Models\Dish;


class CartPricing
{
    public static function unitPriceAfterDiscount(Dish $dish): float
    {
        $price = (float) $dish->price;
        if ($dish->discount_type && $dish->discount) {
            if ($dish->discount_type === 'amount') {
                $price = max(0, $price - (float) $dish->discount);
            } else { // percent
                $price = max(0, $price - ($price * ((float)$dish->discount / 100)));
            }
        }
        return round($price, 2);
    }


    public static function buildItemTotals(float $unitAfterDiscount, float $optionsTotalPerUnit, ?float $vatPercent, int $qty): array
    {
        $lineSubtotal = ($unitAfterDiscount + $optionsTotalPerUnit) * $qty; // before VAT
        $vat = $vatPercent ? $lineSubtotal * ($vatPercent / 100) : 0;
        $lineTotal = $lineSubtotal + $vat;
        return [
            'line_subtotal' => round($lineSubtotal, 2),
            'line_vat' => round($vat, 2),
            'line_total' => round($lineTotal, 2),
        ];
    }
}
