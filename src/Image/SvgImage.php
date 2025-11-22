<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Handler\ImageHandlerInterface;
use ByJG\ImageUtil\Handler\SvgHandler;
use InvalidArgumentException;
use Override;
use SVG\SVG;

class SvgImage implements ImageInterface
{
    /**
     * @inheritDoc
     */
    #[Override]
    public static function mimeType(): string|array
    {
        return "image/svg+xml";
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function extension(): string|array
    {
        return ["svg"];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function load(string $filename): SVG
    {
        $image = SVG::fromFile($filename);
        if (is_null($image)) {
            throw new ImageUtilException("The file is not a valid SVG file");
        }
        return $image;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function save(mixed $resource, ?string $filename = null, array $params = []): void
    {
        if ($resource instanceof SVG) {
            if ($filename === null) {
                throw new InvalidArgumentException("Filename is required to save SVG.");
            }
            file_put_contents($filename, $resource->toXMLString());
        } else {
            throw new InvalidArgumentException("Cannot convert a GdImage to SVG.");
        }
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function output(mixed $resource): void
    {
        if ($resource instanceof SVG) {
            echo $resource->toXMLString();
        } else {
            throw new InvalidArgumentException("The resource is not a SVG object");
        }
    }

    #[Override]
    public function getHandler(): ImageHandlerInterface
    {
        return new SvgHandler();
    }
}