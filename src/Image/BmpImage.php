<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Exception\ImageUtilException;
use GdImage;
use Override;
use Throwable;

class BmpImage implements ImageInterface
{
    use GdImageToSvgTrait;

    /**
     * @inheritDoc
     */
    #[Override]
    public static function mimeType(): string|array
    {
        return [ 'image/bmp', 'image/x-ms-bmp' ];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function extension(): string|array
    {
        return "bmp";
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function load(string $filename): GdImage
    {
        $e = null;
        try {
            $image = imagecreatefrombmp($filename);
        } catch (Throwable $e) {
            $image = false;
        }
        if ($image === false) {
            throw new ImageUtilException("Failed to load BMP image from: " . $filename, 0, $e);
        }
        return $image;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function save(mixed $resource, ?string $filename = null, array $params = []): void
    {
        imagebmp($this->getGgImageFromSvg($resource, $params), $filename);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function output(mixed $resource): void
    {
        imagebmp($this->getGgImageFromSvg($resource, []));
    }
}