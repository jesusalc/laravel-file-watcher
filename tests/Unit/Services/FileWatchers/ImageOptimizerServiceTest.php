<?php

namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use App\Services\FileWatchers\ImageOptimizerService;
use Illuminate\Support\Facades\File;

class ImageOptimizerServiceTest extends TestCase
{
    protected string $file;

    protected function setUp(): void
    {
        parent::setUp();
        $this->file = storage_path('app/test.jpg');

        // Create 10x10 JPEG image using GD
        $img = imagecreatetruecolor(10, 10);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $white);
        imagejpeg($img, $this->file, 100); // high quality for bigger size
        imagedestroy($img);
    }

    protected function tearDown(): void
    {
        File::delete($this->file);
        parent::tearDown();
    }

    public function test_image_is_optimized_and_smaller_or_equal()
    {
        $originalSize = filesize($this->file);

        $service = new ImageOptimizerService();
        $service->handle($this->file);

        $optimizedSize = filesize($this->file);

        $this->assertFileExists($this->file);
        $this->assertLessThanOrEqual($originalSize, $optimizedSize, "Image should be optimized (smaller or equal in size)");
    }

    public function test_file_types_returns_jpg_and_jpeg()
    {
        $service = new ImageOptimizerService();
        $this->assertEquals(['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'ico', 'tiff', 'tif', 'bmp'], $service->fileTypes());
    }
}