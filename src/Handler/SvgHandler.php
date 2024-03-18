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
        $this->resource->getDocument()->setWidth($newWidth);
        $this->resource->getDocument()->setHeight($newHeight);
        $this->resizeAllNodesProportionally($newWidth, $newHeight);
        return $this;
    }

    protected function resizeAllNodesProportionally(?int $newWidth = null, ?int $newHeight = null): static
    {
        // Get the current width and height of the parent node
        $parentWidth = $this->resource->getDocument()->getWidth();
        $parentHeight = $this->resource->getDocument()->getHeight();

        $this->resizeNodeProportionally($this->resource->getDocument(), $parentWidth, $parentHeight);
        return $this;
    }

    protected function resizeNodeProportionally($node, int $parentWidth, int $parentHeight): void
    {
        if (method_exists($node, 'getWidth')) {
            // Get the current width and height
            if (method_exists($node, 'getX')) {
                $currentX = empty($node->getX()) ? 0 : $node->getX();
                $currentY = empty($node->getY()) ? 0 : $node->getY();
            } else {
                $currentX = 0;
                $currentY = 0;
            }
            $currentWidth = empty($node->getWidth()) ? $parentWidth : $node->getWidth();
            $currentHeight = empty($node->getHeight()) ? $parentHeight : $node->getHeight();

            // Calculate the new width and height to fit inside the parent node
            if ($parentWidth / $parentHeight < $currentWidth / $currentHeight) {
                $newX = $currentX;
                $newY = $currentX * $parentHeight / $parentWidth;
                $newWidth = $parentWidth;
                $newHeight = $parentWidth * $currentHeight / $currentWidth;
            } else {
                $newY = $currentY;
                $newX = $currentY * $parentHeight / $parentWidth;
                $newHeight = $parentHeight;
                $newWidth = $parentHeight * $currentWidth / $currentHeight;
            }

            // Set the new width and height
            if (method_exists($node, 'setX')) {
                $node->setX($newX);
                $node->setY($newY);
            }
            $node->setWidth($newWidth);
            $node->setHeight($newHeight);
        }

        if (!method_exists($node, 'countChildren')) {
            return;
        }

        // Call this function for each child node
        $count = $node->countChildren();
        for ($i = 0; $i < $count; $i++) {
            $this->resizeNodeProportionally($node->getChild($i), $newWidth ?? $parentWidth, $newHeight ?? $parentHeight);
        }
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

    public function writeText(string $text, array $point, float $size, int $angle, string $font, int $maxWidth = 0, array $rgbAr = null, TextAlignment $textAlignment = TextAlignment::LEFT): static
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