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

        $source_width = $image->getImageWidth();
        $source_height = $image->getImageHeight();

        $image_ration = $width / $height;
        $source_image_ration = $source_width / $source_height;

        if ($image_ration > $source_image_ration)
        {
            $h = round(($width / $source_width) * $source_height);
            $w = $width;

            $x = 0;
            $y = ($h - $height) / 2;
        }
        else
        {
            $w = round(($height / $source_height) * $source_width);
            $h = $height;

            $x = ($w - $width) / 2;
            $y = 0;
        }

        $image->resizeImage($w, $h, Imagick::FILTER_CATROM, 1);
        $image->cropImage($width, $height, $x, $y);

        $image->writeImage($filename_destination);
    }
}