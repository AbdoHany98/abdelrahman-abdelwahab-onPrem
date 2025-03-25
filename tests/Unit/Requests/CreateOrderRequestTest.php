<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\CreateOrderRequest;
use Illuminate\Support\Facades\Validator;

class CreateOrderRequestTest extends TestCase
{
    /** @test */
    public function it_validates_order_creation_with_valid_data()
    {
        $request = new CreateOrderRequest();
        $validator = Validator::make([
            'items' => [
                ['name' => 'Product', 'price' => 10.99, 'quantity' => 2]
            ]
        ], $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_fails_validation_with_missing_items()
    {
        $request = new CreateOrderRequest();
        $validator = Validator::make([], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertNotEmpty($validator->errors()->get('items'));
    }

    /** @test */
    public function it_fails_validation_with_invalid_item_details()
    {
        $request = new CreateOrderRequest();
        $validator = Validator::make([
            'items' => [
                ['name' => '', 'price' => -10, 'quantity' => 0]
            ]
        ], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertNotEmpty($validator->errors()->get('items.0.name'));
        $this->assertNotEmpty($validator->errors()->get('items.0.price'));
        $this->assertNotEmpty($validator->errors()->get('items.0.quantity'));
    }
}