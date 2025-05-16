<?php
namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\FileWatchers\ZipExtractorService;

class ZipExtractorServiceTest extends TestCase
{
    public function test_zip_is_extracted()
    {
        $sourceZip = storage_path('app/test.zip');
        $extractTo = storage_path('app/');
        $testFile = $extractTo . 'testfile.txt';

        $zip = new \ZipArchive();
        if ($zip->open($sourceZip, \ZipArchive::CREATE) === TRUE) {
            $zip->addFromString('testfile.txt', 'Sample content');
            $zip->close();
        }

        $service = new ZipExtractorService();
        $service->extract($sourceZip);

        $this->assertFileExists($testFile);

        unlink($sourceZip);
        unlink($testFile);
    }
}
