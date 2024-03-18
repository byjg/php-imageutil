<?php

namespace ByJG\ImageUtil\Handler;

class GIFHandler implements ImageInterface
{

    /**
     * @inheritDoc
     */
    public static function mimeType()
    {
        return "image/gif";
    }

    /**
     * @inheritDoc
     */
    public static function extension()
    {
        return 'gif';
    }

    /**
     * @inheritDoc
     */
    public function load($filename)
    {
        $img = getimagesize($filename);
        $oldId = imagecreatefromgif($filename);
        $image = imagecreatetruecolor($img[0], $img[1]);
        imagecopy($image, $oldId, 0, 0, 0, 0, $img[0], $img[1]);
        return $image;
    }

    /**
     * @inheritDoc
     */
    public function save($resource, $filename = null, $params = [])
    {
        imagegif($resource, $filename);
    }

    /**
     * @inheritDoc
     */
    public function output($resource)
    {
        imagegif($resource);
    }
}