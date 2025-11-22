<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Handler\GdHandler;
use ByJG\ImageUtil\Handler\ImageHandlerInterface;
use GdImage;
use InvalidArgumentException;
use SVG\SVG;

trait GdImageToSvgTrait
{
    protected function getGgImageFromSvg($resource, array $params = []): GdImage
    {
        if ($resource instanceof SVG) {
            if (!isset($params['width']) || !isset($params['height'])) {
                throw new InvalidArgumentException("The width and height are required to convert SVG to GdImage");
            }
            $rasterImage = $resource->toRasterImage($params['width'], $params['height']);
            if (!$rasterImage instanceof GdImage) {
                throw new InvalidArgumentException("Failed to convert SVG to GdImage");
            }
            $resource = $rasterImage;
        } elseif (!($resource instanceof GdImage)) {
            throw new InvalidArgumentException("Resource must be either SVG or GdImage");
        }

        return $resource;
    }

    public function getHandler(): ImageHandlerInterface
    {
        return new GdHandler();
    }
}