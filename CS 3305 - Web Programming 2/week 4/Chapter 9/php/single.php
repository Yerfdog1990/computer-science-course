<!DOCTYPE html>
<html lang="en">
<head>
    <title>Single-Page Form Handler</title>
</head>
<body>
<?php
// Examine the page request method
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Display the form when requested by GET method
    echo '<h2>Send us your comments</h2>';
    echo '<form action="single.php" method="POST">';
    echo '<p><textarea name="comment" rows="4" cols="50"></textarea></p>';
    echo '<p><input type="submit" value="Submit"></p>';
    echo '</form>';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission when requested by POST method
    if (!empty($_POST['comment'])) {
        $comment = $_POST['comment'];
        echo "<h2>Comment: $comment</h2>";
        echo '<p><a href="single.php">Submit another comment</a></p>';
    } else {
        $comment = NULL;
        echo '<h2>You must enter a comment</h2>';
        echo '<p><a href="single.php">Try again</a></p>';
    }
}
?>
</body>
</html>
