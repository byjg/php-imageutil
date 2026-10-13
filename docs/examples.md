---
sidebar_position: 1
---

# Examples

## Flip an Image

This example mirrors the given image in the desired way.

```php
<?php
use ByJG\ImageUtil\ImageUtil;
use ByJG\ImageUtil\Enum\Flip;

$img = ImageUtil::fromFile('wheel.png');
$img->flip(Flip::Vertical)->resize(120, null)->save('wheel.jpg');
```

## Rotate

This example rotates the image in any direction using the given angle.

```php
<?php
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('wheel.png');
$img->rotate(45);
$img->save('wheel_rotated.png');
```

## Resize

This example resizes the image to a new size. The size can be specified in the arguments.

```php
<?php
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('wheel.png');
$img->resize(640, 480);
$img->save('wheel_resized.png');
```

## Resize Square

This example resizes the image into a square format while maintaining the aspect ratio. Any remaining space is filled with the provided RGB color.

```php
<?php
use ByJG\ImageUtil\ImageUtil;
use ByJG\ImageUtil\Color;

$img = ImageUtil::fromFile('wheel.png');
$img->resizeSquare(200, new Color(255, 255, 255));
$img->save('wheel_square.png');
```

## Resize and Maintain the Aspect Ratio

This example resizes the image while respecting the aspect ratio. Any remaining space is filled with the provided RGB color.

```php
<?php
use ByJG\ImageUtil\ImageUtil;
use ByJG\ImageUtil\Color;

$img = ImageUtil::fromFile('wheel.png');
$img->resizeAspectRatio(200, 150, new Color(0, 255, 0));
$img->save('wheel_aspect.png');
```

## Stamp Image

This example stamps an image onto the current image.

```php
<?php
use ByJG\ImageUtil\ImageUtil;
use ByJG\ImageUtil\Enum\StampPosition;

$img = ImageUtil::fromFile('wheel.png');
$stamp = ImageUtil::fromFile('https://www.mysite.com/logo.png');
$img->stampImage($stamp, StampPosition::BottomRight);
$img->save('wheel_stamped.png');
```

The position is one of `TopLeft`, `Top`, `TopRight`, `Left`, `Center`, `Right`, `BottomLeft`,
`Bottom`, `BottomRight` (the default) or `Random`, which picks one of the nine on each call.

Three more arguments set the distance from the edges, in pixels, and the opacity of the stamp,
from 0 (invisible) to 100 (solid, the default):

```php
<?php
// 20 pixels from the right edge, 10 from the bottom, half transparent
$img->stampImage($stamp, StampPosition::BottomRight, 20, 10, 50);
```

## Write Text

This example writes text onto the image.

```php
<?php
use ByJG\ImageUtil\ImageUtil;
use ByJG\ImageUtil\Color;

$img = ImageUtil::fromFile('wheel.png');
$img->writeText('Sample', [0, 70], 12, 45, './arial.ttf', 0, new Color(255, 0, 0));
$img->save('wheel_text.png');
```

The arguments are the text, the `[x, y]` point, the font size, the angle in degrees, the path of a
TrueType font, the maximum width, the color (black when omitted) and the alignment.

- **Maximum width**: when it is greater than zero and the angle is `0`, the text is broken into
  lines no wider than that many pixels. `0` writes a single line.
- **Alignment**: `TextAlignment::Left` (the default) starts the text at the point,
  `TextAlignment::Right` ends it there and `TextAlignment::Center` centers it on the point.

```php
<?php
use ByJG\ImageUtil\Enum\TextAlignment;

// Centered on x = 320, wrapped at 400 pixels
$img->writeText('A longer text that will not fit in one line', [320, 70], 20, 0, './arial.ttf', 400, new Color(0, 0, 0), TextAlignment::Center);
```

## Crop Image

This example crops the given image from the ($from_x,$from_y) point to the ($to_x,$to_y) point.

