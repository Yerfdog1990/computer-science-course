<?php
// Must be called before any HTML output!
session_start();

// Set the page title
$page_title = 'Session Getter Script';
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
        .no-session {
            background: #fff3cd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        .session-info {
            background: #e2e3e5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .session-item {
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
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
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
        <h1>🔐 Session Getter Page</h1>
        
        <?php
        // Check if session variables are set
        if (isset($_SESSION['user'])) {
            
            $user = $_SESSION['user'];
            $pass = $_SESSION['pass'] ?? '';
            
            echo '<div class="welcome">';
            echo '<h2>🎉 Welcome Back!</h2>';
            echo '<div class="user-display">Welcome ' . htmlspecialchars($user) . '!</div>';
            echo '</div>';
            
            echo '<div class="session-info">';
            echo '<h3>📋 Your Session Information</h3>';
            
            echo '<div class="session-item">';
            echo '<strong>Username:</strong> ' . htmlspecialchars($user) . '<br>';
            echo '<strong>Password:</strong> ' . htmlspecialchars($pass) . '<br>';
            echo '<strong>Password Length:</strong> ' . strlen($pass) . ' characters<br>';
            echo '<strong>Login Time:</strong> ' . date('Y-m-d H:i:s', $_SESSION['login_time']) . '<br>';
            echo '<strong>Session Duration:</strong> ' . (time() - $_SESSION['login_time']) . ' seconds';
            echo '</div>';
            
            echo '<div class="session-item">';
            echo '<strong>Validation Status:</strong><br>';
            echo '- Username: ' . (ctype_alpha($user) ? '✅ Valid (letters only)' : '❌ Invalid') . '<br>';
            echo '- Password: ' . (preg_match('/^[A-Za-z0-9._]{8,}$/', $pass) ? '✅ Valid (pattern match)' : '❌ Invalid');
            echo '</div>';
            
            echo '</div>';
            
        } else {
            echo '<div class="no-session">';
            echo '<h2>📭 No Session Found</h2>';
            echo '<p>No session data is currently stored. Please log in to continue.</p>';
            echo '</div>';
        }
        ?>
        
        <div class="code">
            <h3>💻 Session Access Code</h3>
            <p><strong>Check Session Variable:</strong></p>
            <code>
                if (isset($_SESSION['user'])) {
                    $user = $_SESSION['user'];
                    echo "Welcome $user!";
                }
            </code>
            
            <p><strong>Access Session Data:</strong></p>
            <code>
                $username = $_SESSION['user'];
                $password = $_SESSION['pass'];
                $login_time = $_SESSION['login_time'];
            </code>
            
            <p><strong>Session Validation:</strong></p>
            <code>
                if (ctype_alpha($_SESSION['user']) && 
                    preg_match('/^[A-Za-z0-9._]{8,}$/', $_SESSION['pass'])) {
                    echo "Session data is valid";
                }
            </code>
        </div>
        
        <a href="session_form.html" class="btn">← Back to Form</a>
        <a href="session_view.php" class="btn">👁️ View Session Data</a>
        <a href="session_logout.php" class="btn btn-danger">🚪 Logout</a>
    </div>
</body>
</html>
