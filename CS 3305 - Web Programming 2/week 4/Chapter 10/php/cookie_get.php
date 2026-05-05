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
    </style>
</head>
<body>
    <div class="container">
        <h1>🍪 Cookie Retrieval Script</h1>
        
        <?php
        // Check if cookies are set
        if (isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
            
            $username = $_COOKIE['user'];
            $password_hash = $_COOKIE['pass'];
            
            echo '<div class="success">';
            echo '✅ Cookies found and retrieved successfully!';
            echo '</div>';
            
            echo '<div class="cookie-display">';
            echo '<h2>📋 Stored Cookie Data</h2>';
            
            echo '<div class="cookie-item">';
            echo '<h3>👤 User Information</h3>';
            echo '<p><strong>Username:</strong> ' . htmlspecialchars($username) . '</p>';
            echo '<p><strong>Cookie Name:</strong> user</p>';
            echo '<p><strong>Cookie Value:</strong> ' . htmlspecialchars($username) . '</p>';
            echo '</div>';
            
            echo '<div class="cookie-item">';
            echo '<h3>🔐 Password Information</h3>';
            echo '<p><strong>Password Hash:</strong> ' . htmlspecialchars($password_hash) . '</p>';
            echo '<p><strong>Cookie Name:</strong> pass</p>';
            echo '<p><strong>Hash Algorithm:</strong> MD5</p>';
            echo '<p><strong>Hash Length:</strong> ' . strlen($password_hash) . ' characters</p>';
            echo '</div>';
            
            echo '</div>';
            
            echo '<div class="hash-info">';
            echo '<h3>🔍 About MD5 Hashing</h3>';
            echo '<p><strong>Original Password:</strong> Not stored (only hash is kept)</p>';
            echo '<p><strong>Security Note:</strong> MD5 is one-way hashing - original password cannot be recovered</p>';
            echo '<p><strong>Modern Security:</strong> MD5 is considered weak for production applications</p>';
            echo '<p><strong>Recommended:</strong> Use bcrypt or Argon2 for real applications</p>';
            echo '</div>';
            
        } else {
            echo '<div class="no-cookies">';
            echo '<h2>📭 No Cookies Found</h2>';
            echo '<p>No cookies are currently stored for this website.</p>';
            echo '<p>Please <a href="setting_cookie_form.html">submit the form</a> to set cookies first.</p>';
            echo '</div>';
        }
        ?>
        
        <div class="code">
            <h3>💻 Cookie Retrieval Code</h3>
            <p><strong>Accessing Cookie Values:</strong></p>
            <code>
                $username = $_COOKIE['user'];
                $password_hash = $_COOKIE['pass'];
            </code>
            
            <p><strong>Checking Cookie Existence:</strong></p>
            <code>
                if (isset($_COOKIE['user']) && isset($_COOKIE['pass'])) {
                    // Process cookies
                }
            </code>
        </div>
        
        <div class="warning">
            <h3>⚠️ Important Security Notes</h3>
            <ul>
                <li><strong>MD5 Weakness:</strong> MD5 is vulnerable to collision attacks</li>
                <li><strong>Rainbow Tables:</strong> MD5 hashes can be pre-computed</li>
                <li><strong>Modern Alternatives:</strong> Use password_hash() with bcrypt or Argon2</li>
                <li><strong>Cookie Security:</strong> Always use HTTPS and HTTPOnly flags</li>
                <li><strong>Storage:</strong> Never store plain passwords in production</li>
            </ul>
        </div>
        
        <a href="setting_cookie_form.html" class="btn">← Back to Form</a>
        <a href="cookie_set.php" class="btn">Set New Cookies</a>
    </div>
</body>
</html>
