<?php

namespace ByJG\ImageUtil\Enum;

/**
 * Used by ImageUtil
 */
enum Flip: int
{
    case Horizontal = 1;
    case Vertical = 2;
    case Both = 3;
}
