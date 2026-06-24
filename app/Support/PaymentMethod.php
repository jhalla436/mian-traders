<?php

namespace App\Support;

class PaymentMethod
{
    public const HARD_CASH = 'hard_cash';
    public const JAZZCASH = 'jazzcash';
    public const EASYPAISA = 'easypaisa';
    public const BANK_TRANSFER = 'bank_transfer';
    public const RAAST = 'raast';

    public static function options(): array
    {
        return [
            self::HARD_CASH => 'Hard Cash',
            self::JAZZCASH => 'JazzCash',
            self::EASYPAISA => 'EasyPaisa',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::RAAST => 'Raast Payment',
        ];
    }

    public static function keys(): array
    {
        return array_keys(self::options());
    }

    public static function normalize(?string $value): ?string
    {
        $value = strtolower(trim((string) $value));
        if ($value === '') {
            return null;
        }

        return match ($value) {
            'cash', 'hard cash', 'hard_cash' => self::HARD_CASH,
            'jazzcash', 'jazz cash' => self::JAZZCASH,
            'easypaisa', 'easy paisa', 'easy_paisa', 'easy pasa', 'easypasa' => self::EASYPAISA,
            'bank', 'bank transfer', 'bank_transfer' => self::BANK_TRANSFER,
            'raast', 'raast payment', 'rasst', 'rasst payment' => self::RAAST,
            default => null,
        };
    }

    public static function label(?string $value): string
    {
        $normalized = self::normalize($value);

        return self::options()[$normalized] ?? 'Payment Method Not Recorded';
    }
}
