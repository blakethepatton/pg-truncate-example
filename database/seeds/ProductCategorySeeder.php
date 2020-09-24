<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('product_categories')->truncate();
        DB::table('product_categories')->insert([
            [
                'id' => 1,
                'name' => 'office'
            ],
            [
                'id' => 2,
                'name' => 'furniture'
            ],
            [
                'id' => 3,
                'name' => 'clothing'
            ],
            [
                'id' => 4,
                'name' => 'produce'
            ],
            [
                'id' => 5,
                'name' => 'homegoods'
            ],
        ]);
    }
}
