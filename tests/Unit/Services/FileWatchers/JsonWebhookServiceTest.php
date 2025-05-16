<?php

namespace Tests\Unit\Services\FileWatchers;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Services\FileWatchers\JsonWebhookService;
use Illuminate\Support\Facades\File;

class JsonWebhookServiceTest extends TestCase
{
	protected string $testFile;

	protected function setUp(): void
	{
		parent::setUp();
    config(['services.fswatcher.webhook' => 'https://fake-url.com']);
		$this->testFile = storage_path('app/test.json');
		File::put($this->testFile, json_encode(['event' => 'test']));
	}

	protected function tearDown(): void
	{
		File::delete($this->testFile);
		parent::tearDown();
	}

	public function test_successful_json_request_is_logged()
	{
		Http::fake([
			'https://fake-url.com' => Http::response([], 200, [], 250),
		]);

		Log::shouldReceive('channel->info')
			->withArgs(fn($msg) => str_contains($msg, 'Sending JSON'))
			->once();

		Log::shouldReceive('channel->info')
			->withArgs(fn($msg) => str_contains($msg, 'JSON sent successfully'))
			->once();

		$service = new JsonWebhookService();
		$service->handle($this->testFile);
	}

	public function test_failed_http_response_is_logged()
	{
		Http::fake([
			'https://fake-url.com' => Http::response('Bad Request', 400),
		]);

		Log::shouldReceive('channel->info')->once();
		Log::shouldReceive('channel->error')
			->withArgs(fn($msg) => str_contains($msg, 'HTTP 400 error'))
			->once();

		$service = new JsonWebhookService();
		$service->handle($this->testFile);
	}

	public function test_timeout_logs_error()
	{
		Http::fake([
			'https://fake-url.com' => fn() => throw new \Illuminate\Http\Client\ConnectionException('Connection timed out'),
		]);

		Log::shouldReceive('channel->info')->once();
		Log::shouldReceive('channel->error')
			->withArgs(fn($msg) => str_contains($msg, 'Failed to send JSON') && str_contains($msg, 'timed out'))
			->once();

		$service = new JsonWebhookService();
		$service->handle($this->testFile);
	}
}
