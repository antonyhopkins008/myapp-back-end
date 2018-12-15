<?php

namespace App\Tests;


use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase {
    public function testAdd()
    {
        $this->assertEquals(4, 2 + 2, 'Four is expected to equal 2+2');
    }
}
