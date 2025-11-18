<?php

namespace Test;

use ByJG\ImageUtil\AlphaColor;
use ByJG\ImageUtil\Color;
use PHPUnit\Framework\TestCase;

class ColorTest extends TestCase
{
    public function testConstructor()
    {
        $color = new Color(255, 128, 64);
        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(128, $color->getGreen());
        $this->assertEquals(64, $color->getBlue());
        $this->assertNull($color->getAlpha());
    }

    public function testFromHexSixDigit()
    {
        $color = Color::fromHex('#FF8040');
        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(128, $color->getGreen());
        $this->assertEquals(64, $color->getBlue());
    }

    public function testFromHexSixDigitNoHash()
    {
        $color = Color::fromHex('FF8040');
        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(128, $color->getGreen());
        $this->assertEquals(64, $color->getBlue());
    }

    public function testFromHexThreeDigit()
    {
        $color = Color::fromHex('#F84');
        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(136, $color->getGreen());
        $this->assertEquals(68, $color->getBlue());
    }

    public function testFromHexThreeDigitNoHash()
    {
        $color = Color::fromHex('F84');
        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(136, $color->getGreen());
        $this->assertEquals(68, $color->getBlue());
    }

    public function testFromHexBlack()
    {
        $color = Color::fromHex('#000000');
        $this->assertEquals(0, $color->getRed());
        $this->assertEquals(0, $color->getGreen());
        $this->assertEquals(0, $color->getBlue());
    }

    public function testFromHexWhite()
    {
        $color = Color::fromHex('#FFFFFF');
        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(255, $color->getGreen());
        $this->assertEquals(255, $color->getBlue());
    }

    public function testGetHex()
    {
        $color = new Color(255, 128, 64);
        $this->assertEquals('#ff8040', $color->getHex());
    }

    public function testGetHexBlack()
    {
        $color = new Color(0, 0, 0);
        $this->assertEquals('#000000', $color->getHex());
    }

    public function testGetHexWhite()
    {
        $color = new Color(255, 255, 255);
        $this->assertEquals('#ffffff', $color->getHex());
    }

    public function testGetRgb()
    {
        $color = new Color(255, 128, 64);
        $this->assertEquals('rgb(255,128,64)', $color->getRgb());
    }

    public function testGetRgbBlack()
    {
        $color = new Color(0, 0, 0);
        $this->assertEquals('rgb(0,0,0)', $color->getRgb());
    }

    public function testGetRgbWhite()
    {
        $color = new Color(255, 255, 255);
        $this->assertEquals('rgb(255,255,255)', $color->getRgb());
    }

    // AlphaColor tests
    public function testAlphaColorConstructorWithAlpha()
    {
        $color = new AlphaColor(255, 128, 64, 100);
        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(128, $color->getGreen());
        $this->assertEquals(64, $color->getBlue());
        $this->assertEquals(100, $color->getAlpha());
    }

    public function testAlphaColorConstructorDefaultAlpha()
    {
        $color = new AlphaColor(255, 128, 64);
        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(128, $color->getGreen());
        $this->assertEquals(64, $color->getBlue());
        $this->assertEquals(127, $color->getAlpha());
    }

    public function testAlphaColorFromHex()
    {
        $color = AlphaColor::fromHex('#FF8040');
        $this->assertEquals(255, $color->getRed());
        $this->assertEquals(128, $color->getGreen());
        $this->assertEquals(64, $color->getBlue());
        $this->assertEquals(127, $color->getAlpha());
    }

    public function testAlphaColorGetRgb()
    {
        $color = new AlphaColor(255, 128, 64, 100);
        $this->assertEquals('rgba(255,128,64,100.000000)', $color->getRgb());
    }

    public function testAlphaColorGetHex()
    {
        $color = new AlphaColor(255, 128, 64, 100);
        $this->assertEquals('#ff8040', $color->getHex());
    }

    public function testAlphaColorFullyOpaque()
    {
        $color = new AlphaColor(255, 0, 0, 0);
        $this->assertEquals(0, $color->getAlpha());
    }

    public function testAlphaColorFullyTransparent()
    {
        $color = new AlphaColor(255, 0, 0, 127);
        $this->assertEquals(127, $color->getAlpha());
    }
}
