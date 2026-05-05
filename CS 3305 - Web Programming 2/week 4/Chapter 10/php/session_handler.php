<?php
// Must be called before any HTML output!
session_start();

// Set the page title
$page_title = 'Session Handler Script';
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
        <h1>🔐 Session Handler</h1>
        
        <?php
        // Define error handling function
        function reject_session($field_name) {
            echo '<div class="error">';
            echo "❌ Invalid $field_name. Please check the validation requirements.";
            echo '</div>';
            echo '<a href="session_form.html" class="btn">← Back to Form</a>';
            exit();
        }
        
        // Check if form was submitted via POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Validate name if present
            if (isset($_POST['name'])) {
                $name = trim($_POST['name']);
                
                // Name validation - letters only
                if (!preg_match('/^[a-zA-Z]+$/', $name)) {
                    reject_session('Name - must contain only letters');
                }
            } else {
                echo '<div class="error">';
                echo '❌ Name field is required.';
                echo '</div>';
                echo '<a href="session_form.html" class="btn">← Back to Form</a>';
                exit();
            }
            
            // Validate password if present
            if (isset($_POST['password'])) {
                $password = trim($_POST['password']);
                
                // Password validation - alphanumeric, dot, underscore, min 8 chars
                if (!preg_match('/^[a-zA-Z0-9._]{8,}$/', $password)) {
                    reject_session('Password - must be at least 8 characters with letters, numbers, dots, and underscores only');
                }
            } else {
                echo '<div class="error">';
                echo '❌ Password field is required.';
                echo '</div>';
                echo '<a href="session_form.html" class="btn">← Back to Form</a>';
                exit();
            }
            
            // Both validations passed - store in session
            $_SESSION['name'] = $name;
            $_SESSION['password'] = $password;
            $_SESSION['login_time'] = time();
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            
            echo '<div class="success">';
            echo '✅ Session data stored successfully!';
            echo '</div>';
            
            echo '<div class="session-info">';
            echo '<h3>📋 Session Information</h3>';
            
            echo '<div class="session-item">';
            echo '<strong>Session ID:</strong> ' . session_id() . '<br>';
            echo '<strong>Session Status:</strong> Active<br>';
            echo '<strong>Login Time:</strong> ' . date('Y-m-d H:i:s', $_SESSION['login_time']) . '<br>';
            echo '<strong>IP Address:</strong> ' . $_SESSION['ip_address'];
            echo '</div>';
            
            echo '<div class="session-item">';
            echo '<strong>Stored Name:</strong> ' . htmlspecialchars($_SESSION['name']) . '<br>';
            echo '<strong>Password Length:</strong> ' . strlen($_SESSION['password']) . ' characters<br>';
            echo '<strong>Password Hash:</strong> ' . md5($_SESSION['password']);
            echo '</div>';
            
            echo '</div>';
            
            echo '<div class="validation-steps">';
            echo '<h3>✅ Session Storage Steps Completed:</h3>';
            echo '<ul>';
            echo '<li>✅ Session started with session_start()</li>';
            echo '<li>✅ Name validated - letters only</li>';
            echo '<li>✅ Password validated - alphanumeric, dot, underscore, min 8 chars</li>';
            echo '<li>✅ Data stored in $_SESSION superglobal</li>';
            echo '<li>✅ Session variables accessible across pages</li>';
            echo '<li>✅ Session ID assigned to user</li>';
            echo '</ul>';
            echo '</div>';
            
        } else {
            echo '<div class="error">';
            echo '❌ No form submission detected. Please use the login form.';
            echo '</div>';
            echo '<a href="session_form.html" class="btn">← Back to Form</a>';
        }
        ?>
        
        <div class="code">
            <h3>💻 Session Storage Code</h3>
            <p><strong>Session Start (Required):</strong></p>
            <code>
                <?php session_start(); ?>
            </code>
            
            <p><strong>Store Session Variables:</strong></p>
            <code>
                $_SESSION['name'] = $name;
                $_SESSION['password'] = $password;
            </code>
            
            <p><strong>Access Session Variables:</strong></p>
            <code>
                $name = $_SESSION['name'];
                $password = $_SESSION['password'];
            </code>
        </div>
        
        <div class="validation-steps">
            <h3>📋 Session vs Cookies Comparison</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr style="background: #f8f9fa;">
                    <th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Aspect</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Sessions</th>
                    <th style="padding: 10px; text-align: left; border: 1px solid #dee2e6;">Cookies</th>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Storage</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Server-side</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Client-side</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Security</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">More secure</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Less secure</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">User Control</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">No user control</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">User can disable</td>
                </tr>
                <tr>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Capacity</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">Server resources</td>
                    <td style="padding: 10px; border: 1px solid #dee2e6;">~4KB limit</td>
                </tr>
            </table>
        </div>
        
        <a href="session_view.php" class="btn">👁️ View Session Data</a>
        <a href="session_form.html" class="btn">← Back to Form</a>
        <a href="session_logout.php" class="btn btn-danger">🚪 Logout</a>
    </div>
</body>
</html>
