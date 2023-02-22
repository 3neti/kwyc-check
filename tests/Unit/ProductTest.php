<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\ProductSeeder;
use App\Helpers\DataHelper;
use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function products_table_has_a_seeder()
    {
        $this->seed(ProductSeeder::class);
        $this->assertDatabaseCount(Product::class, count(DataHelper::products()));
    }

    /** @test */
    public function product_accepts_code_name_and_price()
    {
        /*** arrange ***/
        $code = $this->faker->uuid;
        $name = $this->faker->name;
        $price = $this->faker->numberBetween(100,10000);

        /*** act ***/
        Product::create(compact('code', 'name', 'price'));

        /*** assert ***/
        $this->assertDatabaseHas(Product::class, compact('code', 'name', 'price'));
    }
}
