<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - Login</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom Theme Styles -->
    <style>
        :root {
            --purple-main: #800776;
            --golden-chestnut: #C08552;
            --espresso: #5E3023;
            --toffee-brown: #895737;
        }

        body { 
            background: var(--purple-main) !important; 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .login-card { 
            width: 100%; 
            max-width: 430px; 
            border: 0; 
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 .5rem 1.5rem rgba(0,0,0,.2);
        }

        .login-card-header { 
            background: var(--espresso) !important; 
            color: #ffffff;
            padding: 1.5rem;
            text-align: center;
            border-bottom: 3px solid var(--golden-chestnut);
        }

        .btn-primary-custom { 
            background: var(--golden-chestnut) !important; 
            color: #fff !important; 
            border: none; 
            padding: 0.6rem 1.2rem;
            font-weight: 600;
            border-radius: 6px;
            transition: background-color 0.2s ease;
        }

        .btn-primary-custom:hover { 
            background: var(--toffee-brown) !important; 
            color: #fff !important; 
        }

        .form-label {
            font-weight: 600;
            color: #495057;
        }

        .form-control:focus {
            border-color: var(--golden-chestnut);
            box-shadow: 0 0 0 0.25rem rgba(192, 133, 82, 0.25);
        }
    </style>
</head>
<body>

<div class="card login-card">
    <div class="login-card-header">
        <h4 class="mb-0 fw-bold">Student Portal</h4>
        <small class="text-white-50">Login to your dashboard</small>
    </div>

    <div class="card-body p-4 bg-white">
        @if (session('status'))
            <div class="alert alert-success mb-3" role="alert">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Username Input -->
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Enter username" required autofocus>
            </div>

            <!-- Password Input -->
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" class="form-control" name="password" placeholder="Enter a password" required autocomplete="current-password">
            </div>

            <!-- Remember Me -->
            <div class="mb-3 form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label text-secondary small">Remember me</label>
            </div>

            <div class="d-grid gap-2 mt-4">
                <button type="submit" class="btn btn-primary-custom">
                    Log In
                </button>
            </div>
        </form>

        <!-- Clean, Normal Credentials Text -->
        <div class="text-center mt-4 text-muted small">
            <p class="mb-1">Username: admin</p>
            <p class="mb-0">Password: password123</p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>