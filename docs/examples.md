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
