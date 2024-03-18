<?php

namespace ByJG\ImageUtil\Handler;

class JPGHandler implements ImageInterface
{
    /**
     * @inheritDoc
     */
    public static function mimeType()
    {
        return "image/jpeg";
    }

    /**
     * @inheritDoc
     */
    public static function extension()
    {
        return ["jpg", "jpeg"];
    }

    /**
     * @inheritDoc
     */
    public function load($filename)
    {
        return imagecreatefromjpeg($filename);
    }

    /**
     * @inheritDoc
     */
    public function save($resource, $filename = null, $params = [])
    {
        imagejpeg($resource, $filename, $params['quality']);
    }

    /**
     * @inheritDoc
     */
    public function output($resource)
    {
        imagejpeg($resource);
    }
}