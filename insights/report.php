<?php
require_once '../config/database.php';

$pageTitle = 'Laporan Feedback';
$basePath = '../';
$currentPage = 'insights';
$headerActionText = 'Kembali ke Insights';
$headerActionLink = 'insights/index.php';
$extraCss = 'assets/css/report.css';

// Rentang tanggal default: 30 hari terakhir
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));

$error = '';
$summary = null;
$feedbackData = null;

// Validasi tanggal
$startObject = DateTime::createFromFormat('!Y-m-d', $startDate);
$endObject = DateTime::createFromFormat('!Y-m-d', $endDate);

$validStart = $startObject &&
    $startObject->format('Y-m-d') === $startDate;

$validEnd = $endObject &&
    $endObject->format('Y-m-d') === $endDate;

if (!$validStart || !$validEnd) {
    $error = 'Format tanggal tidak valid.';
} elseif ($startDate > $endDate) {
    $error = 'Tanggal awal tidak boleh melebihi tanggal akhir.';
} else {

    // Ringkasan laporan
    $summaryStmt = mysqli_prepare($conn, "
        SELECT
            COUNT(*) AS total_feedback,
            COALESCE(ROUND(AVG(rating), 2), 0) AS average_rating,
            COALESCE(SUM(CASE WHEN rating >= 4 THEN 1 ELSE 0 END), 0) AS positive_feedback,
            COALESCE(SUM(CASE WHEN rating = 3 THEN 1 ELSE 0 END), 0) AS neutral_feedback,
            COALESCE(SUM(CASE WHEN rating <= 2 THEN 1 ELSE 0 END), 0) AS negative_feedback
        FROM feedback
        WHERE feedback_date BETWEEN ? AND ?
    ");

    mysqli_stmt_bind_param($summaryStmt, "ss", $startDate, $endDate);
    mysqli_stmt_execute($summaryStmt);

    $summaryResult = mysqli_stmt_get_result($summaryStmt);
    $summary = mysqli_fetch_assoc($summaryResult);

    mysqli_stmt_close($summaryStmt);

    // Detail feedback
    $detailStmt = mysqli_prepare($conn, "
        SELECT
            feedback.id,
            customers.customer_name,
            feedback.rating,
            feedback.category,
            feedback.comment,
            feedback.feedback_date
        FROM feedback
        JOIN customers ON feedback.customer_id = customers.id
        WHERE feedback.feedback_date BETWEEN ? AND ?
        ORDER BY feedback.feedback_date DESC, feedback.id DESC
    ");

    mysqli_stmt_bind_param($detailStmt, "ss", $startDate, $endDate);
    mysqli_stmt_execute($detailStmt);

    $feedbackData = mysqli_stmt_get_result($detailStmt);
}

include '../includes/header.php';
?>

<main class="report-page">
    <div class="report-container">

        <div class="report-heading">
            <span class="section-label">FEEDBACK REPORT</span>
            <h1>Laporan Feedback</h1>
            <p>
                Analisis feedback pelanggan berdasarkan periode yang dipilih.
            </p>
        </div>

        <form method="GET" class="report-filter">

            <div class="report-field">
                <label for="start_date">Tanggal Awal</label>
                <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate); ?>"
                    required>
            </div>

            <div class="report-field">
                <label for="end_date">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate); ?>" required>
            </div>

            <button type="submit" class="report-button">
                Tampilkan Laporan
            </button>

        </form>

        <?php if ($error !== ''): ?>

        <div class="report-error">
            <?= htmlspecialchars($error); ?>
        </div>

        <?php elseif ($summary): ?>

        <div class="report-period">
            Periode:
            <strong>
                <?= date('d M Y', strtotime($startDate)); ?>
                –
                <?= date('d M Y', strtotime($endDate)); ?>
            </strong>
        </div>

        <section class="report-stats">

            <div class="report-stat">
                <span>Total Feedback</span>
                <strong><?= (int)$summary['total_feedback']; ?></strong>
            </div>

            <div class="report-stat">
                <span>Rata-rata Rating</span>
                <strong>
                    <?= number_format((float)$summary['average_rating'], 2); ?>/5
                </strong>
            </div>

            <div class="report-stat">
                <span>Feedback Positif</span>
                <strong><?= (int)$summary['positive_feedback']; ?></strong>
            </div>

            <div class="report-stat">
                <span>Feedback Netral</span>
                <strong><?= (int)$summary['neutral_feedback']; ?></strong>
            </div>

            <div class="report-stat">
                <span>Feedback Negatif</span>
                <strong><?= (int)$summary['negative_feedback']; ?></strong>
            </div>

        </section>

        <section class="report-table-section">

            <div class="report-table-heading">
                <h2>Detail Feedback</h2>
                <span>
                    <?= (int)$summary['total_feedback']; ?> data ditemukan
                </span>
            </div>

            <div class="report-table-wrapper">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Rating</th>
                            <th>Kategori</th>
                            <th>Komentar</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (mysqli_num_rows($feedbackData) > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($feedbackData)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['customer_name']); ?></td>
                            <td><?= (int)$row['rating']; ?>/5</td>
                            <td><?= htmlspecialchars($row['category']); ?></td>
                            <td><?= htmlspecialchars($row['comment']); ?></td>
                            <td>
                                <?= date('d M Y', strtotime($row['feedback_date'])); ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>

                        <?php else: ?>

                        <tr>
                            <td colspan="5" class="report-empty">
                                Tidak ada feedback pada periode ini.
                            </td>
                        </tr>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </section>

        <?php endif; ?>

    </div>
</main>

<?php
if (isset($detailStmt)) {
    mysqli_stmt_close($detailStmt);
}

include '../includes/footer.php';
?>