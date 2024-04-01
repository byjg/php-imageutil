<?php

namespace Test;

use ByJG\ImageUtil\AlphaColor;
use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use ByJG\ImageUtil\Handler\ImageHandlerInterface;
use ByJG\ImageUtil\ImageUtil;

class GdHandlerTest extends Base
{
    /**
     * @var ImageHandlerInterface
     */
    protected ImageHandlerInterface $gdHandler;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp(): void
    {
        $this->gdHandler = ImageUtil::empty(500, 100);
    }

    public function testGetWidth()
    {
        $this->assertSame(500, $this->gdHandler->getWidth());
    }

    public function testGetHeight()
    {
        $this->assertSame(100, $this->gdHandler->getHeight());
    }

    protected function getResourceString($resourceImg)
    {
        ob_start();
        imagegd($resourceImg);
        $resourceStr = ob_get_contents();
        ob_end_clean();

        return base64_encode($resourceStr);
    }

    public function testGetImage()
    {
        // Create the object
        $resourceImg = imagecreatetruecolor(500, 100);
        $expected = $this->getResourceString($resourceImg);

        $result = $this->getResourceString($this->gdHandler->getResource());

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testRotate()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/rotate.png');

        $this->gdHandler->rotate(45, 230);

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipVertical()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/flip-vertical.png');

        $this->gdHandler->rotate(10, 230);
        $this->gdHandler->flip(Flip::VERTICAL);

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipBoth()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/flip-both.png');

        $this->gdHandler->rotate(80, 230);
        $this->gdHandler->flip(Flip::BOTH);

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipHorizontal()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/flip-horizontal.png');

        $this->gdHandler->rotate(80, 230);
        $this->gdHandler->flip(Flip::HORIZONTAL);

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    public function testResize()
    {
        // Create the object
        $resourceImg = ImageUtil::empty(800, 30);

        $this->gdHandler->resize(800, 30);

        $this->assertImageSimilar($resourceImg, $this->gdHandler);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testResizeSquare()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/resize-square.png');

        $this->gdHandler->resizeSquare(400, new Color(255, 0, 0));

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    public function testResizeSquareTransparent()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/resize-square2.png');

        $this->gdHandler->resizeSquare(400, new AlphaColor(255, 0, 0));

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testResizeAspectRatio()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/resize-aspectratio.png');

        $this->gdHandler->resizeAspectRatio(400, 200, new Color(255, 0, 0));

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    /**
     * @todo   Implement testStampImage().
     */
    public function testStampImage()
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(600, 400);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::BOTTOM_RIGHT, 0);

        $expected = ImageUtil::fromFile(__DIR__ . '/assets/stamp-expected.png');
        $this->assertImageSimilar($expected, $bgImage);
    }

    /**
     * @todo   Implement testWriteText().
     */
    public function testWriteText()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @todo   Implement testCrop().
     */
    public function testCrop()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testSaveDefault()
    {
        $fileName = $this->gdHandler->getFilename();
        $this->assertEmpty($fileName);

        $fileName = sys_get_temp_dir() . '/testing.png';
        $this->gdHandler->save($fileName);
        $this->assertFileExists($fileName);

        $image = ImageUtil::fromFile($fileName);
        $this->assertImageSimilar($image, $this->gdHandler);

        unlink($fileName);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testSaveNewName()
    {
        $fileName = sys_get_temp_dir() . '/testing.png';

        $this->assertFileDoesNotExist($fileName);
        try {
            $this->gdHandler->save($fileName);
            $this->assertFileExists($fileName);

            $image = ImageUtil::fromFile($fileName);
            $this->assertImageSimilar($image, $this->gdHandler);
        } finally {
            unlink($fileName);
        }
    }

    /**
     * @todo   Implement testShow().
     */
    public function testShow()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @throws ImageUtilException
     */
    public function testRestore()
    {
        $expected = clone $this->gdHandler;

        // Do some operations
        $this->gdHandler->rotate(30);
        $this->gdHandler->flip(Flip::BOTH);
        $this->gdHandler->resizeSquare(40);

        $this->assertImageNotSimilar($expected, $this->gdHandler);

        $this->gdHandler->restore();

        $this->assertImageSimilar($expected, $this->gdHandler);

    }

    /**
     * @todo   Implement testMakeTransparent().
     */
    public function testMakeTransparent()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testSaveAllFormats()
    {
        $image = ImageUtil::fromFile(__DIR__ . '/assets/flip-both.png');

        $fileList = [
            sys_get_temp_dir() . '/anim2.png',
            sys_get_temp_dir() . '/test.gif',
            sys_get_temp_dir() . '/test.jpg',
            sys_get_temp_dir() . '/test.bmp',
            sys_get_temp_dir() . '/test.webp',
        ];

        // Delete file if exists
        foreach ($fileList as $item) {
            if (file_exists($item)) {
                unlink($item);
            }
        }

        // Save to different formats
        foreach ($fileList as $filename) {
            $this->assertFileDoesNotExist($filename);
            $image->save($filename);
            $this->assertFileExists($filename);
            ImageUtil::fromFile($filename);
        }
    }
}
