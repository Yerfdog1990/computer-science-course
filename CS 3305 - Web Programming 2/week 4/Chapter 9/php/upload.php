<!DOCTYPE html>
<html lang="en">
<head>
    <title>File Upload</title>
</head>
<body>
<form action="upload.php" method="POST" enctype="multipart/form-data">
    <p>Select an image to upload:</p>
    <p><input type="file" name="image"></p>
    <p><input type="submit" value="Upload"></p>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    // Initialize variables with file information
    $name = $_FILES['image']['name'];
    $temp = $_FILES['image']['tmp_name'];
    $size = $_FILES['image']['size'];
    $error = $_FILES['image']['error'];

    // Check for upload errors
    if ($error !== UPLOAD_ERR_OK) {
        echo '<p style="color: red;">Upload error occurred. Error code: ' . $error . '</p>';
        exit();
    }

    // Validate file type
    $ext = pathinfo($name, PATHINFO_EXTENSION);
    $ext = strtolower($ext);
    if ($ext != 'png' && $ext != 'jpg' && $ext != 'gif') {
        echo '<p style="color: red;">Format must be PNG, JPG, or GIF</p>';
        exit();
    }

    // Validate file size (500KB limit)
    if ($size > 512000) {
        echo '<p style="color: red;">File size must not exceed 500Kb</p>';
        exit();
    }

    // Check for duplicate files
    if (file_exists($name)) {
        echo '<p style="color: red;">File ' . htmlspecialchars($name) . ' already uploaded</p>';
        exit();
    }

    // Attempt to upload the file
    try {
        if (move_uploaded_file($temp, $name)) {
            echo '<p style="color: green;">File uploaded: ' . htmlspecialchars($name) . '</p>';
            echo '<img src="' . htmlspecialchars($name) . '" alt="Uploaded image" style="max-width: 300px;">';
        } else {
            echo '<p style="color: red;">File upload failed!</p>';
        }
    } catch (Exception $e) {
        echo '<p style="color: red;">File upload failed: ' . $e->getMessage() . '</p>';
    }
}
?>
</body>
</html>
