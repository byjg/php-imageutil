<?php

namespace ByJG\ImageUtil\Handler;

use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\StampPosition;

class SVGHandler implements ImageHandlerInterface
{

    public function getWidth()
    {
        // TODO: Implement getWidth() method.
    }

    public function getHeight()
    {
        // TODO: Implement getHeight() method.
    }

    public function getFilename()
    {
        // TODO: Implement getFilename() method.
    }

    public function getResource()
    {
        // TODO: Implement getResource() method.
    }

    public function empty($width, $height, Color $color = null): static
    {
        // TODO: Implement empty() method.
    }

    public function fromResource($resource)
    {
        // TODO: Implement fromResource() method.
    }

    public function fromFile($imageFile)
    {
        // TODO: Implement fromFile() method.
    }

    public function rotate($angle, $background = 0)
    {
        // TODO: Implement rotate() method.
    }

    public function flip($type)
    {
        // TODO: Implement flip() method.
    }

    public function resize($newWidth = null, $newHeight = null)
    {
        // TODO: Implement resize() method.
    }

    public function resizeSquare($newSize, Color $color = null)
    {
        // TODO: Implement resizeSquare() method.
    }

    public function resizeAspectRatio($newX, $newY, Color $color = null)
    {
        // TODO: Implement resizeAspectRatio() method.
    }

    public function stampImage($srcImage, $position = StampPosition::BOTTOMRIGHT, $padding = 5, $oppacity = 100)
    {
        // TODO: Implement stampImage() method.
    }

    public function writeText($text, $point, $size, $angle, $font, $maxwidth = 0, $rgbAr = null, $textAlignment = 1)
    {
        // TODO: Implement writeText() method.
    }

    public function crop($fromX, $fromY, $toX, $toY)
    {
        // TODO: Implement crop() method.
    }

    public function save($filename = null, $quality = 90)
    {
        // TODO: Implement save() method.
    }

    public function show()
    {
        // TODO: Implement show() method.
    }

    public function makeTransparent(Color $color = null, $image = null)
    {
        // TODO: Implement makeTransparent() method.
    }

    public function restore()
    {
        // TODO: Implement restore() method.
    }
}