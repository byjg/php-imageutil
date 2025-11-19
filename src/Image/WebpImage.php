<?php

namespace ByJG\ImageUtil\Image;

use GdImage;
use InvalidArgumentException;
use SVG\SVG;

class WebpImage implements ImageInterface
{

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function mimeType(): string|array
    {
        return 'image/webp';
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public static function extension(): string|array
    {
        return "webp";
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function load(string $filename): GdImage|false|SVG
    {
        return imagecreatefromwebp($filename);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function save(GdImage|SVG $resource, ?string $filename = null, array $params = []): void
    {
        if ($resource instanceof SVG) {
            if (!isset($params['width']) || !isset($params['height'])) {
                throw new InvalidArgumentException("The width and height are required to convert SVG to WEBP");
            }
            $resource = $resource->toRasterImage($params['width'], $params['height']);
        }
        imagewebp($resource, $filename, $params['quality']);
    }

    /**
     * @inheritDoc
     */
    #[\Override]
    public function output(GdImage|SVG $resource): void
    {
        imagewebp($resource);
    }
}