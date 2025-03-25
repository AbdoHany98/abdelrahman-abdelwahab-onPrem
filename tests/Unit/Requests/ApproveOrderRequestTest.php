<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\ApproveOrderRequest;
use Illuminate\Support\Facades\Validator;

class ApproveOrderRequestTest extends TestCase
{
    /** @test */
    public function it_validates_approval_request_with_valid_data()
    {
        $request = new ApproveOrderRequest();
        $validator = Validator::make([
            'approved' => true,
            'notes' => 'Approved by manager'
        ], $request->rules());

        $this->assertTrue($validator->passes());
    }

    /** @test */
    public function it_requires_approved_boolean()
    {
        $request = new ApproveOrderRequest();
        $validator = Validator::make([], $request->rules());

        $this->assertFalse($validator->passes());
        $this->assertNotEmpty($validator->errors()->get('approved'));
    }

    /** @test */
    public function it_allows_optional_notes_with_max_length()
    {
        $request = new ApproveOrderRequest();
        $longNotes = str_repeat('a', 501);

        // Test valid notes
        $validator = Validator::make([
            'approved' => true,
            'notes' => 'Valid approval notes'
        ], $request->rules());
        $this->assertTrue($validator->passes());

        // Test notes exceeding max length
        $validator = Validator::make([
            'approved' => true,
            'notes' => $longNotes
        ], $request->rules());
        $this->assertFalse($validator->passes());
    }
}