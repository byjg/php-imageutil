<?php

namespace ByJG\ImageUtil\Handler;

use ByJG\ImageUtil\AlphaColor;
use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Enum\TextAlignment;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Image\ImageFactory;
use GdImage;
use InvalidArgumentException;
use Override;

class GdHandler implements ImageHandlerInterface
{
    protected ?GdImage $originalImage = null;

    protected ?GdImage $image = null;

    protected ?string $fileName;

    #[Override]
    public function getWidth(): int
    {
        return imagesx($this->getGdImage());
    }

    #[Override]
    public function getHeight(): int
    {
        return imagesy($this->getGdImage());
    }

    #[Override]
    public function getResource(): GdImage|null
    {
        return $this->image;
    }

    protected function setImage(GdImage $resource, string|null $filename = null): void
    {
        $this->image = $resource;
        $originalImage = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        if ($originalImage === false) {
            throw new ImageUtilException('Failed to create true color image');
        }
        $this->originalImage = $originalImage;
        imagecopy($this->originalImage, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());

        if (!empty($filename)) {
            $this->fileName = $filename;
        }
    }

    #[Override]
    public function getFilename(): ?string
    {
        return $this->fileName;
    }

    #[Override]
    public function empty(int $width, int $height, ?Color $color = null): static
    {
        $image = imagecreatetruecolor($width, $height);
        if ($image === false) {
            throw new ImageUtilException('Failed to create true color image');
        }
        $this->setImage($image);
        $this->fileName = null;

        if (!empty($color)) {
            $fill = $this->allocateColor($color);
            if ($fill === false) {
                throw new ImageUtilException('Error: The specified color is not valid');
            }
            imagefill($image, 0, 0, intval($fill));
        }

        return $this;
    }


    /**
     * @inheritDoc
     */
    #[Override]
    public function fromResource(mixed $resource): static
    {
        if (!($resource instanceof GdImage)) {
            throw new ImageUtilException('Invalid resource type. Expected GdImage.');
        }
        $this->setImage($resource, sys_get_temp_dir() . '/img_' . uniqid() . '.png');
        $this->retainTransparency();

        return $this;
    }


