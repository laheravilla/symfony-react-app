<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class SimplestTest extends TestCase
{
    /** @test */
    public function addition()
    {
        $value = true;
        $array = ["key" => "value"];

        $this->assertEquals(5, 2 + 3, "Five was expected to equal 2 + 3");
        $this->assertEquals("value", $array["key"]);
        $this->assertTrue($value);
        $this->assertArrayHasKey("key", $array);
        $this->assertCount(1, $array);
    }
}