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
            $resize_height = round(($width / $source_width) * $source_height);
            $resize_width = $width;

            $x = 0;
            $y = ($resize_height - $height) / 2;
        }
        else
        {
            $resize_width = round(($height / $source_height) * $source_width);
            $resize_height = $height;

            $x = ($resize_width - $width) / 2;
            $y = 0;
        }

        $image->resizeImage($resize_width, $resize_height, Imagick::FILTER_CATROM, 1);
        $image->cropImage($width, $height, $x, $y);

        $image->writeImage($filename_destination);
    }
}