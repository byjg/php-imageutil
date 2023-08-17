<?php

namespace ByJG\ImageUtil;

class Color
{
    protected $red;
    protected $green;
    protected $blue;

    public function __construct($red, $green, $blue)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    public function getRed()
    {
        return $this->red;
    }

    public function getGreen()
    {
        return $this->green;
    }

    public function getBlue()
    {
        return $this->blue;
    }

    public function allocate($image)
    {
        return imagecolorallocate($image, $this->red, $this->green, $this->blue);
    }

    public function getHex()
    {
        return sprintf("#%02x%02x%02x", $this->red, $this->green, $this->blue);
    }

    public function getRgb()
    {
        return sprintf("rgb(%d,%d,%d)", $this->red, $this->green, $this->blue);
    }
}
