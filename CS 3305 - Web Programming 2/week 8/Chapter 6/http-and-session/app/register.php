<?php

session_start();
require_once 'database.php';

$errors  = [];
$success = false;
$uploadsDir = __DIR__ . '/uploads';

// Create uploads dir if it doesn't exist
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

// --- COOKIE: Track referral code from URL ---
// Visit: register.http-and-session?ref=SCHOLARSHIP2024
if (array_key_exists('ref', $_GET)) {
    setcookie('referral', $_GET['ref'], time() + 60 * 60 * 24 * 30);
    $referralCode = $_GET['ref'];
} else {
    $referralCode = $_COOKIE['referral'] ?? null;
}

// --- POST: Handle form submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 1. Read POST data
    $name  = trim($_POST['name']  ?? '');
    $email = trim($_POST['email'] ?? '');
    $major = trim($_POST['major'] ?? '');

    if (empty($name)) {
        $errors[] = "Full name is required.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email address is required.";
    }
    if (empty($major)) {
        $errors[] = "Please select a major.";
    }

    // 2. Handle profile photo upload ($_FILES)
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $photo = $_FILES['photo'];

        switch ($photo['error']) {
            case UPLOAD_ERR_OK:
                // Always detect MIME type server-side — never trust $photo['type']
                $detectedMime = mime_content_type($photo['tmp_name']);
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

                if (!in_array($detectedMime, $allowedTypes)) {
                    $errors[] = "Profile photo must be JPEG, PNG, or GIF.";
                } else {
                    $ext      = pathinfo($photo['name'], PATHINFO_EXTENSION);
                    $filename = uniqid('student_', true) . '.' . strtolower($ext);
                    $target   = $uploadsDir . '/' . $filename;

                    if (move_uploaded_file($photo['tmp_name'], $target)) {
                        $photoPath = '/uploads/' . $filename;
                    } else {
                        $errors[] = "Failed to save uploaded photo.";
                    }
                }
                break;

            case UPLOAD_ERR_INI_SIZE:
                $errors[] = "Photo is too large (max 2 MB).";
                break;

            default:
                $errors[] = "Photo upload failed (error code: {$photo['error']}).";
        }
    }

    // 3. If valid, INSERT into database using PDO prepared statement
    if (empty($errors)) {
        $pdo  = Database::getInstance()->getPdo();
        $stmt = $pdo->prepare("
            INSERT INTO students (name, email, major, photo, referral_code)
            VALUES (:name, :email, :major, :photo, :referral)
        ");
        $stmt->execute([
            ':name'     => $name,
            ':email'    => $email,
            ':major'    => $major,
            ':photo'    => $photoPath,
            ':referral' => $referralCode,
        ]);

        // Store the new student ID in the session (e.g., for a success banner)
        $_SESSION['last_registered_id'] = $pdo->lastInsertId();
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Registration</title>
</head>
<body>
<h1>Student Registration Form</h1>
<p><strong>Referral Code (from cookie):</strong>
    <?= $referralCode ? htmlspecialchars($referralCode) : 'None' ?>
</p>

<?php if ($success): ?>
    <p style="color: green;">
        ✅ Registration successful! (Student ID: <?= $_SESSION['last_registered_id'] ?>)
        <a href="index.php">View all students →</a>
    </p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <ul style="color: red;">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<!-- POST form with multipart encoding for file upload -->
<form method="post" enctype="multipart/form-data" action="register.php">
    <label>Full Name:
        <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
    </label><br>

    <label>Email:
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
    </label><br>

    <label>Major:
        <select name="major">
            <option value="">-- Select --</option>
            <option value="Computer Science">Computer Science</option>
            <option value="Information Technology">Information Technology</option>
            <option value="Data Science">Data Science</option>
        </select>
    </label><br>

    <label>Profile Photo (PNG/JPEG, max 2MB):
        <input type="file" name="photo" accept="image/*">
    </label><br>

    <input type="submit" value="Register">
</form>

<!-- Debug: Show all superglobal data -->
<hr>
<h3>$_POST:</h3>
<pre><?= var_export($_POST, true) ?></pre>

<h3>$_FILES:</h3>
<pre><?= var_export($_FILES, true) ?></pre>

<h3>$_COOKIE:</h3>
<pre><?= var_export($_COOKIE, true) ?></pre>
</body>
</html>