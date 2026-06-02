<?php

session_start();
require_once 'database.php';

$pdo         = Database::getInstance()->getPdo();
$filterMajor = $_GET['major'] ?? '';

// --- GET: Filter by major using a prepared statement ---
if (!empty($filterMajor)) {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE major = :major ORDER BY created_at DESC");
    $stmt->execute([':major' => $filterMajor]);
} else {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY created_at DESC");
}

$students = $stmt->fetchAll(); // array of associative arrays from DB
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registered Students</title>
</head>
<body>
<h1>Registered Students</h1>
<p><a href="register.php">+ Register a new student</a></p>

<!-- GET filter form — appropriate because results are shareable/bookmarkable -->
<form method="get" action="index.php">
    <label>Filter by Major:
        <select name="major">
            <option value="">All Majors</option>
            <option value="Computer Science"    <?= $filterMajor === 'Computer Science'    ? 'selected' : '' ?>>Computer Science</option>
            <option value="Information Technology" <?= $filterMajor === 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
            <option value="Data Science"        <?= $filterMajor === 'Data Science'        ? 'selected' : '' ?>>Data Science</option>
        </select>
    </label>
    <input type="submit" value="Filter">
    <a href="index.php">Clear filter</a>
</form>

<hr>

<?php if (empty($students)): ?>
    <p>No students found. <?= !empty($filterMajor) ? '(Try clearing the filter)' : '' ?></p>
<?php else: ?>
    <table border="1" cellpadding="8">
        <thead>
        <tr>
            <th>Photo</th><th>Name</th><th>Email</th>
            <th>Major</th><th>Referral Code</th><th>Registered</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td>
                    <?php if ($student['photo']): ?>
                        <img src="<?= htmlspecialchars($student['photo']) ?>"
                             width="60" height="60"
                             style="object-fit: cover; border-radius: 50%;" alt="Photo">
                    <?php else: ?>
                        No photo
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($student['name']) ?></td>
                <td><?= htmlspecialchars($student['email']) ?></td>
                <td><?= htmlspecialchars($student['major']) ?></td>
                <td><?= htmlspecialchars($student['referral_code'] ?? '—') ?></td>
                <td><?= $student['created_at'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<!-- Debug -->
<hr>
<h3>$_GET (active filters):</h3>
<pre><?= var_export($_GET, true) ?></pre>

<h3>$_SESSION:</h3>
<pre><?= var_export($_SESSION, true) ?></pre>
</body>
</html>
