# Examples

## Flip an Image

This example mirrors the given image in the desired way.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->flip(Flip::Vertical)->resize(120, null)->save('wheel.jpg');
```

## Rotate

This example rotates the image in any direction using the given angle.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->rotate(45);
```

## Resize

This example resizes the image to a new size. The size can be specified in the arguments.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->resize(640, 480);
```

## Resize Square

This example resizes the image into a square format while maintaining the aspect ratio. Any remaining space is filled with the provided RGB color.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->resizeSquare(200);
```

## Resize and Maintain the Aspect Ratio

This example resizes the image while respecting the aspect ratio. Any remaining space is filled with the provided RGB color.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->resizeAspectRatio(200, 150, new Color(0, 255, 0));
```

## Stamp Image

This example stamps an image onto the current image.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$stamp = ImageUtil::fromFile('https://www.mysite.com/logo.png');
$img->stampImage($stamp, StampPosition::BottomRight);
```

## Write Text

This example writes text onto the image.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->writeText('Sample', 0, 70, 45, './arial.ttf', new Color(255, 0, 0));
```

## Crop Image

This example crops the given image from the ($from_x,$from_y) point to the ($to_x,$to_y) point.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->crop(250,200,400,250);
```

## Make Transparent

This example makes the image transparent. The transparent color must be provided.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->makeTransparent(new Color(255, 255, 255));
```

## Restore Changes

This example restores the changes made to the image.

```php
<?php
$img->restore();
```

## Destroy the Resource

This example destroys the image resource.

```php
<?php
$img->destroy();
```

## Save the Image

This example saves the image.

```php
<?php
$img->save('filename.gif');
```