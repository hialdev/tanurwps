<?php

namespace Database\Seeders;

use App\Models\Application;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $applications = [
            [
                'code' => 'SSO',
                'name' => 'sso',
                'url' => 'http://sso.teuga.test',
            ],
            [
                'code' => 'ACC',
                'name' => 'account',
                'url' => 'http://acc.teuga.test',
            ],
            [
                'code' => 'OSN',
                'name' => 'osano',
                'url' => 'http://osn.teuga.test',
            ],
            [
                'code' => 'ACCOUNTING',
                'name' => 'Accounting',
                'url' => 'http://calc.teuga.test',
            ],
        ];

        foreach ($applications as $app) {
            Application::firstOrCreate(
                ['code' => $app['code']], // Cek jika data sudah ada berdasarkan kode
                $app // Data yang akan disimpan jika belum ada
            );
        }

        $this->command->info('Applications created successfully.');
    }
}
