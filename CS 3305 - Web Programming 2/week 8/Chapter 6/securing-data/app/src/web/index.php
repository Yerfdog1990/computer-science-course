<?php
declare(strict_types=1);

require_once __DIR__ . '/../components/Template.php';

session_start();

// Generate CSRF token once per session
if (!array_key_exists('csrf-token', $_SESSION)) {
    $_SESSION['csrf-token'] = bin2hex(random_bytes(32));
}

$mainTemplate = new \Components\Template('main');
$templateData = ['title' => 'PHP Security Demo'];

// --- Router ---
$path = $_SERVER['PATH_INFO'] ?? '/';

switch ($path) {

    // --- Sanitization & Validation demo ---
    case '/sanitize':
        $templateData['title'] = 'Sanitize & Validate Demo';
        $errors         = [];
        $successMessage = null;
        $stars          = null;
        $message        = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 1. Validate CSRF token
            if (!array_key_exists('csrf-token', $_POST)
                || $_POST['csrf-token'] !== $_SESSION['csrf-token']) {
                die('ERROR: Invalid or missing CSRF token.');
            }

            // 2. Sanitize — remove unwanted characters
            $stars   = filter_input(INPUT_POST, 'stars',   FILTER_SANITIZE_NUMBER_INT);
            $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_SPECIAL_CHARS);

            // 3. Validate — check that sanitized values meet the rules
            if (null === $stars || false === $stars) {
                $errors[] = "Stars input is missing or invalid.";
            } else {
                $stars = (int)$stars;
                if ($stars < 1 || $stars > 5) {
                    $errors[] = "Stars must be a number between 1 and 5.";
                }
            }

            if (null === $message) {
                $errors[] = "Message input is not set.";
            } elseif (false === $message) {
                $errors[] = "Message failed to pass the sanitization filter.";
            } elseif (strlen(trim($message)) < 10) {
                $errors[] = "Message must be at least 10 characters.";
            }

            if (empty($errors)) {
                $successMessage = sprintf(
                    'Feedback received! Stars: %d | Message: %s',
                    $stars,
                    $message
                );
            }
        }

        $templateData['content'] = (new \Components\Template('sanitize-form'))->render([
            'errors'         => $errors,
            'successMessage' => $successMessage,
            'stars'          => $stars,
            'message'        => $message,
        ]);
        break;

    // --- Reflected XSS / output escaping demo ---
    case '/search':
        $templateData['title']   = 'Search — XSS Demo';
        $templateData['content'] = (new \Components\Template('search'))->render();
        break;

    // --- Home page ---
    default:
        $templateData['content'] = '
            <h2>PHP Security Demo</h2>
            <p>Choose a demo from the navigation bar:</p>
            <ul>
                <li><a href="/sanitize">Sanitize &amp; Validate</a>
                    — input sanitization, validation, and CSRF protection</li>
                <li><a href="/search">XSS Demo</a>
                    — reflected XSS and output escaping with
                    <code>htmlentities()</code></li>
            </ul>';
        break;
}

echo $mainTemplate->render($templateData);
