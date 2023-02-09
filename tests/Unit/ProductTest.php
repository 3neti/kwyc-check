<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\ProductSeeder;
use App\Models\Product;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function products_table_has_a_seeder()
    {
        $this->seed(ProductSeeder::class);
        $this->assertDatabaseHas('products', [
            'code' => 'ABC', 'name' => 'Product ABC', 'price' => 1000000
        ]);
        $this->assertDatabaseHas('products', [
            'code' => 'DEF', 'name' => 'Product DEF', 'price' => 2000000
        ]);
        $this->assertDatabaseHas('products', [
            'code' => 'GHI', 'name' => 'Product GHI', 'price' => 3000000
        ]);
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
