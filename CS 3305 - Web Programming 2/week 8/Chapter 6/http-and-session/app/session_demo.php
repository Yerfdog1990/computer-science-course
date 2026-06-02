<?php
session_start();

$sessionName = session_name(); // "PHPSESSID" by default
$isNewSession = !array_key_exists($sessionName, $_COOKIE);

// Store a timestamp when the session was first created
if ($isNewSession) {
    $_SESSION['first_seen'] = date('Y-m-d H:i:s');
    $_SESSION['visit_count'] = 1;
} else {
    $_SESSION['visit_count'] = ($_SESSION['visit_count'] ?? 0) + 1;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session & Cookie Demo</title>
</head>
<body>
<h1>Session & Cookie Demo</h1>

<?php if ($isNewSession): ?>
    <p>🆕 New session started. Session ID: <code><?= session_id() ?></code></p>
    <p>The <code>Set-Cookie</code> header will be sent to store this ID in your browser.</p>
    <p>Response headers being sent:</p>
    <pre><?= implode("\n", headers_list()) ?></pre>
<?php else: ?>
    <p>✅ Returning visitor! Cookie <code><?= $sessionName ?></code>
        found with value: <code><?= $_COOKIE[$sessionName] ?></code></p>
<?php endif; ?>

<h2>Session Data</h2>
<pre><?= var_export($_SESSION, true) ?></pre>

<h2>All Cookies</h2>
<pre><?= var_export($_COOKIE, true) ?></pre>

<p>
    <a href="session_demo.php">Reload (increment visit count)</a> |
    <a href="register.php?ref=DEMO2024">Set referral cookie via GET</a>
</p>
</body>
</html>
