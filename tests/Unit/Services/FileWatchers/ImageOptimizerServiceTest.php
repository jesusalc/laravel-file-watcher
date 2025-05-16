<?php

namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use App\Services\FileWatchers\ImageOptimizerService;

class ImageOptimizerServiceTest extends TestCase
{
    public function test_image_is_optimized()
    {
        $file = storage_path('app/test.jpg');
        copy(__DIR__.'/test.jpg', $file);

        $service = new ImageOptimizerService();
        $service->optimize($file);

        $this->assertFileExists($file);
        unlink($file);
    }
}
