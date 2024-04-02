<?php

namespace ByJG\ImageUtil\Handler;

use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Enum\TextAlignment;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Image\ImageFactory;
use GdImage;
use SVG\Nodes\Shapes\SVGRect;
use SVG\SVG;

class SvgHandler implements ImageHandlerInterface
{
    protected SVG $originalResource;

    protected SVG $resource;

    protected ?string $filename = null;

    public function getWidth(): int
    {
        return $this->resource->getDocument()->getWidth();
    }

    public function getHeight(): int
    {
        return $this->resource->getDocument()->getHeight();
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    protected function setResource(SVG $resource, ?string $filename = null): void
    {
        $this->resource = $resource;
        $this->originalResource = SVG::fromString($resource->toXMLString());
        $this->filename = $filename;
    }


    public function getResource(): GdImage|SVG
    {
        return $this->resource;
    }

    public function empty(int $width, int $height, Color $color = null): static
    {
        $resource = new SVG($width, $height);
        if (!is_null($color)) {
            $resource->getDocument()->addChild((new SVGRect(0, 0, $width, $height))->setStyle('fill',  $color->getHex()));
        }
        $this->setResource($resource);
        return $this;
    }

    public function fromResource(GdImage|SVG $resource): static
    {
        if ($resource instanceof SVG) {
            $this->setResource($resource);
        } else {
            throw new ImageUtilException('Is not valid resource');
        }
        return $this;
    }

    public function fromFile(string $imageFile): static
    {
        $this->setResource(SVG::fromFile($imageFile), $imageFile);
        return $this;
    }

    public function rotate(int $angle, int $background = 0): static
    {
        throw new ImageUtilException('Not implemented yet');
    }

    public function flip(Flip $type): static
    {
        throw new ImageUtilException('Not implemented yet');
    }

    public function resize(?int $newWidth = null, ?int $newHeight = null): static
    {
        throw new ImageUtilException('Not implemented yet');
    }

    public function resizeSquare(int $newSize, Color $color = null): static
    {
        throw new ImageUtilException('Not implemented yet');
    }

    public function resizeAspectRatio(int $newX, int $newY, Color $color = null): static
    {
        throw new ImageUtilException('Not implemented yet');
    }

    public function stampImage(ImageHandlerInterface $srcImage, StampPosition $position = StampPosition::BOTTOM_RIGHT, int $padding = 5, int $opacity = 100): static
    {
        throw new ImageUtilException('Not implemented yet');
    }

    public function writeText(string $text, array $point, float $size, int $angle, string $font, int $maxWidth = 0, Color $textColor = null, TextAlignment $textAlignment = TextAlignment::LEFT): static
    {
        throw new ImageUtilException('Not implemented yet');
    }

    public function crop(int $fromX, int $fromY, int $toX, int $toY): static
    {
        throw new ImageUtilException('Not implemented yet');
    }

    public function save(?string $filename = null, int $quality = 90): void
    {
        if (is_null($filename)) {
            $filename = $this->filename;
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        ImageFactory::instanceFromExtension($extension)->save($this->resource, $filename, ['quality' => $quality, 'width' => $this->getWidth(), 'height' => $this->getHeight()]);

        $this->setResource($this->resource, $filename);
    }

    public function show(): void
    {
        if (ob_get_level()) {
            ob_clean();
        }
        header("Content-type: image/svg+xml");
        echo $this->resource->toXMLString();
    }

    public function makeTransparent(Color $color = null, $image = null): static
    {
        throw new ImageUtilException('Not implemented yet');
    }

    public function restore(): static
    {
        $this->setResource(SVG::fromString($this->originalResource->toXMLString()));
        return $this;
    }
}