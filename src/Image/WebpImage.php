<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Exception\ImageUtilException;
use GdImage;
use Override;

class WebpImage implements ImageInterface
{
    use GdImageToSvgTrait;

    /**
     * @inheritDoc
     */
    #[Override]
    public static function mimeType(): string|array
    {
        return 'image/webp';
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function extension(): string|array
    {
        return "webp";
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function load(string $filename): GdImage
    {
        $image = imagecreatefromwebp($filename);
        if ($image === false) {
            throw new ImageUtilException("Failed to load WEBP image from: " . $filename);
        }
        return $image;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function save(mixed $resource, ?string $filename = null, array $params = []): void
    {
        imagewebp($this->getGgImageFromSvg($resource, $params), $filename, $params['quality']);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function output(mixed $resource): void
    {
        imagewebp($this->getGgImageFromSvg($resource, []));
    }
}