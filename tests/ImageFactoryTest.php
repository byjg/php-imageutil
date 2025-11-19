<?php

namespace Test;

use ByJG\ImageUtil\Image\BmpImage;
use ByJG\ImageUtil\Image\GIFImage;
use ByJG\ImageUtil\Image\ImageFactory;
use ByJG\ImageUtil\Image\JpgImage;
use ByJG\ImageUtil\Image\PngImage;
use ByJG\ImageUtil\Image\SvgImage;
use ByJG\ImageUtil\Image\WebpImage;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ImageFactoryTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        // Reset static properties before each test
        $reflection = new ReflectionClass(ImageFactory::class);
        $configMime = $reflection->getProperty('configMime');
        $configMime->setValue(null, []);

        $configExt = $reflection->getProperty('configExt');
        $configExt->setValue(null, []);
    }

    public function testRegisterAllRegistersAllHandlers()
    {
        ImageFactory::registerAll();

        // Test that we can get instances from mime types
        $this->assertInstanceOf(PngImage::class, ImageFactory::instanceFromMime('image/png'));
        $this->assertInstanceOf(JpgImage::class, ImageFactory::instanceFromMime('image/jpeg'));
        $this->assertInstanceOf(GIFImage::class, ImageFactory::instanceFromMime('image/gif'));
        $this->assertInstanceOf(BmpImage::class, ImageFactory::instanceFromMime('image/bmp'));
        $this->assertInstanceOf(WebpImage::class, ImageFactory::instanceFromMime('image/webp'));
        $this->assertInstanceOf(SvgImage::class, ImageFactory::instanceFromMime('image/svg+xml'));
    }

    public function testRegisterAllRegistersExtensions()
    {
        ImageFactory::registerAll();

        // Test that we can get instances from extensions
        $this->assertInstanceOf(PngImage::class, ImageFactory::instanceFromExtension('png'));
        $this->assertInstanceOf(JpgImage::class, ImageFactory::instanceFromExtension('jpg'));
        $this->assertInstanceOf(JpgImage::class, ImageFactory::instanceFromExtension('jpeg'));
        $this->assertInstanceOf(GIFImage::class, ImageFactory::instanceFromExtension('gif'));
        $this->assertInstanceOf(BmpImage::class, ImageFactory::instanceFromExtension('bmp'));
        $this->assertInstanceOf(WebpImage::class, ImageFactory::instanceFromExtension('webp'));
        $this->assertInstanceOf(SvgImage::class, ImageFactory::instanceFromExtension('svg'));
    }

    public function testRegisterAllOnlyRegistersOnce()
    {
        ImageFactory::registerAll();
        ImageFactory::registerAll(); // Should not duplicate registrations

        $this->assertInstanceOf(PngImage::class, ImageFactory::instanceFromMime('image/png'));
    }

    public function testRegisterHandlerSuccessfully()
    {
        ImageFactory::registerHandler(PngImage::class);

        $instance = ImageFactory::instanceFromMime('image/png');
        $this->assertInstanceOf(PngImage::class, $instance);

        $instance = ImageFactory::instanceFromExtension('png');
        $this->assertInstanceOf(PngImage::class, $instance);
    }

    public function testRegisterHandlerWithInvalidClassThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("is not a instance of ImageHandlerInterface");

        ImageFactory::registerHandler(\stdClass::class);
    }

    public function testInstanceFromMimeWithInvalidMimeThrowsException()
    {
        ImageFactory::registerAll();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("mime type does not exist");

        ImageFactory::instanceFromMime('image/invalid');
    }

    public function testInstanceFromExtensionWithInvalidExtensionThrowsException()
    {
        ImageFactory::registerAll();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("extension does not exist");

        ImageFactory::instanceFromExtension('invalid');
    }

    public function testJpegMimeType()
    {
        ImageFactory::registerAll();

        // Test JPEG mime type
        $this->assertInstanceOf(JpgImage::class, ImageFactory::instanceFromMime('image/jpeg'));
    }

    public function testBmpMimeTypeVariants()
    {
        ImageFactory::registerAll();

        // Test both mime type variants for BMP
        $this->assertInstanceOf(BmpImage::class, ImageFactory::instanceFromMime('image/bmp'));
        $this->assertInstanceOf(BmpImage::class, ImageFactory::instanceFromMime('image/x-ms-bmp'));
    }
}
