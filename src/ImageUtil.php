<?php

namespace ByJG\ImageUtil;

use ByJG\ImageUtil\Enum\FileType;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use ByJG\ImageUtil\Handler\GDHandler;
use ByJG\ImageUtil\Handler\ImageHandlerInterface;
use ByJG\ImageUtil\Handler\SVGHandler;
use ByJG\ImageUtil\Image\ImageFactory;
use GdImage;
use SVG\SVG;

/**
 * A Wrapper for GD library in PHP. GD must be installed in your system for this to work.
 * Example: $img = new Image('wheel.png');
 *            $img->flip(1)->resize(120, 0)->save('wheel.jpg');
 */
class ImageUtil
{
    public static function empty($width, $height, FileType $type = FileType::Png, Color $color = null): ImageHandlerInterface
    {
        if ($type == FileType::Svg) {
            $image = new SVGHandler();
        } else {
            $image = new GDHandler();
        }
        $image->empty($width, $height, $color);
        return $image;
    }


    /**
     * @inheritDoc
     */
    public static function fromResource($resource): ImageHandlerInterface
    {
        if (is_resource($resource) || $resource instanceof GdImage) {
            $image = new GDHandler();
        } else if ($resource instanceof SVG) {
            $image = new SVGHandler();
        } else {
            throw new ImageUtilException('Is not valid resource');
        }
        return $image->fromResource($resource);
    }

    /**
     * @inheritDoc
     */
    public static function fromFile($imageFile): ImageHandlerInterface
    {
        $http = false;
        if (preg_match('/^(https?:|file:)/', $imageFile)) {
            $http = true;
            $url = $imageFile;
            $imageFile = basename($url);
            $info = pathinfo($imageFile);
            $imageFile = sys_get_temp_dir() . '/img_' . uniqid() . '.' . $info['extension'];
            file_put_contents($imageFile, file_get_contents($url));
        }

        if (!file_exists($imageFile) || !is_readable($imageFile)) {
            throw new NotFoundException("File is not found or not is readable. Cannot continue.");
        }

        $info = pathinfo($imageFile);
        if ($info['extension'] == 'svg') {
            $image = new SVGHandler();
            $resource = $image->load($imageFile);
        } else {
            $img = getimagesize($imageFile);
            if (empty($img)) {
                throw new ImageUtilException("Invalid file: " . $imageFile);
            }

            $resource = ImageFactory::instanceFromMime($img['mime'])->load($imageFile);
        }

        if ($http) {
            unlink($imageFile);
        }

        return self::fromResource($resource);;
    }
}
