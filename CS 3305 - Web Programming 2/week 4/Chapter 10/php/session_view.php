<?php
// Must be called before any HTML output!
session_start();

// Set the page title
$page_title = 'Session Data Viewer';
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
            max-width: 800px;
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
        .session-display {
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
        .no-session {
            background: #fff3cd;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
        .success {
            background: #d4edda;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
            color: #155724;
        }
        .error {
            background: #f8d7da;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
            color: #721c24;
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
        .debug-info {
            background: #d1ecf1;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #bee5eb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border: 1px solid #dee2e6;
        }
        .session-stats {
            background: #e7f3ff;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #2196f3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>👁️ Session Data Viewer</h1>
        
        <?php
        // Check if session variables are set
        if (!empty($_SESSION)) {
            
            echo '<div class="success">';
            echo '✅ Active session found! Total session variables: ' . count($_SESSION);
            echo '</div>';
            
            echo '<div class="session-display">';
            echo '<h2>📋 All Stored Session Data</h2>';
            
            // Display all session variables in a table
            echo '<table>';
            echo '<tr><th>Variable Name</th><th>Value</th><th>Type</th><th>Size</th></tr>';
            
            foreach ($_SESSION as $key => $value) {
                echo '<tr>';
                echo '<td><strong>' . htmlspecialchars($key) . '</strong></td>';
                
                // Handle different variable types
                if ($key === 'password') {
                    // Show partial value for password
                    $display_value = substr($value, 0, 4) . '...';
                    $type = 'Password (masked)';
                } elseif ($key === 'login_time') {
                    $display_value = date('Y-m-d H:i:s', $value);
                    $type = 'Timestamp';
                } else {
                    $display_value = htmlspecialchars($value);
                    $type = gettype($value);
                }
                
                echo '<td>' . $display_value . '</td>';
                echo '<td>' . $type . '</td>';
                echo '<td>' . strlen($value) . ' bytes</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            echo '</div>';
            
            // Session statistics
            echo '<div class="session-stats">';
            echo '<h3>📊 Session Statistics</h3>';
            echo '<p><strong>Session ID:</strong> ' . session_id() . '</p>';
            echo '<p><strong>Session Status:</strong> Active</p>';
            echo '<p><strong>Total Variables:</strong> ' . count($_SESSION) . '</p>';
            echo '<p><strong>Total Data Size:</strong> ' . array_sum(array_map('strlen', $_SESSION)) . ' bytes</p>';
            
            if (isset($_SESSION['login_time'])) {
                $session_age = time() - $_SESSION['login_time'];
                echo '<p><strong>Session Age:</strong> ' . $session_age . ' seconds (' . floor($session_age / 60) . ' minutes)</p>';
            }
            
            echo '<p><strong>Session Save Path:</strong> ' . session_save_path() . '</p>';
            echo '</div>';
            
            // Detailed user information
            if (isset($_SESSION['name'])) {
                echo '<div class="session-item">';
                echo '<h3>👤 User Information</h3>';
                echo '<p><strong>Name:</strong> ' . htmlspecialchars($_SESSION['name']) . '</p>';
                echo '<p><strong>Name Length:</strong> ' . strlen($_SESSION['name']) . ' characters</p>';
                echo '<p><strong>Name Type:</strong> ' . (ctype_alpha($_SESSION['name']) ? 'Letters only' : 'Mixed characters') . '</p>';
                echo '<p><strong>Validation Status:</strong> ' . (preg_match('/^[a-zA-Z]+$/', $_SESSION['name']) ? '✅ Valid' : '❌ Invalid') . '</p>';
                echo '</div>';
            }
            
            // Detailed login information
            if (isset($_SESSION['login_time'])) {
                echo '<div class="session-item">';
                echo '<h3>⏰ Login Information</h3>';
                echo '<p><strong>Login Time:</strong> ' . date('Y-m-d H:i:s', $_SESSION['login_time']) . '</p>';
                echo '<p><strong>Current Time:</strong> ' . date('Y-m-d H:i:s') . '</p>';
                echo '<p><strong>Session Duration:</strong> ' . (time() - $_SESSION['login_time']) . ' seconds</p>';
                echo '<p><strong>Timestamp:</strong> ' . $_SESSION['login_time'] . '</p>';
                echo '</div>';
            }
            
            // Password information
            if (isset($_SESSION['password'])) {
                echo '<div class="session-item">';
                echo '<h3>🔐 Password Information</h3>';
                echo '<p><strong>Password Length:</strong> ' . strlen($_SESSION['password']) . ' characters</p>';
                echo '<p><strong>Password Hash:</strong> ' . md5($_SESSION['password']) . '</p>';
                echo '<p><strong>Hash Algorithm:</strong> MD5</p>';
                echo '<p><strong>Validation Status:</strong> ' . (preg_match('/^[a-zA-Z0-9._]{8,}$/', $_SESSION['password']) ? '✅ Valid' : '❌ Invalid') . '</p>';
                echo '<p><strong>Character Types:</strong> ' . (preg_match('/[a-zA-Z]/', $_SESSION['password']) ? 'Letters ' : '') . (preg_match('/[0-9]/', $_SESSION['password']) ? 'Numbers ' : '') . (preg_match('/[._]/', $_SESSION['password']) ? 'Special' : '') . '</p>';
                echo '</div>';
            }
            
            // IP address information
            if (isset($_SESSION['ip_address'])) {
                echo '<div class="session-item">';
                echo '<h3>🌐 Network Information</h3>';
                echo '<p><strong>Stored IP Address:</strong> ' . htmlspecialchars($_SESSION['ip_address']) . '</p>';
                echo '<p><strong>Current IP Address:</strong> ' . htmlspecialchars($_SERVER['REMOTE_ADDR']) . '</p>';
                echo '<p><strong>IP Match:</strong> ' . ($_SESSION['ip_address'] === $_SERVER['REMOTE_ADDR'] ? '✅ Match' : '❌ Mismatch') . '</p>';
                echo '<p><strong>User Agent:</strong> ' . htmlspecialchars($_SERVER['HTTP_USER_AGENT']) . '</p>';
                echo '</div>';
            }
            
            echo '<div class="debug-info">';
            echo '<h3>🐛 Debug Information</h3>';
            echo '<p><strong>$_SESSION Array Contents:</strong></p>';
            echo '<pre>';
            print_r($_SESSION);
            echo '</pre>';
            
            echo '<p><strong>Session Configuration:</strong></p>';
            echo '<pre>';
            echo 'Session Name: ' . session_name() . "\n";
            echo 'Session ID: ' . session_id() . "\n";
            echo 'Session Save Path: ' . session_save_path() . "\n";
            echo 'Session Status: ' . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "\n";
            echo 'Cookie Parameters: ' . print_r(session_get_cookie_params(), true);
            echo '</pre>';
            echo '</div>';
            
        } else {
            echo '<div class="no-session">';
            echo '<h2>📭 No Active Session</h2>';
            echo '<p>No session data is currently stored for this user.</p>';
            echo '<p>Please <a href="session_form.html">submit the login form</a> to create a session.</p>';
            echo '</div>';
        }
        ?>
        
        <div class="code">
            <h3>💻 Session Access Code</h3>
            <p><strong>Check Session Existence:</strong></p>
            <code>
                if (!empty($_SESSION)) {
                    echo "Session has data";
                }
            </code>
            
            <p><strong>Access Session Variables:</strong></p>
            <code>
                $name = $_SESSION['name'];
                $password = $_SESSION['password'];
                $login_time = $_SESSION['login_time'];
            </code>
            
            <p><strong>Check Specific Variable:</strong></p>
            <code>
                if (isset($_SESSION['name'])) {
                    echo "User: " . $_SESSION['name'];
                }
            </code>
            
            <p><strong>Session Information:</strong></p>
            <code>
                echo "Session ID: " . session_id();
                echo "Session Status: " . session_status();
            </code>
        </div>
        
        <div class="session-stats">
            <h3>🔒 Security Information</h3>
            <ul>
                <li><strong>Session Storage:</strong> Data is stored on the server, not in the browser</li>
                <li><strong>Session ID:</strong> Only a session identifier is stored in browser cookies</li>
                <li><strong>Data Access:</strong> Session data cannot be accessed directly by users</li>
                <li><strong>Security:</strong> More secure than cookies for sensitive information</li>
                <li><strong>Expiration:</strong> Sessions automatically expire after inactivity</li>
            </ul>
        </div>
        
        <a href="session_form.html" class="btn">← Back to Form</a>
        <a href="session_handler.php" class="btn">🔐 Session Handler</a>
        <a href="session_logout.php" class="btn btn-danger">🚪 Logout</a>
    </div>
</body>
</html>
