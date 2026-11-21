<?php

require_once '../config/database.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}


/* =========================
   AMBIL DATA PELANGGAN
========================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT * FROM customers WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$customer = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$customer) {
    header('Location: index.php');
    exit;
}


$error = '';


/* =========================
   UPDATE DATA
========================= */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $customer_name = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);


    if ($customer_name === '' || $email === '') {

        $error = 'Nama pelanggan dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = 'Format email tidak valid.';
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE customers
             SET customer_name = ?,
                 email = ?,
                 phone = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sssi",
            $customer_name,
            $email,
            $phone,
            $id
        );


        if (mysqli_stmt_execute($stmt)) {

            mysqli_stmt_close($stmt);

            header('Location: index.php');
            exit;
        } else {

            $error = 'Data pelanggan gagal diperbarui.';

            mysqli_stmt_close($stmt);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title>Edit Pelanggan — Insightly</title>

    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/buttons.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">

</head>


<body>


    <!-- NAVBAR -->

    <header class="navbar">

        <div class="container nav-wrapper">

            <a href="../index.php" class="logo">

                <span class="logo-mark">i</span>

                insightly

            </a>


            <nav class="nav-menu">

                <a href="../index.php">
                    Beranda
                </a>

                <a href="index.php">
                    Pelanggan
                </a>

                <a href="../feedback/index.php">
                    Feedback
                </a>

            </nav>


            <a href="index.php" class="nav-button">
                Kembali
            </a>

        </div>

    </header>


    <!-- CONTENT -->

    <main class="page-content">

        <div class="container">

            <div class="form-wrapper">


                <div class="form-header">

                    <span class="section-label">
                        DATA PELANGGAN
                    </span>

                    <h1>
                        Edit Pelanggan
                    </h1>

                    <p>
                        Perbarui informasi pelanggan
                        yang tersimpan di Insightly.
                    </p>

                </div>


                <?php if ($error !== ''): ?>

                    <div class="form-error">

                        <?= htmlspecialchars($error); ?>

                    </div>

                <?php endif; ?>


                <form
                    method="POST"
                    class="data-form">


                    <!-- NAMA -->

                    <div class="form-group">

                        <label for="customer_name">
                            Nama Pelanggan
                        </label>

                        <input
                            type="text"
                            id="customer_name"
                            name="customer_name"
                            value="<?= htmlspecialchars(
                                        $customer['customer_name']
                                    ); ?>"
                            required>

                    </div>


                    <!-- EMAIL -->

                    <div class="form-group">

                        <label for="email">
                            Email
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?= htmlspecialchars(
                                        $customer['email']
                                    ); ?>"
                            required>

                    </div>


                    <!-- TELEPON -->

                    <div class="form-group">

                        <label for="phone">
                            Nomor Telepon
                        </label>

                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="<?= htmlspecialchars(
                                        $customer['phone'] ?? ''
                                    ); ?>">

                    </div>


                    <!-- ACTION -->

                    <div class="form-actions">

                        <a
                            href="index.php"
                            class="button button-light">
                            Batal
                        </a>


                        <button
                            type="submit"
                            class="button button-dark">
                            Simpan Perubahan
                            <span>→</span>
                        </button>

                    </div>


                </form>

            </div>

        </div>

    </main>


    <!-- FOOTER -->

    <footer class="footer">

        <div class="container footer-wrapper">

            <div>

                <a href="../index.php" class="logo">

                    <span class="logo-mark">i</span>

                    insightly

                </a>

                <p>
                    Sistem manajemen feedback pelanggan.
                </p>

            </div>


            <span>
                © 2026 Insightly
            </span>

        </div>

    </footer>


</body>

</html>