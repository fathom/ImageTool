<?php

use Fathom\Exceptions\EmptyApiKeysException;
use Fathom\Exceptions\SourceNotSetException;
use Fathom\Exceptions\FileNotExistException;
use Fathom\Exceptions\WrongImageSizeException;
use Fathom\ImageTool;
use Fathom\Services\ImageMagick;
use Fathom\Services\ImageService;
use Fathom\Services\Immaga;
use Fathom\Services\TinyPNG;
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

        $object = new ImageMagick();
        $object->setSource($this->test_image);
        $object->makeThumbnail($this->result_image, 200, 100);

        $this->assertTrue(file_exists($this->result_image));

        $image_info = getimagesize($this->result_image);

        $this->assertEquals(200, $image_info[0]);
        $this->assertEquals(100, $image_info[1]);
    }

    public function testTinyPngException(): void
    {
        if (file_exists($this->result_image)) {
            unlink($this->result_image);
        }

        $this->expectException(EmptyApiKeysException::class);

        $image_tool = new ImageTool(ImageTool::SERVICE_TINY_PNG);
        $image_tool->setSource($this->test_image);
        $image_tool->makeThumb($this->result_image, 200, 100);
    }

    public function testImmagaException(): void
    {
        if (file_exists($this->result_image)) {
            unlink($this->result_image);
        }

        $this->expectException(EmptyApiKeysException::class);

        $image_tool = new ImageTool(ImageTool::SERVICE_IMMAGA);
        $image_tool->setSource($this->test_image);
        $image_tool->makeThumb($this->result_image, 200, 100);
    }

    public function testSetSourceFileException(): void
    {
        $this->expectException(FileNotExistException::class);

        $image_service = $this->getMockForAbstractClass(ImageService::class);
        $image_service->setSource('test');
    }

    public function testCheckImageSizeException(): void
    {
        $this->expectException(WrongImageSizeException::class);

        $stub = new class() extends ImageService {
            public function makeThumbnail(string $filename_destination, int $width, int $height): void
            {
                $this->checkImageSize($width, $height);
            }
        };

        $stub->setSource($this->test_image);
        $stub->makeThumbnail($this->result_image, 2000, 2000);
    }

    public function testCheckSourceException(): void
    {
        $this->expectException(SourceNotSetException::class);

        $stub = new class() extends ImageService {
            public function makeThumbnail(string $filename_destination, int $width, int $height): void {}
            public function checkSourceCaller()
            {
                $this->checkSource();
            }
        };

        $stub->checkSourceCaller();
    }

    public function testImmaga():void
    {
        if (file_exists($this->result_image)) {
            unlink($this->result_image);
        }

        $mock = $this->getMockBuilder(Immaga::class)
            ->setConstructorArgs([
                ['key' => 'test', 'secret' => 'test']
            ])
            ->setMethods(['uploadFile', 'getCroppingData'])
            ->getMock();

        $mock->method('uploadFile')->will($this->returnValue('test'));
        $mock->method('getCroppingData')->willReturn(new class() extends stdClass {
            public $x1 = 0;
            public $y1 = 0;
            public $x2 = 1439;
            public $y2 = 720;
            public $target_width = 200;
            public $target_height = 100;
        });

        $mock->setSource($this->test_image);
        $mock->makeThumbnail($this->result_image, 200, 100);

        $this->assertTrue(file_exists($this->result_image));

        $image_info = getimagesize($this->result_image);

        $this->assertEquals(200, $image_info[0]);
        $this->assertEquals(100, $image_info[1]);
    }

    public function testImageToolStrategy()
    {
        $reflector = new ReflectionClass(ImageTool::class);
        $method = $reflector->getMethod('getService');
        $method->setAccessible(true);

        $object = $reflector->newInstanceWithoutConstructor();

        $service = $method->invokeArgs( $object, [ImageTool::SERVICE_IMAGE_MAGICK, []]);
        $this->assertInstanceOf(ImageMagick::class, $service);

        $service = $method->invokeArgs( $object, [ImageTool::SERVICE_TINY_PNG, ['key' => 'test']]);
        $this->assertInstanceOf(TinyPNG::class, $service);

        $service = $method->invokeArgs( $object, [ImageTool::SERVICE_IMMAGA, ['key' => 'test', 'secret' => 'test']]);
        $this->assertInstanceOf(Immaga::class, $service);
    }
}