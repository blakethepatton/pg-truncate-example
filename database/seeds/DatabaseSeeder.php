<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Product::class, 50)->create();
        factory(\App\User::class, 500)->create();
        factory(\App\Payment::class, 2000)->create(); // make 2000 orders, payments
    }
}
