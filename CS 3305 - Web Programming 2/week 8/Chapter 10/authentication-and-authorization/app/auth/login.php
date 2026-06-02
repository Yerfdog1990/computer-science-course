<?php
declare(strict_types=1);
session_start();
require_once '../Database.php';
use app\Database;

$error = null;

// If already logged in, redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($email) || empty($password)) {
        $error = 'Email and password are required.';
    } else {
        $pdo  = Database::getInstance()->getPdo();

        // Prepared statement — user input never concatenated into query
        $stmt = $pdo->prepare(
            "SELECT id, name, email, password_hash, role FROM users WHERE email = :email"
        );
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Regenerate session ID on login — prevents session fixation
            session_regenerate_id(true);

            // Store only what is needed — never store the password
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            header('Location: /dashboard.php');
            exit;
        }

        // Deliberately vague — do not reveal whether the email exists
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Login</title></head>
<body>
<h1>Login</h1>

<?php if ($error): ?>
    <p style="color:red"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post" action="login.php">
    <label>Email:    <input type="email"    name="email"    required></label><br>
    <label>Password: <input type="password" name="password" required></label><br>
    <button type="submit">Login</button>
</form>
</body>
</html>