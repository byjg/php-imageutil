<?php

namespace ByJG\ImageUtil\Handler;

use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Enum\TextAlignment;
use GdImage;
use SVG\SVG;

class SVGHandler implements ImageHandlerInterface
{

    public function getWidth(): int
    {
        // TODO: Implement getWidth() method.
    }

    public function getHeight(): int
    {
        // TODO: Implement getHeight() method.
    }

    public function getFilename(): ?string
    {
        // TODO: Implement getFilename() method.
    }

    public function getResource(): GdImage|SVG
    {
        // TODO: Implement getResource() method.
    }

    public function empty(int $width, int $height, Color $color = null): static
    {
        // TODO: Implement empty() method.
    }

    public function fromResource(GdImage|SVG $resource): ImageHandlerInterface
    {
        // TODO: Implement fromResource() method.
    }

    public function fromFile(string $imageFile): ImageHandlerInterface
    {
        // TODO: Implement fromFile() method.
    }

    public function rotate(int $angle, int $background = 0): ImageHandlerInterface
    {
        // TODO: Implement rotate() method.
    }

    public function flip(Flip $type): ImageHandlerInterface
    {
        // TODO: Implement flip() method.
    }

    public function resize(?int $newWidth = null, ?int $newHeight = null): ImageHandlerInterface
    {
        // TODO: Implement resize() method.
    }

    public function resizeSquare(int $newSize, Color $color = null): ImageHandlerInterface
    {
        // TODO: Implement resizeSquare() method.
    }

    public function resizeAspectRatio(int $newX, int $newY, Color $color = null): ImageHandlerInterface
    {
        // TODO: Implement resizeAspectRatio() method.
    }

    public function stampImage(ImageHandlerInterface $srcImage, StampPosition $position = StampPosition::BOTTOM_RIGHT, int $padding = 5, int $opacity = 100): ImageHandlerInterface
    {
        // TODO: Implement stampImage() method.
    }

    public function writeText(string $text, array $point, float $size, int $angle, string $font, int $maxwidth = 0, array $rgbAr = null, TextAlignment $textAlignment = \ByJG\ImageUtil\Enum\TextAlignment::LEFT): ImageHandlerInterface
    {
        // TODO: Implement writeText() method.
    }

    public function crop(int $fromX, int $fromY, int $toX, int $toY): ImageHandlerInterface
    {
        // TODO: Implement crop() method.
    }

    public function save(?string $filename = null, int $quality = 90): void
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