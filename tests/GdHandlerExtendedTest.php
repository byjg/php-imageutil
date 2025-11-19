<?php

namespace Test;

use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\FileType;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Enum\TextAlignment;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Handler\ImageHandlerInterface;
use ByJG\ImageUtil\ImageUtil;

class GdHandlerExtendedTest extends Base
{
    protected ImageHandlerInterface $gdHandler;

    #[\Override]
    protected function setUp(): void
    {
        $this->gdHandler = ImageUtil::empty(500, 100);
    }

    // Test all StampPosition enum values
    public function testStampImageTopLeft()
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(100, 100);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::TOP_LEFT, 10, 10);

        // Verify dimensions remain unchanged
        $this->assertEquals(2000, $bgImage->getWidth());
        $this->assertEquals(2000, $bgImage->getHeight());
    }

    public function testStampImageTopRight()
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(100, 100);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::TOP_RIGHT, 10, 10);

        $this->assertEquals(2000, $bgImage->getWidth());
        $this->assertEquals(2000, $bgImage->getHeight());
    }

    public function testStampImageBottomLeft()
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(100, 100);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::BOTTOM_LEFT, 10, 10);

        $this->assertEquals(2000, $bgImage->getWidth());
        $this->assertEquals(2000, $bgImage->getHeight());
    }

    public function testStampImageCenter()
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(100, 100);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::CENTER, 0, 0);

        $this->assertEquals(2000, $bgImage->getWidth());
        $this->assertEquals(2000, $bgImage->getHeight());
    }

    public function testStampImageTop()
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(100, 100);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::TOP, 0, 10);

        $this->assertEquals(2000, $bgImage->getWidth());
        $this->assertEquals(2000, $bgImage->getHeight());
    }

    public function testStampImageBottom()
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(100, 100);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::BOTTOM, 0, 10);

        $this->assertEquals(2000, $bgImage->getWidth());
        $this->assertEquals(2000, $bgImage->getHeight());
    }

    public function testStampImageLeft()
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(100, 100);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::LEFT, 10, 0);

        $this->assertEquals(2000, $bgImage->getWidth());
        $this->assertEquals(2000, $bgImage->getHeight());
    }

    public function testStampImageRight()
    {
        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(100, 100);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::RIGHT, 10, 0);

        $this->assertEquals(2000, $bgImage->getWidth());
        $this->assertEquals(2000, $bgImage->getHeight());
    }

    public function testStampImageRandom()
    {
        $this->expectException(ImageUtilException::class);
        $this->expectExceptionMessage('Invalid Stamp Position');

        $stamp = ImageUtil::fromFile(__DIR__ . '/assets/stamp-image.png')
            ->resize(100, 100);

        $bgImage = ImageUtil::fromFile(__DIR__ . '/assets/stamp-background.png')
            ->stampImage($stamp, StampPosition::RANDOM, 0, 0);
    }

    // Test TextAlignment enum values
    public function testWriteTextLeft()
    {
        $img = ImageUtil::empty(1000, 300, FileType::Png, new Color(255, 255, 255));
        $img->writeText("Left Aligned", [100, 100], 50, 0, __DIR__ . '/assets/Rotulonahand-aGyx.ttf', 0, new Color(0, 0, 0), TextAlignment::LEFT);

        $this->assertEquals(1000, $img->getWidth());
        $this->assertEquals(300, $img->getHeight());
    }

    public function testWriteTextRight()
    {
        $img = ImageUtil::empty(1000, 300, FileType::Png, new Color(255, 255, 255));
        $img->writeText("Right Aligned", [900, 100], 50, 0, __DIR__ . '/assets/Rotulonahand-aGyx.ttf', 0, new Color(0, 0, 0), TextAlignment::RIGHT);

        $this->assertEquals(1000, $img->getWidth());
        $this->assertEquals(300, $img->getHeight());
    }

    public function testWriteTextCenter()
    {
        $img = ImageUtil::empty(1000, 300, FileType::Png, new Color(255, 255, 255));
        $img->writeText("Center Aligned", [500, 100], 50, 0, __DIR__ . '/assets/Rotulonahand-aGyx.ttf', 0, new Color(0, 0, 0), TextAlignment::CENTER);

        $this->assertEquals(1000, $img->getWidth());
        $this->assertEquals(300, $img->getHeight());
    }

    // Error condition tests
    public function testResizeWithBothDimensionsNull()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('There are no valid values');
        $this->gdHandler->resize(null, null);
    }

    public function testWriteTextWithInvalidFont()
    {
        $this->expectException(ImageUtilException::class);
        $this->expectExceptionMessage('The specified font not found');
        $img = ImageUtil::empty(1000, 300, FileType::Png, new Color(255, 255, 255));
        $img->writeText("Test", [100, 100], 50, 0, '/nonexistent/font.ttf', 0, new Color(0, 0, 0));
    }

    // Edge case tests
    public function testEmptyWithSmallDimensions()
    {
        $img = ImageUtil::empty(1, 1);
        $this->assertEquals(1, $img->getWidth());
        $this->assertEquals(1, $img->getHeight());
    }

    public function testResizeWithZeroHeight()
    {
        // When height is 0, it should calculate based on aspect ratio
        $img = ImageUtil::empty(500, 100);
        $img->resize(250, 0);
        $this->assertEquals(250, $img->getWidth());
        $this->assertEquals(50, $img->getHeight());
    }

    public function testResizeWithZeroWidth()
    {
        // When width is 0, it should calculate based on aspect ratio
        $img = ImageUtil::empty(500, 100);
        $img->resize(0, 50);
        $this->assertEquals(250, $img->getWidth());
        $this->assertEquals(50, $img->getHeight());
    }

    public function testResizeToSameDimensions()
    {
        $img = ImageUtil::empty(500, 100);
        $img->resize(500, 100);
        $this->assertEquals(500, $img->getWidth());
        $this->assertEquals(100, $img->getHeight());
    }

    public function testMultipleRestores()
    {
        $original = clone $this->gdHandler;

        $this->gdHandler->resize(200, 50);
        $this->gdHandler->restore();

        $this->assertImageSimilar($original, $this->gdHandler);

        $this->gdHandler->resize(300, 75);
        $this->gdHandler->restore();

        $this->assertImageSimilar($original, $this->gdHandler);
    }

    public function testRotateBy360Degrees()
    {
        $original = clone $this->gdHandler;
        $this->gdHandler->rotate(360);

        // After 360 degree rotation, image should be similar to original
        $this->assertImageSimilar($original, $this->gdHandler);
    }

    public function testRotateByNegativeAngle()
    {
        $originalWidth = $this->gdHandler->getWidth();
        $this->gdHandler->rotate(-45);
        // After rotation by any non-zero angle, dimensions should change
        $this->assertNotEquals($originalWidth, $this->gdHandler->getWidth());
    }

    public function testMakeTransparentWithDifferentColors()
    {
        $img = ImageUtil::empty(100, 100, FileType::Png, new Color(255, 0, 0));
        $img->makeTransparent(Color::fromHex('#FF0000'));

        // Image should still have valid dimensions
        $this->assertEquals(100, $img->getWidth());
        $this->assertEquals(100, $img->getHeight());
    }

    public function testChainedOperations()
    {
        $img = ImageUtil::empty(500, 500)
            ->resize(400, 400)
            ->rotate(45)
            ->resizeSquare(300, new Color(255, 255, 255));

        $this->assertEquals(300, $img->getWidth());
        $this->assertEquals(300, $img->getHeight());
    }

    public function testCropWithValidCoordinates()
    {
        $img = ImageUtil::empty(500, 100);
        $img->crop(50, 20, 200, 80);

        $this->assertEquals(150, $img->getWidth());
        $this->assertEquals(60, $img->getHeight());
    }
}
