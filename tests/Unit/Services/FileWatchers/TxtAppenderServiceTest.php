<?php
namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\FileWatchers\TxtAppenderService;

class TxtAppenderServiceTest extends TestCase
{
    public function test_bacon_text_is_appended()
    {
        Http::fake([
            '*' => Http::response(['Bacon Ipsum paragraph'], 200),
        ]);

        $file = storage_path('app/test.txt');
        file_put_contents($file, "Initial");

        $service = new TxtAppenderService();
        $service->append($file);

        $content = file_get_contents($file);
        $this->assertStringContainsString('Bacon Ipsum', $content);

        unlink($file);
    }
}
