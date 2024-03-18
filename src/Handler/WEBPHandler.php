<?php

namespace ByJG\ImageUtil\Handler;

class WEBPHandler implements ImageInterface
{

    /**
     * @inheritDoc
     */
    public static function mimeType()
    {
        return 'image/webp';
    }

    /**
     * @inheritDoc
     */
    public static function extension()
    {
        return "webp";
    }

    /**
     * @inheritDoc
     */
    public function load($filename)
    {
        return imagecreatefromwebp($filename);
    }

    /**
     * @inheritDoc
     */
    public function save($resource, $filename = null, $params = [])
    {
        imagewebp($resource, $filename, $params['quality']);
    }

    /**
     * @inheritDoc
     */
    public function output($resource)
    {
        imagewebp($resource);
    }
}