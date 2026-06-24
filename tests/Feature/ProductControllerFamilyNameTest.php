<?php

namespace Tests\Feature;

use App\Http\Controllers\ProductController;
use Tests\TestCase;

class ProductControllerFamilyNameTest extends TestCase
{
    public function test_family_name_strips_dimension_suffixes_with_quotes(): void
    {
        $controller = new ProductController();

        $method = new \ReflectionMethod($controller, 'deriveProductFamilyName');
        $method->setAccessible(true);

        $this->assertSame('Dura Comfort', $method->invoke($controller, 'Dura Comfort 72x36x4"'));
        $this->assertSame('Dura Memory 2in1', $method->invoke($controller, 'Dura Memory 2in1 72x39x5"'));
    }
}
