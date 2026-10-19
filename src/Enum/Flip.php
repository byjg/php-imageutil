<?php

namespace ByJG\ImageUtil\Enum;

/**
 * Direction used by ImageHandlerInterface::flip()
 */
enum Flip: int
{
    case Horizontal = 1;
    case Vertical = 2;
    case Both = 3;
}
