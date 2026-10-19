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
 * Entry point to create an image handler: GdHandler for the raster formats (GD must be installed)
 * and SvgHandler for SVG.
 * Example: $img = ImageUtil::fromFile('wheel.png');
 *          $img->flip(Flip::Horizontal)->resize(120, 0)->save('wheel.jpg');
 */
class ImageUtil
{
    /**
     * Creates a blank image. FileType::Svg returns an SvgHandler, any other type a GdHandler.
     * The image has no file name until it is saved.
     *
     * @param int $width
     * @param int $height
     * @param FileType $type
     * @param Color|null $color Background color; when null the image is left as created (black for GD, empty for SVG)
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
     * Wraps an existing resource: a GdImage returns a GdHandler, an SVG an SvgHandler.
     * Without $filename, a GdHandler gets a temporary .png file name and an SvgHandler none.
     *
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
     * Loads an image from a local file or from a http(s):// or file:// URL. The format is chosen by the
     * file extension. A URL is downloaded to a temporary file that is removed after loading, so the
     * handler does not keep it as its file name (see fromResource()).
     *
     * @param string $imageFile
     * @return ImageHandlerInterface
     * @throws ImageUtilException When the URL has no extension, cannot be fetched or the file cannot be loaded
     * @throws NotFoundException When the file does not exist or is not readable
     * @throws \InvalidArgumentException When the extension is not supported
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

        // A downloaded image has no file of its own: the temporary copy is removed below
        $handler = ImageUtil::fromResource($resource, $http ? null : $imageFile);

        if ($http) {
            unlink($imageFile);
        }

        return $handler;
    }
}
