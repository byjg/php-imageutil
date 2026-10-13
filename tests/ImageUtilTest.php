<?php

namespace Tests;

use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\FileType;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use ByJG\ImageUtil\Handler\GdHandler;
use ByJG\ImageUtil\Handler\SvgHandler;
use ByJG\ImageUtil\ImageUtil;
use PHPUnit\Framework\TestCase;

class ImageUtilTest extends TestCase
{
    public function testFromFile(): void
    {
        $image = ImageUtil::fromFile(__DIR__ . '/assets/anim2.svg');
        $this->assertInstanceOf(SvgHandler::class, $image);

        $image = ImageUtil::fromFile(__DIR__ . '/assets/flip-both.png');
        $this->assertInstanceOf(GdHandler::class, $image);
    }

    public function testFromFilePng(): void
    {
        $image = ImageUtil::fromFile(__DIR__ . '/assets/flip-both.png');
        $this->assertInstanceOf(GdHandler::class, $image);
        $this->assertGreaterThan(0, $image->getWidth());
        $this->assertGreaterThan(0, $image->getHeight());
    }

    public function testFromFileSvg(): void
    {
        $image = ImageUtil::fromFile(__DIR__ . '/assets/anim2.svg');
        $this->assertInstanceOf(SvgHandler::class, $image);
    }

