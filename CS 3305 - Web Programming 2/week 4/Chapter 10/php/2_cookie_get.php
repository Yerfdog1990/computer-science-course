<?php
// Set the page title
$page_title = 'Cookie Retrieval Script';
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
            max-width: 700px;
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
        .welcome {
            background: #d4edda;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .no-cookie {
            background: #fff3cd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        .cookie-info {
            background: #e2e3e5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .cookie-item {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            border-left: 4px solid #007bff;
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
        .user-display {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>🍪 Cookie Retrieval Page</h1>

    <?php
    // Check if the user cookie is present
    if (isset($_COOKIE['user'])) {

        // Retrieve the username from cookie
        $user = $_COOKIE['user'];

        echo '<div class="welcome">';
        echo '<h2>🎉 Welcome Back!</h2>';
        echo '<div class="user-display">Welcome ' . htmlspecialchars($user) . '!</div>';
        echo '</div>';

        // Display cookie information
        echo '<div class="cookie-info">';
        echo '<h3>📋 Your Stored Information</h3>';

        echo '<div class="cookie-item">';
        echo '<strong>Username:</strong> ' . htmlspecialchars($user) . '<br>';
        echo '<strong>Cookie Name:</strong> user<br>';
        echo '<strong>Value Retrieved:</strong> ' . htmlspecialchars($_COOKIE['user']) . '<br>';
        echo '<strong>Cookie Status:</strong> ✅ Present and accessible';
        echo '</div>';

        // Check if password cookie is also present
        if (isset($_COOKIE['pass'])) {
            echo '<div class="cookie-item">';
            echo '<strong>Password Hash:</strong> ' . htmlspecialchars(substr($_COOKIE['pass'], 0, 8)) . '...<br>';
            echo '<strong>Cookie Name:</strong> pass<br>';
            echo '<strong>Hash Algorithm:</strong> MD5<br>';
            echo '<strong>Cookie Status:</strong> ✅ Present (hashed for security)';
            echo '</div>';
        }

        echo '</div>';

        echo '<a href="cookie_data.admin" class="btn">🔍 View Cookie Data</a>';

    } else {
        // No user cookie found
        echo '<div class="no-cookie">';
        echo '<h2>📭 No User Session Found</h2>';
        echo '<p>No user cookie is present. Please log in to continue.</p>';
        echo '</div>';

        echo '<div class="cookie-info">';
        echo '<h3>🔍 What This Means:</h3>';
        echo '<ul>';
        echo '<li>You haven\'t logged in yet, or your session has expired</li>';
        echo '<li>The user cookie was not found in your browser</li>';
        echo '<li>You need to submit the login form to set cookies</li>';
        echo '</ul>';
        echo '</div>';

        echo 'Please Log In';
    }
    ?>

    <div class="code">
        <h3>💻 Cookie Retrieval Code</h3>
        <p><strong>Basic Cookie Check:</strong></p>
        <code>
            if (isset($_COOKIE['user'])) {
            $user = $_COOKIE['user'];
            echo "Welcome $user!";
            } else {
            echo "Please Log In";
            }
        </code>

        <p><strong>Multiple Cookie Check:</strong></p>
        <code>
            if (isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
            // Both cookies are present
            $username = $_COOKIE['user'];
            $password_hash = $_COOKIE['pass'];
            }
        </code>
    </div>

    <a href="setting_cookie_form.html" class="btn">← Back to Form</a>
    <a href="cookie_set.php" class="btn">🔐 Login</a>
</div>
</body>
</html>
