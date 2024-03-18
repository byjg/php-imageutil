<?php

use ByJG\ImageUtil\AlphaColor;
use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use ByJG\ImageUtil\Handler\ImageHandlerInterface;
use ByJG\ImageUtil\ImageUtil;
use PHPUnit\Framework\TestCase;

class ImageUtilTest extends TestCase
{
    /**
     * @var ImageHandlerInterface
     */
    protected ImageHandlerInterface $actual;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     */
    protected function setUp(): void
    {
        $this->actual = ImageUtil::empty(500, 100);
    }

    public function testGetWidth()
    {
        $this->assertSame(500, $this->actual->getWidth());
    }

    public function testGetHeight()
    {
        $this->assertSame(100, $this->actual->getHeight());
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

        $result = $this->getResourceString($this->actual->getResource());

        $this->assertEquals($expected, $result);
    }

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

    protected function assertImageSimilar($expected, $actual)
    {
        $this->assertImages($expected, $actual, 0.1, true);
    }

    protected function assertImageNotSimilar($expected, $actual)
    {
        $this->assertImages($expected, $actual, 0.1, false);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testRotate()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/rotate.png');

        $this->actual->rotate(45, 230);

        $this->assertImageSimilar($expected, $this->actual);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipVertical()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/flip-vertical.png');

        $this->actual->rotate(10, 230);
        $this->actual->flip(Flip::VERTICAL);

        $this->assertImageSimilar($expected, $this->actual);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipBoth()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/flip-both.png');

        $this->actual->rotate(80, 230);
        $this->actual->flip(Flip::BOTH);

        $this->assertImageSimilar($expected, $this->actual);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipHorizontal()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/flip-horizontal.png');

        $this->actual->rotate(80, 230);
        $this->actual->flip(Flip::HORIZONTAL);

        $this->assertImageSimilar($expected, $this->actual);
    }

    public function testResize()
    {
        // Create the object
        $resourceImg = ImageUtil::empty(800, 30);

        $this->actual->resize(800, 30);

        $this->assertImageSimilar($resourceImg, $this->actual);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testResizeSquare()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/resize-square.png');

        $this->actual->resizeSquare(400, new Color(255, 0, 0));

        $this->assertImageSimilar($expected, $this->actual);
    }

    public function testResizeSquareTransparent()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/resize-square2.png');

        $this->actual->resizeSquare(400, new AlphaColor(255, 0, 0));

        $this->assertImageSimilar($expected, $this->actual);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testResizeAspectRatio()
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/resize-aspectratio.png');

        $this->actual->resizeAspectRatio(400, 200, new Color(255, 0, 0));

        $this->assertImageSimilar($expected, $this->actual);
    }

    /**
     * @todo   Implement testStampImage().
     */
    public function testStampImage()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
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
        $fileName = $this->actual->getFilename();
        $this->assertEmpty($fileName);

        $fileName = sys_get_temp_dir() . '/testing.png';
        $this->actual->save($fileName);
        $this->assertFileExists($fileName);

        $image = ImageUtil::fromFile($fileName);
        $this->assertImageSimilar($image, $this->actual);

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
            $this->actual->save($fileName);
            $this->assertFileExists($fileName);

            $image = ImageUtil::fromFile($fileName);
            $this->assertImageSimilar($image, $this->actual);
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
        $expected = clone $this->actual;

        // Do some operations
        $this->actual->rotate(30);
        $this->actual->flip(Flip::BOTH);
        $this->actual->resizeSquare(40);

        $this->assertImageNotSimilar($expected, $this->actual);

        $this->actual->restore();

        $this->assertImageSimilar($expected, $this->actual);

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
            sys_get_temp_dir() . '/test.png',
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
