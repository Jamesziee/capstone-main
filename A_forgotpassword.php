<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Forgot Password</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #ecf0f1;
        }

        .card {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
        }

        .logo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin-bottom: 30px;
            object-fit: cover;
        }

        .input-field {
            display: block;
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f5f5f5;
            font-size: 16px;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        .input-field:hover {
            border-color: #4a4aff;
            background-color: #eaf2ff;
        }

        .button {
            width: 100%;
            padding: 15px;
            margin: 20px 0;
            border: none;
            border-radius: 5px;
            background-color: #4a4aff;
            color: white;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .loader {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            border: 4px solid rgba(255, 255, 255, 0.3);
            border-top: 4px solid #4a4aff;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

    </style>
</head>
<body>
    <div class="card">
        <header>
            <img src="/capstone-main/logo/logo.png" alt="Logo" class="logo">
        </header>
        <h2>Forgot Password</h2>
        <p>Enter your admin email to reset your password.</p>
        <form id="forgotPasswordForm" action="reset_link.php" method="POST" onsubmit="showLoader(event)">
            <input type="email" name="email" class="input-field" placeholder="Admin Email" required>
            <button type="submit" class="button">Send Reset Link</button>
        </form>

        <!-- Loader spinner -->
        <div class="loader" id="loader"></div>
    </div>

    <script>
        function showLoader(event) {
            
            event.preventDefault();

            // Show loader on form submit
            document.getElementById('loader').style.display = 'block';
            
            document.getElementById('forgotPasswordForm').querySelectorAll('input, button').forEach(function(el) {
                el.disabled = true;
            });

            
            setTimeout(function() {
                document.getElementById('loader').style.display = 'none';
                alert("A password reset link has been sent to your email!");
                
                document.getElementById('forgotPasswordForm').reset();
                document.getElementById('forgotPasswordForm').querySelectorAll('input, button').forEach(function(el) {
                    el.disabled = false;
                });
            }, 2000); 
        }
    </script>
</body>
</html>
