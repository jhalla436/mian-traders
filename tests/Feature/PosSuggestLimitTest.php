<?php

namespace Tests\Feature;

use App\Http\Controllers\PosController;
use Tests\TestCase;

class PosSuggestLimitTest extends TestCase
{
    public function test_pos_suggest_uses_a_large_result_limit_for_variant_searches(): void
    {
        $controller = new PosController();

        $method = new \ReflectionMethod($controller, 'suggestResultLimit');
        $method->setAccessible(true);

        $this->assertSame(250, $method->invoke($controller));
    }
}
