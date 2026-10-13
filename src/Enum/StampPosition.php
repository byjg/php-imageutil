<?php

namespace ByJG\ImageUtil\Enum;

enum StampPosition: int
{
    case TopRight = 1;
    case TopLeft = 2;
    case BottomRight = 3;
    case BottomLeft = 4;
    case Center = 5;
    case Top = 6;
    case Bottom = 7;
    case Left = 8;
    case Right = 9;
    case Random = 999;
}
