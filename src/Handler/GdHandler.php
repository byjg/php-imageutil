<?php

namespace ByJG\ImageUtil\Handler;

use ByJG\ImageUtil\AlphaColor;
use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Enum\TextAlignment;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use ByJG\ImageUtil\Image\ImageFactory;
use GdImage;
use InvalidArgumentException;
use SVG\SVG;

class GdHandler implements ImageHandlerInterface
{
    protected GdImage $originalImage;

    protected GdImage $image;

    protected ?string $fileName;

    public function getWidth(): int
    {
        return imagesx($this->image);
    }

    public function getHeight(): int
    {
        return imagesy($this->image);
    }

    public function getResource(): GdImage|SVG
    {
        return $this->image;
    }

    protected function setImage($resource, $filename = null): void
    {
        $this->image = $resource;
        $this->originalImage = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        imagecopy($this->originalImage, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());

        if (!empty($filename)) {
            $this->fileName = $filename;
        }
    }

    public function getFilename(): ?string
    {
        return $this->fileName;
    }

    public function empty(int $width, int $height, Color $color = null): static
    {
        $image = imagecreatetruecolor($width, $height);
        if (!empty($color)) {
            $fill = $this->allocateColor($color);
            imagefill($image, 0, 0, $fill);
        }

        $this->setImage($image);
        $this->fileName = null;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function fromResource(GdImage|SVG $resource): static
    {
        if ($resource instanceof SVG) {
            $image = new GdHandler();
            $resource = $image->fromResource($resource->toRasterImage($resource->getDocument()->getWidth(), $resource->getDocument()->getHeight()))->getResource();
        }

        if ($resource instanceof GdImage) {
            $this->setImage($resource, sys_get_temp_dir() . '/img_' . uniqid() . '.png');
            $this->retainTransparency();
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fromFile(string $imageFile): static
    {
        $http = false;
        if (preg_match('/^(https?:|file:)/', $imageFile)) {
            $http = true;
            $url = $imageFile;
            $imageFile = basename($url);
            $info = pathinfo($imageFile);
            $imageFile = sys_get_temp_dir() . '/img_' . uniqid() . '.' . $info['extension'];
            file_put_contents($imageFile, file_get_contents($url));
            $this->setImage($imageFile, $imageFile);
        }

        if (!file_exists($imageFile) || !is_readable($imageFile)) {
            throw new NotFoundException("File is not found or not is readable. Cannot continue.");
        }

        $img = getimagesize($imageFile);
        if (empty($img)) {
            throw new ImageUtilException("Invalid file: " . $imageFile);
        }
        $this->setImage(ImageFactory::instanceFromMime($img['mime'])->load($imageFile), $imageFile);
        $this->retainTransparency();

        if ($http) {
            unlink($imageFile);
            $this->fileName = null;
        }

        return $this;
    }

    protected function retainTransparency($image = null): void
    {
        if (empty($image)) {
            $image = $this->image;
        }
        imagealphablending($image, false);
        imagesavealpha($image, true);
    }

    /**
     * @inheritDoc
     */
    public function rotate(int $angle, int $background = 0): static
    {
        if (!is_numeric($angle)) {
            throw new InvalidArgumentException('You need to pass the angle');
        }

        $this->retainTransparency();
        $this->image = imagerotate($this->image, $angle, $background);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function flip(Flip $type): static
    {
        if ($type !== Flip::HORIZONTAL && $type !== Flip::VERTICAL && $type !== Flip::BOTH) {
            throw new InvalidArgumentException('You need to pass the flip type');
        }

        $width = $this->getWidth();
        $height = $this->getHeight();
        $imgDest = imagecreatetruecolor($width, $height);
        $this->retainTransparency($imgDest);
        $imgSrc = $this->image;

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
                for ($y = 0; $y < ($height / 2); $y++) {
                    imagecopy($rowBuffer, $imgDest, 0, 0, 0, $height - $y - 1, $width, 1);
                    imagecopy($imgDest, $imgDest, 0, $height - $y - 1, 0, $y, $width, 1);
                    imagecopy($imgDest, $rowBuffer, 0, $y, 0, 0, $width, 1);
                }

                imagedestroy($rowBuffer);
                break;
        }

        $this->image = $imgDest;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resize(?int $newWidth = null, ?int $newHeight = null): static
    {
        if (!is_numeric($newHeight) && !is_numeric($newWidth)) {
            throw new InvalidArgumentException('There are no valid values');
        }

        $height = $this->getHeight();
        $width = $this->getWidth();

        //If the width or height is give as 0, find the correct ratio using the other value
        if (!$newHeight && $newWidth) {
            $newHeight = $height * $newWidth / $width; //Get the new height in the correct ratio
        }

        if ($newHeight && !$newWidth) {
            $newWidth = $width * $newHeight / $height; //Get the new width in the correct ratio
        }


        //Create the image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        $this->retainTransparency($newImage);
        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, intval($newWidth), intval($newHeight), $width, $height);

        $this->image = $newImage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resizeSquare(int $newSize, Color $color = null): static
    {
        return $this->resizeAspectRatio($newSize, $newSize, $color);
    }

    /**
     * @inheritDoc
     */
    public function resizeAspectRatio(int $newX, int $newY, Color $color = null): static
    {
        if (empty($color)) {
            $color = new AlphaColor(255, 255, 255, 127);
        }

        if (!is_numeric($newX) || !is_numeric($newY)) {
            throw new ImageUtilException('There are no valid values');
        }

        $image = $this->image;

        $width = $this->getWidth();
        $height = $this->getHeight();

        $ratio = $width / $height;
        $newRatio = $newX / $newY;

        if ($newRatio > $ratio) {
            $newWidth = $newY * $ratio;
            $newHeight = $newY;
        } else {
            $newHeight = $newX / $ratio;
            $newWidth = $newX;
        }

        $newImage = imagecreatetruecolor($newX, $newY);
        $this->retainTransparency($newImage);
        imagefill($newImage, 0, 0, $this->allocateColor($color, $newImage));

        imagecopyresampled(
            $newImage,
            $image,
            intval(($newX - $newWidth) / 2),
            intval(($newY - $newHeight) / 2),
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
     * @inheritDoc
     */
    public function stampImage(ImageHandlerInterface $srcImage, StampPosition $position = StampPosition::BOTTOM_RIGHT, int $padding = 5, int $opacity = 100): static
    {
        $dstImage = $this->image;

        $watermark = $srcImage->getResource();

        $this->retainTransparency($dstImage);
        $this->retainTransparency($watermark);

        $dstWidth = imagesx($dstImage);
        $dstHeight = imagesy($dstImage);
        $srcWidth = imagesx($watermark);
        $srcHeight = imagesy($watermark);

        if (is_array($padding)) {
            $padX = $padding[0];
            $padY = $padding[1];
        } else {
            $padX = $padY = $padding;
        }

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
                $dstX = (($dstWidth / 2) - ($srcWidth / 2));
                $dstY = (($dstHeight / 2) - ($srcHeight / 2));
                break;
            case StampPosition::TOP:
                $dstX = (($dstWidth / 2) - ($srcWidth / 2));
                $dstY = $padY;
                break;
            case StampPosition::BOTTOM:
                $dstX = (($dstWidth / 2) - ($srcWidth / 2));
                $dstY = ($dstHeight - $srcHeight) - $padY;
                break;
            case StampPosition::LEFT:
                $dstX = $padX;
                $dstY = (($dstHeight / 2) - ($srcHeight / 2));
                break;
            case StampPosition::RIGHT:
                $dstX = ($dstWidth - $srcWidth) - $padX;
                $dstY = (($dstHeight / 2) - ($srcHeight / 2));
                break;
            default:
                throw new ImageUtilException('Invalid Stamp Position');
        }

//        imagecopy($dstImage, $watermark, $dstX, $dstY, 0, 0, $srcWidth, $srcHeight);
        $this->imagecopymergeAlpha($dstImage, $watermark, $dstX, $dstY, 0, 0, $srcWidth, $srcHeight, $opacity);
        $this->image = $dstImage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function writeText(string $text, array $point, float $size, int $angle, string $font, int $maxWidth = 0, array $rgbAr = null, TextAlignment $textAlignment = TextAlignment::LEFT): static
    {
        if (!is_readable($font)) {
            throw new ImageUtilException('Error: The specified font not found');
        }

        if (!is_array($rgbAr)) {
            $rgbAr = [0, 0, 0];
        }

        $color = imagecolorallocate($this->image, $rgbAr[0], $rgbAr[1], $rgbAr[2]);

        // Determine the line break if required.
        if (($maxWidth > 0) && ($angle == 0)) {
            $words = explode(' ', $text);
            $lines = [$words[0]];
            $currentLine = 0;

            $numberOfWords = count($words);
            for ($i = 1; $i < $numberOfWords; $i++) {
                $lineSize = imagettfbbox($size, 0, $font, $lines[$currentLine] . ' ' . $words[$i]);
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

            switch ($textAlignment) {
                case TextAlignment::RIGHT:
                    $curX = $point[0] - abs($bbox[2] - $bbox[0]);
                    break;

                case TextAlignment::CENTER:
                    $curX = $point[0] - (abs($bbox[2] - $bbox[0]) / 2);
                    break;

                case TextAlignment::LEFT:
                    // Don't change anything
            }

            imagettftext($this->image, $size, $angle, $curX, $curY, $color, $font, $text);

            $curY += ($size * 1.35);
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function crop(int $fromX, int $fromY, int $toX, int $toY): static
    {
        $newWidth = $toX - $fromX;
        $newHeight = $toY - $fromY;

        //Create the image
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        $this->retainTransparency($newImage);
        imagecopy($newImage, $this->image, 0, 0, $fromX, $fromY, $newWidth, $newHeight);
        $this->image = $newImage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function save(?string $filename = null, int $quality = 90): void
    {
        if (is_null($filename)) {
            $filename = $this->fileName;
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        ImageFactory::instanceFromExtension($extension)->save($this->image, $filename, ['quality' => $quality]);

        $this->setImage($this->image, $filename);
    }

    /**
     * @inheritDoc
     */
    public function show(): void
    {
        if (ob_get_level()) {
            ob_clean();
        }
        $info = getimagesize($this->fileName);
        header("Content-type: " . $info['mime']);
        ImageFactory::instanceFromMime($info['mime'])->output($this->image);
    }



    /**
     * @inheritDoc
     */
    public function makeTransparent(Color $color = null, $image = null): static
    {
        if (empty($color)) {
            $color = new Color(255, 255, 255);
        }

        $customImage = true;
        if (empty($image) && !is_resource($image) && !($image instanceof GdImage)) {
            $image = $this->image;
            $customImage = false;
        }

        // Get image dimensions
        $width = imagesx($image);
        $height = imagesy($image);

        // Define the black color
        $transparentColor = imagecolorallocate($image, $color->getRed(), $color->getGreen(), $color->getBlue());

        // Loop through each pixel and make black background transparent
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $colorAt = imagecolorat($image, $x, $y);

                if ($colorAt === $transparentColor) {
                    imagesetpixel($image, $x, $y, imagecolorallocatealpha($image, $color->getRed(), $color->getGreen(), $color->getBlue(), 127));
                }
            }
        }

        // Enable alpha blending and save the modified image
        imagealphablending($image, true);
        imagesavealpha($image, true);

        if ($customImage) {
            return $image;
        } else {
            $this->image = $image;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function restore(): static
    {
        $this->image = imagecreatetruecolor(imagesx($this->originalImage), imagesy($this->originalImage));
        imagecopy($this->image, $this->originalImage, 0, 0, 0, 0, imagesx($this->originalImage), imagesy($this->originalImage));
        $this->retainTransparency();

        return $this;
    }

    protected function allocateColor(Color $color, GdImage $image = null): bool|int
    {
        if (is_null($image)) {
            $image = $this->image;
        }
        if (is_null($color->getAlpha()))
        {
            return imagecolorallocate($image, $color->getRed(), $color->getGreen(), $color->getBlue());
        }
        else
        {
            return imagecolorallocatealpha($image, $color->getRed(), $color->getGreen(), $color->getBlue(), $color->getAlpha());
        }

    }

    /**
     * Destroy the image to save the memory. Do this after all operations are complete.
     */
    public function __destruct()
    {
        if (isset($this->image)) {
            imagedestroy($this->image);
            imagedestroy($this->originalImage);

            unset($this->image);
            unset($this->originalImage);
        }
    }

    /**
     * PNG ALPHA CHANNEL SUPPORT for imagecopymerge();
     * This is a function like imagecopymerge but it handle alpha channel well!!!
     * A fix to get a function like imagecopymerge WITH ALPHA SUPPORT
     *
     * Main script by aiden dot mail at freemail dot hu
     * Transformed to imagecopymerge_alpha() by rodrigo dot polo at gmail dot com
     **/


    public function imagecopymergeAlpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
    {
        if(!isset($pct)) {
            return false;
        }
        $pct /= 100;

        // Get image width and height
        $w = imagesx( $src_im );
        $h = imagesy( $src_im );

        // Turn alpha blending off
        imagealphablending( $src_im, false );

        // Find the most opaque pixel in the image (the one with the smallest alpha value)
        $minalpha = 127;

        for( $x = 0; $x < $w; $x++ ) {
            for ($y = 0; $y < $h; $y++) {
                $alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
                if ($alpha < $minalpha) {
                    $minalpha = $alpha;
                }
            }
        }

        //loop through image pixels and modify alpha for each
        for( $x = 0; $x < $w; $x++ ) {
            for( $y = 0; $y < $h; $y++ ) {
                //get current alpha value (represents the TANSPARENCY!)
                $colorxy = imagecolorat( $src_im, $x, $y );
                $alpha = ( $colorxy >> 24 ) & 0xFF;

                //calculate new alpha
                if( $minalpha !== 127 ){
                    $alpha = 127 + 127 * $pct * ( $alpha - 127 ) / ( 127 - $minalpha );
                } else {
                    $alpha += 127 * $pct;
                }

                //get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha( $src_im, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, intval($alpha) );

                //set pixel with the new color + opacity

                if( !imagesetpixel( $src_im, $x, $y, $alphacolorxy ) ){
                    return false;
                }
            }
        }

        // The image copy

        imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);

    }


}