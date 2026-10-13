<?php

namespace ByJG\ImageUtil;

use ByJG\ImageUtil\Enum\FileType;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use ByJG\ImageUtil\Handler\GdHandler;
use ByJG\ImageUtil\Handler\ImageHandlerInterface;
use ByJG\ImageUtil\Handler\SvgHandler;
use ByJG\ImageUtil\Image\ImageFactory;
use GdImage;
use SVG\SVG;

/**
 * A Wrapper for GD library in PHP. GD must be installed in your system for this to work.
 * Example: $img = new Image('wheel.png');
 *            $img->flip(1)->resize(120, 0)->save('wheel.jpg');
 */
class ImageUtil
{
    /**
     *
     * @param int $width
     * @param int $height
     * @param FileType $type
     * @param Color|null $color
     * @return GdHandler|SvgHandler
     * @throws ImageUtilException
     */
    public static function empty(int $width, int $height, FileType $type = FileType::Png, ?Color $color = null): SvgHandler|GdHandler
    {
        if ($type == FileType::Svg) {
            $image = new SvgHandler();
        } else {
            $image = new GdHandler();
        }
        $image->empty($width, $height, $color);
        return $image;
    }


    /**
     * @param GdImage|SVG $resource
     * @param string|null $filename The file the resource was loaded from, when there is one
     *
     * @return GdHandler|SvgHandler
     *
     * @throws ImageUtilException
     */
    public static function fromResource(GdImage|SVG $resource, ?string $filename = null): SvgHandler|GdHandler
    {
        if ($resource instanceof GdImage) {
            $image = new GdHandler();
        } else {
            $image = new SvgHandler();
        }
        return $image->fromResource($resource, $filename);
    }

    /**
     * @param string $imageFile
     * @return ImageHandlerInterface
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public static function fromFile(string $imageFile): ImageHandlerInterface
    {
        $http = false;
        if (preg_match('/^(https?:|file:)/', $imageFile)) {
            $http = true;
            $url = $imageFile;
            // The extension is in the path of the URL; a query string or a fragment is not part of it
            $extension = pathinfo((string)parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
            if ($extension === '') {
                throw new ImageUtilException("Cannot determine file extension from URL");
            }
            $imageFile = sys_get_temp_dir() . '/img_' . uniqid() . '.' . $extension;
            $contents = file_get_contents($url);
            if ($contents === false) {
                throw new ImageUtilException("Failed to fetch image from URL: " . $url);
            }
            file_put_contents($imageFile, $contents);
        }

        if (!file_exists($imageFile) || !is_readable($imageFile)) {
            throw new NotFoundException("File is not found or not is readable. Cannot continue.");
        }

        $image = ImageFactory::instanceFromExtension(strtolower(pathinfo($imageFile, PATHINFO_EXTENSION)));

        $resource = $image->load($imageFile);
        if ($resource === false) {
            throw new ImageUtilException("Invalid file: " . $imageFile);
        }

        // A downloaded image has no file of its own: the temporary copy is removed below
        $handler = ImageUtil::fromResource($resource, $http ? null : $imageFile);

        if ($http) {
            unlink($imageFile);
        }

        return $handler;
    }
}
