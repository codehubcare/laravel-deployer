<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laravel Deployer</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column">
    <div class="container my-auto">
        <h1 class="display-4 mt-3 font-weight-bold text-primary">Laravel Deployer</h1>
        <h2 class="mt-4">Changed files</h2>
        <ul>
            @foreach ($changedFiles as $file)
                <li>{{ $file }}</li>
            @endforeach
        </ul>
    </div>
</body>
</html>