<?php

namespace ByJG\ImageUtil;

enum FileType: string
{
    case Bitmap = "bmp";
    case Jpg = "jpg";
    case Jpeg = "jpeg";
    case Png = "png";
    case Gif = "gif";
    case Webp = "webp";
    case Svg = "svg";
}
