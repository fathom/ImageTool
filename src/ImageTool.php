<?php

namespace Fathom;

use Fathom\Services\ImageServiceInterface;
use Fathom\Services\ImageMagick;
use Fathom\Services\Immaga;
use Fathom\Services\TinyPNG;
use Fathom\Exeptions\WrongServiceException;

class ImageTool
{
    const SERVICE_IMAGE_MAGICK = 1;
    const SERVICE_TINY_PNG = 2;
    const SERVICE_IMMAGA = 3;

    private $service;

    public function __construct(int $service, array $options = [])
    {
        $this->service = $this->getService($service, $options);
    }

    private function getService(int $service, array $options): ImageServiceInterface
    {
        switch ($service)
        {
            case static::SERVICE_IMAGE_MAGICK:
                return new ImageMagick();
            case static::SERVICE_TINY_PNG:
                return new TinyPNG($options);
            case static::SERVICE_IMMAGA:
                return new Immaga($options);
            default:
                throw new WrongServiceException();
        }
    }

    public function setSource(string $filename): void
    {
        $this->service->setSource($filename);
    }

    public function makeThumb(string $filename, int $width, int $height): void
    {
        $this->service->makeThumbnail($filename, $width, $height);
    }
}