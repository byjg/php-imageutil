---
sidebar_key: imageutil
sidebar_category: text-content
tags: [php, image]
---

# Image Manipulation Library

Image operations in one fluent line over GD: resize, crop, rotate, stamp, write text, and convert between formats, SVG included.

[![Sponsor](https://img.shields.io/badge/Sponsor-%23ea4aaa?logo=githubsponsors&logoColor=white&labelColor=0d1117)](https://github.com/sponsors/byjg)
[![Build Status](https://github.com/byjg/php-imageutil/actions/workflows/phpunit.yml/badge.svg?branch=master)](https://github.com/byjg/php-imageutil/actions/workflows/phpunit.yml)
[![Opensource ByJG](https://img.shields.io/badge/opensource-byjg-success.svg)](https://opensource.byjg.com)
[![Install MCP Server](https://img.shields.io/badge/Install-MCP_Server-8A2BE2?logo=modelcontextprotocol&logoColor=white)](https://opensource.byjg.com/docs/ai/mcpserver-byjg-docs/)
[![GitHub source](https://img.shields.io/badge/Github-source-informational?logo=github)](https://github.com/byjg/php-imageutil/)
[![GitHub license](https://img.shields.io/github/license/byjg/php-imageutil.svg)](https://opensource.byjg.com/license/)
[![GitHub release](https://img.shields.io/github/release/byjg/php-imageutil.svg)](https://github.com/byjg/php-imageutil/releases/)

## Why

GD can do all of it, but every operation is several calls with coordinates, resources and
colors to manage, and each format has its own load and save function.

ImageUtil chains the operations on one object and picks the format from the file name:

```php
ImageUtil::fromFile('photo.png')
    ->flip(Flip::Vertical)
    ->resize(120, null)          // keep the aspect ratio
    ->save('photo.jpg');         // PNG in, JPEG out

ImageUtil::fromFile('logo.svg')->save('logo.png');   // SVG to a raster format
```

Load from a file, a URL or an existing GD resource; save as GIF, JPEG, PNG, BMP or WEBP.

## Installation

To install ImageUtil, use the following composer command:

```bash
composer require "byjg/imageutil"
```

## Supported Formats

ImageUtil supports the following image formats:

- GIF
- JPEG
- PNG
- BMP
- WEBP
- SVG (Partial support. Primarily for converting SVG to other formats, not for SVG manipulation.)

## Creating an Image

You can create an image from a file, URL, existing resource, or create an empty image:

```php
<?php
use ByJG\ImageUtil\ImageUtil;
use ByJG\ImageUtil\Color;
use ByJG\ImageUtil\Enum\FileType;

// From a file
$img = ImageUtil::fromFile('path_to_image.png');

// From a URL. The format comes from the extension in the path, so a query string is fine
$img2 = ImageUtil::fromFile('https://somesite/someimage.jpg?size=large');

// From an existing resource image
$resourceImg = imagecreatetruecolor(200, 300);
$img3 = ImageUtil::fromResource($resourceImg);

// Or an empty image
$img4 = ImageUtil::empty(200, 300, FileType::Png, new Color(255, 255, 255));
```

## SVG Files

:::caution
ImageUtil provides basic support for SVG files, primarily for converting SVG images to other GD-supported formats.
It does not support all SVG features and does not support operations like resize, flip, etc.
:::

```php
<?php
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('path_to_image.svg');
$img->save('path_to_image.png');
```

## Image Manipulation

ImageUtil provides several methods for image manipulation:

- **Flip**: Mirror the image vertically or horizontally.
- **Rotate**: Rotate the image by a specified angle.
- **Resize**: Resize the image to specified dimensions.
- **Resize Square**: Resize the image to a square format while maintaining the aspect ratio. Any extra space is filled with the provided RGB color.
- **Resize AspectRatio**: Resize the image while maintaining the aspect ratio. Any extra space is filled with the provided RGB color.
- **Stamp Image**: Stamp another image onto the current image.
- **Write Text**: Write text onto the image.
- **Crop Image**: Crop the image from a specified point to another point.
- **Make Transparent**: Make the image transparent. The transparent color must be provided.

See detailed examples [here](docs/examples.md).

## Saving and Restoring Changes

You can discard the changes and go back to the image as it was loaded, or save them:

```php
<?php
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('wheel.png');
$img->resize(640, 480);

// Discard the resize: the image is back to what was loaded
$img->restore();

$img->resize(320, 240);

// Save to file. The format comes from the extension
$img->save('filename.gif');
```

:::note
`save()` makes the saved image the new starting point: a `restore()` after it goes back to what
was saved, not to what was first loaded.
:::

## Other Functions

ImageUtil also provides methods to get the image dimensions and the image resource:

```php
<?php
use ByJG\ImageUtil\ImageUtil;

$img = ImageUtil::fromFile('wheel.png');

$width = $img->getWidth();
$height = $img->getHeight();
$resource = $img->getResource();   // the GdImage
$filename = $img->getFilename();   // the file it was loaded from or last saved to
```

Colors, text alignment, stamp options, sending the image to the browser and adding a custom
format are covered in the [examples](docs/examples.md).

## Dependencies

ImageUtil depends on the GD extension for PHP (`ext-gd`, required in
`composer.json`).

## Running Tests

```bash
vendor/bin/phpunit
```

----
[Open source ByJG](https://opensource.byjg.com)
