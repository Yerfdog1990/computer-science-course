<?php
declare(strict_types=1);

/**
 * Require the user to be logged in.
 * Redirects to login page if not authenticated.
 */
function requireLogin(): void
{
    if (!isset($_SESSION['user_id'])) {
        header('Location: /auth/login.php');
        exit;
    }
}

/**
 * Require the user to have one of the allowed roles.
 * Returns a 403 error if the user does not have permission.
 */
function requireRole(string ...$roles): void
{
    requireLogin();

    if (!in_array($_SESSION['role'], $roles, strict: true)) {
        http_response_code(403);
        echo '<h1>403 Forbidden</h1>';
        echo '<p>You do not have permission to access this resource.</p>';
        exit;
    }
}