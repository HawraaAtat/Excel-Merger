<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
            background-color:rgb(255, 255, 255); /* Light background */
        }
        .container {
            padding: 2rem;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="mb-4">Welcome</h1>
    <a href="{{ route('inventory.form') }}" class="btn btn-primary btn-lg">Go to Inventory Form</a>
</div>

</body>
</html>
