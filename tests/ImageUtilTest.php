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
    protected function setUp()
    {
        $resourceImg = imagecreatetruecolor(500, 100);
        $this->object = new ImageUtil($resourceImg);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

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
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testRotate()
    {
        $image = new ImageUtil(__DIR__ . '/assets/rotate.bmp');

        $this->object->rotate(45, 230);

        $this->assertEquals(
            $this->getResourceString($image->getImage()),
            $this->getResourceString($this->object->getImage())
        );
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipVertical()
    {
        $image = new ImageUtil(__DIR__ . '/assets/flip-vertical.bmp');

        $this->object->rotate(10, 230);
        $this->object->flip(Flip::VERTICAL);

        $this->assertEquals(
            $this->getResourceString($image->getImage()),
            $this->getResourceString($this->object->getImage())
        );
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipBoth()
    {
        $image = new ImageUtil(__DIR__ . '/assets/flip-both.bmp');

        $this->object->rotate(80, 230);
        $this->object->flip(Flip::BOTH);

        $this->assertEquals(
            $this->getResourceString($image->getImage()),
            $this->getResourceString($this->object->getImage())
        );
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipHorizontal()
    {
        $image = new ImageUtil(__DIR__ . '/assets/flip-horizontal.bmp');

        $this->object->rotate(80, 230);
        $this->object->flip(Flip::HORIZONTAL);

        $this->assertEquals(
            $this->getResourceString($image->getImage()),
            $this->getResourceString($this->object->getImage())
        );
    }

    public function testResize()
    {
        // Create the object
        $resourceImg = imagecreatetruecolor(800, 30);
        $expected = $this->getResourceString($resourceImg);

        $this->object->resize(800, 30);
        $result = $this->getResourceString($this->object->getImage());

        $this->assertEquals($expected, $result);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testResizeSquare()
    {
        $image = new ImageUtil(__DIR__ . '/assets/resize_square.bmp');

        $this->object->resizeSquare(400, 255, 0, 0);

        $this->assertEquals(
            $this->getResourceString($image->getImage()),
            $this->getResourceString($this->object->getImage())
        );
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testResizeAspectRatio()
    {
        $image = new ImageUtil(__DIR__ . '/assets/resize_aspectratio.bmp');

        $this->object->resizeAspectRatio(400, 200, 255, 0, 0);

        $this->assertEquals(
            $this->getResourceString($image->getImage()),
            $this->getResourceString($this->object->getImage())
        );
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
        $this->assertEquals(
            $this->getResourceString($this->object->getImage()),
            $this->getResourceString($image->getImage())
        );

        unlink($fileName);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testSaveNewName()
    {
        $fileName = sys_get_temp_dir() . '/testing.png';

        $this->assertFileNotExists($fileName);
        try {
            $this->object->save($fileName);
            $this->assertFileExists($fileName);

            $image = new ImageUtil($fileName);
            $this->assertEquals(
                $this->getResourceString($this->object->getImage()),
                $this->getResourceString($image->getImage())
            );
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
        $expected = $this->getResourceString($this->object->getImage());

        // Do some operations
        $this->object->rotate(30);
        $this->object->flip(Flip::BOTH);
        $this->object->resizeSquare(40);

        $this->assertNotEquals($expected, $this->getResourceString($this->object->getImage()));

        $this->object->restore();

        $this->assertEquals($expected, $this->getResourceString($this->object->getImage()));

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
}
