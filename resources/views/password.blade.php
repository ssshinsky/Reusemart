<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - ReUse Mart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            display: flex;
            justify-content: center;
            padding: 50px;
        }

        .container {
            width: 100%;
            max-width: 500px;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .logo {
            width: 40px;
        }

        h2 {
            font-size: 20px;
            font-weight: bold;
        }

        .card {
            border: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 0 10px #eee;
            margin-bottom: 30px;
        }

        h3 {
            color: green;
            text-align: center;
            margin-bottom: 20px;
        }

        .step {
            margin-bottom: 25px;
        }

        .step p {
            margin: 5px 0;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .green-btn {
            background-color: #00c800;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            cursor: pointer;
            border-radius: 4px;
        }

        .green-btn:hover {
            background-color: #009f00;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('assets/image/logo.png') }}" alt="ReUse Mart Logo" class="logo">
            <h2>Password</h2>
        </div>

        <div class="card">
            <h3>Change Your Password</h3>

            <!-- Step 1 -->
            <div class="step">
                <p><strong>Step 1: Enter Your Email</strong></p>
                <p>Please enter the email associated with your account. We’ll send a verification code to reset your
                    password.</p>
                <form method="POST" action="{{ route('password.sendCode') }}">
                    @csrf
                    <input type="email" name="email" placeholder="you@example.com" required>
                    <button type="submit" class="green-btn">Send Verification Code Button</button>
                </form>
            </div>

            <!-- Step 2 -->
            <div class="step">
                <p><strong>Step 2: Enter Verification Code</strong></p>
                <p>We’ve sent a code to your email. Please enter it below.</p>
                <form method="POST" action="{{ route('password.verifyCode') }}">
                    @csrf
                    <input type="text" name="code" placeholder="6-digit code" required>
                    <button type="submit" class="green-btn">Verify Code Button</button>
                </form>
            </div>
        </div>

        <div class="card">
            <h3>Change Your Password</h3>
            <div class="step">
                <p><strong>Step 3: Set New Password</strong></p>
                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="password" name="password" placeholder="New Password" required>
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
                    <button type="submit" class="green-btn">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
