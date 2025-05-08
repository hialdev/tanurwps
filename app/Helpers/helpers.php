<?php

use Illuminate\Support\Facades\Schema;

if (!function_exists('getModelAttributes')) {
    function getModelAttributes($modelName, $excepts)
    {
        $excepts = $excepts ?? [];
        // Validasi input nama model
        if (empty($modelName) || !preg_match('/^[a-zA-Z0-9_]+$/', $modelName)) {
            return [];
        }

        $modelClass = "\App\Models\\".$modelName;

        // Pastikan kelas model ada
        if (!class_exists($modelClass)) {
            return [];
        }

        // Buat instance model
        $model = new $modelClass();

        // Pastikan tabel model ada
        if (!Schema::hasTable($model->getTable())) {
            return [];
        }

        // Ambil nama tabel dan kolom
        $table = $model->getTable();
        $columns = Schema::getColumnListing($table);

        // Keluarkan kolom 'id' dari daftar
        return array_values(array_filter($columns, fn($column) => $column !== 'id' && !in_array($column, $excepts)));

    }
}

if (!function_exists('toPascalCase')){
    function toPascalCase($string)
    {
        // Pisahkan string dengan underscore (_)
        $words = explode('_', $string);
        // Ubah setiap kata menjadi kapital dan gabungkan
        $pascalCaseString = implode(' ', array_map('ucwords', $words));
        // Pastikan huruf pertama adalah kapital
        return $pascalCaseString;
    }
}

if (!function_exists('urlApp')) {
    function urlApp($code, $path = '')
    {
        if (!$path) $path = '';
        if (!class_exists(\App\Models\Application::class) || !Schema::hasTable('applications')) {
            return null;
        }

        $app = \App\Models\Application::where('code', $code)->first();
        return $app ? $app->url.$path : null;
    }
}

if (!function_exists('filePath')) {
    function filePath($data)
    {
        $path = urlApp('SSO','/storage/'.$data);
        return $path;
    }
}

if (!function_exists('setting')) {
    function setting($code)
    {
        // Cek apakah model Setting dan tabel settings ada
        if (!class_exists(\App\Models\Setting::class) || !Schema::hasTable('settings')) {
            return null;
        }

        $explode = explode('.', $code);

        // Pastikan array $explode memiliki setidaknya dua elemen
        if (count($explode) < 2) {
            return null;
        }

        $setting = \App\Models\Setting::where('group_key', $explode[0])
            ->where('key', $explode[1])
            ->first();

        return $setting->value ?? null;
    }
}


if(!function_exists('formatRupiah')){
    function formatRupiah($angka) {
        return "Rp " . number_format($angka, 0, ',', '.');
    }
}

if(!function_exists('parseRupiah')){
    function parseRupiah($value){
        return (int) str_replace(['Rp', '.', ','], '', $value);
    }
}

if (!function_exists('generateCode')) {
    function generateCode($type, $number, $companyCode = null, $month = null, $year = null)
    {
        $companyCode = $companyCode ?? config('al.company')['code']; // Default ke config
        $month = $month ?? date('n'); // Default ke bulan sekarang
        $year = $year ?? date('y');  // Default ke tahun sekarang

        $formattedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        $romanMonths = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $romanMonth = $romanMonths[$month - 1]; // Ambil berdasarkan indeks

        return "{$type}/{$formattedNumber}/{$companyCode}/{$romanMonth}/{$year}";
    }
}

if (!function_exists('isImage')) {
    function isImage($filePath)
    {
        $storagePath = storage_path('app/public/' . $filePath);

        // Check if the file exists
        if (!file_exists($storagePath)) {
            return false;
        }

        // Get MIME type of the file
        $mimeType = mime_content_type($storagePath);

        // Return true if MIME type starts with "image/"
        return strpos($mimeType, 'image/') === 0;
    }
}

if (!function_exists('isPdf')) {
    function isPdf($fileName)
    {
        return str_ends_with($fileName, '.pdf');
    }
}

if (!function_exists('createHistory')) {
    // function createHistory($)
}