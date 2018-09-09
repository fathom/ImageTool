<?php

namespace Fathom\Services;

use Imagick;

class ImageMagick extends ImageService
{
    public function makeThumbnail(string $filename_destination, int $width, int $height): void
    {
        $this->checkSource();
        $this->checkImageSize($width, $height);

        $image = new Imagick();

        $image->readImage($this->source);

        $w = $image->getImageWidth();
        $h = $image->getImageHeight();

        if ($w > $h) {
            $resize_w = $w * $height / $h;
            $resize_h = $height;
        }
        else {
            $resize_w = $width;
            $resize_h = $h * $width / $w;
        }

        $image->resizeImage($resize_w, $resize_h, Imagick::FILTER_CATROM, 1);
        $image->cropImage($width, $height, ($resize_w - $width) / 2, ($resize_h - $height) / 2);

        $image->writeImage($filename_destination);
    }
}