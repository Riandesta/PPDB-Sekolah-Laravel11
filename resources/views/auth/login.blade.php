<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login PPDB</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <!-- Add this in the <head> section of your login page or layout -->
<meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
<section class="ftco-section">
    <div class="container " style="">
        <div class="row justify-content-center  " >
            <div class="col-md-12 col-lg-10 ">
                <div class="wrap d-md-flex ">
                    <!-- Welcome Section -->
                    <div class=" text-wrap p-4 p-lg-5 text-center d-flex align-items-center">
                        <div class="text w-100">
                            <h2>Selamat Datang di PPDB Online</h2>
                        </div>
                    </div>

                    <!-- Login Form Section -->
                    <div class="login-wrap p-4 p-lg-5 ">
                        <div class="d-flex">
                            <div class="w-100">
                                <h3 class="mb-4">Sign In</h3>
                            </div>
                        </div>

                        <!-- Show validation errors if any -->
                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('auth.verify') }}" class="signin-form">
                            @csrf
                            <!-- Username Field -->
                            <div class="form-group mb-3">
                                <label class="label">Username</label>
                                <input type="text"
                                       name="username"
                                       class="form-control @error('username') is-invalid @enderror"
                                       value="{{ old('username') }}"
                                       required>
                            </div>

                            <!-- Password Field -->
                            <div class="form-group mb-3">
                                <label class="label">Password</label>
                                <input type="password"
                                       name="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-group">
                                <button type="submit" class="form-control btn btn-primary">
                                    Sign In
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>

@push('style')
<style>
/* Background color of the login page */
body {
    background-color: #007BFF; /* Blue background */
}

/* Form and wrap background color */
.wrap {
    background-color: #ffffff; /* White background for the login box */
}

/* Button styling */
button.btn.btn-primary {
    background-color: #007BFF; /* Blue button */
    border-color: #007BFF; /* Button border color */
}

button.btn.btn-primary:hover {
    background-color: #0056b3; /* Darker blue on hover */
    border-color: #0056b3; /* Darker border color on hover */
}

/* Label color */
label {
    color: #007BFF; /* Blue label text */
}
</style>
@endpush
