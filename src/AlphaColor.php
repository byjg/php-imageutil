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

    /**
     * The color as a CSS rgba() value. GD's alpha goes from 0 (opaque) to 127 (transparent), CSS
     * the other way round, from 1 (opaque) to 0 (transparent).
     */
    #[\Override]
    public function getRgb(): string
    {
        return sprintf("rgba(%d,%d,%d,%s)", $this->red, $this->green, $this->blue, round(1.0 - (float)$this->alpha / 127.0, 2));
    }
}
