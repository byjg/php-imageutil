<?php

namespace ByJG\ImageUtil\Handler;

use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Enum\TextAlignment;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use GdImage;

interface ImageHandlerInterface
{
    public function getWidth(): int;

    public function getHeight(): int;

    public function getFilename(): ?string;

    public function getResource(): mixed;

    public function empty(int $width, int $height, ?Color $color = null): static;

    /**
     * @param mixed $resource
     * @return $this
     * @throws ImageUtilException
     */
    public function fromResource(mixed $resource): static;


    /**
     * Rotates the image to any direction using the given angle.
     * Arguments: $angle - The rotation angle, in degrees.
     * Example: $img = new Image("file.png"); $img->rotate(180); $img->show(); // Turn the image upside down.
     *
     * @param int $angle
     * @param int $background
     * @return $this
     */
    public function rotate(int $angle, int $background = 0): static;

    /**
     * Mirrors the given image in the desired way.
     * Example: $img = new Image("file.png"); $img->flip(2); $img->show();
     *
     * @param Flip $type Direction of mirroring. This can be 1(Horizontal Flip), 2(Vertical Flip) or 3(Both Horizontal)
     *     and Vertical Flip)
     * @return $this
     */
    public function flip(Flip $type): static;


    /**
     * Resize the image to a new size. Size can be specified in the arguments.
     *
     * @param int|null $newWidth The width of the desired image. If 0, the function will automatically calculate the width
     *     using the height ratio.
     * @param int|null $newHeight The width of the desired image. If 0, the function will automatically calculate the value
     *     using the width ratio.
     * @return $this
     */
    public function resize(?int $newWidth = null, ?int $newHeight = null): static;


    /**
     * Resize the image in a square format and maintain the aspect ratio. The space are filled the RGB color provided.
     *
     * @param int $newSize The new size of desired image (width and height are equals)
     * @param Color|null $color
     * @return $this
     * @throws ImageUtilException
     */
    public function resizeSquare(int $newSize, ?Color $color = null): static;


    /**
     * Resize the image but the aspect ratio is respected. The spaces left are filled with the RGB color provided.
     *
     * @param int $newX
     * @param int $newY
     * @param Color|null $color
     * @return $this
     * @throws ImageUtilException
     */
    public function resizeAspectRatio(int $newX, int $newY, ?Color $color = null): static;

    /**
     * Stamp an image in the current image.
     *
     * @param ImageHandlerInterface $srcImage The image path or the image gd resource.
     * @param StampPosition $position
     * @param int $padX
     * @param int $padY
     * @param int $opacity
     * @return $this
     * @throws ImageUtilException
     * @throws NotFoundException
     */
    public function stampImage(ImageHandlerInterface $srcImage, StampPosition $position = StampPosition::BOTTOM_RIGHT, int $padX = 5, int $padY = 5, int $opacity = 100): static;

    /**
     * Writes a text on the image.
     *
     * @param string $text
     * @param float[] $point
     * @param float $size
     * @param int $angle
     * @param string $font
     * @param int $maxWidth
     * @param Color|null $textColor
     * @param TextAlignment $textAlignment
     * @return $this
     * @throws ImageUtilException
     */
    public function writeText(string $text, array $point, float $size, int $angle, string $font, int $maxWidth = 0, ?Color $textColor = null, TextAlignment $textAlignment = TextAlignment::LEFT): static;

    /**
     * Crops the given image from the ($from_x,$from_y) point to the ($to_x,$to_y) point.
     * Example: $img -> crop(250,200,400,250);
     *
     * @param int $fromX X coordinate from where the crop should start
     * @param int $fromY Y coordinate from where the crop should start
     * @param int $toX X coordinate from where the crop should end
     * @param int $toY Y coordinate from where the crop should end
     * @return $this
     */
    public function crop(int $fromX, int $fromY, int $toX, int $toY): static;

    /**
     * Save the image to the given file. You can use this function to convert image types to. Just specify the image
     * format you want as the extension. Argument:$file_name - the file name to which the image should be saved to
     * Returns: false if save operation fails. Example: $img->save("image.png");
     *            $image->save('file.jpg');
     *
     * @param string|null $filename
     * @param int $quality
     * @return void The object if not destroyed
     */
    public function save(?string $filename = null, int $quality = 90): void;

    /**
     * Display the image.
     * Example: $img->show();
     */
    public function show(): void;

    /**
     * Make transparent the image. The transparent color must be provided
     *
     * @param Color|null $color
     * @param int $tolerance
     * @return $this The image util object
     */
    public function makeTransparent(?Color $color = null, int $tolerance = 0): static;

    /**
     * Discard any changes made to the image and restore the original state
     */
    public function restore(): static;

    public function getGdImage(): GdImage;

}