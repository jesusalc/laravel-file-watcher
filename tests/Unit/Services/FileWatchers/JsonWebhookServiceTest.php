<?php
namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\FileWatchers\JsonWebhookService;


class JsonWebhookServiceTest extends TestCase
{
    public function test_json_file_is_sent_via_post()
    {
        Http::fake();

        $file = storage_path('app/test.json');
        file_put_contents($file, json_encode(['key' => 'value']));

        $service = new JsonWebhookService();
        $service->send($file);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://fswatcher.requestcatcher.com/' &&
                   $request['key'] === 'value' &&
                   $request->method() === 'POST';
        });

        unlink($file);
    }

    public function test_file_types_returns_json()
    {
        $service = new JsonWebhookService();
        $this->assertEquals(['json'], $service->fileTypes());
    }
}
