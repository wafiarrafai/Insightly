<?php

require_once '../config/database.php';

// Proses hapus hanya boleh melalui POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    header('Location: index.php');
    exit;
}

// Hapus pelanggan berdasarkan ID
$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM customers WHERE id = ?"
);

mysqli_stmt_bind_param($stmt, "i", $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php?deleted=success');
    exit;
}

mysqli_stmt_close($stmt);

header('Location: index.php?deleted=failed');
exit;