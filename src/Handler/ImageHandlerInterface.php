<?php

namespace ByJG\ImageUtil\Handler;

use GdImage;

interface ImageHandlerInterface
{
    /**
     * @return string|array
     */
    public static function mimeType();

    /**
     * @return string|array
     */
    public static function extension();

    /**
     * @param $filename
     * @return resource|GdImage
     */
    public function load($filename);

    /**
     * @param resource|GdImage $resource
     * @param string $filename
     * @param array $params
     * @return void
     */
    public function save($resource, $filename, $params = []);

    /**
     * @param resource|GdImage $resource
     * @return mixed
     */
    public function output($resource);
}