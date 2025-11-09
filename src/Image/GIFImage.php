<?php

namespace ByJG\ImageUtil\Image;

use GdImage;
use InvalidArgumentException;
use SVG\SVG;

class GIFImage implements ImageInterface
{

    /**
     * @inheritDoc
     */
    public static function mimeType(): string|array
    {
        return "image/gif";
    }

    /**
     * @inheritDoc
     */
    public static function extension(): string|array
    {
        return 'gif';
    }

    /**
     * @inheritDoc
     */
    public function load(string $filename): GdImage|SVG
    {
        $img = getimagesize($filename);
        $oldId = imagecreatefromgif($filename);
        $image = imagecreatetruecolor($img[0], $img[1]);
        imagecopy($image, $oldId, 0, 0, 0, 0, $img[0], $img[1]);
        return $image;
    }

    /**
     * @inheritDoc
     */
    public function save(GdImage|SVG $resource, ?string $filename = null, array $params = []): void
    {
        if ($resource instanceof SVG) {
            if (!isset($params['width']) || !isset($params['height'])) {
                throw new InvalidArgumentException("The width and height are required to convert SVG to GIF");
            }
            $resource = $resource->toRasterImage($params['width'], $params['height']);
        }
        imagegif($resource, $filename);
    }

    /**
     * @inheritDoc
     */
    public function output(GdImage|SVG $resource): void
    {
        imagegif($resource);
    }
}