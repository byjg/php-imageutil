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
use ByJG\ImageUtil\ImageUtil;
use GdImage;
use InvalidArgumentException;

class GDHandler implements ImageHandlerInterface
{
    protected $orgImage;

    protected $image;

    protected $fileName;

    public function getWidth()
    {
        return imagesx($this->image);
    }

    public function getHeight()
    {
        return imagesy($this->image);
    }

    public function getResource()
    {
        return $this->image;
    }

    protected function setImage($resource, $filename = null)
    {
        $this->image = $resource;
        $this->orgImage = imagecreatetruecolor($this->getWidth(), $this->getHeight());
        imagecopy($this->orgImage, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());

        if (!empty($filename)) {
            $this->fileName = $filename;
        }
    }

    public function getFilename()
    {
        return $this->fileName;
    }

    public function empty($width, $height, Color $color = null): static
    {
        $image = imagecreatetruecolor($width, $height);
        if (!empty($color)) {
            $fill = $color->allocate($image);
            imagefill($image, 0, 0, $fill);
        }

        $this->setImage($image);
        $this->fileName = null;
        return $this;
    }


    /**
     * @inheritDoc
     */
    public function fromResource($resource): static
    {
        if (is_resource($resource) || $resource instanceof GdImage) {
            $this->setImage($resource, sys_get_temp_dir() . '/img_' . uniqid() . '.png');
            $this->retainTransparency();

            return $this;
        }
        throw new ImageUtilException('Is not valid resource');
    }

    /**
     * @inheritDoc
     */
    public function fromFile($imageFile): static
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

