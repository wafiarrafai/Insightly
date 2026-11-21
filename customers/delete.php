<?php

require_once '../config/database.php';


$id = isset($_GET['id'])
    ? (int) $_GET['id']
    : 0;


if ($id <= 0) {

    header('Location: index.php');
    exit;
}


/* =========================
   HAPUS DATA
========================= */

$stmt = mysqli_prepare(
    $conn,
    "DELETE FROM customers WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);


mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


/* =========================
   KEMBALI
========================= */

header('Location: index.php');
exit;
