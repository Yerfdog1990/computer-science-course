<h2>Feedback Form — Sanitization & Validation Demo</h2>
<p class="text-muted">Try entering <code>3a</code> for stars and
    <code>Hello &lt;script&gt;alert(1)&lt;/script&gt;</code> for the message.</p>

<?php if (isset($stars) || isset($message)): ?>
    <div class="card mb-4">
        <div class="card-header">Sanitized Values</div>
        <div class="card-body">
            <p><strong>Stars (sanitized):</strong>
                <code><?= var_export($stars ?? null, true) ?></code></p>
            <p><strong>Message (sanitized):</strong>
                <code><?= var_export($message ?? null, true) ?></code></p>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlentities($error, ENT_QUOTES) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (!empty($successMessage)): ?>
    <div class="alert alert-success">
        <?= htmlentities($successMessage, ENT_QUOTES) ?>
    </div>
<?php endif; ?>

<form method="post" action="/sanitize">
    <input type="hidden" name="csrf-token" value="<?= htmlentities($_SESSION['csrf-token'] ?? '', ENT_QUOTES) ?>">

    <div class="mb-3">
        <label for="stars" class="form-label">Stars (1–5):</label>
        <input type="text" class="form-control" id="stars" name="stars"
               value="<?= htmlentities($_POST['stars'] ?? '', ENT_QUOTES) ?>">
        <div class="form-text">Try entering <code>3a</code> to see sanitization in action.</div>
    </div>

    <div class="mb-3">
        <label for="message" class="form-label">Message:</label>
        <textarea class="form-control" id="message" name="message" rows="4"><?= htmlentities($_POST['message'] ?? '', ENT_QUOTES) ?></textarea>
        <div class="form-text">Try entering HTML tags to see them sanitized.</div>
    </div>

    <button type="submit" class="btn btn-primary">Submit Feedback</button>
</form>
