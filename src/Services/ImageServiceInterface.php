<?php

namespace Fathom\Services;

interface ImageServiceInterface
{
    public function setSource(string $filename): void;

    public function makeThumbnail(string $filename_destination, int $width, int $height): void;
}