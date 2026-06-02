<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlentities($title ?? '(no title)', ENT_QUOTES); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">Learning PHP — Security</span>
    <div>
        <a href="/" class="text-white me-3">Home</a>
        <a href="/sanitize" class="text-white me-3">Sanitize Demo</a>
        <a href="/search" class="text-white">XSS Demo</a>
    </div>
</nav>
<div class="container my-5">
    <?php if (isset($content)) echo $content; ?>
</div>
</body>
</html>
