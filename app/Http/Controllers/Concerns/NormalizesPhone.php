<?php

namespace App\Http\Controllers\Concerns;

trait NormalizesPhone
{
    /**
     * Normalize a phone number to the international 62… format expected by the
     * WhatsApp (Fonnte) API. Strips non-digits, converts a leading 0 to 62,
     * and leaves an existing 62 prefix untouched.
     */
    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($digits, '62')) return $digits;
        if (str_starts_with($digits, '0'))  return '62' . substr($digits, 1);

        return '62' . $digits;
    }
}
