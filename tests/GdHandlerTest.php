<?php

namespace Test;

use ByJG\ImageUtil\AlphaColor;
use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\FileType;
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
    #[\Override]
    protected function setUp(): void
    {
        $this->gdHandler = ImageUtil::empty(500, 100);
    }

    public function testGetWidth(): void
    {
        $this->assertSame(500, $this->gdHandler->getWidth());
    }

    public function testGetHeight(): void
    {
        $this->assertSame(100, $this->gdHandler->getHeight());
    }

    protected function getResourceString(\GdImage|\SVG\SVG|false|null $resourceImg): string
    {
        ob_start();
        imagepng($resourceImg);
        $resourceStr = ob_get_contents();
        ob_end_clean();

        return base64_encode($resourceStr);
    }

    public function testGetImage(): void
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
    public function testRotate(): void
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/rotate.png');

        $this->gdHandler->rotate(45, 230);

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testFlipVertical(): void
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
    public function testFlipBoth(): void
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
    public function testFlipHorizontal(): void
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/flip-horizontal.png');

        $this->gdHandler->rotate(80, 230);
        $this->gdHandler->flip(Flip::HORIZONTAL);

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    public function testResize(): void
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
    public function testResizeSquare(): void
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/resize-square.png');

        $this->gdHandler->resizeSquare(400, new Color(255, 0, 0));

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    public function testResizeSquareTransparent(): void
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/resize-square2.png');

        $this->gdHandler->resizeSquare(400, new AlphaColor(255, 0, 0));

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testResizeAspectRatio(): void
    {
        $expected = ImageUtil::fromFile(__DIR__ . '/assets/resize-aspectratio.png');

        $this->gdHandler->resizeAspectRatio(400, 200, new Color(255, 0, 0));

        $this->assertImageSimilar($expected, $this->gdHandler);
    }

    public function testStampImage(): void
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(600, 600);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::BOTTOM_RIGHT, 0, 0);

        $expected = ImageUtil::fromFile(__DIR__ . '/assets/stamp-expected.png');
        $this->assertImageSimilar($expected, $bgImage);
    }

    public function testWriteText(): void
    {
        $img = ImageUtil::empty(1000, 300, FileType::Png, new Color(255, 255, 255));

        $img->writeText("Hello World", [100, 100], 50, 0, __DIR__ . '/assets/Rotulonahand-aGyx.ttf', 0, new Color(0, 0, 0));

        $expected = ImageUtil::fromFile(__DIR__ . '/assets/write-expected.png');
        $this->assertImageSimilar($expected, $img);
    }

    public function testCrop(): void
    {
        $img = ImageUtil::fromFile(__DIR__ . '/assets/write-expected.png');
        $img->crop(100, 30, 300, 100);

        $expected = ImageUtil::fromFile(__DIR__ . '/assets/crop-expected.png');
        $this->assertImageSimilar($expected, $img);
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testSaveDefault(): void
    {
        $fileName = $this->gdHandler->getFilename();
        $this->assertEmpty($fileName);

        $fileName = sys_get_temp_dir() . '/testing.png';
        try {
            $this->gdHandler->save($fileName);
            $this->assertFileExists($fileName);

            $image = ImageUtil::fromFile($fileName);
            $this->assertImageSimilar($image, $this->gdHandler);
        } finally {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
    }

    /**
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function testSaveNewName(): void
    {
        $fileName = sys_get_temp_dir() . '/testing.png';

        // Clean up any leftover file from previous failed test runs
        if (file_exists($fileName)) {
            unlink($fileName);
        }

        $this->assertFileDoesNotExist($fileName);
        try {
            $this->gdHandler->save($fileName);
            $this->assertFileExists($fileName);

            $image = ImageUtil::fromFile($fileName);
            $this->assertImageSimilar($image, $this->gdHandler);
        } finally {
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
    }

    /**
     * @throws ImageUtilException
     */
    public function testRestore(): void
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

    public function testMakeTransparent(): void
    {
        $img = ImageUtil::fromFile(__DIR__ . '/assets/flip-vertical.png');
        $img->makeTransparent(Color::fromHex('#000000'));

        $expected = ImageUtil::fromFile(__DIR__ . '/assets/transparent-expected.png');
        $this->assertImageSimilar($expected, $img);
    }

    public function testSaveAllFormats(): void
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
