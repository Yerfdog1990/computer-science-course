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
    </style>
</head>
<body>
    <div class="container">
        <h1>👁️ Session Data Viewer</h1>
        
        <?php
        // Check if any session variables are set
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
                if ($key === 'pass') {
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
            
            // Detailed user information
            if (isset($_SESSION['user'])) {
                echo '<div class="session-item">';
                echo '<h3>👤 User Information</h3>';
                echo '<p><strong>Username:</strong> ' . htmlspecialchars($_SESSION['user']) . '</p>';
                echo '<p><strong>Username Length:</strong> ' . strlen($_SESSION['user']) . ' characters</p>';
                echo '<p><strong>Character Type:</strong> ' . (ctype_alpha($_SESSION['user']) ? 'Letters only' : 'Mixed characters') . '</p>';
                echo '<p><strong>Validation Status:</strong> ' . (preg_match('/^[a-zA-Z]+$/', $_SESSION['user']) ? '✅ Valid' : '❌ Invalid') . '</p>';
                echo '</div>';
            }
            
            // Password information
            if (isset($_SESSION['pass'])) {
                echo '<div class="session-item">';
                echo '<h3>🔐 Password Information</h3>';
                echo '<p><strong>Password Length:</strong> ' . strlen($_SESSION['pass']) . ' characters</p>';
                echo '<p><strong>Password Pattern:</strong> ' . (preg_match('/^[A-Za-z0-9._]{8,}$/', $_SESSION['pass']) ? '✅ Valid' : '❌ Invalid') . '</p>';
                echo '<p><strong>Contains Numbers:</strong> ' . (preg_match('/[0-9]/', $_SESSION['pass']) ? '✅ Yes' : '❌ No') . '</p>';
                echo '<p><strong>Contains Letters:</strong> ' . (preg_match('/[a-zA-Z]/', $_SESSION['pass']) ? '✅ Yes' : '❌ No') . '</p>';
                echo '<p><strong>Contains Special:</strong> ' . (preg_match('/[._]/', $_SESSION['pass']) ? '✅ Yes' : '❌ No') . '</p>';
                echo '</div>';
            }
            
            // Session metadata
            echo '<div class="session-item">';
            echo '<h3>📊 Session Metadata</h3>';
            echo '<p><strong>Session ID:</strong> ' . session_id() . '</p>';
            echo '<p><strong>Session Status:</strong> Active</p>';
            echo '<p><strong>Total Variables:</strong> ' . count($_SESSION) . '</p>';
            echo '<p><strong>Total Data Size:</strong> ' . array_sum(array_map('strlen', $_SESSION)) . ' bytes</p>';
            
            if (isset($_SESSION['login_time'])) {
                $session_age = time() - $_SESSION['login_time'];
                echo '<p><strong>Session Age:</strong> ' . $session_age . ' seconds (' . floor($session_age / 60) . ' minutes)</p>';
            }
            
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
            <h3>💻 Session Data Access Code</h3>
            <p><strong>Check Session Existence:</strong></p>
            <code>
                if (!empty($_SESSION)) {
                    echo "Session has " . count($_SESSION) . " variables";
                }
            </code>
            
            <p><strong>Iterate Session Variables:</strong></p>
            <code>
                foreach ($_SESSION as $key => $value) {
                    echo "$key => $value<br>";
                }
            </code>
            
            <p><strong>Access Specific Variables:</strong></p>
            <code>
                $username = $_SESSION['user'] ?? 'Guest';
                $password = $_SESSION['pass'] ?? '';
                $login_time = $_SESSION['login_time'] ?? time();
            </code>
        </div>
        
        <a href="session_get.php" class="btn">← Back to Session Getter</a>
        <a href="session_form.html" class="btn">🔐 Login Form</a>
        <a href="session_logout.php" class="btn btn-danger">🚪 Logout</a>
    </div>
</body>
</html>
