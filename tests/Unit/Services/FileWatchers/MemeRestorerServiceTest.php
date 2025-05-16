<?php
namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\FileWatchers\MemeRestorerService;

class MemeRestorerServiceTest extends TestCase
{
    public function test_meme_is_downloaded_on_delete()
    {
        Http::fake([
            'https://meme-api.com/*' => Http::response(['url' => 'https://test.com/meme.jpg']),
            'https://test.com/meme.jpg' => Http::response('image content'),
        ]);

        $file = storage_path('app/deleted.jpg');

        $service = new MemeRestorerService();
        $service->handle($file);

        $this->assertFileExists($file);
        unlink($file);
    }
}
