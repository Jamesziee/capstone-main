<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <style>
        /* General body and layout */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f6f9;
            font-family: 'Arial', sans-serif;
            overflow: hidden;
        }

        /* Card for the login form */
        .card {
            background-color: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        /* Logo styling */
        .logo {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            margin-bottom: 20px;
            object-fit: cover;
        }

        /* Input field styling */
        .input-field {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            font-size: 16px;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        /* Input field hover effect */
        .input-field:hover, .input-field:focus {
            border-color: #4a90e2;
            background-color: #eaf6ff;
        }

        /* Button styling */
        .button {
            width: 100%;
            padding: 15px;
            margin: 20px 0;
            border: none;
            border-radius: 8px;
            background-color: #4a90e2;
            color: white;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        /* Button hover effect */
        .button:hover {
            background-color: #357ABD;
        }

        /* Links below the form */
        .links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 10px;
        }

        .link {
            color: #4a90e2;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .link:hover {
            color: #2c3e50;
            text-decoration: underline;
        }

       

        

    </style>
</head>
<body>

    <!-- Preloader overlay -->
    <div id="overlay" class="overlay">
        <div class="loader" id="loader"></div>
    </div>

    <!-- Login Form Card -->
    <div class="card">
        <header>
            <!-- Logo -->
            <img src="logo/logo/logo.png" alt="Logo" class="logo">
        </header>

        <form id="loginForm" action="verify_login.php" method="POST">
            <input type="email" name="email" id="email" class="input-field" placeholder="Email" required>
            <input type="password" name="password" id="password" class="input-field" placeholder="Password" required>
            <button type="submit" class="button">Login</button>

            <div class="links">
                <a href="A_forgotpassword.php" class="link">Forgot Password</a>
            </div>
        </form>
    </div>
<script>
</script>

</body>
</html>
