<?php
require_once '../config/database.php';

$pageTitle = 'Pelanggan';
$basePath = '../';
$currentPage = 'customers';

$headerActionText = '+ Tambah Pelanggan';
$headerActionLink = 'customers/create.php';

$extraCss = 'assets/css/crud.css';

/* PENCARIAN PELANGGAN */
$search = trim($_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

$sortOptions = [
    'newest' => 'id DESC',
    'oldest' => 'id ASC',
    'name_asc' => 'customer_name ASC',
    'name_desc' => 'customer_name DESC'
];

if (!array_key_exists($sort, $sortOptions)) {
    $sort = 'newest';
}
/* PAGINATION */
$limit = 5;
$page = max(1, (int) ($_GET['page'] ?? 1));

/* HITUNG TOTAL PELANGGAN */
$countSql = "SELECT COUNT(*) AS total FROM customers";

if ($search !== '') {
    $countSql .= " WHERE customer_name LIKE ?
                   OR email LIKE ?
                   OR phone LIKE ?";

    $countStmt = mysqli_prepare($conn, $countSql);
    $keyword = "%{$search}%";

    mysqli_stmt_bind_param(
        $countStmt,
        "sss",
        $keyword,
        $keyword,
        $keyword
    );
} else {
    $countStmt = mysqli_prepare($conn, $countSql);
}

mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalRows = (int) mysqli_fetch_assoc($countResult)['total'];

$totalPages = max(1, (int) ceil($totalRows / $limit));

if ($page > $totalPages) {
    $page = $totalPages;
}

$offset = ($page - 1) * $limit;

/* AMBIL DATA PELANGGAN */
$sql = "SELECT * FROM customers";

if ($search !== '') {
    $sql .= " WHERE customer_name LIKE ?
              OR email LIKE ?
              OR phone LIKE ?";
}

$sql .= " ORDER BY " . $sortOptions[$sort] . " LIMIT ? OFFSET ?";

$stmt = mysqli_prepare($conn, $sql);

if ($search !== '') {
    mysqli_stmt_bind_param(
        $stmt,
        "sssii",
        $keyword,
        $keyword,
        $keyword,
        $limit,
        $offset
    );
} else {
    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $limit,
        $offset
    );
}

mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);

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

        <!-- CUSTOMER SEARCH -->
        <form method="GET" class="customer-search">

            <div class="search-input">
                <input type="text" name="search" placeholder="Cari nama, email, atau nomor telepon..."
                    value="<?= htmlspecialchars($search); ?>">
            </div>
            <select name="sort" onchange="this.form.submit()">
                <option value="newest" <?= $sort === 'newest' ? 'selected' : ''; ?>>
                    Terbaru
                </option>

                <option value="oldest" <?= $sort === 'oldest' ? 'selected' : ''; ?>>
                    Terlama
                </option>

                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : ''; ?>>
                    Nama A–Z
                </option>

                <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : ''; ?>>
                    Nama Z–A
                </option>
            </select>

            <button type="submit">
                Cari Pelanggan
            </button>

            <?php if ($search !== ''): ?>
                <a href="index.php" class="search-reset">
                    Reset
                </a>
            <?php endif; ?>

        </form>

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

                        <?php $no = $offset + 1; ?>

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

                                        <a href="edit.php?id=<?= (int) $customer['id']; ?>" class="action-edit">
                                            Edit
                                        </a>

                                        <form action="delete.php" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?');">

                                            <input type="hidden" name="id" value="<?= (int) $customer['id']; ?>">

                                            <button type="submit" class="action-delete">
                                                Hapus
                                            </button>

                                        </form>

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

                                <a href="create.php" class="button button-dark">
                                    Tambah Pelanggan
                                </a>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>
        <?php if ($totalRows > 0): ?>

            <div class="pagination">

                <div class="pagination-info">
                    Menampilkan
                    <?= $offset + 1; ?>–<?= min($offset + $limit, $totalRows); ?>
                    dari <?= $totalRows; ?> pelanggan
                </div>

                <div class="pagination-links">

                    <?php $queryString = $_GET; ?>

                    <?php if ($page > 1): ?>
                        <?php $queryString['page'] = $page - 1; ?>

                        <a href="?<?= htmlspecialchars(http_build_query($queryString)); ?>">
                            Sebelumnya &raquo;
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php $queryString['page'] = $i; ?>

                        <a href="?<?= htmlspecialchars(http_build_query($queryString)); ?>"
                            class="<?= $page === $i ? 'active' : ''; ?>">
                            <?= $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <?php $queryString['page'] = $page + 1; ?>

                        <a href="?<?= htmlspecialchars(http_build_query($queryString)); ?>">
                            Selanjutnya &raquo;
                        </a>
                    <?php endif; ?>

                </div>

            </div>

        <?php endif; ?>

    </div>

</main>


<?php include '../includes/footer.php'; ?>

</body>

</html>