```php
<?php
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('wheel.png');
$img->crop(250, 200, 400, 250);
$img->save('wheel_cropped.png');
```

## Make Transparent

This example makes the image transparent. The transparent color must be provided.

```php
<?php
use ByJG\ImageUtil\ImageUtil;
use ByJG\ImageUtil\Color;

$img = ImageUtil::fromFile('wheel.png');
$img->makeTransparent(new Color(255, 255, 255));
$img->save('wheel_transparent.png');
```

Without a color, black is made transparent. A second argument is the tolerance: how far each of the
red, green and blue values of a pixel may be from the color and still be made transparent.

```php
<?php
// Also catches the off-white pixels around the edges
$img->makeTransparent(new Color(255, 255, 255), 10);
```

## Restore Changes

This example restores the image to its original state, discarding all changes made since it was loaded.

```php
<?php
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('wheel.png');
$img->resize(100, 100);
$img->restore();  // Image is back to original dimensions
$img->save('wheel_original.png');
```

## Save the Image

This example saves the image. You can save to different formats by changing the file extension.

```php
<?php
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('wheel.png');
$img->resize(640, 480);

// Save as GIF
$img->save('output.gif');

// Save as JPEG with quality
$img->save('output.jpg', 90);

// Save as PNG
$img->save('output.png');

// Save as WEBP
$img->save('output.webp');
```

- The second argument goes from 0 to 100 and defaults to 90. For JPEG and WEBP it is the image
  quality; for PNG, which loses nothing, it sets how hard the file is compressed; GIF and BMP
  ignore it.
- `save()` with no file name writes over the file the image was loaded from (or last saved to).
- After `save()`, [`restore()`](#restore-changes) goes back to the saved image.

## Display the Image

`show()` sends the image to the browser with the right `Content-Type` header, in the format of the
file it was loaded from or last saved to.

```php
<?php
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('wheel.png');
$img->resize(320, null);
$img->show();
```

An image that has no file behind it — loaded from a URL, created with `ImageUtil::empty()` or
built with `ImageUtil::fromResource()` — has to be saved first.

## Colors

A `Color` is built from its red, green and blue values (0 to 255) or from a hexadecimal string:

```php
<?php
use ByJG\ImageUtil\Color;

$red = new Color(255, 0, 0);
$orange = Color::fromHex('#FF8040');
$white = Color::fromHex('#FFF');

echo $orange->getHex(); // #ff8040
echo $orange->getRgb(); // rgb(255,128,64)
```

`AlphaColor` adds transparency, in the scale GD uses: `0` is solid and `127`, the default, is fully
transparent. Use it where a fill color is expected. `resizeSquare()` and `resizeAspectRatio()` fill
with transparent white when no color is given.

```php
<?php
use ByJG\ImageUtil\AlphaColor;
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('wheel.png');
$img->resizeSquare(200, new AlphaColor(255, 0, 0, 63)); // half-transparent red around the image
$img->save('wheel_square.png');

echo (new AlphaColor(255, 0, 0, 63))->getRgb(); // rgba(255,0,0,0.5)
```

## Adding a Format

Each format is a class that implements `ByJG\ImageUtil\Image\ImageInterface`: it says which
extensions and MIME types it handles and how to load, save and output the image. Register yours
and `fromFile()`, `save()` and `show()` will use it for those extensions:

```php
<?php
use ByJG\ImageUtil\Image\ImageFactory;
use ByJG\ImageUtil\Image\PngImage;
use ByJG\ImageUtil\ImageUtil;

// The same as PNG, under another extension
class ScreenshotImage extends PngImage
{
    public static function extension(): string|array
    {
        return 'shot';
    }

    public static function mimeType(): string|array
    {
        return 'image/x-screenshot';
    }
}

ImageFactory::registerAll();                             // the built-in formats first
ImageFactory::registerHandler(ScreenshotImage::class);

$img = ImageUtil::fromFile('wheel.png');
$img->save('wheel.shot');
```
