<?php

namespace Fathom\Services;

use GuzzleHttp\Client;
use Imagick;
use stdClass;

class Immaga extends ImageService
{
    protected $key = null;
    protected $secret = null;

    protected $api_url = 'http://api.imagga.com/v1/';

    public function __construct(array $options)
    {
        if (!isset($options['key']) || empty($options['key'])) {
            throw new \InvalidArgumentException();
        }

        if (!isset($options['secret']) || empty($options['secret'])) {
            throw new \InvalidArgumentException();
        }

        $this->key = $options['key'];
        $this->secret = $options['secret'];
    }

    public function makeThumbnail(string $filename_destination, int $width, int $height): void
    {
        $this->checkSource();
        $this->checkImageSize($width, $height);

        $content_id = $this->uploadFile();
        $cropping_data = $this->getCroppingData($content_id, $width, $height);
        $this->cropImage($filename_destination, $cropping_data);
    }

    protected function uploadFile(): string
    {
        $client = new Client([
            'base_uri' => $this->api_url,
        ]);

        $response = $client->request('POST', 'content', [
            'auth' => [
                $this->key,
                $this->secret,
            ],
            'timeout' => 60,
            'connect_timeout' => 5,
            'multipart' => [
                [
                    'name'     => basename($this->source),
                    'contents' => fopen($this->source, 'r')
                ],
            ],
        ]);

        $data_response = json_decode($response->getBody());
        return reset($data_response->uploaded)->id;
    }

    protected function getCroppingData(string $content_id, int $width, int $height): stdClass
    {
        $client = new Client([
            'base_uri' => $this->api_url,
        ]);

        $response = $client->request('GET', 'croppings', [
            'auth' => [
                $this->key,
                $this->secret,
            ],
            'query' => [
                'content' => urlencode($content_id),
                'resolution' => $width . 'x' . $height,
            ],
        ]);

        $data_response = json_decode($response->getBody());
        return reset(reset($data_response->results)->croppings);
    }

    protected function cropImage(string $filename_destination, stdClass $data): void
    {
        $crop_width = $data->x2 - $data->x1;
        $crop_height = $data->y2 - $data->y1;

        $image = new Imagick();
        $image->readImage($this->source);
        $image->cropImage($crop_width, $crop_height, $data->x1, $data->y1);
        $image->resizeImage($data->target_width, $data->target_height, Imagick::FILTER_CATROM, 1);
        $image->writeImage($filename_destination);
    }
}