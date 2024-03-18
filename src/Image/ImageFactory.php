<?php

namespace ByJG\ImageUtil\Image;

use InvalidArgumentException;

class ImageFactory
{
    private static $configMime = [];
    private static $configExt = [];

    public static function registerHandler($class)
    {
        if (!in_array(ImageInterface::class, class_implements($class))) {
            throw new InvalidArgumentException(
                "The class '$class' is not a instance of ImageHandlerInterface"
            );
        }

        if (empty($class::extension()) || empty($class::mimeType())) {
            throw new InvalidArgumentException(
                "The class '$class' must implement the static method extension() and mimeType()"
            );
        }

        $extensionList = $class::extension();
        foreach ((array)$extensionList as $item) {
            self::$configExt[$item] = $class;
        }

        $mimeTypeList = $class::mimeType();
        foreach ((array)$mimeTypeList as $item) {
            self::$configMime[$item] = $class;
        }
    }

    public static function registerAll()
    {
        if (!empty(self::$configExt)) {
            return;
        }

        self::registerHandler(PNGImage::class);
        self::registerHandler(GIFImage::class);
        self::registerHandler(JPGImage::class);
        self::registerHandler(BMPImage::class);
        self::registerHandler(WEBPImage::class);
    }

    /**
     * @param $mime
     * @return ImageInterface
     */
    public static function instanceFromMime($mime)
    {
        self::registerAll();
        if (!isset(self::$configMime[$mime])) {
            throw new InvalidArgumentException("The '$mime' mime type does not exist.");
        }

        $class = self::$configMime[$mime];
        return new $class();
    }

    /**
     * @param $ext
     * @return ImageInterface
     */
    public static function instanceFromExtension($ext)
    {
        self::registerAll();
        if (!isset(self::$configExt[$ext])) {
            throw new InvalidArgumentException("The '$ext' extension does not exist.");
        }

        $class = self::$configExt[$ext];
        return new $class();
    }
}