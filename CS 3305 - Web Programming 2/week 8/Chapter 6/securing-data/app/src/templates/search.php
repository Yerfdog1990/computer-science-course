<h2>Search — Reflected XSS Demo</h2>
<p class="text-muted">Try searching for
    <code>&lt;script&gt;alert('xss')&lt;/script&gt;</code> to see it rendered safely.</p>

<?php if (isset($_GET['s']) && $_GET['s'] !== ''): ?>
    <div class="alert alert-info">
        You searched for: <strong><?= htmlentities($_GET['s'], ENT_QUOTES) ?></strong>
    </div>
<?php else: ?>
    <p>Use the form below to search.</p>
<?php endif; ?>

<form method="get" action="/search">
    <div class="input-group mb-3">
        <input type="text" class="form-control" name="s"
               placeholder="Search term..."
               value="<?= htmlentities($_GET['s'] ?? '', ENT_QUOTES) ?>">
        <button class="btn btn-outline-secondary" type="submit">Search</button>
    </div>
</form>

<hr>
<h5>What the browser receives (escaped HTML):</h5>
<pre class="bg-light p-3 rounded"><code><?= htmlentities(
            sprintf('You searched for: <strong>%s</strong>', htmlentities($_GET['s'] ?? '', ENT_QUOTES)),
            ENT_QUOTES
        ) ?></code></pre>