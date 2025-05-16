<?php

require 'vendor/autoload.php';

use Spatie\Watcher\Watch;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use GuzzleHttp\Client;

$directoryToWatch = __DIR__ . '/watched_directory';

$optimizerChain = OptimizerChainFactory::create();
$httpClient = new Client();

Watch::path($directoryToWatch)
    ->onFileCreated(function (string $newFilePath) use ($optimizerChain, $httpClient) {
        $extension = pathinfo($newFilePath, PATHINFO_EXTENSION);

        switch (strtolower($extension)) {
            case 'jpg':
            case 'jpeg':
                $optimizerChain->optimize($newFilePath);
                break;

            case 'json':
                $jsonData = file_get_contents($newFilePath);
                $httpClient->post('https://fswatcher.requestcatcher.com/', [
                    'body' => $jsonData,
                    'headers' => ['Content-Type' => 'application/json']
                ]);
                break;

            case 'txt':
                $response = $httpClient->get('https://baconipsum.com/api/?type=meat-and-filler&paras=1');
                $baconText = json_decode($response->getBody()->getContents(), true)[0];
                file_put_contents($newFilePath, "\n" . $baconText, FILE_APPEND);
                break;

            case 'zip':
                $zip = new ZipArchive();
                if ($zip->open($newFilePath) === TRUE) {
                    $extractPath = pathinfo($newFilePath, PATHINFO_DIRNAME);
                    $zip->extractTo($extractPath);
                    $zip->close();
                }
                break;
        }
    })
    ->onFileDeleted(function (string $deletedFilePath) use ($httpClient) {
        $memeResponse = $httpClient->get('https://meme-api.com/gimme');
        $memeData = json_decode($memeResponse->getBody()->getContents(), true);
        $memeUrl = $memeData['url'] ?? null;

        if ($memeUrl) {
            $memeImage = $httpClient->get($memeUrl)->getBody()->getContents();
            file_put_contents($deletedFilePath, $memeImage);
        }
    })
    ->onFileModified(function (string $modifiedFilePath) {
        // Implement any specific actions for modified files here
    })
    ->start();
