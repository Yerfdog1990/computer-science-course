<!DOCTYPE html>
<html lang="eg">
<head>
    <title>PHP Form Handler</title>
</head>
<body>
<?php
// Assign form values to like-named PHP variables
$name = $_POST['name'];
$mail = $_POST['email'];
$comment = $_POST['comments'];

// Display the submitted data
echo "<h2>Thanks for this comment $name ...</h2>";
echo "<p>$comment</p>";
echo "<p>We will reply to $mail</p>";
?>
</body>
</html>