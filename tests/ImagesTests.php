<?php

use Fathom\Exeptions\EmptyApiKeysException;
use Fathom\ImageTool;
use PHPUnit\Framework\TestCase;

final class ImagesTests extends TestCase
{
    private $test_image = __DIR__ . '/test.jpg';
    private $result_image = __DIR__ . '/result.jpg';

    public function testImageMagick(): void
    {
        if (file_exists($this->result_image)) {
            unlink($this->result_image);
        }

        $image_tool = new ImageTool(ImageTool::SERVICE_IMAGE_MAGICK);
        $image_tool->setSource($this->test_image);
        $image_tool->makeThumb($this->result_image, 200, 100);

        $this->assertTrue(file_exists($this->result_image));

        $image_info = getimagesize($this->result_image);

        $this->assertEquals(200, $image_info[0]);
        $this->assertEquals(100, $image_info[1]);
    }
}