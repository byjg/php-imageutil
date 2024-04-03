<?php

namespace Test;

use ByJG\ImageUtil\ImageUtil;
use PHPUnit\Framework\TestCase;

class ImageUtilTest extends TestCase
{
    public function testFromFile()
    {
        $image = ImageUtil::fromFile(__DIR__ . '/assets/anim2.svg');
        $image = ImageUtil::fromFile(__DIR__ . '/assets/flip-both.png');

        $this->assertTrue(true);
    }
}
