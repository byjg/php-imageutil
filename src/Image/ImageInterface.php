<?php

namespace ByJG\ImageUtil\Image;

use ByJG\ImageUtil\Handler\ImageHandlerInterface;

interface ImageInterface
{
    /**
     * @return string|array
     */
    public static function mimeType(): string|array;

    /**
     * @return string|array
     */
    public static function extension(): string|array;

    /**
     * @param string $filename
     * @return mixed
     */
    public function load(string $filename): mixed;

    /**
     * @param mixed $resource
     * @param string|null $filename
     * @param array $params
     * @return void
     */
    public function save(mixed $resource, ?string $filename = null, array $params = []): void;

    /**
     * @param mixed $resource
     * @return void
     */
    public function output(mixed $resource): void;

    public function getHandler(): ImageHandlerInterface;

}