<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authorize Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Authorize Application</h4>
                    </div>
                    <div class="card-body">
                        <p class="lead">
                            <strong>{{ $client->name }}</strong> is requesting permission to access your account.
                        </p>

                        @if (count($scopes) > 0)
                            <p class="text-muted">This application will be able to:</p>
                            <ul class="list-group mb-3">
                                @foreach ($scopes as $scope)
                                    <li class="list-group-item">{{ $scope->description }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <form method="POST" action="{{ route('passport.authorizations.approve') }}">
                            @csrf
                            <input type="hidden" name="client_id" value="{{ $client->id }}">
                            <input type="hidden" name="auth_token" value="{{ $authToken }}">

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Authorize
                                </button>
                                <a href="{{ route('passport.authorizations.deny') }}"
                                    class="btn btn-outline-secondary btn-lg">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
