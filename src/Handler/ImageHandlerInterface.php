<?php

namespace ByJG\ImageUtil\Handler;

use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Enum\TextAlignment;
use ByJG\ImageUtil\Exception\ImageUtilException;
use GdImage;

interface ImageHandlerInterface
{
    public function getWidth(): int;

    public function getHeight(): int;

    /**
     * The file the image was loaded from or last saved to, or null when there is none.
     */
    public function getFilename(): ?string;

    /**
     * The underlying resource: a GdImage for GdHandler, an SVG for SvgHandler.
     */
    public function getResource(): mixed;

    /**
     * Replaces the current image with a blank one of the given size, filled with $color when provided.
     *
     * @param int $width
     * @param int $height
     * @param Color|null $color
     * @return $this
     * @throws ImageUtilException
     */
    public function empty(int $width, int $height, ?Color $color = null): static;

    /**
     * @param mixed $resource
     * @param string|null $filename The file the resource was loaded from, when there is one
     * @return $this
     * @throws ImageUtilException
     */
    public function fromResource(mixed $resource, ?string $filename = null): static;


    /**
     * Rotates the image counter-clockwise by the given angle.
     * Example: $img = ImageUtil::fromFile("file.png"); $img->rotate(180); $img->show(); // Turn the image upside down.
     *
     * @param int $angle The rotation angle, in degrees
     * @param int $background The color (as returned by imagecolorallocate()) of the area left uncovered after the rotation
     * @return $this
     * @throws ImageUtilException
     */
    public function rotate(int $angle, int $background = 0): static;

    /**
     * Mirrors the given image in the desired way.
     * Example: $img = ImageUtil::fromFile("file.png"); $img->flip(Flip::Vertical); $img->show();
     *
     * @param Flip $type Direction of mirroring: Flip::Horizontal, Flip::Vertical or Flip::Both
     * @return $this
     * @throws ImageUtilException
     */
    public function flip(Flip $type): static;


    /**
     * Resize the image to a new size. At least one of the sizes must be given.
     *
     * @param int|null $newWidth The width of the desired image. If 0 or null, it is calculated from $newHeight
     *     keeping the aspect ratio.
     * @param int|null $newHeight The height of the desired image. If 0 or null, it is calculated from $newWidth
     *     keeping the aspect ratio.
     * @return $this
     * @throws \InvalidArgumentException When neither size is given
     * @throws ImageUtilException
     */
    public function resize(?int $newWidth = null, ?int $newHeight = null): static;


    /**
     * Resize the image in a square format and maintain the aspect ratio. The space left is filled with the color provided.
     *
     * @param int $newSize The new size of desired image (width and height are equals)
     * @param Color|null $color Fill color; transparent white when null
     * @return $this
     * @throws ImageUtilException
     */
    public function resizeSquare(int $newSize, ?Color $color = null): static;


    /**
     * Resize the image but the aspect ratio is respected. The image is centered and the spaces left are filled
     * with the color provided.
     *
     * @param int $newX
     * @param int $newY
     * @param Color|null $color Fill color; transparent white when null
     * @return $this
     * @throws ImageUtilException
     */
    public function resizeAspectRatio(int $newX, int $newY, ?Color $color = null): static;

    /**
     * Stamp an image in the current image.
     *
     * @param ImageHandlerInterface $srcImage The image to stamp; an SvgHandler is rasterized at its own width and height
     * @param StampPosition $position StampPosition::Random picks one of the other positions
     * @param int $padX Horizontal distance, in pixels, from the left or right edge (not used when centered horizontally)
     * @param int $padY Vertical distance, in pixels, from the top or bottom edge (not used when centered vertically)
     * @param int $opacity From 0 (invisible) to 100 (opaque)
     * @return $this
     * @throws ImageUtilException
     */
    public function stampImage(ImageHandlerInterface $srcImage, StampPosition $position = StampPosition::BottomRight, int $padX = 5, int $padY = 5, int $opacity = 100): static;

    /**
     * Writes a text on the image using a TrueType font.
     *
     * @param string $text
     * @param float[] $point The [x, y] position of the baseline of the first line; with TextAlignment::Right or
     *     TextAlignment::Center, x is where the line ends or its center
     * @param float $size The font size, in points
     * @param int $angle The text angle, in degrees
     * @param string $font Path to the TrueType font file
     * @param int $maxWidth When greater than 0 and $angle is 0, wraps the text so each line stays narrower than this, in pixels
     * @param Color|null $textColor Black when null
     * @param TextAlignment $textAlignment
     * @return $this
     * @throws ImageUtilException When the font is not readable or the text cannot be drawn
     */
    public function writeText(string $text, array $point, float $size, int $angle, string $font, int $maxWidth = 0, ?Color $textColor = null, TextAlignment $textAlignment = TextAlignment::Left): static;

    /**
     * Crops the image from the ($fromX, $fromY) point to the ($toX, $toY) point.
     * Example: $img->crop(250, 200, 400, 250);
     *
     * @param int $fromX X coordinate from where the crop should start
     * @param int $fromY Y coordinate from where the crop should start
     * @param int $toX X coordinate from where the crop should end
     * @param int $toY Y coordinate from where the crop should end
     * @return $this
     * @throws ImageUtilException
     */
    public function crop(int $fromX, int $fromY, int $toX, int $toY): static;

    /**
     * Save the image to the given file. You can use this function to convert image types too: the format comes from
     * the file extension. The saved image becomes the new state that restore() goes back to.
     * Example: $img->save("image.png");
     *
     * @param string|null $filename The file to save to; when null, the file the image was loaded from or last saved to
     * @param int $quality From 0 to 100; used by JPEG and WEBP, and mapped to the 0-9 compression level for PNG
     * @return void
     * @throws ImageUtilException When there is no file name to save to
     * @throws \InvalidArgumentException When the extension is not supported or the image cannot be saved in that format
     */
    public function save(?string $filename = null, int $quality = 90): void;

    /**
     * Sends the Content-type header and outputs the image. GdHandler uses the format of the file the image was
     * loaded from or last saved to.
     * Example: $img->show();
     *
     * @throws ImageUtilException
     */
    public function show(): void;

    /**
     * Makes transparent every pixel of the given color.
     *
     * @param Color|null $color The color to make transparent; black when null
     * @param int $tolerance How much each of the red, green and blue values may differ from $color and still match
     * @return $this
     * @throws ImageUtilException
     */
    public function makeTransparent(?Color $color = null, int $tolerance = 0): static;

    /**
     * Discard any changes made to the image and restore it to how it was loaded, created or last saved.
     *
     * @return $this
     * @throws ImageUtilException
     */
    public function restore(): static;

    /**
     * The image as a GdImage. SvgHandler returns a rasterized copy.
     *
     * @throws ImageUtilException
     */
    public function getGdImage(): GdImage;

}