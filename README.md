# Image library

## Install
The preferred way to install this extension is through [composer](http://getcomposer.org/download/):
```
composer require fathom/imagetool-library
```

## Example

```php
use Fathom\ImageTool;

$filename = __DIR__ . '/image.jpg';

// Create thumb with ImageMagick
$imagetool = new ImageTool(ImageTool::SERVICE_IMAGE_MAGICK);
$imagetool->setSource($filename);
$imagetool->makeThumb('thumb_image_magick.jpg', 200, 200);

// Create thumb with TinyPNG
$imagetool = new ImageTool(ImageTool::SERVICE_TINY_PNG, [
    'key' => 'xxxxxxxxxxxxx'
]);
$imagetool->setSource($filename);
$imagetool->makeThumb('thumb_tinypng.jpg', 200, 200);

// Create thumb with Immaga
$imagetool = new ImageTool(ImageTool::SERVICE_IMMAGA, [
    'key' => 'xxxxxxxxxxxxx',
    'secret' => 'xxxxxxxxxxxxx',
]);
$imagetool->setSource($filename);
$imagetool->makeThumb('thumb_immaga.jpg', 200, 200);

```