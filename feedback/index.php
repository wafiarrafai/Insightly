<?php
require_once '../config/database.php';

$query = mysqli_query(
    $conn,
    "SELECT 
        feedback.*,
        customers.customer_name
    FROM feedback
    JOIN customers ON feedback.customer_id = customers.id
    ORDER BY feedback.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Feedback — Insightly</title>

    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/buttons.css">
    <link rel="stylesheet" href="../assets/css/footer.css">
    <link rel="stylesheet" href="../assets/css/crud.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>

<body>

    <?php
    require_once '../config/database.php';

    $pageTitle = 'Pelanggan';
    $basePath = '../';
    $currentPage = 'feedback';

    $headerActionText = '+ Tambah Pelanggan';
    $headerActionLink = 'customers/create.php';

    $extraCss = 'assets/css/crud.css';

    include '../includes/header.php';
    ?>


    <!-- MAIN CONTENT -->
    <main class="page-content">

        <div class="container">

            <div class="page-header">

                <div>

                    <span class="section-label">
                        DATA FEEDBACK
                    </span>

                    <h1>
                        Feedback
                    </h1>

                    <p>
                        Kelola masukan dan penilaian pelanggan Insightly.
                    </p>

                </div>

                <a href="create.php" class="button button-dark">
                    + Tambah Feedback
                </a>

            </div>


            <!-- FEEDBACK TABLE -->
            <div class="table-container">

                <table>

                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Nama Pelanggan</th>
                            <th>Rating</th>
                            <th>Kategori</th>
                            <th>Komentar</th>
                            <th>Tanggal Feedback</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>


                    <tbody>

                        <?php if ($query && mysqli_num_rows($query) > 0): ?>

                            <?php $no = 1; ?>

                            <?php while ($feedback = mysqli_fetch_assoc($query)): ?>

                                <tr>

                                    <td>
                                        <?= $no++; ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($feedback['customer_name']); ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= (int) $feedback['rating']; ?> / 5
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($feedback['category']); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars($feedback['comment']); ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            'd M Y',
                                            strtotime($feedback['feedback_date'])
                                        ); ?>
                                    </td>

                                    <td>

                                        <div class="table-actions">

                                            <a
                                                href="edit.php?id=<?= (int) $feedback['id']; ?>"
                                                class="action-edit">
                                                Edit
                                            </a>

                                            <form
                                                action="delete.php"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus feedback ini?');">

                                                <input
                                                    type="hidden"
                                                    name="id"
                                                    value="<?= (int) $feedback['id']; ?>">

                                                <button
                                                    type="submit"
                                                    class="action-delete">
                                                    Hapus
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>

                                <td colspan="7" class="empty-state">

                                    <div>
                                        Belum ada data feedback.
                                    </div>

                                    <p>
                                        Tambahkan feedback pertama untuk mulai menganalisis masukan pelanggan.
                                    </p>

                                    <a
                                        href="create.php"
                                        class="button button-dark">
                                        Tambah Feedback
                                    </a>

                                </td>

                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </main>


    <?php include '../includes/footer.php'; ?>

</body>

</html>