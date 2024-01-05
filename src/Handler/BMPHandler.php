<?php

namespace ByJG\ImageUtil\Handler;

class BMPHandler implements ImageHandlerInterface
{

    /**
     * @inheritDoc
     */
    public static function mimeType()
    {
        return [ 'image/bmp', 'image/x-ms-bmp' ];
    }

    /**
     * @inheritDoc
     */
    public static function extension()
    {
        return "bmp";
    }

    /**
     * @inheritDoc
     */
    public function load($filename)
    {
        return imagecreatefrombmp($filename);
    }

    /**
     * @inheritDoc
     */
    public function save($resource, $filename = null, $params = [])
    {
        imagebmp($resource, $filename);
    }

    /**
     * @inheritDoc
     */
    public function output($resource)
    {
        imagebmp($resource);
    }
}