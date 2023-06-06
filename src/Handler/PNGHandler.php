<?php

namespace ByJG\ImageUtil\Handler;

class PNGHandler implements ImageHandlerInterface
{

    /**
     * @inheritDoc
     */
    public static function mimeType()
    {
        return "image/png";
    }

    /**
     * @inheritDoc
     */
    public static function extension()
    {
        return "png";
    }

    /**
     * @inheritDoc
     */
    public function load($filename)
    {
        return imagecreatefrompng($filename);
    }

    /**
     * @inheritDoc
     */
    public function save($resource, $filename = null, $params = [])
    {
        $pngQuality = round((9 * $params['quality']) / 100);
        imagepng($resource, $filename, $pngQuality);
    }

    /**
     * @inheritDoc
     */
    public function output($resource)
    {
        imagepng($resource);
    }
}