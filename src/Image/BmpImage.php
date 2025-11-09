<?php

namespace ByJG\ImageUtil\Image;

use GdImage;
use InvalidArgumentException;
use SVG\SVG;

class BmpImage implements ImageInterface
{

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function mimeType(): string|array
    {
        return [ 'image/bmp', 'image/x-ms-bmp' ];
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function extension(): string|array
    {
        return "bmp";
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function load(string $filename): GdImage|false|false|SVG
    {
        return imagecreatefrombmp($filename);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function save(GdImage|SVG $resource, ?string $filename = null, array $params = []): void
    {
        if ($resource instanceof SVG) {
            if (!isset($params['width']) || !isset($params['height'])) {
                throw new InvalidArgumentException("The width and height are required to convert SVG to BMP");
            }
            $resource = $resource->toRasterImage($params['width'], $params['height']);
        }
        imagebmp($resource, $filename);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function output(GdImage|SVG $resource): void
    {
        imagebmp($resource);
    }
}