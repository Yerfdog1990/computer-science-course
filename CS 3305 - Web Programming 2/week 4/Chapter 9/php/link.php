<?php
// Set the page title dynamically
$page_title = 'Link Data Example - Animal Selector';
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
        .selection {
            background: #e8f5e8;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #dc3545;
        }
        .links {
            margin: 20px 0;
        }
        .links a {
            display: inline-block;
            margin: 5px 10px;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .links a:hover {
            background: #0056b3;
        }
        .debug {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            font-family: monospace;
            font-size: 14px;
        }
        h1, h2 {
            color: #333;
        }
        .animal-info {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 20px;
            margin: 20px 0;
        }
        .animal-icon {
            font-size: 60px;
            text-align: center;
        }
        .animal-details h3 {
            color: #28a745;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🐾 Animal Selector Demo</h1>
        <p>Click on the links below to see how URL parameters work in PHP.</p>
        
        <?php
        // Check if the 'id' parameter exists in the URL
        if (isset($_GET['id'])) {
            // Assign the passed value to a variable
            $id = $_GET['id'];
            
            // Validate the input (security measure)
            $id = filter_var($id, FILTER_VALIDATE_INT);
            
            if ($id === false) {
                echo '<div class="error">❌ Invalid ID parameter. Please use a valid number.</div>';
            } else {
                echo '<div class="selection">';
                
                // Output appropriate response based on the value
                switch($id) {
                    case 1:
                        echo '<h2>🐄 Cow Selected</h2>';
                        echo '<div class="animal-info">';
                        echo '<div class="animal-icon">🐄</div>';
                        echo '<div class="animal-details">';
                        echo '<h3>About Cows</h3>';
                        echo '<p>Cows are domesticated cattle raised for milk, meat, and as draft animals. They are herbivores and are known for their gentle nature.</p>';
                        echo '<p><strong>Facts:</strong> Cows can produce up to 6-7 gallons of milk per day!</p>';
                        echo '</div>';
                        echo '</div>';
                        break;
                        
                    case 2:
                        echo '<h2>🐕 Dog Selected</h2>';
                        echo '<div class="animal-info">';
                        echo '<div class="animal-icon">🐕</div>';
                        echo '<div class="animal-details">';
                        echo '<h3>About Dogs</h3>';
                        echo '<p>Dogs are domesticated mammals known as "man\'s best friend." They come in many breeds and are loyal companions.</p>';
                        echo '<p><strong>Facts:</strong> Dogs have been human companions for over 15,000 years!</p>';
                        echo '</div>';
                        echo '</div>';
                        break;
                        
                    case 3:
                        echo '<h2>🐐 Goat Selected</h2>';
                        echo '<div class="animal-info">';
                        echo '<div class="animal-icon">🐐</div>';
                        echo '<div class="animal-details">';
                        echo '<h3>About Goats</h3>';
                        echo '<p>Goats are domesticated animals raised for milk, meat, and fiber. They are curious and intelligent animals.</p>';
                        echo '<p><strong>Facts:</strong> Goats were one of the first animals to be domesticated by humans!</p>';
                        echo '</div>';
                        echo '</div>';
                        break;
                        
                    default:
                        echo '<h2>❓ Unknown Animal</h2>';
                        echo '<p>You selected ID: ' . htmlspecialchars($id) . '</p>';
                        echo '<p>This ID doesn\'t correspond to any known animal in our database.</p>';
                        break;
                }
                
                echo '</div>';
            }
        }
        ?>
        
        <div class="links">
            <h2>Select a Buddy</h2>
            <a href="link.php?id=1">🐄 Cow</a>
            <a href="link.php?id=2">🐕 Dog</a>
            <a href="link.php?id=3">🐐 Goat</a>
        </div>
        
        <?php
        // Debug section to show all GET parameters
        if (!empty($_GET)) {
            echo '<div class="debug">';
            echo '<h3>🔍 Debug Information</h3>';
            echo '<p><strong>Current URL Parameters:</strong></p>';
            echo '<ul>';
            foreach ($_GET as $key => $value) {
                echo '<li><strong>' . htmlspecialchars($key) . ':</strong> ' . htmlspecialchars($value) . '</li>';
            }
            echo '</ul>';
            echo '<p><strong>Full Query String:</strong> ' . htmlspecialchars($_SERVER['QUERY_STRING']) . '</p>';
            echo '</div>';
        }
        ?>
        
        <div class="debug">
            <h3>📚 How This Works</h3>
            <p>When you click on the links above, the browser appends parameters to the URL:</p>
            <ul>
                <li><strong>Cow link:</strong> link.php?id=1</li>
                <li><strong>Dog link:</strong> link.php?id=2</li>
                <li><strong>Goat link:</strong> link.php?id=3</li>
            </ul>
            <p>The PHP script then reads these parameters using the $_GET superglobal array.</p>
        </div>
    </div>
</body>
</html>
