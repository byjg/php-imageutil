<?php

namespace ByJG\ImageUtil;

use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

class ImageUtilTest extends TestCase
{
    /**
     * @var ImageUtil
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    protected function setUp(): void
    {
        $resourceImg = imagecreatetruecolor(500, 100);
        $this->object = new ImageUtil($resourceImg);
    }

    public function testGetWidth()
    {
        $this->assertSame(500, $this->object->getWidth());
    }

    public function testGetHeight()
    {
        $this->assertSame(100, $this->object->getHeight());
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

        $result = $this->getResourceString($this->object->getImage());

        $this->assertEquals($expected, $result);
    }

    /**
     * @param ImageUtil $expected
     * @param ImageUtil $actual
     * @param float $threshold
     * @param bool $lessThan
     * @return void
     */
    protected function assertImages($expected, $actual, $threshold, $lessThan)
    {
        if (!class_exists('\imagick')) {
            $this->markTestIncomplete('PECL Imagick not installed');
        }
        $expected->save(sys_get_temp_dir() . '/expected.png');
        $actual->save(sys_get_temp_dir() . '/actual.png');

        $image1 = new \imagick(sys_get_temp_dir() . '/expected.png');
        $image2 = new \imagick(sys_get_temp_dir() . '/actual.png');

        $result = $image1->compareImages($image2, \Imagick::METRIC_MEANSQUAREERROR);
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
        $image = new ImageUtil(__DIR__ . '/assets/rotate.png');

        $this->object->rotate(45, 230);

        $this->assertImageSimilar($image, $this->object);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipVertical()
    {
        $image = new ImageUtil(__DIR__ . '/assets/flip-vertical.png');

        $this->object->rotate(10, 230);
        $this->object->flip(Flip::VERTICAL);

        $this->assertImageSimilar($image, $this->object);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipBoth()
    {
        $image = new ImageUtil(__DIR__ . '/assets/flip-both.png');

        $this->object->rotate(80, 230);
        $this->object->flip(Flip::BOTH);

        $this->assertImageSimilar($image, $this->object);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipHorizontal()
    {
        $image = new ImageUtil(__DIR__ . '/assets/flip-horizontal.png');

        $this->object->rotate(80, 230);
        $this->object->flip(Flip::HORIZONTAL);

        $this->assertImageSimilar($image, $this->object);
    }

    public function testResize()
    {
        // Create the object
        $resourceImg = new ImageUtil(imagecreatetruecolor(800, 30));

        $this->object->resize(800, 30);

        $this->assertImageSimilar($resourceImg, $this->object);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testResizeSquare()
    {
        $image = new ImageUtil(__DIR__ . '/assets/resize-square.png');

        $this->object->resizeSquare(400, 255, 0, 0);

        $this->assertImageSimilar($image, $this->object);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testResizeAspectRatio()
    {
        $image = new ImageUtil(__DIR__ . '/assets/resize-aspectratio.png');

        $this->object->resizeAspectRatio(400, 200, 255, 0, 0);

        $this->assertImageSimilar($image, $this->object);
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
        $fileName = $this->object->getFilename();

        $this->object->save();
        $this->assertFileExists($fileName);

        $image = new ImageUtil($fileName);
        $this->assertImageSimilar($image, $this->object);

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
            $this->object->save($fileName);
            $this->assertFileExists($fileName);

            $image = new ImageUtil($fileName);
            $this->assertImageSimilar($image, $this->object);
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
        $expected = clone $this->object;

        // Do some operations
        $this->object->rotate(30);
        $this->object->flip(Flip::BOTH);
        $this->object->resizeSquare(40);

        $this->assertImageNotSimilar($expected, $this->object);

        $this->object->restore();

        $this->assertImageSimilar($expected, $this->object);

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
        $image = new ImageUtil(__DIR__ . '/assets/flip-both.png');

        $fileList = [
            sys_get_temp_dir() . '/test.png',
            sys_get_temp_dir() . '/test.gif',
            sys_get_temp_dir() . '/test.jpg',
            sys_get_temp_dir() . '/test.bmp'
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
            new ImageUtil($filename);
        }
    }
}
