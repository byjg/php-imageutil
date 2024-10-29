<?php

namespace ByJG\ImageUtil;

class Color
{
    protected int $red;
    protected int $green;
    protected int $blue;

    public function __construct(int $red, int $green, int $blue)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    public static function fromHex(string $hex): static
    {
        $hex = str_replace("#", "", $hex);
        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $red = hexdec(substr($hex, 0, 2));
        $green = hexdec(substr($hex, 2, 2));
        $blue = hexdec(substr($hex, 4, 2));
        return new static($red, $green, $blue);
    }

    public function getRed(): int
    {
        return $this->red;
    }

    public function getGreen(): int
    {
        return $this->green;
    }

    public function getBlue(): int
    {
        return $this->blue;
    }

    public function getAlpha(): ?int
    {
        return null;
    }

    public function getHex(): string
    {
        return sprintf("#%02x%02x%02x", $this->red, $this->green, $this->blue);
    }

    public function getRgb(): string
    {
        return sprintf("rgb(%d,%d,%d)", $this->red, $this->green, $this->blue);
    }
}
