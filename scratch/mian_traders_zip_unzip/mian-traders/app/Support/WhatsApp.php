<?php

namespace App\Support;

/**
 * Small helper for WhatsApp links (Pakistan default).
 */
class WhatsApp
{
    /**
     * Convert a phone string to a WhatsApp-friendly digits-only number.
     *
     * Rules:
     * - strips non-digits
     * - if starts with 0 (Pakistan local), converts to 92xxxxxxxxxx
     */
    public static function toNumber(?string $phone): string
    {
        $phone = trim((string) $phone);
        if ($phone === '') return '';

        $p = preg_replace('/[^0-9]/', '', $phone) ?? '';
        if ($p === '') return '';

        // If starts with 0 (local PK), convert to 92xxxxxxxxxx
        if (strlen($p) >= 11 && str_starts_with($p, '0')) {
            $p = '92' . substr($p, 1);
        }

        return $p;
    }

    /**
     * Build a WhatsApp wa.me link. Returns null if phone is empty/invalid.
     */
    public static function url(?string $phone, ?string $text = null): ?string
    {
        $n = self::toNumber($phone);
        if ($n === '') return null;

        $u = 'https://wa.me/' . $n;

        if ($text !== null && trim($text) !== '') {
            $u .= '?text=' . rawurlencode($text);
        }

        return $u;
    }
}
