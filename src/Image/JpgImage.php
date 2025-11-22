<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Exception\ImageUtilException;
use GdImage;
use Override;
use Throwable;

class JpgImage implements ImageInterface
{
    use GdImageToSvgTrait;

    /**
     * @inheritDoc
     */
    #[Override]
    public static function mimeType(): string|array
    {
        return "image/jpeg";
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function extension(): string|array
    {
        return ["jpg", "jpeg"];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function load(string $filename): GdImage
    {
        $e = null;
        try {
            $image = imagecreatefromjpeg($filename);
        } catch (Throwable $e) {
            $image = false;
        }
        if ($image === false) {
            throw new ImageUtilException("Failed to load JPEG image from: " . $filename, 0, $e);
        }
        return $image;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function save(mixed $resource, ?string $filename = null, array $params = []): void
    {
        imagejpeg($this->getGgImageFromSvg($resource, $params), $filename, $params['quality']);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function output(mixed $resource): void
    {
        imagejpeg($this->getGgImageFromSvg($resource, []));
    }
}