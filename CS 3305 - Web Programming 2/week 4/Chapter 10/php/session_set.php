<?php
// Must be called before any HTML output!
session_start();

// Set the page title
$page_title = 'Session Setter Script';

// Define error handling function
function reject($field_name) {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation Error</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .error {
            background: #f8d7da;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 10px 5px 0 0;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>❌ Validation Error</h1>
        <div class="error">
            <h3>Invalid ' . htmlspecialchars($field_name) . '</h3>
            <p>Please check the validation requirements and try again.</p>
        </div>
        <p>Please Log In</p>
        <a href="session_form.html" class="btn">← Back to Form</a>
    </div>
</body>
</html>';
    exit();
}

// Check if form was submitted via POST
if (isset($_POST['user'])) {
    
    // Validate user field
    $user = trim($_POST['user']);
    
    // Check if user name contains only letters
    if (!ctype_alpha($user)) {
        reject('User Name - must contain only letters');
    }
    
    // Validate password field
    if (isset($_POST['pass'])) {
        $pass = trim($_POST['pass']);
        
        // Check password pattern: letters, numbers, dots, underscores, min 8 chars
        if (!preg_match('/^[A-Za-z0-9._]{8,}$/', $pass)) {
            reject('Password - must be at least 8 characters with letters, numbers, dots, and underscores only');
        }
        
        // Both validations passed - store in session
        $_SESSION['user'] = $user;
        $_SESSION['pass'] = $pass;
        $_SESSION['login_time'] = time();
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
        
        // Redirect to session getter page
        header('Location: session_get.admin');
        exit();
        
    } else {
        reject('Password field is required');
    }
    
} else {
    // No form submission - redirect to form
    header('Location: session_form.html');
    exit();
}
?>
