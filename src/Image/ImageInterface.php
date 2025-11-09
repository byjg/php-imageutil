<?php

namespace ByJG\ImageUtil\Image;

use GdImage;
use SVG\SVG;

interface ImageInterface
{
    /**
     * @return string|array
     */
    public static function mimeType(): string|array;

    /**
     * @return string|array
     */
    public static function extension(): string|array;

    /**
     * @param string $filename
     * @return GdImage|SVG|false
     */
    public function load(string $filename): GdImage|SVG|false;

    /**
     * @param GdImage|SVG $resource
     * @param string $filename
     * @param array $params
     * @return void
     */
    public function save(GdImage|SVG $resource, ?string $filename = null, array $params = []): void;

    /**
     * @param GdImage|SVG $resource
     * @return void
     */
    public function output(GdImage|SVG $resource): void;

}