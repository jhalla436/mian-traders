<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class LocalSmsService
{
    public static function configured(): bool
    {
        return (bool)(env('SMS_API_URL') && env('SMS_API_TOKEN'));
    }

    public static function send(string $to, string $message): void
    {
        // Normalize Pakistani number: 03xx... -> 92xxx...
        $digits = preg_replace('/\D+/', '', $to);
        if (str_starts_with($digits, '0')) {
            $digits = '92' . substr($digits, 1);
        }

        $url   = env('SMS_API_URL');
        $token = env('SMS_API_TOKEN');
        $mask  = env('SMS_MASK', 'MIANTRADERS'); // sender name if provider supports

        if (!$url || !$token) {
            throw new \RuntimeException('SMS provider not configured (SMS_API_URL / SMS_API_TOKEN missing in .env).');
        }

        // Generic POST payload (you will adjust keys to match provider)
        $resp = Http::timeout(20)
            ->asForm()
            ->post($url, [
                'token'   => $token,
                'to'      => $digits,
                'message' => $message,
                'mask'    => $mask,
            ]);

        if (!$resp->successful()) {
            throw new \RuntimeException('SMS failed: ' . $resp->status() . ' ' . $resp->body());
        }
    }
}