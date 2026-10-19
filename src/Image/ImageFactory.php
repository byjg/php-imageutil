<?php

namespace ByJG\ImageUtil\Image;

use InvalidArgumentException;

class ImageFactory
{
    private static array $configMime = [];
    private static array $configExt = [];

    /**
     * Registers an image format, making it available by its extensions and mime types.
     *
     * @param string $class A class implementing ImageInterface
     * @throws InvalidArgumentException When the class does not implement ImageInterface or declares no extension or mime type
     */
    public static function registerHandler(string $class): void
    {
        $implements = class_implements($class);
        if ($implements === false || !in_array(ImageInterface::class, $implements)) {
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

    /**
     * Registers the built-in formats (PNG, GIF, JPEG, BMP, WEBP and SVG), unless any format is already registered.
     */
    public static function registerAll(): void
    {
        if (!empty(self::$configExt)) {
            return;
        }

        self::registerHandler(PngImage::class);
        self::registerHandler(GIFImage::class);
        self::registerHandler(JpgImage::class);
        self::registerHandler(BmpImage::class);
        self::registerHandler(WebpImage::class);
        self::registerHandler(SvgImage::class);
    }

    /**
     * @param string $mime
     * @return ImageInterface
     * @throws InvalidArgumentException When no format handles the mime type
     */
    public static function instanceFromMime(string $mime): ImageInterface
    {
        self::registerAll();
        if (!isset(self::$configMime[$mime])) {
            throw new InvalidArgumentException("The '$mime' mime type does not exist.");
        }

        $class = self::$configMime[$mime];
        $instance = new $class();
        assert($instance instanceof ImageInterface);
        return $instance;
    }

    /**
     * @param string $ext Lowercase, without the dot
     * @return ImageInterface
     * @throws InvalidArgumentException When no format handles the extension
     */
    public static function instanceFromExtension(string $ext): ImageInterface
    {
        self::registerAll();
        if (!isset(self::$configExt[$ext])) {
            throw new InvalidArgumentException("The '$ext' extension does not exist.");
        }

        $class = self::$configExt[$ext];
        $instance = new $class();
        assert($instance instanceof ImageInterface);
        return $instance;
    }
}