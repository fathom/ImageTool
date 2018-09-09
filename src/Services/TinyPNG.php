<?php

namespace Fathom\Services;

use EmptyApiKeysException;

class TinyPNG extends ImageService
{
    protected $key = null;

    public function __construct(array $options)
    {
        if (!isset($options['key']) || empty($options['key'])) {
            throw new EmptyApiKeysException();
        }

        $this->key = $options['key'];
    }

    public function makeThumbnail(string $filename_destination, int $width, int $height): void
    {
        $this->checkSource();
        $this->checkImageSize($width, $height);

        \Tinify\setKey($this->key);

        $source = \Tinify\fromFile($this->source);

        $resized = $source->resize(array(
            "method" => "thumb",
            "width" => $width,
            "height" => $height
        ));

        $resized->toFile($filename_destination);
    }
}