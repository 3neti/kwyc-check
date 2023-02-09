<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    /** @var Order */
    protected $order;

    /** @var OrderItem */
    protected $orderItem;

    /** @var User */
    protected $user;

    /** @var Product */
    protected $product;


    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->createQuietly();
        $this->product = Product::factory()->create();
        $this->order = tap(Order::make([]), function (Order $order) {
            $order->user()->associate($this->user);
            $order->save();
        });
    }

    /** @test  */
    public function order_associates_user()
    {
        /*** arrange ***/

        /*** act ***/
        $order = Order::make([]);
        $order->user()->associate($this->user);
        $order->save();

        /*** assert ***/
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
        ]);
        $this->assertTrue($this->user->is($order->user));
    }

    /** @test  */
    public function order_item_associates_order_and_product()
    {
        /*** arrange ***/

        /*** act ***/
        $orderItem = OrderItem::make();
        $orderItem->order()->associate($this->order);
        $orderItem->product()->associate($this->product);
        $orderItem->price = $this->product->price;
        $orderItem->qty = 1;
        $orderItem->save();

        /*** assert ***/
        $this->assertDatabaseHas(OrderItem::class, [
            'id' => $orderItem->id,
            'order_id' => $orderItem->order_id,
            'product_id' => $orderItem->product_id,
            'price' => $orderItem->price,
            'qty' => $orderItem->qty,
        ]);
        $this->assertTrue($this->order->is($orderItem->order));
        $this->assertTrue($this->product->is($orderItem->product));
        $this->assertEquals($this->product->price, $orderItem->price);
        $this->assertEquals(1, $orderItem->qty);
    }

    /** @test  */
    public function order_saves_order_item()
    {
        /*** arrange ***/
        $orderItem = OrderItem::make();
        $orderItem->product()->associate($this->product);
        $orderItem->price = $this->product->price;
        $orderItem->qty = 1;

        /*** act ***/
        $this->order->orderItems()->save($orderItem);

        /*** assert ***/
        $this->assertDatabaseHas(OrderItem::class, [
            'id' => $orderItem->id,
            'order_id' => $orderItem->order_id,
            'product_id' => $orderItem->product_id,
            'price' => $orderItem->price,
            'qty' => $orderItem->qty,
        ]);
        $this->assertTrue($this->order->is($orderItem->order));
        $this->assertTrue($this->product->is($orderItem->product));
        $this->assertEquals($this->product->price, $orderItem->price);
        $this->assertEquals(1, $orderItem->qty);
    }
}
