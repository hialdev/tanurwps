<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Path file SQL
        $filePath = database_path('sql/initial_settings.sql');

        // Membaca isi file SQL
        $sql = file_get_contents($filePath);

        // Eksekusi SQL
        DB::unprepared($sql);

        // Tampilkan pesan sukses
        $this->command->info('Inital Settings table seeded successfully!');
    }
}