    protected function retainTransparency(?GdImage $image = null): void
    {
        if ($image === null) {
            $image = $this->image;
        }
        if ($image === null) {
            throw new ImageUtilException('Image resource is not initialized');
        }
        imagealphablending($image, false);
        imagesavealpha($image, true);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function rotate(int $angle, int $background = 0): static
    {
        $this->retainTransparency();
        $rotated = imagerotate($this->getGdImage(), $angle, $background);
        if ($rotated === false) {
            throw new ImageUtilException('Failed to rotate image');
        }
        $this->image = $rotated;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function flip(Flip $type): static
    {
        if ($type !== Flip::HORIZONTAL && $type !== Flip::VERTICAL && $type !== Flip::BOTH) {
            throw new InvalidArgumentException('You need to pass the flip type');
        }

        $width = $this->getWidth();
        $height = $this->getHeight();
        $imgDest = imagecreatetruecolor($width, $height);
        if ($imgDest === false) {
            throw new ImageUtilException('Failed to create destination image');
        }
        $this->retainTransparency($imgDest);
        $imgSrc = $this->getGdImage();

        switch ($type) {
            //Mirroring direction
            case Flip::HORIZONTAL:
                for ($x = 0; $x < $width; $x++) {
                    imagecopy($imgDest, $imgSrc, $width - $x - 1, 0, $x, 0, 1, $height);
                }
                break;

            case Flip::VERTICAL:
                for ($y = 0; $y < $height; $y++) {
                    imagecopy($imgDest, $imgSrc, 0, $height - $y - 1, 0, $y, $width, 1);
                }
                break;

            default:
                for ($x = 0; $x < $width; $x++) {
                    imagecopy($imgDest, $imgSrc, $width - $x - 1, 0, $x, 0, 1, $height);
                }

                $rowBuffer = imagecreatetruecolor($width, 1);
                if ($rowBuffer === false) {
                    throw new ImageUtilException('Failed to create row buffer');
                }
                for ($y = 0; $y < ($height / 2); $y++) {
                    imagecopy($rowBuffer, $imgDest, 0, 0, 0, $height - $y - 1, $width, 1);
                    imagecopy($imgDest, $imgDest, 0, $height - $y - 1, 0, $y, $width, 1);
                    imagecopy($imgDest, $rowBuffer, 0, $y, 0, 0, $width, 1);
                }

                break;
        }

        $this->image = $imgDest;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function resize(?int $newWidth = null, ?int $newHeight = null): static
    {
        if (!is_numeric($newHeight) && !is_numeric($newWidth)) {
            throw new InvalidArgumentException('There are no valid values');
        }

        $height = $this->getHeight();
        $width = $this->getWidth();

        //If the width or height is give as 0, find the correct ratio using the other value
        if (!$newHeight && $newWidth) {
            $newHeight = intval($height * $newWidth / $width); //Get the new height in the correct ratio
        }

        if ($newHeight && !$newWidth) {
            $newWidth = intval($width * $newHeight / $height); //Get the new width in the correct ratio
        }

        if ($newWidth === null || $newHeight === null) {
            throw new InvalidArgumentException('Width and height must be specified');
        }

        //Create the image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($newImage === false) {
            throw new ImageUtilException('Failed to create resized image');
        }
        $this->retainTransparency($newImage);
        imagecopyresampled($newImage, $this->getGdImage(), 0, 0, 0, 0, intval($newWidth), intval($newHeight), $width, $height);

        $this->image = $newImage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function resizeSquare(int $newSize, ?Color $color = null): static
    {
        return $this->resizeAspectRatio($newSize, $newSize, $color);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function resizeAspectRatio(int $newX, int $newY, ?Color $color = null): static
    {
        if (empty($color)) {
            $color = new AlphaColor(255, 255, 255, 127);
        }

        $image = $this->getGdImage();

        $width = $this->getWidth();
        $height = $this->getHeight();

        $ratio = (float)$width / (float)$height;
        $newRatio = (float)$newX / (float)$newY;

        if ($newRatio > $ratio) {
            $newWidth = (float)$newY * $ratio;
            $newHeight = (float)$newY;
        } else {
            $newHeight = (float)$newX / $ratio;
            $newWidth = (float)$newX;
        }

        $newImage = imagecreatetruecolor($newX, $newY);
        if ($newImage === false) {
            throw new ImageUtilException('Failed to create aspect ratio image');
        }
        $this->retainTransparency($newImage);
        $allocateColor = $this->allocateColor($color, $newImage);
        if ($allocateColor === false) {
            throw new ImageUtilException('Error: The specified color is not valid');
        }
        imagefill($newImage, 0, 0, intval($allocateColor));

        imagecopyresampled(
            $newImage,
            $image,
            intval(((float)$newX - $newWidth) / 2.0),
            intval(((float)$newY - $newHeight) / 2.0),
            0,
            0,
            intval($newWidth),
            intval($newHeight),
            $width,
            $height
        );

        $this->image = $newImage;

        return $this;
    }

    /**
     * @param int $padY
     * @inheritDoc
     */
    #[Override]
    public function stampImage(ImageHandlerInterface $srcImage, StampPosition $position = StampPosition::BOTTOM_RIGHT, int $padX = 5, int $padY = 5, int $opacity = 100): static
    {
        $dstImage = $this->getGdImage();

        $watermark = $srcImage->getResource();
        if ($watermark === null) {
            throw new ImageUtilException('Watermark image resource is not initialized');
        }

        if (!($watermark instanceof GdImage)) {
            $watermark = $watermark->getGdImage();
        }

        $this->retainTransparency($dstImage);
        $this->retainTransparency($watermark);

        $dstWidth = imagesx($dstImage);
        $dstHeight = imagesy($dstImage);
        $srcWidth = imagesx($watermark);
        $srcHeight = imagesy($watermark);

        if ($position == StampPosition::RANDOM) {
            $position = rand(1, 9);
        }
        switch ($position) {
            case StampPosition::TOP_RIGHT:
                $dstX = ($dstWidth - $srcWidth) - $padX;
                $dstY = $padY;
                break;
            case StampPosition::TOP_LEFT:
                $dstX = $padX;
                $dstY = $padY;
                break;
            case StampPosition::BOTTOM_RIGHT:
                $dstX = ($dstWidth - $srcWidth) - $padX;
                $dstY = ($dstHeight - $srcHeight) - $padY;
                break;
            case StampPosition::BOTTOM_LEFT:
                $dstX = $padX;
                $dstY = ($dstHeight - $srcHeight) - $padY;
                break;
            case StampPosition::CENTER:
                $dstX = (((float)$dstWidth / 2.0) - ((float)$srcWidth / 2.0));
                $dstY = (((float)$dstHeight / 2.0) - ((float)$srcHeight / 2.0));
                break;
            case StampPosition::TOP:
                $dstX = (((float)$dstWidth / 2.0) - ((float)$srcWidth / 2.0));
                $dstY = $padY;
                break;
            case StampPosition::BOTTOM:
                $dstX = (((float)$dstWidth / 2.0) - ((float)$srcWidth / 2.0));
                $dstY = ($dstHeight - $srcHeight) - $padY;
                break;
            case StampPosition::LEFT:
                $dstX = $padX;
                $dstY = (((float)$dstHeight / 2.0) - ((float)$srcHeight / 2.0));
                break;
            case StampPosition::RIGHT:
                $dstX = ($dstWidth - $srcWidth) - $padX;
                $dstY = (((float)$dstHeight / 2.0) - ((float)$srcHeight / 2.0));
                break;
            default:
                throw new ImageUtilException('Invalid Stamp Position');
        }

        $cut = imagecreatetruecolor($srcWidth, $srcHeight);
        if ($cut === false) {
            throw new ImageUtilException('Failed to create stamp cut image');
        }
        $dstX = intval(round($dstX));
        $dstY = intval(round($dstY));
        imagecopy($cut, $dstImage, 0, 0, $dstX, $dstY, $srcWidth, $srcHeight);
        imagecopy($cut, $watermark, 0, 0, 0, 0, $srcWidth, $srcHeight);
        imagecopymerge($dstImage, $cut, $dstX, $dstY, 0, 0, $srcWidth, $srcHeight, $opacity);

        $this->image = $dstImage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function writeText(string $text, array $point, float $size, int $angle, string $font, int $maxWidth = 0, ?Color $textColor = null, TextAlignment $textAlignment = TextAlignment::LEFT): static
    {
        if ($this->image === null) {
            throw new ImageUtilException('Image resource is not initialized');
        }

        if (!is_readable($font)) {
            throw new ImageUtilException('Error: The specified font not found');
        }

        if (empty($textColor)) {
            $textColor = new Color(0, 0, 0);
        }

        $color = imagecolorallocate($this->image, $textColor->getRed(), $textColor->getGreen(), $textColor->getBlue());
        if ($color === false) {
            throw new ImageUtilException('Failed to allocate color for text');
        }

        // Determine the line break if required.
        if (($maxWidth > 0) && ($angle == 0)) {
            $words = explode(' ', $text);
            $lines = [$words[0]];
            $currentLine = 0;

            $numberOfWords = count($words);
            for ($i = 1; $i < $numberOfWords; $i++) {
                $lineSize = imagettfbbox($size, 0, $font, $lines[$currentLine] . ' ' . $words[$i]);
                if ($lineSize === false) {
                    throw new ImageUtilException('Failed to calculate text bounding box');
                }
                if ($lineSize[2] - $lineSize[0] < $maxWidth) {
                    $lines[$currentLine] .= ' ' . $words[$i];
                } else {
                    $currentLine++;
                    $lines[$currentLine] = $words[$i];
                }
            }
        } else {
            $lines = [$text];
        }

        $curX = $point[0];
        $curY = $point[1];

        foreach ($lines as $text) {
            $bbox = imagettfbbox($size, $angle, $font, $text);
            if ($bbox === false) {
                throw new ImageUtilException('Failed to calculate text bounding box');
            }

            switch ($textAlignment) {
                case TextAlignment::RIGHT:
                    $curX = (float)$point[0] - (float)abs($bbox[2] - $bbox[0]);
                    break;

                case TextAlignment::CENTER:
                    $curX = (float)$point[0] - ((float)abs($bbox[2] - $bbox[0]) / 2.0);
                    break;

                case TextAlignment::LEFT:
                    // Don't change anything
            }

            imagettftext($this->image, $size, $angle, intval(round($curX)), intval(round($curY)), $color, $font, $text);

            $curY += ($size * 1.35);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function crop(int $fromX, int $fromY, int $toX, int $toY): static
    {
        $newWidth = $toX - $fromX;
        $newHeight = $toY - $fromY;

        //Create the image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($newImage === false) {
            throw new ImageUtilException('Failed to create cropped image');
        }
        $this->retainTransparency($newImage);
        imagecopy($newImage, $this->getGdImage(), 0, 0, $fromX, $fromY, $newWidth, $newHeight);
        $this->image = $newImage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function save(?string $filename = null, int $quality = 90): void
    {
        if ($this->image === null) {
            throw new ImageUtilException('Image resource is not initialized');
        }

        if (is_null($filename)) {
            $filename = $this->fileName;
        }

        if ($filename === null) {
            throw new ImageUtilException('Filename is required for save operation');
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        ImageFactory::instanceFromExtension($extension)->save($this->image, $filename, ['quality' => $quality]);

        $this->setImage($this->image, $filename);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function show(): void
    {
        if ($this->image === null) {
            throw new ImageUtilException('Image resource is not initialized');
        }

        if ($this->fileName === null) {
            throw new ImageUtilException('Filename is required for show operation');
        }

        if (ob_get_level()) {
            ob_clean();
        }
        $info = getimagesize($this->fileName);
        if ($info === false) {
            throw new ImageUtilException('Failed to get image size from file');
        }
        header("Content-type: " . $info['mime']);
        ImageFactory::instanceFromMime($info['mime'])->output($this->image);
    }



    /**
     * @param int $tolerance
     * @inheritDoc
     */
    #[Override]
    public function makeTransparent(?Color $color = null, int $tolerance = 0): static
    {
        if ($this->image === null) {
            throw new ImageUtilException('Image resource is not initialized');
        }

        if (empty($color)) {
            $color = new AlphaColor(0, 0, 0, 127);
        } else {
            $color = new AlphaColor($color->getRed(), $color->getGreen(), $color->getBlue(), 127);
        }

        $isColorInRange = function($currentColor, $targetColor) use ($tolerance): bool {
            if ($this->image === null) {
                return false;
            }
            $currentColors = imagecolorsforindex($this->image, $currentColor);
            $targetColors = imagecolorsforindex($this->image, $targetColor);

            return (
                abs($currentColors['red'] - $targetColors['red']) <= $tolerance &&
                abs($currentColors['green'] - $targetColors['green']) <= $tolerance &&
                abs($currentColors['blue'] - $targetColors['blue']) <= $tolerance
            );
        };

        // Get image dimensions
        $width = imagesx($this->image);
        $height = imagesy($this->image);

        // Define the color to make transparent
        // You can adjust the tolerance if the color isn't exactly the one you defined.
        $black = imagecolorallocate($this->image, $color->getRed(), $color->getGreen(), $color->getBlue());
        if ($black === false) {
            throw new ImageUtilException('Failed to allocate color for transparency');
        }

        // Loop through each pixel in the image
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                // Get the current pixel color
                $currentColor = imagecolorat($this->image, $x, $y);
                if ($currentColor === false) {
                    continue;
                }

                // Check if the current color is within the tolerance range of black
                if ($isColorInRange($currentColor, $black)) {
                    // Set the pixel to transparent
                    $alphaColor = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
                    if ($alphaColor !== false) {
                        imagesetpixel($this->image, $x, $y, $alphaColor);
                    }
                }
            }
        }

        // Save the image
        imagealphablending($this->image, false);
        imagesavealpha($this->image, true);

        return $this;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public function restore(): static
    {
        if ($this->originalImage === null) {
            throw new ImageUtilException('Original image is not initialized');
        }

        $newImage = imagecreatetruecolor(imagesx($this->originalImage), imagesy($this->originalImage));
        if ($newImage === false) {
            throw new ImageUtilException('Failed to create restored image');
        }
        $this->image = $newImage;
        imagecopy($this->image, $this->originalImage, 0, 0, 0, 0, imagesx($this->originalImage), imagesy($this->originalImage));
        $this->retainTransparency();

        return $this;
    }

    protected function allocateColor(Color $color, ?GdImage $image = null): bool|int
    {
        if ($image === null) {
            $image = $this->image;
        }
        if ($image === null) {
            throw new ImageUtilException('Image resource is not initialized');
        }

        $alpha = $color->getAlpha();
        if ($alpha === null)
        {
            return imagecolorallocate($image, $color->getRed(), $color->getGreen(), $color->getBlue());
        }
        else
        {
            return imagecolorallocatealpha($image, $color->getRed(), $color->getGreen(), $color->getBlue(), $alpha);
        }

    }

    /**
     * Destroy the image to save the memory. Do this after all operations are complete.
     */
    public function __destruct()
    {
        if (isset($this->image)) {
            unset($this->image);
            unset($this->originalImage);
        }
    }

    #[Override]
    public function getGdImage(): GdImage
    {
        if ($this->image === null) {
            throw new ImageUtilException('Image resource is not initialized');
        }
        return $this->image;
    }
}