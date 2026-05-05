<?php
// Must be called before any HTML output!
session_start();

// Set the page title
$page_title = 'Session Logout';
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
        .warning {
            background: #fff3cd;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        .logout-info {
            background: #e2e3e5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .logout-item {
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
        <h1>🚪 Session Logout</h1>
        
        <?php
        // Check if session exists before logout
        $session_existed = !empty($_SESSION);
        $user_name = $_SESSION['name'] ?? 'Unknown User';
        $session_id = session_id();
        $login_time = $_SESSION['login_time'] ?? 0;
        $session_duration = $login_time > 0 ? time() - $login_time : 0;
        
        // Perform logout operations
        if ($session_existed) {
            
            // Store session info for display before destroying
            $session_data = $_SESSION;
            
            // Destroy session
            session_destroy();
            
            // Clear session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            echo '<div class="success">';
            echo '✅ Session successfully terminated!';
            echo '</div>';
            
            echo '<div class="logout-info">';
            echo '<h2>📋 Logout Summary</h2>';
            
            echo '<div class="logout-item">';
            echo '<h3>👤 User Information</h3>';
            echo '<p><strong>User Name:</strong> ' . htmlspecialchars($user_name) . '</p>';
            echo '<p><strong>Session ID:</strong> ' . htmlspecialchars($session_id) . '</p>';
            echo '<p><strong>Session Duration:</strong> ' . $session_duration . ' seconds (' . floor($session_duration / 60) . ' minutes)</p>';
            echo '</div>';
            
            echo '<div class="logout-item">';
            echo '<h3>🗑️ Cleared Data</h3>';
            echo '<p><strong>Variables Cleared:</strong> ' . count($session_data) . '</p>';
            echo '<p><strong>Data Types:</strong> ' . implode(', ', array_unique(array_map('gettype', $session_data))) . '</p>';
            echo '<p><strong>Total Size:</strong> ' . array_sum(array_map('strlen', $session_data)) . ' bytes</p>';
            echo '</div>';
            
            echo '<div class="logout-item">';
            echo '<h3>🔒 Security Actions</h3>';
            echo '<p><strong>Session Destroyed:</strong> ✅ Yes</p>';
            echo '<p><strong>Session Cookie Cleared:</strong> ✅ Yes</p>';
            echo '<p><strong>Server Data Removed:</strong> ✅ Yes</p>';
            echo '<p><strong>Session ID Invalidated:</strong> ✅ Yes</p>';
            echo '</div>';
            
            echo '</div>';
            
        } else {
            
            echo '<div class="warning">';
            echo '⚠️ No active session was found to logout.';
            echo '</div>';
            
            echo '<div class="logout-info">';
            echo '<h2>📋 Logout Status</h2>';
            
            echo '<div class="logout-item">';
            echo '<h3>🔍 Session Check</h3>';
            echo '<p><strong>Session Status:</strong> No active session</p>';
            echo '<p><strong>Session Variables:</strong> None found</p>';
            echo '<p><strong>Current Session ID:</strong> ' . htmlspecialchars($session_id) . '</p>';
            echo '<p><strong>Recommendation:</strong> Login first to create a session</p>';
            echo '</div>';
            
            echo '</div>';
        }
        ?>
        
        <div class="code">
            <h3>💻 Logout Code Explanation</h3>
            <p><strong>Session Destruction:</strong></p>
            <code>
                session_destroy();
            </code>
            
            <p><strong>Clear Session Cookie:</strong></p>
            <code>
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            </code>
            
            <p><strong>Complete Logout Function:</strong></p>
            <code>
                function logout_user() {
                    session_start();
                    session_destroy();
                    
                    // Clear session cookie
                    if (ini_get("session.use_cookies")) {
                        $params = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000,
                            $params["path"], $params["domain"],
                            $params["secure"], $params["httponly"]
                        );
                    }
                }
            </code>
        </div>
        
        <div class="logout-info">
            <h3>🔒 Security Best Practices</h3>
            <ul>
                <li><strong>Always destroy sessions</strong> when users logout</li>
                <li><strong>Clear session cookies</strong> to prevent session fixation</li>
                <li><strong>Regenerate session IDs</strong> after login for security</li>
                <li><strong>Set appropriate timeouts</strong> for automatic session expiration</li>
                <li><strong>Use HTTPS</strong> to protect session data in transit</li>
                <li><strong>Validate session integrity</strong> on each page load</li>
            </ul>
        </div>
        
        <div class="logout-info">
            <h3>📚 Session Management Information</h3>
            <p><strong>What happens during logout:</strong></p>
            <ul>
                <li>All session variables are destroyed</li>
                <li>Session file is deleted from server</li>
                <li>Session cookie is cleared from browser</li>
                <li>Session ID becomes invalid</li>
                <li>User must login again to access protected content</li>
            </ul>
            
            <p><strong>Why logout is important:</strong></p>
            <ul>
                <li>Prevents unauthorized access on shared computers</li>
                <li>Frees up server resources</li>
                <li>Protects user privacy</li>
                <li>Ensures clean session state for next login</li>
            </ul>
        </div>
        
        <?php if ($session_existed): ?>
            <div class="code">
                <h3>🐛 Debug Information</h3>
                <p><strong>Session Before Logout:</strong></p>
                <pre>
<?php
foreach ($session_data as $key => $value) {
    if ($key === 'password') {
        echo "$key => " . substr($value, 0, 4) . "...\n";
    } elseif ($key === 'login_time') {
        echo "$key => " . date('Y-m-d H:i:s', $value) . "\n";
    } else {
        echo "$key => $value\n";
    }
}
?>
                </pre>
                
                <p><strong>Session After Logout:</strong></p>
                <pre>
Session Status: <?php echo session_status() === PHP_SESSION_NONE ? 'None' : (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Disabled'); ?>
Session Variables: <?php echo count($_SESSION); ?>
Session ID: <?php echo session_id(); ?>
                </pre>
            </div>
        <?php endif; ?>
        
        <a href="session_form.html" class="btn">🔐 Login Again</a>
        <a href="session_view.php" class="btn">👁️ Check Session</a>
        <a href="session_handler.php" class="btn">📋 Session Handler</a>
    </div>
</body>
</html>
