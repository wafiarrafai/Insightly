<?php
require_once '../config/database.php';

$query = mysqli_query(
    $conn,
    "SELECT * FROM customers ORDER BY id DESC"
);
?>

<?php
require_once '../config/database.php';

$pageTitle = 'Pelanggan';
$basePath = '../';
$currentPage = 'customers';

$headerActionText = '+ Tambah Pelanggan';
$headerActionLink = 'customers/create.php';

$extraCss = 'assets/css/crud.css';

include '../includes/header.php';
?>

<main class="page-content">

    <div class="container">

        <div class="page-header">

            <div>
                <span class="section-label">
                    DATA PELANGGAN
                </span>

                <h1>
                    Pelanggan
                </h1>

                <p>
                    Kelola informasi pelanggan Insightly.
                </p>
            </div>

            <a href="create.php" class="button button-dark">
                + Tambah Pelanggan
            </a>

        </div>


        <div class="table-container">

            <table>

                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Pelanggan</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Tanggal Ditambahkan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>


                <tbody>

                    <?php if (mysqli_num_rows($query) > 0): ?>

                        <?php $no = 1; ?>

                        <?php while ($customer = mysqli_fetch_assoc($query)): ?>

                            <tr>

                                <td>
                                    <?= $no++; ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($customer['customer_name']); ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= htmlspecialchars($customer['email']); ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($customer['phone'] ?? '-'); ?>
                                </td>

                                <td>
                                    <?= date(
                                        'd M Y',
                                        strtotime($customer['created_at'])
                                    ); ?>
                                </td>

                                <td>

                                    <div class="table-actions">

                                        <a
                                            href="edit.php?id=<?= $customer['id']; ?>"
                                            class="action-edit">
                                            Edit
                                        </a>

                                        <a
                                            href="delete.php?id=<?= $customer['id']; ?>"
                                            class="action-delete"
                                            onclick="return confirm('Yakin ingin menghapus pelanggan ini?');">
                                            Hapus
                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6" class="empty-state">

                                <div>
                                    Belum ada data pelanggan.
                                </div>

                                <p>
                                    Tambahkan pelanggan pertama kamu untuk mulai menggunakan Insightly.
                                </p>

                                <a
                                    href="create.php"
                                    class="button button-dark">
                                    Tambah Pelanggan
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