    protected function retainTransparency($image = null)
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
    public function rotate($angle, $background = 0)
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
    public function flip($type)
    {
        if ($type !== Flip::HORIZONTAL && $type !== Flip::VERTICAL && $type !== Flip::BOTH) {
            throw new InvalidArgumentException('You need to pass the flip type');
        }

        $width = $this->getWidth();
        $height = $this->getHeight();
        $imgdest = imagecreatetruecolor($width, $height);
        $this->retainTransparency($imgdest);
        $imgsrc = $this->image;

        switch ($type) {
            //Mirroring direction
            case Flip::HORIZONTAL:
                for ($x = 0; $x < $width; $x++) {
                    imagecopy($imgdest, $imgsrc, $width - $x - 1, 0, $x, 0, 1, $height);
                }
                break;

            case Flip::VERTICAL:
                for ($y = 0; $y < $height; $y++) {
                    imagecopy($imgdest, $imgsrc, 0, $height - $y - 1, 0, $y, $width, 1);
                }
                break;

            default:
                for ($x = 0; $x < $width; $x++) {
                    imagecopy($imgdest, $imgsrc, $width - $x - 1, 0, $x, 0, 1, $height);
                }

                $rowBuffer = imagecreatetruecolor($width, 1);
                for ($y = 0; $y < ($height / 2); $y++) {
                    imagecopy($rowBuffer, $imgdest, 0, 0, 0, $height - $y - 1, $width, 1);
                    imagecopy($imgdest, $imgdest, 0, $height - $y - 1, 0, $y, $width, 1);
                    imagecopy($imgdest, $rowBuffer, 0, $y, 0, 0, $width, 1);
                }

                imagedestroy($rowBuffer);
                break;
        }

        $this->image = $imgdest;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resize($newWidth = null, $newHeight = null)
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
    public function resizeSquare($newSize, Color $color = null)
    {
        return $this->resizeAspectRatio($newSize, $newSize, $color);
    }

    /**
     * @inheritDoc
     */
    public function resizeAspectRatio($newX, $newY, Color $color = null)
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
        imagefill($newImage, 0, 0, $color->allocate($newImage));

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
    public function stampImage($srcImage, $position = StampPosition::BOTTOMRIGHT, $padding = 5, $oppacity = 100)
    {
        $dstImage = $this->image;

        if ($srcImage instanceof ImageUtil) {
            $imageUtil = $srcImage;
        } else {
            $imageUtil = new ImageUtil($srcImage);
        }

        $watermark = $imageUtil->getImage();

        $this->retainTransparency($dstImage);
        $this->retainTransparency($watermark);

        $dstWidth = imagesx($dstImage);
        $dstHeight = imagesy($dstImage);
        $srcWIdth = imagesx($watermark);
        $srcHeight = imagesy($watermark);

        if (is_array($padding)) {
            $padx = $padding[0];
            $pady = $padding[1];
        } else {
            $padx = $pady = $padding;
        }

        if ($position == StampPosition::RANDOM) {
            $position = rand(1, 9);
        }
        switch ($position) {
            case StampPosition::TOPRIGHT:
                $dstX = ($dstWidth - $srcWIdth) - $padx;
                $dstY = $pady;
                break;
            case StampPosition::TOPLEFT:
                $dstX = $padx;
                $dstY = $pady;
                break;
            case StampPosition::BOTTOMRIGHT:
                $dstX = ($dstWidth - $srcWIdth) - $padx;
                $dstY = ($dstHeight - $srcHeight) - $pady;
                break;
            case StampPosition::BOTTOMLEFT:
                $dstX = $padx;
                $dstY = ($dstHeight - $srcHeight) - $pady;
                break;
            case StampPosition::CENTER:
                $dstX = (($dstWidth / 2) - ($srcWIdth / 2));
                $dstY = (($dstHeight / 2) - ($srcHeight / 2));
                break;
            case StampPosition::TOP:
                $dstX = (($dstWidth / 2) - ($srcWIdth / 2));
                $dstY = $pady;
                break;
            case StampPosition::BOTTOM:
                $dstX = (($dstWidth / 2) - ($srcWIdth / 2));
                $dstY = ($dstHeight - $srcHeight) - $pady;
                break;
            case StampPosition::LEFT:
                $dstX = $padx;
                $dstY = (($dstHeight / 2) - ($srcHeight / 2));
                break;
            case StampPosition::RIGHT:
                $dstX = ($dstWidth - $srcWIdth) - $padx;
                $dstY = (($dstHeight / 2) - ($srcHeight / 2));
                break;
            default:
                throw new ImageUtilException('Invalid Stamp Position');
        }

        imagecopymerge($dstImage, $watermark, $dstX, $dstY, 0, 0, $srcWIdth, $srcHeight, $oppacity);
        $this->image = $dstImage;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function writeText($text, $point, $size, $angle, $font, $maxwidth = 0, $rgbAr = null, $textAlignment = 1)
    {
        if (!is_readable($font)) {
            throw new ImageUtilException('Error: The specified font not found');
        }

        if (!is_array($rgbAr)) {
            $rgbAr = [0, 0, 0];
        }

        $color = imagecolorallocate($this->image, $rgbAr[0], $rgbAr[1], $rgbAr[2]);

        // Determine the line break if required.
        if (($maxwidth > 0) && ($angle == 0)) {
            $words = explode(' ', $text);
            $lines = [$words[0]];
            $currentLine = 0;

            $numberOfWords = count($words);
            for ($i = 1; $i < $numberOfWords; $i++) {
                $lineSize = imagettfbbox($size, 0, $font, $lines[$currentLine] . ' ' . $words[$i]);
                if ($lineSize[2] - $lineSize[0] < $maxwidth) {
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
            }

            imagettftext($this->image, $size, $angle, $curX, $curY, $color, $font, $text);

            $curY += ($size * 1.35);
        }
    }

    /**
     * @inheritDoc
     */
    public function crop($fromX, $fromY, $toX, $toY)
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
    public function save($filename = null, $quality = 90)
    {
        if (is_null($filename)) {
            $filename = $this->fileName;
        } else {
            $this->fileName = $filename;
        }

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        ImageFactory::instanceFromExtension($extension)->save($this->image, $filename, ['quality' => $quality]);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function show()
    {
        if (ob_get_level()) {
            ob_clean();
        }
        header("Content-type: " . $this->info['mime']);
        ImageFactory::instanceFromMime($this->info['mime'])->output($this->image);
        return $this;
    }



    /**
     * @inheritDoc
     */
    public function makeTransparent(Color $color = null, $image = null)
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
    public function restore()
    {
        $this->image = imagecreatetruecolor(imagesx($this->orgImage), imagesy($this->orgImage));
        imagecopy($this->image, $this->orgImage, 0, 0, 0, 0, imagesx($this->orgImage), imagesy($this->orgImage));
        $this->retainTransparency();

        return $this;
    }

    /**
     * Destroy the image to save the memory. Do this after all operations are complete.
     */
    public function __destruct()
    {
        if (!is_null($this->image)) {
            imagedestroy($this->image);
            imagedestroy($this->orgImage);

            unset($this->image);
            unset($this->orgImage);
        }
    }
}