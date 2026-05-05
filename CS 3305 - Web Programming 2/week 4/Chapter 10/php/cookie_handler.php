<?php
// Set the page title
$page_title = 'Cookie Handler - Data Storage';
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
        .success {
            background: #d4edda;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .cookie-info {
            background: #e2e3e5;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .cookie-list {
            list-style: none;
            padding: 0;
        }
        .cookie-list li {
            background: #f8f9fa;
            padding: 10px;
            margin: 5px 0;
            border-radius: 3px;
            border-left: 3px solid #007bff;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>🍪 Cookie Processing Results</h1>
        
        <?php
        // Check if the form was submitted using POST method
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            // Check if username field is present and not empty
            if (isset($_POST['username']) && !empty($_POST['username'])) {
                
                // Validate username - alphanumeric only
                $username = $_POST['username'];
                
                if (ctype_alnum($username)) {
                    // Set username cookie for 1 hour (3600 seconds)
                    $expiry_time = time() + 3600;
                    setcookie('username', $username, $expiry_time, '/', '', false, true);
                    
                    echo '<div class="success">';
                    echo '✅ Username cookie successfully set!';
                    echo '</div>';
                } else {
                    echo '<div class="error">';
                    echo '❌ Username must contain only alphanumeric characters.';
                    echo '</div>';
                }
            } else {
                echo '<div class="error">';
                echo '❌ Username field is required.';
                echo '</div>';
            }
            
            // Check if password field is present and not empty
            if (isset($_POST['password']) && !empty($_POST['password'])) {
                
                // Store password (note: this is for demonstration only)
                $password = $_POST['password'];
                $expiry_time = time() + 3600;
                setcookie('user_password', $password, $expiry_time, '/', '', false, true);
                
                echo '<div class="success">';
                echo '✅ Password cookie successfully set!';
                echo '</div>';
                
                echo '<div class="warning" style="background: #fff3cd; border-left: 4px solid #ffc107;">';
                echo '⚠️ <strong>Security Warning:</strong> Storing passwords in cookies is not recommended for production applications.';
                echo '</div>';
            } else {
                echo '<div class="error">';
                echo '❌ Password field is required.';
                echo '</div>';
            }
        }
        
        // Display current cookies
        if (!empty($_COOKIE)) {
            echo '<div class="cookie-info">';
            echo '<h2>📋 Currently Stored Cookies</h2>';
            echo '<ul class="cookie-list">';
            
            foreach ($_COOKIE as $name => $value) {
                echo '<li>';
                echo '<strong>' . htmlspecialchars($name) . ':</strong> ' . htmlspecialchars($value);
                echo '</li>';
            }
            
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="cookie-info">';
            echo '<h2>📋 No Cookies Found</h2>';
            echo '<p>No cookies are currently stored on your browser for this website.</p>';
            echo '</div>';
        }
        ?>
        
        <div class="cookie-info">
            <h2>🔍 Cookie Information</h2>
            <p><strong>Cookie Details:</strong></p>
            <ul>
                <li><strong>Expiry Time:</strong> 1 hour from creation</li>
                <li><strong>Path:</strong> Entire website (/)</li>
                <li><strong>Secure:</strong> No (can be sent over HTTP)</li>
                <li><strong>HTTP Only:</strong> Yes (not accessible via JavaScript)</li>
            </ul>
        </div>
        
        <div class="code">
            <h3>💻 Code Explanation</h3>
            <p><strong>Setting Cookies:</strong></p>
            <code>
                setcookie('username', $username, time() + 3600, '/', '', false, true);
            </code>
            <p><strong>Retrieving Cookies:</strong></p>
            <code>
                $username = $_COOKIE['username'];
            </code>
        </div>
        
        <a href="setting_cookie_form.html" class="btn">← Back to Form</a>
        <a href="2_cookie_get.php" class="btn">Test Cookie Retrieval</a>
    </div>
</body>
</html>
