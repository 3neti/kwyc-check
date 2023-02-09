<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Database\Seeders\PackageSeeder;
use App\Models\PackageItem;
use App\Models\Package;
use App\Models\Product;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @test */
    public function packages_table_has_a_seeder()
    {
        $this->seed(PackageSeeder::class);
        $this->assertDatabaseHas('packages', [
            'code' => 'ABC', 'name' => 'Package ABC', 'price' => 1000000
        ]);
        $this->assertDatabaseHas('packages', [
            'code' => 'DEF', 'name' => 'Package DEF', 'price' => 2000000
        ]);
        $this->assertDatabaseHas('packages', [
            'code' => 'GHI', 'name' => 'Package GHI', 'price' => 3000000
        ]);
    }

    /** @test */
    public function packages_accepts_code_name_and_price()
    {
        /*** arrange ***/
        $code = $this->faker->uuid;
        $name = $this->faker->name;
        $price = $this->faker->numberBetween(100,10000);

        /*** act ***/
        Package::create(compact('code', 'name', 'price'));

        /*** assert ***/
        $this->assertDatabaseHas(Package::class, compact('code', 'name', 'price'));
    }

    /** @test  */
    public function package_item_associates_package_and_product()
    {
        /*** arrange ***/
        $package = Package::factory()->create();
        $product = Product::factory()->create();

        /*** act ***/
        $packageItem = PackageItem::make();
        $packageItem->package()->associate($package);
        $packageItem->product()->associate($product);
        $packageItem->price = $product->price;
        $packageItem->qty = 1;
        $packageItem->save();

        /*** assert ***/
        $this->assertDatabaseHas(PackageItem::class, [
            'id' => $packageItem->id,
            'package_id' => $packageItem->package_id,
            'product_id' => $packageItem->product_id,
            'price' => $packageItem->price,
            'qty' => $packageItem->qty,
        ]);
        $this->assertTrue($package->is($packageItem->package));
        $this->assertTrue($product->is($packageItem->product));
        $this->assertEquals($product->price, $packageItem->price);
        $this->assertEquals(1, $packageItem->qty);
    }

    /** @test  */
    public function package_saves_package_item()
    {
        /*** arrange ***/
        $package = Package::factory()->create();
        $product = Product::factory()->create();
        $packageItem = PackageItem::make();
        $packageItem->product()->associate($product);
        $packageItem->price = $product->price;
        $packageItem->qty = 1;

        /*** act ***/
        $package->packageItems()->save($packageItem);

        /*** assert ***/
        $this->assertDatabaseHas(PackageItem::class, [
            'id' => $packageItem->id,
            'package_id' => $packageItem->package_id,
            'product_id' => $packageItem->product_id,
            'price' => $packageItem->price,
            'qty' => $packageItem->qty,
        ]);
        $this->assertTrue($package->is($packageItem->package));
        $this->assertTrue($product->is($packageItem->product));
        $this->assertEquals($product->price, $packageItem->price);
        $this->assertEquals(1, $packageItem->qty);
    }
}
