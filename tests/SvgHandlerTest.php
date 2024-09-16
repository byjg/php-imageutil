<?php

namespace Test;

use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Handler\GdHandler;
use ByJG\ImageUtil\Handler\SvgHandler;
use ByJG\ImageUtil\ImageUtil;

class SvgHandlerTest extends Base
{
    /**
     * @var SvgHandler
     */
    protected SvgHandler $svgHandler;

    protected function setUp(): void
    {
        $this->svgHandler = new SvgHandler();
    }

    public function testEmpty(): void
    {
        $this->svgHandler->empty(200, 400, new Color(255, 0, 0));
        $this->assertSame(200, $this->svgHandler->getWidth());
        $this->assertSame(400, $this->svgHandler->getHeight());

        $this->assertEquals(
            '<?xml version="1.0" encoding="utf-8"?>' .
            '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="200" height="400">' .
            '<rect x="0" y="0" width="200" height="400" style="fill: #ff0000" />' .
            '</svg>',
            $this->svgHandler->getResource()->toXMLString()
        );
    }

    public function testSave(): void
    {
        $this->svgHandler->empty(500, 500);
        $filename = sys_get_temp_dir() . '/test.svg';
        $this->svgHandler->save($filename);
        $this->assertFileExists($filename);
        $this->assertEquals(
            '<?xml version="1.0" encoding="utf-8"?>' .
            '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="500" height="500" />',
            file_get_contents($filename)
        );
        unlink($filename);
        $this->assertFileDoesNotExist($filename);
    }

    public function testSaveToPng(): void
    {
        $filename = sys_get_temp_dir() . '/anim2.png';
        $this->svgHandler->fromFile(__DIR__ . "/assets/anim2.svg");
        $this->svgHandler->save($filename);
        $this->assertFileExists($filename);
        $this->assertImageSimilar(ImageUtil::fromFile(__DIR__ . "/assets/anim2.png"), $this->svgHandler);
        unlink($filename);
        $this->assertFileDoesNotExist($filename);
    }

    public function testGdHandlerFromSVG(): void
    {
        $this->svgHandler->fromFile(__DIR__ . "/assets/anim2.svg");
        $handler = (new GdHandler())->fromResource($this->svgHandler->getResource());
        $this->assertImageSimilar(ImageUtil::fromFile(__DIR__ . "/assets/anim2.png"), $handler);
    }
}