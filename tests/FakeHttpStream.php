<?php

namespace Tests;

/**
 * Stands in for PHP's "http" stream wrapper, so loading an image from a URL can be tested
 * without a web server: whatever URL is opened, it serves the file set in $file.
 */
class FakeHttpStream
{
    public static string $file = '';

    /** @var resource|null */
    public $context;

    /** @var resource */
    private $handle;

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        $handle = fopen(self::$file, 'rb');
        if ($handle === false) {
            return false;
        }
        $this->handle = $handle;
        return true;
    }

    public function stream_read(int $count): string|false
    {
        return fread($this->handle, $count);
    }

    public function stream_eof(): bool
    {
        return feof($this->handle);
    }

    public function stream_stat(): array|false
    {
        return fstat($this->handle);
    }

    public function stream_close(): void
    {
        fclose($this->handle);
    }
}
