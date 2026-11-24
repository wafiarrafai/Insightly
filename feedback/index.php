<?php
require_once '../config/database.php';

$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';
$rating = $_GET['rating'] ?? '';
$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';

/* Validasi format tanggal */
$startObject = DateTime::createFromFormat('!Y-m-d', $startDate);
if (!$startObject || $startObject->format('Y-m-d') !== $startDate) {
    $startDate = '';
}

$endObject = DateTime::createFromFormat('!Y-m-d', $endDate);
if (!$endObject || $endObject->format('Y-m-d') !== $endDate) {
    $endDate = '';
}

$categories = ['Produk', 'Pelayanan', 'Harga', 'Pengiriman'];
$sort = $_GET['sort'] ?? 'newest';

$sortOptions = [
    'newest' => 'feedback.feedback_date DESC, feedback.id DESC',
    'oldest' => 'feedback.feedback_date ASC, feedback.id ASC',
    'rating_high' => 'feedback.rating DESC',
    'rating_low' => 'feedback.rating ASC'
];

if (!array_key_exists($sort, $sortOptions)) {
    $sort = 'newest';
}

$where = [];
$params = [];
$types = '';

if ($search !== '') {
    $where[] = "(customers.customer_name LIKE ? OR feedback.comment LIKE ?)";
    $keyword = "%{$search}%";

    $params[] = $keyword;
    $params[] = $keyword;
    $types .= 'ss';
}

if (in_array($category, $categories, true)) {
    $where[] = "feedback.category = ?";
    $params[] = $category;
    $types .= 's';
}

if (in_array($rating, ['1', '2', '3', '4', '5'], true)) {
    $where[] = "feedback.rating = ?";
    $params[] = (int) $rating;
    $types .= 'i';
}
/* FILTER RENTANG TANGGAL */
if ($startDate !== '') {
    $where[] = "feedback.feedback_date >= ?";
    $params[] = $startDate;
    $types .= 's';
}

if ($endDate !== '') {
    $where[] = "feedback.feedback_date <= ?";
    $params[] = $endDate;
    $types .= 's';
}

// Membuat kondisi WHERE
$whereSql = '';

if (!empty($where)) {
    $whereSql = " WHERE " . implode(" AND ", $where);
}

// Menghitung total feedback sesuai filter
$countSql = "
    SELECT COUNT(*) AS total
    FROM feedback
    JOIN customers ON feedback.customer_id = customers.id
    $whereSql
";

$countStmt = mysqli_prepare($conn, $countSql);

if (!empty($params)) {
    mysqli_stmt_bind_param($countStmt, $types, ...$params);
}

mysqli_stmt_execute($countStmt);
$countResult = mysqli_stmt_get_result($countStmt);
$totalRows = mysqli_fetch_assoc($countResult)['total'];

// Pengaturan pagination
$limit = 5;
$totalPages = max(1, (int) ceil($totalRows / $limit));

$page = max(1, (int) ($_GET['page'] ?? 1));
$page = min($page, $totalPages);

$offset = ($page - 1) * $limit;

// Mengambil feedback sesuai halaman
$sql = "
    SELECT feedback.*, customers.customer_name
    FROM feedback
    JOIN customers ON feedback.customer_id = customers.id
    $whereSql
    ORDER BY $sortOptions[$sort]
    LIMIT ? OFFSET ?
";

$stmt = mysqli_prepare($conn, $sql);

$finalParams = $params;
$finalParams[] = $limit;
$finalParams[] = $offset;

$finalTypes = $types . 'ii';

mysqli_stmt_bind_param($stmt, $finalTypes, ...$finalParams);

mysqli_stmt_execute($stmt);
$query = mysqli_stmt_get_result($stmt);
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


                <div class="page-actions">

                    <a href="export.php?<?= htmlspecialchars(http_build_query($_GET)); ?>" class="button button-export">
                        ↓ Export CSV
                    </a>

                    <a href="create.php" class="button button-dark">
                        + Tambah Feedback
                    </a>

                </div>


            </div>

            <!-- SEARCH AND FILTER -->
            <form method="GET" class="filter-form">
                <div class="date-filter">
                    <input type="date" name="start_date" value="<?= htmlspecialchars($startDate); ?>"
                        title="Tanggal mulai">

                    <span>sampai</span>

                    <input type="date" name="end_date" value="<?= htmlspecialchars($endDate); ?>" title="Tanggal akhir">
                </div>
                <div class="filter-search">
                    <input type="text" name="search" placeholder="Cari nama pelanggan atau komentar..."
                        value="<?= htmlspecialchars($search); ?>">
                </div>

                <select name="category">
                    <option value="">Semua Kategori</option>

                    <?php foreach ($categories as $item): ?>
                    <option value="<?= htmlspecialchars($item); ?>" <?= $category === $item ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($item); ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <select name="rating">
                    <option value="">Semua Rating</option>

                    <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i; ?>" <?= $rating === (string) $i ? 'selected' : ''; ?>>
                        <?= $i; ?> Bintang
                    </option>
                    <?php endfor; ?>
                </select>

                <button type="submit" class="button button-dark">
                    Cari
                </button>

                <a href="index.php" class="filter-reset">
                    Reset
                </a>
                <select name="sort" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : ''; ?>>
                        Feedback Terbaru
                    </option>

                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : ''; ?>>
                        Feedback Terlama
                    </option>

                    <option value="rating_high" <?= $sort === 'rating_high' ? 'selected' : ''; ?>>
                        Rating Tertinggi
                    </option>

                    <option value="rating_low" <?= $sort === 'rating_low' ? 'selected' : ''; ?>>
                        Rating Terendah
                    </option>
                </select>

            </form>



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

                        <?php $no = $offset + 1; ?>

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

                                    <a href="edit.php?id=<?= (int) $feedback['id']; ?>" class="action-edit">
                                        Edit
                                    </a>

                                    <form action="delete.php" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus feedback ini?');">

                                        <input type="hidden" name="id" value="<?= (int) $feedback['id']; ?>">

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

                            <td colspan="7" class="empty-state">

                                <div>
                                    Belum ada data feedback.
                                </div>

                                <p>
                                    Tambahkan feedback pertama untuk mulai menganalisis masukan pelanggan.
                                </p>

                                <a href="create.php" class="button button-dark">
                                    Tambah Feedback
                                </a>

                            </td>

                        </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>
            <?php if ($totalRows > 0 && $totalPages > 1): ?>
            <div class="pagination">

                <?php if ($page > 1): ?>
                <a href="?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['page' => $page - 1]))) ?>"
                    class="pagination-link">
                    &laquo; Sebelumnya
                </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['page' => $i]))) ?>"
                    class="pagination-link <?= $page === $i ? 'active' : '' ?>">
                    <?= $i ?>
                </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                <a href="?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['page' => $page + 1]))) ?>"
                    class="pagination-link">
                    Selanjutnya &raquo;
                </a>
                <?php endif; ?>

            </div>
            <?php endif; ?>
        </div>

    </main>


    <?php include '../includes/footer.php'; ?>

</body>

</html>