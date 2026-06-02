<?php

declare(strict_types=1);
session_start();

// Regenerate ID and destroy all session data
session_regenerate_id(true);
session_destroy();

header('Location: /auth/login.php');
exit;
