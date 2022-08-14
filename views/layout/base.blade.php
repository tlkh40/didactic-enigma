<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File viewer</title>
    <link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">
    <style>
        .navigation {
            display: flex;
            width: 100%;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        body {
            padding-top: 2rem;
        }
    </style>
</head>

<body class="container">
    @yield("content")
</body>