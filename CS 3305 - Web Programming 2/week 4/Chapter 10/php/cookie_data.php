<?php
// Set the page title
$page_title = 'Cookie Data Viewer';
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
        .cookie-display {
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
        .no-cookies {
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
        .code {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            font-family: monospace;
            margin: 10px 0;
            border: 1px solid #dee2e6;
        }
        .hash-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border: 1px solid #dee2e6;
        }
        .warning {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
            border-left: 4px solid #ffc107;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🍪 Cookie Data Viewer</h1>
        
        <?php
        // Check if any cookies are set
        if (!empty($_COOKIE)) {
            
            echo '<div class="success">';
            echo '✅ Cookies found! Total cookies: ' . count($_COOKIE);
            echo '</div>';
            
            echo '<div class="cookie-display">';
            echo '<h2>📋 All Stored Cookies</h2>';
            
            // Display all cookies in a table
            echo '<table>';
            echo '<tr><th>Cookie Name</th><th>Cookie Value</th><th>Length</th><th>Type</th></tr>';
            
            foreach ($_COOKIE as $name => $value) {
                echo '<tr>';
                echo '<td><strong>' . htmlspecialchars($name) . '</strong></td>';
                
                // Handle different cookie types
                if ($name === 'pass') {
                    // Show partial hash for password
                    $display_value = substr($value, 0, 8) . '...';
                    $type = 'Password Hash (MD5)';
                } else {
                    $display_value = htmlspecialchars($value);
                    $type = 'Plain Text';
                }
                
                echo '<td>' . $display_value . '</td>';
                echo '<td>' . strlen($value) . ' characters</td>';
                echo '<td>' . $type . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            echo '</div>';
            
            // Detailed user information
            if (isset($_COOKIE['user'])) {
                echo '<div class="cookie-item">';
                echo '<h3>👤 User Cookie Details</h3>';
                echo '<p><strong>Cookie Name:</strong> user</p>';
                echo '<p><strong>Stored Value:</strong> ' . htmlspecialchars($_COOKIE['user']) . '</p>';
                echo '<p><strong>Value Length:</strong> ' . strlen($_COOKIE['user']) . ' characters</p>';
                echo '<p><strong>Character Type:</strong> ' . (ctype_alnum($_COOKIE['user']) ? 'Alphanumeric' : 'Mixed') . '</p>';
                echo '<p><strong>Is Valid Username:</strong> ' . (ctype_alnum($_COOKIE['user']) && strlen($_COOKIE['user']) >= 3 ? '✅ Yes' : '❌ No') . '</p>';
                echo '</div>';
            }
            
            // Detailed password information
            if (isset($_COOKIE['pass'])) {
                echo '<div class="cookie-item">';
                echo '<h3>🔐 Password Cookie Details</h3>';
                echo '<p><strong>Cookie Name:</strong> pass</p>';
                echo '<p><strong>Full Hash:</strong> ' . htmlspecialchars($_COOKIE['pass']) . '</p>';
                echo '<p><strong>Hash Length:</strong> ' . strlen($_COOKIE['pass']) . ' characters</p>';
                echo '<p><strong>Hash Algorithm:</strong> MD5</p>';
                echo '<p><strong>Hash Type:</strong> One-way cryptographic hash</p>';
                echo '</div>';
                
                echo '<div class="hash-info">';
                echo '<h3>🔍 About This MD5 Hash</h3>';
                echo '<p><strong>Original Password:</strong> Cannot be recovered (one-way hash)</p>';
                echo '<p><strong>Purpose:</strong> Secure storage of password data</p>';
                echo '<p><strong>Validation:</strong> Compare hashes, not plain passwords</p>';
                echo '<p><strong>Security Note:</strong> MD5 is educational - use bcrypt for production</p>';
                echo '</div>';
            }
            
            echo '<div class="debug-info">';
            echo '<h3>🐛 Debug Information</h3>';
            echo '<p><strong>$_COOKIE Array Contents:</strong></p>';
            echo '<pre>';
            print_r($_COOKIE);
            echo '</pre>';
            echo '</div>';
            
        } else {
            echo '<div class="no-cookies">';
            echo '<h2>📭 No Cookies Found</h2>';
            echo '<p>No cookies are currently stored in your browser for this website.</p>';
            echo '<p>Please <a href="setting_cookie_form.html">submit the login form</a> to set cookies first.</p>';
            echo '</div>';
        }
        ?>
        
        <div class="code">
            <h3>💻 Cookie Access Methods</h3>
            <p><strong>Individual Cookie Access:</strong></p>
            <code>
                $username = $_COOKIE['user'];
                $password_hash = $_COOKIE['pass'];
            </code>
            
            <p><strong>Check Cookie Existence:</strong></p>
            <code>
                if (isset($_COOKIE['user'])) {
                    echo "User cookie exists";
                }
            </code>
            
            <p><strong>Iterate All Cookies:</strong></p>
            <code>
                foreach ($_COOKIE as $name => $value) {
                    echo "$name = $value\n";
                }
            </code>
        </div>
        
        <div class="warning">
            <h3>⚠️ Important Security Notes</h3>
            <ul>
                <li><strong>Cookie Visibility:</strong> Cookies are stored on client machine</li>
                <li><strong>Data Integrity:</strong> Never trust cookie data completely</li>
                <li><strong>Validation:</strong> Always validate cookie values before use</li>
                <li><strong>Expiration:</strong> Cookies expire automatically after set time</li>
                <li><strong>Security:</strong> Use HTTPS and HTTPOnly flags in production</li>
            </ul>
        </div>
        
        <a href="cookie_get.php" class="btn">← Back to Cookie Page</a>
        <a href="setting_cookie_form.html" class="btn">🔐 Login Form</a>
    </div>
</body>
</html>
