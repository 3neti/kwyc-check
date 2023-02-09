<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Helpers\DataHelper;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = DataHelper::products();

        foreach ($products as $product) {
            $data = [];
            $data['code'] = $product['code'];
            $data['name'] = $product['name'];
            $data['price'] = $product['price'];

            DB::table('products')->insert($data);
        }
    }
}
