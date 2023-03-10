<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
		User::create([
			'name'     => env('CHAT_USER_NAME', 'USER'),
			'email'    => 'admin@local.test',
			'password' => bcrypt('password'),
		]);

		User::create([
			'name'     => env('CHAT_TARS_NAME', 'TARS'),
			'email'    => 'tars@local.test',
			'password' => bcrypt('password'),
		]);
	}
}
