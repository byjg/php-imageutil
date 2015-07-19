<?php

namespace ByJG\ImageUtil;

use ByJG\ImageUtil\Enum\Flip;
use ByJG\ImageUtil\Enum\StampPosition;
use ByJG\ImageUtil\Enum\TextAlignment;
use ByJG\ImageUtil\Exception\ImageUtilException;
use ByJG\ImageUtil\Exception\NotFoundException;
use RuntimeException;

/**
 * A Wrapper for GD library in PHP. GD must be installed in your system for this to work.
 * Example: $img = new Image('wheel.png');
 * 			$img->flip(1)->resize(120, 0)->save('wheel.jpg');
 */
class ImageUtil
{

	private $file_name;
	private $info;
	private $width;
	private $height;
	private $image;
	private $org_image;

	/**
	 * Construct an Image Handler based on an image resource or file name
	 *
	 * @param string|resource $image_file The path or URL to image or the image resource.
	 */
	public function __construct($image_file)
	{
		if (!function_exists('imagecreatefrompng'))
		{
			throw new RuntimeException("GD module is not installed");
		}

		if (!is_string($image_file))
		{
			$info = $this->createFromResource($image_file);
		}
		else
		{
			$info = $this->createFromFilename($image_file);
		}

		if (is_null($this->image))
		{
			throw new ImageUtilException("'$image_file' is not a valid image file");
		}

		$this->info = $info;
		$this->width = imagesx($this->image);
		$this->height = imagesy($this->image);

		$this->org_image = imagecreatetruecolor($this->getWidth(), $this->getHeight());
		imagecopy($this->org_image, $this->image, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
	}

	protected function createFromResource($resource)
	{
		if (get_resource_type($resource) == 'gd')
		{
			$this->image = $resource;
			$this->file_name = sys_get_temp_dir() . '/img_' . uniqid() .  '.png';
			return array('mime' => 'image/png');
		}
		throw new ImageUtilException('Is not valid resource');
	}

	protected function createFromFilename($image_file)
	{
		$http = false;
		if (preg_match('/^(https?:|file:)/', $image_file))
		{
			$http = true;
			$url = $image_file;
			$image_file = basename($url);
			$info = pathinfo($image_file);
			$image_file = sys_get_temp_dir() . '/img_' . uniqid() .  '.' . $info['extension'];
			file_put_contents($image_file, file_get_contents($url));
		}

		if (!file_exists($image_file) || !is_readable($image_file))
		{
			throw new NotFoundException("File is not found or not is readable. Cannot continue.");
		}

		$this->file_name = $image_file;
		$img = getimagesize($image_file);
		$image = null;

		//Create the image depending on what kind of file it is.
		switch ($img['mime'])
		{
			case 'image/png':
				$image = imagecreatefrompng($image_file);
				break;
			case 'image/jpeg':
				$image = imagecreatefromjpeg($image_file);
				break;
			case 'image/gif':
				$old_id = imagecreatefromgif($image_file);
				$image = imagecreatetruecolor($img[0], $img[1]);
				imagecopy($image, $old_id, 0, 0, 0, 0, $img[0], $img[1]);
				break;
			default:
				break;
		}

		if ($http)
		{
			unlink($image_file);
		}

		$this->image = $image;

		return $img;
	}

	public function getWidth()
	{
		return imagesx($this->image);
	}

	public function getHeight()
	{
		return imagesy($this->image);
	}

	public function getFilename()
	{
		return $this->file_name;
	}

	/**
	 * Enter description here...
	 *
	 * @return image
	 */
	public function getImage()
	{
		return $this->image;
	}

	/**
	 * Rotates the image to any direction using the given angle.
	 * Arguments: $angle - The rotation angle, in degrees.
	 * Example: $img = new Image("file.png"); $img->rotate(180); $img->show(); // Turn the image upside down.
	 */
	public function rotate($angle, $background = 0)
	{
		if (!is_numeric($angle))
		{
			throw new \InvalidArgumentException('You need to pass the angle');
		}

		$this->image = imagerotate($this->image, $angle, $background);
		return $this;
	}

	/**
	 * Mirrors the given image in the desired way.
	 *
	 * Example: $img = new Image("file.png"); $img->flip(2); $img->show();
	 *
	 * @param int $type Direction of mirroring. This can be 1(Horizondal Flip), 2(Vertical Flip) or 3(Both Horizondal and Vertical Flip)
	 * @return ImageUtil
	 */
	public function flip($type)
	{
		if ($type !== Flip::Horizontal && $type !== Flip::Vertical && $type !== Flip::Both)
		{
			throw new \InvalidArgumentException('You need to pass the flip type');;
		}

		$width = $this->getWidth();
		$height = $this->getHeight();
		$imgdest = imagecreatetruecolor($width, $height);
		$imgsrc = $this->image;

		switch ($type)
		{
			//Mirroring direction
			case Flip::Horizontal:
				for ($x = 0; $x < $width; $x ++)
				{
					imagecopy($imgdest, $imgsrc, $width - $x - 1, 0, $x, 0, 1, $height);
				}
				break;

			case Flip::Vertical:
				for ($y = 0; $y < $height; $y ++)
				{
					imagecopy($imgdest, $imgsrc, 0, $height - $y - 1, 0, $y, $width, 1);
				}
				break;

			default:
				for ($x = 0; $x < $width; $x ++)
				{
					imagecopy($imgdest, $imgsrc, $width - $x - 1, 0, $x, 0, 1, $height);
				}

				$rowBuffer = imagecreatetruecolor($width, 1);
				for ($y = 0; $y < ($height / 2); $y ++)
				{
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
	 * Resize the image to an new size. Size can be specified in the arugments.
	 * @param int $new_width The width of the desired image. If 0, the function will automatically calculate the width using the height ratio.
	 * @param int $new_height The width of the desired image. If 0, the function will automatically calculate the value using the width ratio.
	 * @return ImageUtil
	 */
	public function resize($new_width = null, $new_height = null)
	{
		if (!is_numeric($new_height) && !is_numeric($new_width))
		{
			throw new \InvalidArgumentException('There are no valid values');
		}

		$height = $this->getHeight();
		$width = $this->getWidth();

		//If the width or height is give as 0, find the correct ratio using the other value
		if (!$new_height && $new_width)
		{
			$new_height = $height * $new_width / $width; //Get the new height in the correct ratio
		}

		if ($new_height && !$new_width)
		{
			$new_width = $width * $new_height / $height; //Get the new width in the correct ratio
		}


		//Create the image
		$new_image = imagecreatetruecolor($new_width, $new_height);
		imagealphablending($new_image, false);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

		$this->image = $new_image;

		return $this;
	}

	/**
	 * Resize the image in a square format and maintain the aspect ratio. The space are filled the RGB color provided.
	 *
	 * @param int $new_size The new size of desired image (width and height are equals)
	 * @param int $fillRed
	 * @param int $fillGreen
	 * @param int $fillBlue
	 * @return ImageUtil
	 */
	public function resizeSquare($new_size, $fillRed = 255, $fillGreen = 255, $fillBlue = 255)
	{
		return $this->resizeAspectRatio($new_size, $new_size, $fillRed, $fillGreen, $fillBlue);
	}

	/**
	 * Resize the image but the aspect ratio is respected. The spaces left are filled with the RGB color provided.
	 * @param int $newX
	 * @param int $newY
	 * @param int $fillRed
	 * @param int $fillGreen
	 * @param int $fillBlue
	 * @return ImageUtil
	 */
	public function resizeAspectRatio($newX, $newY, $fillRed = 255, $fillGreen = 255, $fillBlue = 255)
	{
		if (!is_numeric($newX) || !is_numeric($newY))
		{
			throw new ImageUtilException('There are no valid values');
		}

		$im = $this->image;

		if (imagesy($im) >= $newY || imagesx($im) >= $newX)
		{
			if (imagesx($im) >= imagesy($im))
			{
				$x = $newX;
				$y = ($x * imagesy($im)) / imagesx($im);
				$yyy = - ($y - $newY) / 2;
				$xxx = 0;
			}
			else
			{
				$y = $newY;
				$x = ($y * imagesx($im)) / imagesy($im);
				$xxx = - ($x - $newX) / 2;
				$yyy = 0;
			}
		}
		else
		{
			$x = imagesx($im);
			$y = imagesy($im);
			$yyy = 0;
			$xxx = 0;
		}

		$imw = imagecreatetruecolor($newX, $newY);
		$color = imagecolorallocate($imw, $fillRed, $fillGreen, $fillBlue);
		imagefill($imw, 0, 0, $color);
		imagealphablending($imw, false);

		imagecopyresampled($imw, $im, $xxx, $yyy, 0, 0, $x, $y, imagesx($im), imagesy($im));

		$this->image = $imw;

		return $this;
	}

	/**
	 * Stamp an image in the current image.
	 *
	 * @param ImageUtil|string $src_image The image path or the image gd resource.
	 * @param int $position
	 * @param int $padding
	 * @param int $oppacity
	 * @return ImageUtil
	 */
	public function stampImage($src_image, $position = StampPosition::BottomRight, $padding = 5, $oppacity = 100)
	{
		$dst_image = $this->image;

		if ($src_image instanceof ImageUtil)
		{
			$imageUtil = $src_image;
		}
		else
		{
			$imageUtil = new ImageUtil($src_image);
		}

		$watermark = $imageUtil->getImage();

		imagealphablending($dst_image, true);
		imagealphablending($watermark, true);

		$dst_w = imagesx($dst_image);
		$dst_h = imagesy($dst_image);
		$src_w = imagesx($watermark);
		$src_h = imagesy($watermark);

		if (is_array($padding))
		{
			$padx = $padding[0];
			$pady = $padding[1];
		}
		else
		{
			$padx = $pady = $padding;
		}

		if ($position == StampPosition::Random)
		{
			$position = rand(1, 9);
		}
		switch ($position)
		{
			case StampPosition::TopRight:
				imagecopymerge($dst_image, $watermark, ($dst_w - $src_w) - $padx, $pady, 0, 0, $src_w, $src_h, $oppacity);
				break;
			case StampPosition::TopLeft:
				imagecopymerge($dst_image, $watermark, $padx, $pady, 0, 0, $src_w, $src_h, $oppacity);
				break;
			case StampPosition::BottomRight:
				imagecopymerge($dst_image, $watermark, ($dst_w - $src_w) - $padx, ($dst_h - $src_h) - $pady, 0, 0, $src_w, $src_h, $oppacity);
				break;
			case StampPosition::BottomLeft:
				imagecopymerge($dst_image, $watermark, $padx, ($dst_h - $src_h) - $pady, 0, 0, $src_w, $src_h, $oppacity);
				break;
			case StampPosition::Center:
				imagecopymerge($dst_image, $watermark, (($dst_w / 2) - ($src_w / 2)), (($dst_h / 2) - ($src_h / 2)), 0, 0, $src_w, $src_h, $oppacity);
				break;
			case StampPosition::Top:
				imagecopymerge($dst_image, $watermark, (($dst_w / 2) - ($src_w / 2)), $pady, 0, 0, $src_w, $src_h, $oppacity);
				break;
			case StampPosition::Bottom:
				imagecopymerge($dst_image, $watermark, (($dst_w / 2) - ($src_w / 2)), ($dst_h - $src_h) - $pady, 0, 0, $src_w, $src_h, $oppacity);
				break;
			case StampPosition::Left:
				imagecopymerge($dst_image, $watermark, $padx, (($dst_h / 2) - ($src_h / 2)), 0, 0, $src_w, $src_h, $oppacity);
				break;
			case StampPosition::Right:
				imagecopymerge($dst_image, $watermark, ($dst_w - $src_w) - $padx, (($dst_h / 2) - ($src_h / 2)), 0, 0, $src_w, $src_h, $oppacity);
				break;
		}

		$this->image = $dst_image;

		return $this;
	}

	/**
	 * Writes a text on the image.
	 *
	 * @param type $text
	 * @param type $point
	 * @param type $size
	 * @param type $angle
	 * @param type $font
	 * @param type $maxwidth
	 * @param int $rgbAr
	 * @param type $textAlignment
	 * @throws ImageUtilException
	 */
	public function writeText($text, $point, $size, $angle, $font, $maxwidth = 0, $rgbAr = null, $textAlignment = 1)
	{
		if (!is_readable($font))
		{
			throw new ImageUtilException('Error: The specified font not found');
		}

		if (!is_array($rgbAr))
		{
			$rgbAr = array(0, 0, 0);
		}

		$color = imagecolorallocate($this->image, $rgbAr[0], $rgbAr[1], $rgbAr[2]);

		// Determine the line break if required.
		if (($maxwidth > 0) && ($angle == 0))
		{
			$words = explode(' ', $text);
			$lines = array($words[0]);
			$currentLine = 0;

			$numberOfWords = count($words);
			for ($i = 1; $i < $numberOfWords; $i++)
			{
				$lineSize = imagettfbbox($size, 0, $font, $lines[$currentLine] . ' ' . $words[$i]);
				if ($lineSize[2] - $lineSize[0] < $maxwidth)
				{
					$lines[$currentLine] .= ' ' . $words[$i];
				}
				else
				{
					$currentLine++;
					$lines[$currentLine] = $words[$i];
				}
			}
		}
		else
		{
			$lines = array($text);
		}

		$x = $point[0];
		$y = $point[1];

		foreach ($lines as $text)
		{
			$bbox = imagettfbbox($size, $angle, $font, $text);

			switch ($textAlignment)
			{
				case TextAlignment::Right:
					$x = $point[0] - abs($bbox[2] - $bbox[0]);
					break;

				case TextAlignment::Center:
					$x = $point[0] - (abs($bbox[2] - $bbox[0]) / 2);
					break;
			}

			imagettftext($this->image, $size, $angle, $x, $y, $color, $font, $text);

			$y += ($size * 1.35);
		}
	}

	/**
	 * Crops the given image from the ($from_x,$from_y) point to the ($to_x,$to_y) point.
	 *
	 * Example: $img -> crop(250,200,400,250);
	 *
	 * @param type $from_x X coordinate from where the crop should start
	 * @param type $from_y Y coordinate from where the crop should start
	 * @param type $to_x X coordinate from where the crop should end
	 * @param type $to_y Y coordinate from where the crop should end
	 * @return ImageUtil
	 */
	public function crop($from_x, $from_y, $to_x, $to_y)
	{
		$new_width = $to_x - $from_x;
		$new_height = $to_y - $from_y;
		//Create the image
		$new_image = imagecreatetruecolor($new_width, $new_height);
		imagealphablending($new_image, false);
		imagecopy($new_image, $this->image, 0, 0, $from_x, $from_y, $new_width, $new_height);
		$this->image = $new_image;

		return $this;
	}

	/**
	 * Save the image to the given file. You can use this function to convert image types to. Just specify the image format you want as the extension.
	 * Argument:$file_name - the file name to which the image should be saved to
	 * Returns: false if save operation fails.
	 * Example: $img->save("image.png");
	 * 			$image->save('file.jpg');
	 * @return ImageUtil The object if not destroyed
	 */
	public function save($file_name = null)
	{
		if (is_null($file_name))
		{
			$file_name = $this->file_name;
		}
		
		$extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

		switch ($extension)
		{
			case 'png':
				return imagepng($this->image, $file_name);

			case 'jpeg':
			case 'jpg':
				return imagejpeg($this->image, $file_name);

			case 'gif':
				return imagegif($this->image, $file_name);

			default:
				break;
		}
		
		return $this;
	}

	/**
	 * Display the image.
	 * Example: $img->show();
	 */
	public function show()
	{
		if (ob_get_level())
		{
			ob_clean();
		}
		header("Content-type: " . $this->info['mime']);
		switch ($this->info['mime'])
		{
			case 'image/png':
				imagepng($this->image);
				break;
			case 'image/jpeg':
				imagejpeg($this->image);
				break;
			case 'image/gif':
				imagegif($this->image);
				break;
			default:
				break;
		}

		return $this;
	}

	/**
	 * Discard any changes made to the image and restore the original state
	 */
	public function restore()
	{
		$this->image = imagecreatetruecolor(imagesx($this->org_image), imagesy($this->org_image));
		imagecopy($this->image, $this->org_image, 0, 0, 0, 0, imagesx($this->org_image), imagesy($this->org_image));

		return $this;
	}

	/**
	 * Make transparent the image. The transparent color must be provided
	 * @param int $transpRed
	 * @param int $transpGreen
	 * @param int $transpBlue
	 * @return ImageUtil The image util object
	 */
	public function makeTransparent($transpRed=255, $transpGreen=255, $transpBlue=255)
	{
		$x = imagesx($this->image);
		$y = imagesy($this->image);

		$imw = imagecreatetruecolor($x, $y);

		$color = imagecolorallocate($imw, $transpRed, $transpGreen, $transpBlue);
		imagefill($imw, 0, 0, $color);
		imagecolortransparent($imw, $color);

		imagecopyresampled($imw, $this->image, 0, 0, 0, 0, $x, $y, $x, $y);

		$this->image = $imw;

		return $this;
	}

	/**
	 * Destroy the image to save the memory. Do this after all operations are complete.
	 */
	public function __destruct()
	{
		if (!is_null($this->image))
		{
			imagedestroy($this->image);
			imagedestroy($this->org_image);

			unset($this->image);
			unset($this->org_image);
		}
	}

}
