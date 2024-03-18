<?php

namespace ByJG\ImageUtil\Handler;

use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use ByJG\ImageUtil\ImageUtil;
use GdImage;

interface ImageHandlerInterface
{
    public function getWidth();

    public function getHeight();

    public function getFilename();

    public function getResource();

    public function empty($width, $height, Color $color = null): static;

    /**
     * @param $resource
     * @return array
     * @throws ImageUtilException
     */
    public function fromResource($resource);


    /**
     * @param $imageFile
     * @return array
     * @throws NotFoundException
     * @throws ImageUtilException
     */
    public function fromFile($imageFile);

    /**
     * Rotates the image to any direction using the given angle.
     * Arguments: $angle - The rotation angle, in degrees.
     * Example: $img = new Image("file.png"); $img->rotate(180); $img->show(); // Turn the image upside down.
     *
     * @param float $angle
     * @param int $background
     * @return $this
     */
    public function rotate($angle, $background = 0);

    /**
     * Mirrors the given image in the desired way.
     * Example: $img = new Image("file.png"); $img->flip(2); $img->show();
     *
     * @param int $type Direction of mirroring. This can be 1(Horizondal Flip), 2(Vertical Flip) or 3(Both Horizondal
     *     and Vertical Flip)
     * @return ImageUtil
     */
    public function flip($type);


    /**
     * Resize the image to an new size. Size can be specified in the arugments.
     *
     * @param int $newWidth The width of the desired image. If 0, the function will automatically calculate the width
     *     using the height ratio.
     * @param int $newHeight The width of the desired image. If 0, the function will automatically calculate the value
     *     using the width ratio.
     * @return ImageUtil
     */
    public function resize($newWidth = null, $newHeight = null);


    /**
     * Resize the image in a square format and maintain the aspect ratio. The space are filled the RGB color provided.
     *
     * @param int $newSize The new size of desired image (width and height are equals)
     * @param int $fillRed
     * @param int $fillGreen
     * @param int $fillBlue
     * @return ImageUtil
     * @throws ImageUtilException
     */
    public function resizeSquare($newSize, Color $color = null);


    /**
     * Resize the image but the aspect ratio is respected. The spaces left are filled with the RGB color provided.
     *
     * @param int $newX
     * @param int $newY
     * @param int $fillRed
     * @param int $fillGreen
     * @param int $fillBlue
     * @return ImageUtil
     * @throws ImageUtilException
     */
    public function resizeAspectRatio($newX, $newY, Color $color = null);

    /**
     * Stamp an image in the current image.
     *
     * @param ImageUtil|string $srcImage The image path or the image gd resource.
     * @param int $position
     * @param int $padding
     * @param int $oppacity
     * @return ImageUtil
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function stampImage($srcImage, $position = StampPosition::BOTTOMRIGHT, $padding = 5, $oppacity = 100);

    /**
     * Writes a text on the image.
     *
     * @param string $text
     * @param float[] $point
     * @param float $size
     * @param float $angle
     * @param string $font
     * @param int $maxwidth
     * @param float[] $rgbAr
     * @param int $textAlignment
     * @throws ImageUtilException
     */
    public function writeText($text, $point, $size, $angle, $font, $maxwidth = 0, $rgbAr = null, $textAlignment = 1);

    /**
     * Crops the given image from the ($from_x,$from_y) point to the ($to_x,$to_y) point.
     * Example: $img -> crop(250,200,400,250);
     *
     * @param float $fromX X coordinate from where the crop should start
     * @param float $fromY Y coordinate from where the crop should start
     * @param float $toX X coordinate from where the crop should end
     * @param float $toY Y coordinate from where the crop should end
     * @return ImageUtil
     */
    public function crop($fromX, $fromY, $toX, $toY);

    /**
     * Save the image to the given file. You can use this function to convert image types to. Just specify the image
     * format you want as the extension. Argument:$file_name - the file name to which the image should be saved to
     * Returns: false if save operation fails. Example: $img->save("image.png");
     *            $image->save('file.jpg');
     *
     * @param null $filename
     * @param int $quality
     * @return ImageUtil The object if not destroyed
     */
    public function save($filename = null, $quality = 90);

    /**
     * Display the image.
     * Example: $img->show();
     */
    public function show();

    /**
     * Make transparent the image. The transparent color must be provided
     *
     * @param int $transpRed
     * @param int $transpGreen
     * @param int $transpBlue
     * @return ImageUtil|GdImage|resource The image util object
     */
    public function makeTransparent(Color $color = null, $image = null);
    /**
     * Discard any changes made to the image and restore the original state
     */
    public function restore();

}