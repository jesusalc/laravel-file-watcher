<?php
namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Services\FileWatchers\TxtAppenderService;

class TxtAppenderServiceTest extends TestCase
{
    protected string $testFile;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testFile = storage_path('app/test.txt');
        File::put($this->testFile, "Initial content.");
    }

    protected function tearDown(): void
    {
        File::delete($this->testFile);
        parent::tearDown();
    }

    public function test_appends_bacon_ipsum_successfully()
    {
        Http::fake([
            '*' => Http::response([
                'Bacon ipsum paragraph one.',
                'Bacon ipsum paragraph two.',
            ], 200),
        ]);

        Log::shouldReceive('channel->info')->withArgs(fn($msg) => str_contains($msg, 'Requesting Bacon Ipsum'))->once();
        Log::shouldReceive('channel->info')->withArgs(fn($msg) => str_contains($msg, 'Appended Bacon Ipsum'))->once();

        $service = new TxtAppenderService();
        $service->handle($this->testFile);

        $content = File::get($this->testFile);

        $this->assertStringContainsString("Initial content.", $content);
        $this->assertStringContainsString("Bacon ipsum paragraph one.", $content);
        $this->assertStringContainsString("Bacon ipsum paragraph two.", $content);
    }

    public function test_logs_error_when_api_fails()
    {
        Http::fake([
            '*' => Http::response('Server error', 500),
        ]);

        Log::shouldReceive('channel->info')->once();
        Log::shouldReceive('channel->error')->withArgs(fn($msg) => str_contains($msg, 'HTTP 500'))->once();

        $service = new TxtAppenderService();
        $service->handle($this->testFile);
    }

    public function test_logs_error_when_invalid_json_returned()
    {
        Http::fake([
            '*' => Http::response('not-json', 200),
        ]);

        Log::shouldReceive('channel->info')->once();
        Log::shouldReceive('channel->error')->withArgs(fn($msg) => str_contains($msg, 'invalid format'))->once();

        $service = new TxtAppenderService();
        $service->handle($this->testFile);
    }

    public function test_logs_error_when_file_is_not_writable()
    {
        $file = '/dev/null/readonly.txt'; // simulate unwritable

        Log::shouldReceive('channel->warning')->withArgs(fn($msg) => str_contains($msg, 'not writable'))->once();

        $service = new TxtAppenderService();
        $service->handle($file);
    }

    public function test_file_types_returns_txt()
    {
        $service = new TxtAppenderService();
        $this->assertEquals(['txt'], $service->fileTypes());
    }
}
