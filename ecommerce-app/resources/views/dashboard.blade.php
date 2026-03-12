<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Ecommerce App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Ecommerce App</a>
            <div class="d-flex">
                <span class="navbar-text me-3">
                    Welcome, {{ Auth::user()->name }}
                </span>
                <a href="{{ route('logout') }}" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>Dashboard</h1>
        <p class="lead">Welcome to your Ecommerce App dashboard!</p>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Your Profile</h5>
                        <p class="card-text">Name: {{ Auth::user()->name }}</p>
                        <p class="card-text">Email: {{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
