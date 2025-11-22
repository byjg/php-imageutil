<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Exception\ImageUtilException;
use GdImage;
use Override;
use Throwable;

class PngImage implements ImageInterface
{
    use GdImageToSvgTrait;

    /**
     * @inheritDoc
     */
    #[Override]
    public static function mimeType(): string|array
    {
        return "image/png";
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function extension(): string|array
    {
        return "png";
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function load(string $filename): GdImage
    {
        $e = null;
        try {
            $image = imagecreatefrompng($filename);
        } catch (Throwable $e) {
            $image = false;
        }
        if ($image === false) {
            throw new ImageUtilException("Failed to load PNG image from: " . $filename, 0, $e);
        }
        return $image;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function save(mixed $resource, ?string $filename = null, array $params = []): void
    {
        $pngQuality = intval(round((9 * $params['quality']) / 100));
        imagepng($this->getGgImageFromSvg($resource, $params), $filename, $pngQuality);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function output(mixed $resource): void
    {
        imagepng($this->getGgImageFromSvg($resource, []));
    }
}