<?php

namespace ByJG\ImageUtil;

class AlphaColor extends Color
{
    protected int $alpha;

    public function __construct(int $red, int $green, int $blue, int $alpha = 127)
    {
        $this->alpha = $alpha;
        parent::__construct($red, $green, $blue);
    }

    #[\Override]
    public function getAlpha(): ?int
    {
        return $this->alpha;
    }

    #[\Override]
    public function getRgb(): string
    {
        return sprintf("rgba(%d,%d,%d,%f)", $this->red, $this->green, $this->blue, $this->alpha);
    }
}
