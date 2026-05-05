<?php
// Set the page title
$page_title = 'Cookie Setting Script';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .error {
            background: #f8d7da;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .success {
            background: #d4edda;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .info {
            background: #d1ecf1;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #bee5eb;
            color: #0c5460;
        }
        h1, h2 {
            color: #333;
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
        .code {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            margin: 10px 0;
            border: 1px solid #dee2e6;
        }
        .validation-steps {
            background: #e2e3e5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .validation-steps h3 {
            margin-top: 0;
            color: #495057;
        }
        .validation-steps ul {
            margin: 10px 0;
        }
        .validation-steps li {
            margin: 5px 0;
            padding: 5px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🍪 Cookie Setting Script</h1>

    <?php
    // Define error handling function
    function reject($field_name) {
        echo '<div class="error">';
        echo "❌ Invalid $field_name. Only alphanumeric characters are allowed.";
        echo '</div>';
        echo 'Please Log In';
        exit();
    }

    // Check if form was submitted via POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Validate username if present
        if (isset($_POST['user'])) {
            $user = trim($_POST['user']);

            if (!ctype_alnum($user)) {
                reject('User Name');
            }
        } else {
            echo '<div class="error">';
            echo '❌ User Name field is required.';
            echo '</div>';
            echo 'Please Log In';
            exit();
        }

        // Validate password if present
        if (isset($_POST['pass'])) {
            $pass = trim($_POST['pass']);

            if (!ctype_alnum($pass)) {
                reject('Password');
            }
        } else {
            echo '<div class="error">';
            echo '❌ Password field is required.';
            echo '</div>';
            echo 'Please Log In';
            exit();
        }

        // Both validations passed - set cookies
        $expiry_time = time() + 3600; // 1 hour from now

        // Set username cookie
        setcookie('user', $user, $expiry_time, '/');

        // Set password cookie with MD5 hash
        $hashed_pass = md5($pass);
        setcookie('pass', $hashed_pass, $expiry_time, '/');

        echo '<div class="success">';
        echo '✅ Cookies successfully set!';
        echo '</div>';

        echo '<div class="info">';
        echo '<strong>User:</strong> ' . htmlspecialchars($user) . '<br>';
        echo '<strong>Password Hash:</strong> ' . htmlspecialchars($hashed_pass) . '<br>';
        echo '<strong>Expires:</strong> ' . date('Y-m-d H:i:s', $expiry_time) . '<br>';
        echo '</div>';

        echo '<div class="validation-steps">';
        echo '<h3>✅ Validation Steps Completed:</h3>';
        echo '<ul>';
        echo '<li>✅ Username contains only alphanumeric characters</li>';
        echo '<li>✅ Password contains only alphanumeric characters</li>';
        echo '<li>✅ Both fields were successfully validated</li>';
        echo '<li>✅ Cookies have been set with 1-hour expiration</li>';
        echo '<li>✅ Password has been securely hashed using MD5</li>';
        echo '</ul>';
        echo '</div>';

        // Redirect to cookie retrieval page after 3 seconds
        echo '<div class="code">';
        echo '<h3>🔄 Redirecting...</h3>';
        echo '<p>You will be redirected to cookie retrieval page in 3 seconds.</p>';
        echo '<p><strong>Manual Link:</strong> <a href="cookie_get.php">View Cookies Now</a></p>';
        echo '</div>';

        // JavaScript redirect
        echo '<script>';
        echo 'setTimeout(function() {';
        echo '    window.location.href = "cookie_get.php";';
        echo '}, 3000);';
        echo '</script>';

    } else {
        // Form not submitted - redirect to form page
        header('Location: setting_cookie_form.html');
        exit();
    }
    ?>

    <div class="validation-steps">
        <h3>📋 Validation Process</h3>
        <p>This script performs the following validation steps:</p>
        <ul>
            <li><strong>Check Request Method:</strong> Ensures data is submitted via POST</li>
            <li><strong>Field Presence:</strong> Uses isset() to verify required fields</li>
            <li><strong>Input Sanitization:</strong> Uses trim() to remove whitespace</li>
            <li><strong>Character Validation:</strong> Uses ctype_alnum() for alphanumeric check</li>
            <li><strong>Error Handling:</strong> Calls reject() function on validation failure</li>
            <li><strong>Cookie Setting:</strong> Stores validated data in secure cookies</li>
            <li><strong>Password Hashing:</strong> Uses md5() to hash password data</li>
            <li><strong>Redirection:</strong> Uses header() to redirect on success</li>
        </ul>
    </div>

    <div class="code">
        <h3>💻 Key Functions Used</h3>
        <p><strong>ctype_alnum() validation:</strong></p>
        <code>
            if (!ctype_alnum($username)) {
            reject('User Name');
            }
        </code>

        <p><strong>MD5 Password Hashing:</strong></p>
        <code>
            $hashed_pass = md5($password);
            setcookie('pass', $hashed_pass, $expiry_time, '/');
        </code>

        <p><strong>Cookie Setting:</strong></p>
        <code>
            setcookie('user', $username, time() + 3600, '/');
        </code>

        <p><strong>Header Redirection:</strong></p>
        <code>
            header('Location: cookie_get.php');
        </code>
    </div>

    <a href="setting_cookie_form.html" class="btn">← Back to Form</a>
</div>
</body>
</html>
