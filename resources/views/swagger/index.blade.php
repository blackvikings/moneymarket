<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Money Market') }} API Documentation</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        body {
            margin: 0;
            background: #f7f7f7;
        }

        .swagger-ui .topbar {
            display: none;
        }
    </style>
</head>
<body>
<div id="swagger-ui"></div>
<script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
<script>
    window.onload = function () {
        window.ui = SwaggerUIBundle({
            url: @json(url('/api/docs.json')),
            dom_id: '#swagger-ui',
            deepLinking: true,
            persistAuthorization: true,
            displayRequestDuration: true,
        });
    };
</script>
</body>
</html>
