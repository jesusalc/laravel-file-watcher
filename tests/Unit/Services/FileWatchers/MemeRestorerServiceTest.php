<?php

namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Services\FileWatchers\MemeRestorerService;

class MemeRestorerServiceTest extends TestCase
{
    protected string $path;

    protected function setUp(): void
    {
        parent::setUp();
        $this->path = storage_path('app/deleted.jpg');
        File::delete($this->path); // Simulate deleted file
    }

    protected function tearDown(): void
    {
        File::delete($this->path);
        parent::tearDown();
    }

    public function test_meme_image_is_fetched_and_written_successfully()
    {
        Http::fake([
            'https://meme-api.com/gimme' => Http::response([
                'url' => 'https://i.redd.it/fake.jpg',
                'title' => 'Test Meme',
                'subreddit' => 'unitmemes',
            ], 200),

            'https://i.redd.it/fake.jpg' => Http::response('FAKE_IMAGE_DATA', 200),
        ]);

        Log::shouldReceive('channel->info')->withArgs(fn($msg) => str_contains($msg, 'Fetching meme'))->once();
        Log::shouldReceive('channel->info')->withArgs(fn($msg) => str_contains($msg, 'Restored meme'))->once();

        $service = new MemeRestorerService();
        $service->handle($this->path);

        $this->assertFileExists($this->path);
        $this->assertEquals('FAKE_IMAGE_DATA', File::get($this->path));
    }

    public function test_meme_api_returns_error_status()
    {
        Http::fake([
            'https://meme-api.com/gimme' => Http::response('Error', 500),
        ]);

        Log::shouldReceive('channel->info')->once();
        Log::shouldReceive('channel->error')->withArgs(fn($msg) => str_contains($msg, 'Meme API error'))->once();

        $service = new MemeRestorerService();
        $service->handle($this->path);

        $this->assertFileDoesNotExist($this->path);
    }

    public function test_missing_url_in_meme_api_response()
    {
        Http::fake([
            'https://meme-api.com/gimme' => Http::response([
                'title' => 'No image URL',
            ], 200),
        ]);

        Log::shouldReceive('channel->info')->once();
        Log::shouldReceive('channel->error')->withArgs(fn($msg) => str_contains($msg, 'no valid image URL'))->once();

        $service = new MemeRestorerService();
        $service->handle($this->path);

        $this->assertFileDoesNotExist($this->path);
    }

    public function test_meme_url_is_not_image()
    {
        Http::fake([
            'https://meme-api.com/gimme' => Http::response([
                'url' => 'https://some.site/not-an-image.txt',
                'title' => 'Invalid Format',
                'subreddit' => 'wrongmemes'
            ], 200),
        ]);

        Log::shouldReceive('channel->info')->once();
        Log::shouldReceive('channel->error')->withArgs(fn($msg) => str_contains($msg, 'no valid image URL'))->once();

        $service = new MemeRestorerService();
        $service->handle($this->path);

        $this->assertFileDoesNotExist($this->path);
    }

    public function test_file_recreated_even_if_previously_deleted()
    {
        Http::fake([
            'https://meme-api.com/gimme' => Http::response([
                'url' => 'https://i.redd.it/revive.jpg',
                'title' => 'It lives!',
                'subreddit' => 'resurrectedmemes',
            ], 200),

            'https://i.redd.it/revive.jpg' => Http::response('NEW_MEME', 200),
        ]);

        $service = new MemeRestorerService();
        $service->handle($this->path);

        $this->assertFileExists($this->path);
        $this->assertEquals('NEW_MEME', File::get($this->path));
    }
}
