<?php
namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use App\Services\FileWatchers\ZipExtractorService;
use Illuminate\Support\Facades\File;

class ZipExtractorServiceTest extends TestCase
{
    protected string $zipPath;
    protected string $extractPath;
    protected string $testFile;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extractPath = storage_path('app');
        $this->zipPath = "{$this->extractPath}/test.zip";
        $this->testFile = "{$this->extractPath}/extracted.txt";

        // Create a test zip file
        $zip = new \ZipArchive();
        if ($zip->open($this->zipPath, \ZipArchive::CREATE) === true) {
            $zip->addFromString('extracted.txt', 'Hello from ZIP');
            $zip->close();
        }
    }

    protected function tearDown(): void
    {
        File::delete($this->zipPath);
        File::delete($this->testFile);
        parent::tearDown();
    }

    public function test_zip_is_extracted_successfully()
    {
        $this->assertFileExists($this->zipPath);

        $service = new ZipExtractorService();
        $service->handle($this->zipPath);

        $this->assertFileExists($this->testFile);
        $this->assertEquals('Hello from ZIP', File::get($this->testFile));
    }

    public function test_file_types_returns_zip()
    {
        $service = new ZipExtractorService();
        $this->assertEquals(['zip'], $service->fileTypes());
    }
}
