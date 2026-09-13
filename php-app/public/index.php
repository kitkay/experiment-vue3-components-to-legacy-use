<?php

declare(strict_types=1);

// require '../Vite.php';

$payload = [
    'user' => [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ],
    'orders' => [
        [
            'id' => 1001,
            'product' => 'Keyboard',
            'quantity' => 2,
            'price' => 99.9,
            'status' => 'paid',
        ],
        [
            'id' => 1002,
            'product' => 'Mouse',
            'quantity' => 1,
            'price' => 49.99,
            'status' => 'pending',
        ],
    ],
];

// $vueJs = viteAsset('/dist/assets/index-qXAbCZMf.js');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/assets/vue-app.css">
    <title>RR :: App</title>
</head>
<body>
    <h1>PHP App  for Built Vue3 Components</h1>
    <h2>RoadRunner App</h2>
    <p>
        <?php
            echo '<p>Instance: ' . getenv('APP_INSTANCE') . '</p>';
            echo '<p>Container: ' . gethostname() . '</p>';
            echo '<p>PID: ' . getmypid() . '</p>';
        ?>
    </p>

    <div data-role="app-header"></div>
    <div data-role="summary-card"></div>
    <div data-role="order-list"></div>

    <script>
    window.__APP_DATA__ = <?= json_encode(
        $payload,
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    ) ?>;
    </script>

    <script type="module" src="/assets/vue-app.js"></script>
</body>
</html>