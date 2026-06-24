<?php

namespace App\Support;

class Authz
{
    public static function role(): string
    {
        return (string)(auth()->user()?->role ?? 'cashier');
    }

    public static function isAdmin(): bool
    {
        return self::role() === 'admin';
    }

    public static function isManager(): bool
    {
        return self::role() === 'manager';
    }

    public static function canSeeCost(): bool
    {
        return in_array(self::role(), ['admin','manager'], true);
    }
}
