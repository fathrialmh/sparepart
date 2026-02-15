<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class NumberGenerator
{
    public static function generateRunningNumber(string $table, string $column, string $prefix): string
    {
        $ym = now()->format('Ym');
        $start = $prefix . '-' . $ym . '-';

        $lastNumber = DB::table($table)
            ->where($column, 'like', $start . '%')
            ->orderByDesc('id')
            ->value($column);

        if (!$lastNumber) {
            return $start . '0001';
        }

        $lastSeq = (int) substr((string) $lastNumber, -4);
        $newSeq = str_pad((string) ($lastSeq + 1), 4, '0', STR_PAD_LEFT);

        return $start . $newSeq;
    }

    public static function generateMasterCode(string $table, string $column, string $prefix, int $pad = 4): string
    {
        $lastCode = (string) (DB::table($table)->orderByDesc('id')->value($column) ?? '');

        if ($lastCode === '' || !str_starts_with($lastCode, $prefix . '-')) {
            return $prefix . '-' . str_pad('1', $pad, '0', STR_PAD_LEFT);
        }

        $lastSeq = (int) substr($lastCode, -$pad);
        $newSeq = str_pad((string) ($lastSeq + 1), $pad, '0', STR_PAD_LEFT);

        return $prefix . '-' . $newSeq;
    }

    public static function rupiah(float $value): string
    {
        return 'Rp ' . number_format($value, 0, ',', '.');
    }
}
