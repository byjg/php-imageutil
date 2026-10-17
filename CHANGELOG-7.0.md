# Changelog - Version 7.0

> **Status: in development.** This document tracks changes landing on the `7.0` branch.
> Nothing here is released yet, and the contents may still change.

## Breaking Changes

### Enum cases renamed to PascalCase

Enum cases follow the ByJG standard and are written in PascalCase. The backed values did not
change, so `->value`, `from()`, `tryFrom()` and stored data are not affected; only code that
names a case (or reads `->name`) has to be updated.

| Enum | Before (6.x) | After (7.0) |
|---|---|---|
| `Flip` | `HORIZONTAL`, `VERTICAL`, `BOTH` | `Horizontal`, `Vertical`, `Both` |
| `StampPosition` | `TOP_RIGHT`, `TOP_LEFT`, `BOTTOM_RIGHT`, `BOTTOM_LEFT`, `CENTER`, `TOP`, `BOTTOM`, `LEFT`, `RIGHT`, `RANDOM` | `TopRight`, `TopLeft`, `BottomRight`, `BottomLeft`, `Center`, `Top`, `Bottom`, `Left`, `Right`, `Random` |
| `TextAlignment` | `LEFT`, `RIGHT`, `CENTER` | `Left`, `Right`, `Center` |

### `AlphaColor::getRgb()` returns a CSS opacity

`AlphaColor::getRgb()` returns a valid CSS color. It printed GD's alpha (0 solid, 127
transparent) where CSS expects an opacity from 0 to 1: `new AlphaColor(255, 0, 0, 63)` gave
`rgba(255,0,0,63.000000)` and now gives `rgba(255,0,0,0.5)`.

### `ImageHandlerInterface::fromResource()` takes the file name

`fromResource(mixed $resource, ?string $filename = null)`. A class of yours that implements
`ImageHandlerInterface` has to add the second parameter. Callers are not affected.

`ImageUtil::fromResource()` takes the same optional `$filename`, used by `save()` with no file
name, `show()` and `getFilename()`.

## Bug Fixes

- An image loaded with `ImageUtil::fromFile()` remembers the file it came from. It was given a
  made-up temporary name instead, with three consequences that are now gone:
  - `show()` threw "Failed to get image size from file" unless `save()` had been called first;
  - `save()` with no file name wrote to that temporary name instead of the loaded file. **It now
    writes over the loaded file**, which is what the method was documented to do;
  - `getFilename()` returned the temporary name.

  An image loaded from a URL still has no file behind it and has to be saved before `show()`.

- `ImageUtil::fromFile()` accepts a file whose extension is in upper case (`PHOTO.PNG`). It threw
  "The 'PNG' extension does not exist", while `save()` already accepted it.
- `ImageUtil::fromFile()` accepts a URL with a query string or a fragment
  (`https://host/logo.png?v=2`). The format is read from the path of the URL only.
- `stampImage()` with `StampPosition::Random` now stamps the image on one of the
  nine positions, picked at random. It used to throw `ImageUtilException`
  ("Invalid Stamp Position") on every call, because the random number was never
  converted to a `StampPosition`.

## Requirements

- PHP 8.3, 8.4, 8.5 and 8.6 are now supported: `"php": ">=8.3 <8.7"`.
  The previous `<8.6` upper bound excluded PHP 8.6, since `<8.6` is exclusive.

## Toolchain

- PHPUnit updated to `^12.5`.
- Psalm is installed as `psalm/phar` (`^6.16`) instead of `vimeo/psalm`.

  `vimeo/psalm` lists the PHP versions it supports and no published release includes
  8.6, so as a dev dependency it made `composer install` fail on the 8.6 build job
  before any test ran. `psalm/phar` requires only `php ^8.2` and bundles its own
  dependencies, so it installs on every PHP version in the matrix and cannot conflict
  with the project's. Psalm itself still refuses to *run* on 8.6, which is why the
  Psalm job uses 8.5. `composer psalm` runs it.

  PHPUnit 13 is deliberately **not** used. It requires PHP `>=8.4.1`, which would
  break the 8.3 floor.

## Continuous Integration

- The build matrix now includes PHP 8.6.
- The Psalm job now runs on PHP 8.5. Psalm declares
  `~8.1.31 || ~8.2.27 || ~8.3.16 || ~8.4.3 || ~8.5.0` and therefore does not run on
  PHP 8.6.

## Housekeeping

- `phpunit.xml.dist` renamed to `phpunit.xml`.
