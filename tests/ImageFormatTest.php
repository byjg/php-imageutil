<?php

namespace Test;

use ByJG\ImageUtil\Image\BmpImage;
use ByJG\ImageUtil\Image\GIFImage;
use ByJG\ImageUtil\Image\JpgImage;
use ByJG\ImageUtil\Image\PngImage;
use ByJG\ImageUtil\Image\SvgImage;
use ByJG\ImageUtil\Image\WebpImage;
use PHPUnit\Framework\TestCase;

class ImageFormatTest extends TestCase
{
    // PNG Image Tests
    public function testPngImageMimeType()
    {
        $this->assertEquals('image/png', PngImage::mimeType());
    }

    public function testPngImageExtension()
    {
        $this->assertEquals('png', PngImage::extension());
    }

    public function testPngImageLoad()
    {
        $png = new PngImage();
        $resource = $png->load(__DIR__ . '/assets/flip-both.png');
        $this->assertNotFalse($resource);
        $this->assertIsObject($resource);
    }

    public function testPngImageSave()
    {
        $tempFile = sys_get_temp_dir() . '/test_png_' . uniqid() . '.png';

        try {
            $png = new PngImage();
            $resource = imagecreatetruecolor(100, 100);

            $png->save($resource, $tempFile, ['quality' => 90]);
            $this->assertFileExists($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    // JPG Image Tests
    public function testJpgImageMimeType()
    {
        $mimeType = JpgImage::mimeType();
        $this->assertEquals('image/jpeg', $mimeType);
    }

    public function testJpgImageExtension()
    {
        $extensions = JpgImage::extension();
        $this->assertIsArray($extensions);
        $this->assertContains('jpg', $extensions);
        $this->assertContains('jpeg', $extensions);
    }

    public function testJpgImageSave()
    {
        $tempFile = sys_get_temp_dir() . '/test_jpg_' . uniqid() . '.jpg';

        try {
            $jpg = new JpgImage();
            $resource = imagecreatetruecolor(100, 100);

            $jpg->save($resource, $tempFile, ['quality' => 85]);
            $this->assertFileExists($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    // GIF Image Tests
    public function testGifImageMimeType()
    {
        $this->assertEquals('image/gif', GIFImage::mimeType());
    }

    public function testGifImageExtension()
    {
        $this->assertEquals('gif', GIFImage::extension());
    }

    public function testGifImageSave()
    {
        $tempFile = sys_get_temp_dir() . '/test_gif_' . uniqid() . '.gif';

        try {
            $gif = new GIFImage();
            $resource = imagecreatetruecolor(100, 100);

            $gif->save($resource, $tempFile, ['quality' => 90]);
            $this->assertFileExists($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    // BMP Image Tests
    public function testBmpImageMimeType()
    {
        $mimeTypes = BmpImage::mimeType();
        $this->assertIsArray($mimeTypes);
        $this->assertContains('image/bmp', $mimeTypes);
        $this->assertContains('image/x-ms-bmp', $mimeTypes);
    }

    public function testBmpImageExtension()
    {
        $this->assertEquals('bmp', BmpImage::extension());
    }

    public function testBmpImageSave()
    {
        $tempFile = sys_get_temp_dir() . '/test_bmp_' . uniqid() . '.bmp';

        try {
            $bmp = new BmpImage();
            $resource = imagecreatetruecolor(100, 100);

            $bmp->save($resource, $tempFile, ['quality' => 90]);
            $this->assertFileExists($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    // WebP Image Tests
    public function testWebpImageMimeType()
    {
        $this->assertEquals('image/webp', WebpImage::mimeType());
    }

    public function testWebpImageExtension()
    {
        $this->assertEquals('webp', WebpImage::extension());
    }

    public function testWebpImageSave()
    {
        $tempFile = sys_get_temp_dir() . '/test_webp_' . uniqid() . '.webp';

        try {
            $webp = new WebpImage();
            $resource = imagecreatetruecolor(100, 100);

            $webp->save($resource, $tempFile, ['quality' => 85]);
            $this->assertFileExists($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    // SVG Image Tests
    public function testSvgImageMimeType()
    {
        $this->assertEquals('image/svg+xml', SvgImage::mimeType());
    }

    public function testSvgImageExtension()
    {
        $extension = SvgImage::extension();
        $this->assertIsArray($extension);
        $this->assertContains('svg', $extension);
    }

    public function testSvgImageSave()
    {
        $tempFile = sys_get_temp_dir() . '/test_svg_' . uniqid() . '.svg';

        try {
            $svg = new SvgImage();
            $resource = $svg->load(__DIR__ . '/assets/anim2.svg');

            $svg->save($resource, $tempFile);
            $this->assertFileExists($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    // Test quality parameter handling
    public function testPngImageSaveWithDifferentQuality()
    {
        $tempFile = sys_get_temp_dir() . '/test_png_quality_' . uniqid() . '.png';

        try {
            $png = new PngImage();
            $resource = imagecreatetruecolor(100, 100);

            // Test with low quality
            $png->save($resource, $tempFile, ['quality' => 10]);
            $this->assertFileExists($tempFile);

            // Test with high quality
            $png->save($resource, $tempFile, ['quality' => 100]);
            $this->assertFileExists($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function testJpgImageSaveWithDifferentQuality()
    {
        $tempFile = sys_get_temp_dir() . '/test_jpg_quality_' . uniqid() . '.jpg';

        try {
            $jpg = new JpgImage();
            $resource = imagecreatetruecolor(100, 100);

            // Test with low quality
            $jpg->save($resource, $tempFile, ['quality' => 10]);
            $this->assertFileExists($tempFile);

            // Test with high quality
            $jpg->save($resource, $tempFile, ['quality' => 100]);
            $this->assertFileExists($tempFile);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    // Test output method exists
    public function testPngImageOutput()
    {
        $png = new PngImage();
        $resource = imagecreatetruecolor(100, 100);

        ob_start();
        $png->output($resource);
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    public function testJpgImageOutput()
    {
        $jpg = new JpgImage();
        $resource = imagecreatetruecolor(100, 100);

        ob_start();
        $jpg->output($resource);
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    public function testGifImageOutput()
    {
        $gif = new GIFImage();
        $resource = imagecreatetruecolor(100, 100);

        ob_start();
        $gif->output($resource);
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    public function testWebpImageOutput()
    {
        $webp = new WebpImage();
        $resource = imagecreatetruecolor(100, 100);

        ob_start();
        $webp->output($resource);
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }

    // Test save without filename (should output to stdout)
    public function testPngImageSaveWithoutFilename()
    {
        $png = new PngImage();
        $resource = imagecreatetruecolor(100, 100);

        ob_start();
        $png->save($resource, null, ['quality' => 90]);
        $output = ob_get_clean();

        $this->assertNotEmpty($output);
    }
}
