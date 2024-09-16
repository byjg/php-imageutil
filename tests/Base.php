<?php

namespace Test;

use ByJG\ImageUtil\Handler\ImageHandlerInterface;
use Imagick;
use ImagickException;
use PHPUnit\Framework\TestCase;

class Base extends TestCase
{
    /**
     * @param ImageHandlerInterface $expected
     * @param ImageHandlerInterface $actual
     * @param float $threshold
     * @param bool $lessThan
     * @return void
     * @throws ImagickException
     */
    protected function assertImages(ImageHandlerInterface $expected, ImageHandlerInterface $actual, float $threshold, bool $lessThan)
    {
        if (!class_exists('\imagick')) {
            $this->markTestIncomplete('PECL Imagick not installed');
        }
        $expected->save(sys_get_temp_dir() . '/expected.png');
        $actual->save(sys_get_temp_dir() . '/actual.png');

        $image1 = new Imagick(sys_get_temp_dir() . '/expected.png');
        $image2 = new Imagick(sys_get_temp_dir() . '/actual.png');

        $result = $image1->compareImages($image2, Imagick::METRIC_MEANSQUAREERROR);
        $lessThan ? $this->assertLessThan($threshold, $result[1]) : $this->assertGreaterThanOrEqual($threshold, $result[1]);
    }

    protected function assertImageSimilar(ImageHandlerInterface $expected, ImageHandlerInterface $actual): void
    {
        $this->assertImages($expected, $actual, 0.1, true);
    }

    protected function assertImageNotSimilar(ImageHandlerInterface $expected, ImageHandlerInterface $actual): void
    {
        $this->assertImages($expected, $actual, 0.1, false);
    }

}