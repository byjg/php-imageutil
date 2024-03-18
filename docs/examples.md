# Examples

## Flip an image

Mirrors the given image in the desired way.i

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->flip(Flip::Vertical)->resize(120, null)->save('wheel.jpg');
```

## Rotate

Rotates the image to any direction using the given angle.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->rotate(45);
```

## Resize

Resize the image to an new size. Size can be specified in the arguments.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->resize(640, 480);
```

## Resize Square

Resize the image into a square format and maintain the aspect ratio. The spaces left are filled with the RGB color provided.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->resizeSquare(200);
```

## Resize and maintain the AspectRatio

Resize the image but the aspect ratio is respected. The spaces left are filled with the RGB color provided.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->resizeAspectRatio(200, 150)
```

## Stamp Image

Stamp an image in the current image.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$stamp = ImageUtil::fromFile('https://www.mysite.com/logo.png');
$img->stampImage($stamp, StampPosition::BottomRight);
```

## Write Text

Writes a text on the image.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->writeText('Sample', 0, 70, 45, 'Arial');
```

## Crop Image

Crops the given image from the ($from_x,$from_y) point to the ($to_x,$to_y) point.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->crop(250,200,400,250);
```

## Make Transparent

Make the image transparent. The transparent color must be provided.

```php
<?php
$img = ImageUtil::fromFile('wheel.png');
$img->makeTransparent(new Color(255, 255, 255));
```

## Restoring the changes

```php
<?php
$img->restore();
```

## Destroy the resouce

```php
<?php
$img->destroy();
```

## Saving the Image

```php
<?php
$img->save('filename.gif')
```
