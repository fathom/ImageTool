<?php

namespace Fathom\Services;

use Fathom\Exeptions\FileNotExistException;
use Fathom\Exeptions\SourceNotSetException;
use Fathom\Exeptions\WrongImageSizeException;

abstract class ImageService implements ImageServiceInterface
{
    protected $source = null;
    protected $image_info = null;

    public function setSource(string $filename): void
    {
        if (!file_exists($filename)) {
            throw new FileNotExistException();
        }

        $this->source = $filename;
    }

    protected function checkImageSize(int $width, int $height): void
    {
        $this->image_info = getimagesize($this->source);

        if ($this->image_info[0] < $width || $this->image_info[1] < $height) {
            throw new WrongImageSizeException();
        }
    }

    protected function checkSource(): void
    {
        if (is_null($this->source)) {
            throw new SourceNotSetException();
        }
    }
}