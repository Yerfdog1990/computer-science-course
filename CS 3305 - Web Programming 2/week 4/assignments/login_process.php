<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Processing</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .error {
            color: red;
            margin-bottom: 10px;
        }
        .back-link {
            display: block;
            margin-top: 20px;
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        // Debug: Check if form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<!-- Debug: Form submitted successfully -->";
            // Initialize error array
            $errors = [];
            
            // Validate username
            if (empty($_POST['username'])) {
                $errors['username'] = "Username is required";
            } elseif (!preg_match('/^[a-zA-Z0-9]{4,20}$/', $_POST['username'])) {
                $errors['username'] = "Username must be 4-20 alphanumeric characters";
            }
            
            // Validate and sanitize email
            if (empty($_POST['email'])) {
                $errors['email'] = "Email is required";
            } else {
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = "Invalid email format";
                }
            }
            
            // Validate password
            if (empty($_POST['password'])) {
                $errors['password'] = "Password is required";
            } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*]{8,}$/', $_POST['password'])) {
                $errors['password'] = "Password must be 8+ chars with letters and numbers";
            }
            
            // Confirm password match
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $errors['confirm_password'] = "Passwords do not match";
            }
            
            // Process registration if no errors
            if (empty($errors)) {
                // Sanitize all inputs before database insertion
                $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                // Hash password for secure storage
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

                // Database insertion would go here with prepared statements
                // Redirect to success page
                header("Location: registration_success.html");
                exit();
            } else {
                echo "<h2>Registration Errors</h2>";
                // Display validation errors
                foreach ($errors as $error) {
                    echo "<p class='error'>$error</p>";
                }
                echo "<a href='register.html' class='back-link'>← Back to Registration</a>";
            }
        } else {
            echo "<h2>Access Denied</h2>";
            echo "<p>Please submit the registration form first.</p>";
            echo "<a href='register.html' class='back-link'>← Go to Registration</a>";
        }
        ?>
    </div>
</body>
</html>