    public function testFromFileNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("File is not found or not is readable");
        ImageUtil::fromFile(__DIR__ . '/assets/nonexistent.png');
    }

    public function testFromFileInvalidFile(): void
    {
        // Create a temporary invalid image file
        $tempFile = sys_get_temp_dir() . '/invalid_image_' . uniqid() . '.png';
        file_put_contents($tempFile, 'This is not a valid image file');

        try {
            $this->expectException(ImageUtilException::class);
            $this->expectExceptionMessage("Failed to load");
            ImageUtil::fromFile($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function testEmptyPng(): void
    {
        $image = ImageUtil::empty(800, 600, FileType::Png);
        $this->assertInstanceOf(GdHandler::class, $image);
        $this->assertEquals(800, $image->getWidth());
        $this->assertEquals(600, $image->getHeight());
    }

    public function testEmptySvg(): void
    {
        $image = ImageUtil::empty(800, 600, FileType::Svg);
        $this->assertInstanceOf(SvgHandler::class, $image);
    }

    public function testEmptyWithColor(): void
    {
        $color = new Color(255, 0, 0);
        $image = ImageUtil::empty(500, 300, FileType::Png, $color);
        $this->assertInstanceOf(GdHandler::class, $image);
        $this->assertEquals(500, $image->getWidth());
        $this->assertEquals(300, $image->getHeight());
    }

    public function testEmptyDefaultTypeIsPng(): void
    {
        $image = ImageUtil::empty(1000, 800);
        $this->assertInstanceOf(GdHandler::class, $image);
    }

    public function testFromResourceGdImage(): void
    {
        $gdResource = imagecreatetruecolor(640, 480);
        $image = ImageUtil::fromResource($gdResource);

        $this->assertInstanceOf(GdHandler::class, $image);
        $this->assertEquals(640, $image->getWidth());
        $this->assertEquals(480, $image->getHeight());
    }

    public function testFromResourceSvg(): void
    {
        $svgContent = ImageUtil::fromFile(__DIR__ . '/assets/anim2.svg');
        $svgResource = $svgContent->getResource();

        $image = ImageUtil::fromResource($svgResource);
        $this->assertInstanceOf(SvgHandler::class, $image);
    }

    public function testFromFileMultipleFormats(): void
    {
        $formats = [
            'flip-both.png' => GdHandler::class,
            'anim2.svg' => SvgHandler::class,
        ];

        foreach ($formats as $file => $expectedClass) {
            $image = ImageUtil::fromFile(__DIR__ . '/assets/' . $file);
            $this->assertInstanceOf($expectedClass, $image, "Failed for file: $file");
        }
    }

    public function testEmptyWithDifferentDimensions(): void
    {
        $dimensions = [
            [100, 100],
            [1920, 1080],
            [500, 300],
            [1, 1],
        ];

        foreach ($dimensions as [$width, $height]) {
            $image = ImageUtil::empty($width, $height);
            $this->assertEquals($width, $image->getWidth());
            $this->assertEquals($height, $image->getHeight());
        }
    }

    public function testSaveAndLoadRoundTrip(): void
    {
        $tempFile = sys_get_temp_dir() . '/roundtrip_test_' . uniqid() . '.png';

        try {
            $original = ImageUtil::empty(300, 200, FileType::Png, new Color(100, 150, 200));
            $original->save($tempFile);

            $this->assertFileExists($tempFile);

            $loaded = ImageUtil::fromFile($tempFile);
            $this->assertEquals(300, $loaded->getWidth());
            $this->assertEquals(200, $loaded->getHeight());
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function testFromFileWithUpperCaseExtension(): void
    {
        $tempFile = sys_get_temp_dir() . '/imageutil_' . uniqid() . '.PNG';
        copy(__DIR__ . '/assets/flip-both.png', $tempFile);

        try {
            $image = ImageUtil::fromFile($tempFile);
            $this->assertInstanceOf(GdHandler::class, $image);
        } finally {
            unlink($tempFile);
        }
    }

    public function testFromUrlWithQueryString(): void
    {
        FakeHttpStream::$file = __DIR__ . '/assets/flip-both.png';
        stream_wrapper_unregister('http');
        stream_wrapper_register('http', FakeHttpStream::class);

        try {
            $image = ImageUtil::fromFile('http://example.test/images/Logo.PNG?v=2&size=large#top');
        } finally {
            stream_wrapper_restore('http');
        }

        $expected = ImageUtil::fromFile(__DIR__ . '/assets/flip-both.png');
        $this->assertInstanceOf(GdHandler::class, $image);
        $this->assertSame($expected->getWidth(), $image->getWidth());
        $this->assertSame($expected->getHeight(), $image->getHeight());
    }

    public function testFromUrlWithoutExtension(): void
    {
        $this->expectException(ImageUtilException::class);
        $this->expectExceptionMessage('Cannot determine file extension from URL');
        ImageUtil::fromFile('http://example.test/images/logo?format=file.png');
    }

    public function testFromFileRemembersTheFile(): void
    {
        $png = __DIR__ . '/assets/flip-both.png';
        $svg = __DIR__ . '/assets/anim2.svg';

        $this->assertSame($png, ImageUtil::fromFile($png)->getFilename());
        $this->assertSame($svg, ImageUtil::fromFile($svg)->getFilename());
        $this->assertSame($svg, ImageUtil::fromFile($svg)->restore()->getFilename());
    }

    public function testSaveWithoutNameWritesBackToTheLoadedFile(): void
    {
        $tempFile = sys_get_temp_dir() . '/imageutil_' . uniqid() . '.png';
        copy(__DIR__ . '/assets/flip-both.png', $tempFile);

        try {
            $image = ImageUtil::fromFile($tempFile);
            $image->resize(50, 40);
            $image->save();

            $saved = ImageUtil::fromFile($tempFile);
            $this->assertSame(50, $saved->getWidth());
            $this->assertSame(40, $saved->getHeight());
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * show() reads the format from the file behind the image. A freshly loaded image used to
     * carry a made-up temporary name, so show() only worked after a save().
     */
    public function testShowAfterFromFileUsesTheFormatOfTheFile(): void
    {
        $tempFile = sys_get_temp_dir() . '/imageutil_' . uniqid() . '.jpg';
        ImageUtil::fromFile(__DIR__ . '/assets/flip-both.png')->save($tempFile);

        try {
            ob_start();
            ImageUtil::fromFile(__DIR__ . '/assets/flip-both.png')->show();
            $png = (string)ob_get_clean();

            ob_start();
            ImageUtil::fromFile($tempFile)->show();
            $jpg = (string)ob_get_clean();
        } finally {
            unlink($tempFile);
        }

        $this->assertSame("\x89PNG", substr($png, 0, 4));
        $this->assertSame("\xFF\xD8\xFF", substr($jpg, 0, 3));
    }
}
