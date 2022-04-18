<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_statuses')->insert([
                [
                    'title' => 'Онлайн',
                    'slug' => 'success'
                ],
                [
                    'title' => 'Отошел',
                    'slug' => 'warning'
                ],
                [
                    'title' => 'Не беспокоить',
                    'slug' => 'danger'
                ]
            ]
        );
    }
}
