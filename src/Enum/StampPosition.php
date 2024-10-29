<?php

namespace ByJG\ImageUtil\Enum;

enum StampPosition: int
{
    case TOP_RIGHT = 1;
    case TOP_LEFT = 2;
    case BOTTOM_RIGHT = 3;
    case BOTTOM_LEFT = 4;
    case CENTER = 5;
    case TOP = 6;
    case BOTTOM = 7;
    case LEFT = 8;
    case RIGHT = 9;
    case RANDOM = 999;
}
