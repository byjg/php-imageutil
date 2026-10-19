<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Handler\ImageHandlerInterface;

interface ImageInterface
{
    /**
     * The mime type, or the list of mime types, this format handles.
     *
     * @return string|array
     */
    public static function mimeType(): string|array;

    /**
     * The file extension, or the list of file extensions, this format handles (lowercase, without the dot).
     *
     * @return string|array
     */
    public static function extension(): string|array;

    /**
     * Loads the file into a resource (a GdImage, or an SVG for the SVG format).
     *
     * @param string $filename
     * @return mixed
     * @throws ImageUtilException When the file cannot be loaded
     */
    public function load(string $filename): mixed;

    /**
     * Saves the resource (GdImage or SVG) to the file in this format.
     *
     * @param mixed $resource
     * @param string|null $filename
     * @param array $params 'quality' (0-100), and 'width'/'height' to rasterize an SVG
     * @return void
     * @throws \InvalidArgumentException When the resource cannot be saved in this format
     */
    public function save(mixed $resource, ?string $filename = null, array $params = []): void;

    /**
     * Writes the resource (GdImage or SVG) to the output in this format.
     *
     * @param mixed $resource
     * @return void
     * @throws \InvalidArgumentException When the resource cannot be written in this format
     */
    public function output(mixed $resource): void;

    /**
     * A new, empty handler for this format (GdHandler or SvgHandler).
     */
    public function getHandler(): ImageHandlerInterface;

}