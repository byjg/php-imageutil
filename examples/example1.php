<?php

use ByJG\ImageUtil\ImageUtil;

require 'vendor/autoload.php';

$resourceImg = imagecreatetruecolor(200, 300);
$img = ImageUtil::fromResource($resourceImg);
$img->save('/tmp/resource.png');


$img = ImageUtil::fromFile('/tmp/resource.png');
$img->resize(500, 400);
$img->save('/tmp/resource2.png');


// From an URL
$img2 = ImageUtil::fromFile('https://raw.github.com/byjg/xmlnuke/master/xmlnuke-common/imgs/logo_xmlnuke.gif');
$img2->rotate(45);
$img2->save('/tmp/resource3.png');
// From an existing resource image

