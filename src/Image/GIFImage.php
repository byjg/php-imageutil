<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Exception\ImageUtilException;
use GdImage;
use Override;

class GIFImage implements ImageInterface
{
    use GdImageToSvgTrait;

    /**
     * @inheritDoc
     */
    #[Override]
    public static function mimeType(): string|array
    {
        return "image/gif";
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function extension(): string|array
    {
        return 'gif';
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function load(string $filename): GdImage
    {
        $img = @getimagesize($filename);
        if ($img === false) {
            throw new ImageUtilException("Failed to get image size for GIF from: " . $filename);
        }
        $oldId = @imagecreatefromgif($filename);
        if ($oldId === false) {
            throw new ImageUtilException("Failed to load GIF image from: " . $filename);
        }
        $image = imagecreatetruecolor($img[0], $img[1]);
        if ($image === false) {
            throw new ImageUtilException("Failed to create true color image for GIF from: " . $filename);
        }
        imagecopy($image, $oldId, 0, 0, 0, 0, $img[0], $img[1]);
        return $image;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function save(mixed $resource, ?string $filename = null, array $params = []): void
    {
        @imagegif($this->getGgImageFromSvg($resource, $params), $filename);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function output(mixed $resource): void
    {
        imagegif($this->getGgImageFromSvg($resource, []));
    }
}