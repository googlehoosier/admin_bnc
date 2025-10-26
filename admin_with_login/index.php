<?php
session_start();
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: booking_form.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Binge Celebrations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #666;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .input-with-icon {
            position: relative;
        }

        .input-with-icon input {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .input-with-icon input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            font-size: 18px;
        }

        .password-toggle:hover {
            color: #333;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .error-message {
            background: #fee;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .admin-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            gap: 12px;
        }

        .admin-logo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
        }

        .admin-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .admin-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            letter-spacing: 0.05em;
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 20px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="admin-brand">
            <div class="admin-logo">
                <img src="BINGEN.png" alt="BINGE'N Logo">
            </div>
            <h1 class="admin-title">BINGE'N CELEBRATIONS</h1>
        </div>

        <div class="login-header">
            <h1>Admin Login</h1>
            <p>Access your booking management panel</p>
        </div>

        <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
            <div class="error-message show" id="errorMessage">
                Invalid username or password!
            </div>
        <?php elseif (isset($_GET['error']) && $_GET['error'] == 2): ?>
            <div class="error-message show" id="errorMessage">
                Please log in to access the admin panel.
            </div>
        <?php endif; ?>

        <div class="error-message" id="errorMessage"></div>

        <form action="auth.php" method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-with-icon">
                    <input type="text" id="username" name="username" required placeholder="Enter admin username">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-with-icon">
                    <input type="password" id="password" name="password" required placeholder="Enter admin password">
                    <button type="button" class="password-toggle" id="passwordToggle">
                        👁️
                    </button>
                </div>
            </div>

            <button type="submit" class="login-btn">Sign In</button>
        </form>
    </div>

    <script>
        // Password visibility toggle
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        const errorMessage = document.getElementById('errorMessage');

        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? '👁️' : '🔒';
        });

        // Form validation
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!username || !password) {
                e.preventDefault();
                errorMessage.textContent = 'Please fill in all fields!';
                errorMessage.classList.add('show');
            }
        });

        // Hide error message when user starts typing
        document.getElementById('username').addEventListener('input', hideError);
        document.getElementById('password').addEventListener('input', hideError);

        function hideError() {
            errorMessage.classList.remove('show');
        }

        // Auto-focus on username field
        document.getElementById('username').focus();
    </script>
</body>
</html>