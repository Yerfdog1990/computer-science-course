<?php

// Connect without a dbname first so we can create the database
$pdo = new PDO(
    "mysql:host=127.0.0.1;port=3306;charset=utf8mb4",
    'http-and-session-user',
    'http-and-session-pass',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

// ── 1. Create the database ────────────────────────────────────────────────────
$pdo->exec("CREATE DATABASE IF NOT EXISTS books_api CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$pdo->exec("USE books_api");

echo "database 'books_api' ready." . PHP_EOL;

// ── 2. Create the books table ─────────────────────────────────────────────────
$pdo->exec("DROP TABLE IF EXISTS books");

$pdo->exec("
    CREATE TABLE books (
        id           INT          NOT NULL PRIMARY KEY AUTO_INCREMENT,
        title        VARCHAR(255) NOT NULL,
        author       VARCHAR(255) NOT NULL,
        genre        VARCHAR(100) NOT NULL,
        published    YEAR         NOT NULL,
        isbn         VARCHAR(20)  NOT NULL UNIQUE,
        available    BOOLEAN      NOT NULL DEFAULT TRUE,
        created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
    )
");

echo "Table 'books' created." . PHP_EOL;

// ── 3. Insert seed data ───────────────────────────────────────────────────────
$insert = $pdo->prepare("
    INSERT INTO books (title, author, genre, published, isbn, available)
    VALUES (:title, :author, :genre, :published, :isbn, :available)
");

$books = [
    [
        'title'     => 'The Pragmatic Programmer',
        'author'    => 'Andrew Hunt',
        'genre'     => 'Technology',
        'published' => 1999,
        'isbn'      => '978-0201616224',
        'available' => true,
    ],
    [
        'title'     => 'Clean Code',
        'author'    => 'Robert C. Martin',
        'genre'     => 'Technology',
        'published' => 2008,
        'isbn'      => '978-0132350884',
        'available' => true,
    ],
    [
        'title'     => 'Design Patterns',
        'author'    => 'Gang of Four',
        'genre'     => 'Technology',
        'published' => 1994,
        'isbn'      => '978-0201633610',
        'available' => false,
    ],
    [
        'title'     => 'The Great Gatsby',
        'author'    => 'F. Scott Fitzgerald',
        'genre'     => 'Fiction',
        'published' => 1925,
        'isbn'      => '978-0743273565',
        'available' => true,
    ],
    [
        'title'     => 'To Kill a Mockingbird',
        'author'    => 'Harper Lee',
        'genre'     => 'Fiction',
        'published' => 1960,
        'isbn'      => '978-0061935466',
        'available' => true,
    ],
    [
        'title'     => 'Dune',
        'author'    => 'Frank Herbert',
        'genre'     => 'Science Fiction',
        'published' => 1965,
        'isbn'      => '978-0441013593',
        'available' => false,
    ],
    [
        'title'     => 'Sapiens',
        'author'    => 'Yuval Noah Harari',
        'genre'     => 'Non-Fiction',
        'published' => 2011,
        'isbn'      => '978-0062316097',
        'available' => true,
    ],
    [
        'title'     => '1984',
        'author'    => 'George Orwell',
        'genre'     => 'Fiction',
        'published' => 1949,
        'isbn'      => '978-0451524935',
        'available' => true,
    ],
];

foreach ($books as $book) {
    $insert->execute([
        ':title'     => $book['title'],
        ':author'    => $book['author'],
        ':genre'     => $book['genre'],
        ':published' => $book['published'],
        ':isbn'      => $book['isbn'],
        ':available' => $book['available'] ? 1 : 0,
    ]);
}

echo "Inserted " . count($books) . " seed books." . PHP_EOL;
echo "Seed complete." . PHP_EOL;
