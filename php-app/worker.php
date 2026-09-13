<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Spiral\RoadRunner\Http\PSR7Worker;
use Spiral\RoadRunner\Worker;

$worker = Worker::create();
$factory = new Psr17Factory();

$psr7 = new PSR7Worker(
    $worker,
    $factory,
    $factory,
    $factory,
);

while (true) {
    try {
        $request = $psr7->waitRequest();

        if ($request === null) {
            break;
        }

        $path = $request->getUri()->getPath();

        // Prevent directory traversal.
        $path = '/' . ltrim($path, '/');

        $publicPath = realpath(__DIR__ . '/public');

        if ($publicPath === false) {
            throw new RuntimeException('Public directory does not exist.');
        }

        $requestedFile = realpath($publicPath . $path);

        // Serve existing static files directly.
        if (
            $requestedFile !== false &&
            str_starts_with($requestedFile, $publicPath) &&
            is_file($requestedFile)
        ) {
            $contentType = contentType($requestedFile);

            $psr7->respond(
                new Response(
                    200,
                    ['Content-Type' => $contentType],
                    fopen($requestedFile, 'rb'),
                ),
            );

            continue;
        }

        // Everything else goes through public/index.php.
        ob_start();

        require $publicPath . '/index.php';

        $body = ob_get_clean();

        $psr7->respond(
            new Response(
                200,
                ['Content-Type' => 'text/html; charset=UTF-8'],
                $body,
            ),
        );
    } catch (Throwable $e) {
        $psr7->respond(
            new Response(
                500,
                ['Content-Type' => 'text/plain; charset=UTF-8'],
                'Internal Server Error',
            ),
        );

        $worker->error((string) $e);
    }
}

function contentType(string $path): string
{
    return match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
        'css' => 'text/css; charset=UTF-8',
        'js', 'mjs' => 'application/javascript; charset=UTF-8',
        'json' => 'application/json; charset=UTF-8',
        'html' => 'text/html; charset=UTF-8',
        'svg' => 'image/svg+xml',
        'png' => 'image/png',
        'jpg', 'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'ico' => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        default => 'application/octet-stream',
    };
}