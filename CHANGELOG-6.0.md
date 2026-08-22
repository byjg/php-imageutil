# CHANGELOG 6.0

## Release Date
2025

## Overview
Version 6.0 is a major release that introduces PHP 8.3+ compatibility, enhanced error handling, improved type safety with Psalm static analysis, and significant internal refactoring. This release includes breaking changes that require migration from 5.x.

---

## New Features

### PHP 8.4 and 8.5 Support
- Added support for PHP 8.4
- Added support for PHP 8.5
- Updated CI/CD pipeline to test against PHP 8.3, 8.4, and 8.5

### Enhanced Static Analysis
- Integrated Psalm static analysis into the project
- Added Psalm workflow to CI/CD pipeline
- Improved type safety across all image handlers and interfaces
- Added proper type hints and nullable type declarations throughout the codebase

### New GdImageToSvgTrait
- Introduced `GdImageToSvgTrait` for converting SVG resources to GdImage
- Provides consistent SVG-to-GdImage conversion logic across all image handler implementations
- Standardized the handling of SVG resources in GD-based operations

### Improved Error Handling
- Enhanced exception handling across all image handler implementations (JPG, BMP, GIF, WEBP, PNG)
- Suppressed GD function warnings using `@` operator for cleaner error handling
- Better error reporting with more descriptive exception messages

### Extended Test Coverage
- Added `GdHandlerExtendedTest` with 272 lines of comprehensive tests
- Added `ImageFormatTest` with 306 lines of format-specific tests
- Added `ImageFactoryTest` with 120 lines of factory pattern tests
- Added `ColorTest` with 155 lines of color manipulation tests
- Significantly improved overall test coverage

### Updated Dependencies
- Updated `meyfa/php-svg` dependency to flexible range `^v0` (previously `^v0.15`)
- Updated PHPUnit to `^10.5|^11.5` (previously `^9.6`)
- Updated Psalm to `^5.9|^6.13` (previously `^5.9`)

### Enhanced Documentation
- Updated README.md with clearer examples and usage instructions
- Enhanced docs/examples.md with more detailed code samples
- Improved inline documentation throughout the codebase

---

## Bug Fixes

- Fixed GD resource handling in arithmetic operations
- Fixed legacy configuration compatibility issues
- Corrected method signatures to properly handle nullable parameters
- Fixed edge cases in image format detection and conversion
- Improved handling of transparency in various image operations

---

## Breaking Changes

| Before (5.x) | After (6.0) | Description |
|--------------|-------------|-------------|
| PHP 8.1, 8.2, 8.3 | PHP 8.3, 8.4, 8.5 | Dropped support for PHP 8.1 and 8.2. Minimum required version is now PHP 8.3. |
| `GdImage\|SVG` type hints | `mixed` type hints | Changed resource type hints from union types to `mixed` for better flexibility and SVG handling. |
| `ImageHandlerInterface::fromFile()` method | Removed from interface | The `fromFile()` method was removed from the interface (still available in implementations). |
| `ImageHandlerInterface::getResource(): GdImage\|SVG\|null` | `ImageHandlerInterface::getResource(): mixed` | Return type changed to `mixed` for better compatibility. |
| Optional parameters without `?` | Optional parameters with `?` or explicit `null` | Added nullable type hints (`?Color $color = null`) for better type safety. |
| No `getGdImage()` method | Added `getGdImage(): GdImage` | New method added to interface to explicitly get GdImage resource. |
| `ImageInterface::save($resource, $filename, $params)` | `ImageInterface::save($resource, ?$filename = null, $params)` | Filename parameter now nullable and optional. |
| `ImageInterface::load(): GdImage\|SVG` | `ImageInterface::load(): mixed` | Return type changed to `mixed`. |
| No `getHandler()` method | Added `getHandler(): ImageHandlerInterface` | New method added to ImageInterface. |
| `meyfa/php-svg: ^v0.15` | `meyfa/php-svg: ^v0` | Relaxed version constraint for php-svg dependency. |

---

## Path to Upgrade from 5.x to 6.x

### 1. Update PHP Version
**Required:** Upgrade your PHP installation to version 8.3 or higher.

```bash
# Check your current PHP version
php -v

# Ensure you're running PHP 8.3, 8.4, or 8.5
```

### 2. Update Composer Dependencies
Update your `composer.json` to require the new version:

```bash
composer require byjg/imageutil:^6.0
composer update
```

### 3. Update Type Hints in Custom Implementations
If you have custom classes implementing `ImageHandlerInterface` or `ImageInterface`:

**Before (5.x):**
```php
public function getResource(): GdImage|SVG|null
{
    return $this->resource;
}

public function resizeSquare(int $newSize, Color $color = null): static
{
    // implementation
}
```

**After (6.0):**
```php
public function getResource(): mixed
{
    return $this->resource;
}

public function resizeSquare(int $newSize, ?Color $color = null): static
{
    // implementation
}

// Add new required method
public function getGdImage(): GdImage
{
    // return GdImage resource
}
```

### 4. Update ImageInterface Implementations
If you have custom image format classes:

**Before (5.x):**
```php
class CustomImage implements ImageInterface
{
    public function load(string $filename): GdImage|SVG
    {
        // implementation
    }

    public function save(GdImage|SVG $resource, string $filename, array $params = []): void
    {
        // implementation
    }
}
```

**After (6.0):**
```php
class CustomImage implements ImageInterface
{
    use GdImageToSvgTrait; // Optional, for SVG support

    public function load(string $filename): mixed
    {
        // implementation
    }

    public function save(mixed $resource, ?string $filename = null, array $params = []): void
    {
        // implementation
    }

    public function getHandler(): ImageHandlerInterface
    {
        return new GdHandler();
    }
}
```

### 5. Remove Direct fromFile() Interface Dependencies
If your code relies on `fromFile()` being in the interface:

**Before (5.x):**
```php
// Relied on interface contract
function processImage(ImageHandlerInterface $handler, string $file) {
    $handler->fromFile($file); // This was in the interface
}
```

**After (6.0):**
```php
// Use concrete implementations or check method existence
function processImage(ImageHandlerInterface $handler, string $file) {
    if (method_exists($handler, 'fromFile')) {
        $handler->fromFile($file);
    }
    // Or use ImageUtil::fromFile() instead
}
```

### 6. Update Error Handling
The library now suppresses GD warnings and throws exceptions instead. Ensure your error handling is updated:

```php
try {
    $img = ImageUtil::fromFile('image.jpg');
    $img->resize(800, 600);
    $img->save('output.jpg');
} catch (ImageUtilException $e) {
    // Handle exceptions properly
    // GD warnings are now suppressed and converted to exceptions
    error_log("Image processing failed: " . $e->getMessage());
}
```

### 7. Test Your Application
Run your test suite to ensure compatibility:

```bash
vendor/bin/phpunit
```

Consider using Psalm for static analysis to catch type-related issues:

```bash
composer require --dev vimeo/psalm
vendor/bin/psalm --init
vendor/bin/psalm
```

### 8. Optional: Leverage New Features
- Use the new `getGdImage()` method when you specifically need a GdImage resource
- Benefit from improved error messages in exception handling
- Utilize the enhanced type safety with your IDE's autocomplete

---

## Notes

- This release maintains backward compatibility for most common use cases
- The main breaking changes affect custom implementations and PHP version requirements
- Applications using only the public API through `ImageUtil` class should require minimal changes
- Test thoroughly in a development environment before upgrading production systems

---

## Contributors
- Community contributors and maintainers
- Special thanks to all who reported issues and tested pre-release versions
