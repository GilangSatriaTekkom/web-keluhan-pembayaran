<?php

use Carbon\Carbon;

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        return 'Rp ' . number_format($angka, 0, ',', '.');
    }
}

if (!function_exists('formatTanggalIndonesia')) {
    function formatTanggalIndonesia($tanggal)
    {
        return Carbon::parse($tanggal)->translatedFormat('d F Y');
    }
}

if (!function_exists('alert')) {
    function alert($title, $message = '', $type = 'success')
    {
        session()->flash('alert', [
            'title' => $title,
            'message' => $message,
            'type' => $type
        ]);
    }
}
