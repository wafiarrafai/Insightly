<?php
require_once '../config/database.php';

// Statistik feedback

$statsQuery = mysqli_query($conn, "
    SELECT
        COUNT(*) AS total_feedback,
        COALESCE(AVG(rating), 0) AS average_rating,

        COALESCE(SUM(
            CASE WHEN rating >= 4 THEN 1 ELSE 0 END
        ), 0) AS positive_feedback,

        COALESCE(SUM(
            CASE WHEN rating = 3 THEN 1 ELSE 0 END
        ), 0) AS neutral_feedback,

        COALESCE(SUM(
            CASE WHEN rating <= 2 THEN 1 ELSE 0 END
        ), 0) AS negative_feedback

    FROM feedback
");

$stats = mysqli_fetch_assoc($statsQuery);

// Total pelanggan
$customerQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total_customers
    FROM customers
");

$customerData = mysqli_fetch_assoc($customerQuery);

// Data sentimen
$totalFeedback = (int) $stats['total_feedback'];
$positiveFeedback = (int) $stats['positive_feedback'];
$neutralFeedback = (int) $stats['neutral_feedback'];
$negativeFeedback = (int) $stats['negative_feedback'];

$positivePercentage = $totalFeedback > 0
    ? ($positiveFeedback / $totalFeedback) * 100
    : 0;

$neutralPercentage = $totalFeedback > 0
    ? ($neutralFeedback / $totalFeedback) * 100
    : 0;

$negativePercentage = $totalFeedback > 0
    ? ($negativeFeedback / $totalFeedback) * 100
    : 0;


// Data rating
$ratingQuery = mysqli_query($conn, "
    SELECT rating, COUNT(*) AS total
    FROM feedback
    GROUP BY rating
");

$ratingData = [
    1 => 0,
    2 => 0,
    3 => 0,
    4 => 0,
    5 => 0
];

while ($row = mysqli_fetch_assoc($ratingQuery)) {
    $ratingData[(int) $row['rating']] = (int) $row['total'];
}

$maxRating = max($ratingData) ?: 1;

// Data kategori
$categoryQuery = mysqli_query($conn, "
    SELECT category, COUNT(*) AS total
    FROM feedback
    GROUP BY category
    ORDER BY total DESC
");

$categories = [];

while ($row = mysqli_fetch_assoc($categoryQuery)) {
    $categories[] = $row;
}

$maxCategory = 1;

foreach ($categories as $category) {
    $maxCategory = max($maxCategory, (int) $category['total']);
}


// MONTHLY FEEDBACK TREND
$monthlyQuery = mysqli_query($conn, "
    SELECT
        YEAR(feedback_date) AS feedback_year,
        MONTH(feedback_date) AS feedback_month,
        COUNT(*) AS total_feedback,
        ROUND(AVG(rating), 1) AS average_rating
    FROM feedback
    WHERE feedback_date >= DATE_SUB(
        CURDATE(),
        INTERVAL 6 MONTH
    )
    GROUP BY
        YEAR(feedback_date),
        MONTH(feedback_date)
    ORDER BY
        feedback_year ASC,
        feedback_month ASC
");

$monthlyData = [];

while ($row = mysqli_fetch_assoc($monthlyQuery)) {
    $row['month_label'] = date(
        'M Y',
        mktime(0, 0, 0, (int)$row['feedback_month'], 1, (int)$row['feedback_year'])
    );

    $monthlyData[] = $row;
}

$maxMonthlyFeedback = 1;

foreach ($monthlyData as $month) {
    $maxMonthlyFeedback = max(
        $maxMonthlyFeedback,
        (int)$month['total_feedback']
    );
}

?>

<?php
$pageTitle = 'Customer Insights';
$basePath = '../';
$currentPage = 'insights';

$headerActionText = '+ Tambah Feedback';
$headerActionLink = 'feedback/create.php';

$extraCss = 'assets/css/insights.css';

include '../includes/header.php';
?>
<!-- MAIN CONTENT -->
<main class="page-content">

    <div class="container">

        <!-- PAGE HEADER -->
        <div class="page-header">

            <div>
                <span class="section-label">
                    DATA ANALYTICS
                </span>

                <h1>
                    Customer Insights
                </h1>

                <p>
                    Pahami pengalaman pelanggan melalui data feedback
                    yang telah terkumpul.
                </p>
            </div>

            <a href="../feedback/index.php" class="button button-dark">
                Lihat Feedback
            </a>

        </div>


        <!-- STATISTICS -->
        <section class="stats-grid">

            <div class="stat-card">
                <span class="stat-label">Total Feedback</span>

                <h2>
                    <?= number_format($totalFeedback); ?>
                </h2>

                <p>Seluruh feedback terkumpul</p>
            </div>


            <div class="stat-card">
                <span class="stat-label">Average Rating</span>

                <h2>
                    <?= number_format((float) $stats['average_rating'], 1); ?>
                    <small>/ 5</small>
                </h2>

                <p>Rata-rata penilaian pelanggan</p>
            </div>


            <div class="stat-card">
                <span class="stat-label">Positive Feedback</span>

                <h2>
                    <?= number_format($positivePercentage, 1); ?>%
                </h2>

                <p>Feedback dengan rating 4–5</p>
            </div>


            <div class="stat-card">
                <span class="stat-label">Total Customers</span>

                <h2>
                    <?= number_format((int) $customerData['total_customers']); ?>
                </h2>

                <p>Pelanggan terdaftar</p>
            </div>

        </section>


        <!-- ANALYSIS -->
        <section class="analysis-grid">

            <!-- RATING DISTRIBUTION -->
            <div class="analysis-card">

                <div class="analysis-heading">
                    <h2>Rating Distribution</h2>
                    <p>Jumlah feedback berdasarkan rating pelanggan.</p>
                </div>

                <div class="rating-chart">

                    <?php foreach ($ratingData as $rating => $total): ?>

                    <div class="rating-row">

                        <span class="rating-label">
                            <?= $rating; ?> ★
                        </span>

                        <div class="bar-background">
                            <div class="bar-fill" style="width: <?= ($total / $maxRating) * 100; ?>%">
                            </div>
                        </div>

                        <span class="bar-value">
                            <?= $total; ?>
                        </span>

                    </div>

                    <?php endforeach; ?>

                </div>

            </div>


            <!-- CATEGORY ANALYSIS -->
            <div class="analysis-card">

                <div class="analysis-heading">
                    <h2>Feedback by Category</h2>
                    <p>Jumlah feedback berdasarkan kategori.</p>
                </div>

                <div class="category-chart">

                    <?php if (count($categories) > 0): ?>

                    <?php foreach ($categories as $category): ?>

                    <div class="category-item">

                        <div class="category-info">

                            <span>
                                <?= htmlspecialchars($category['category']); ?>
                            </span>

                            <strong>
                                <?= (int) $category['total']; ?>
                            </strong>

                        </div>

                        <div class="bar-background">
                            <div class="bar-fill category-fill"
                                style="width: <?= ((int) $category['total'] / $maxCategory) * 100; ?>%">
                            </div>
                        </div>

                    </div>

                    <?php endforeach; ?>

                    <?php else: ?>

                    <p class="empty-insight">
                        Belum ada data kategori feedback.
                    </p>

                    <?php endif; ?>

                </div>

            </div>

        </section>


        <!-- SENTIMENT ANALYSIS -->
        <section class="sentiment-section">

            <div class="sentiment-heading">
                <span class="section-label">CUSTOMER SENTIMENT</span>
                <h2>Analisis Kepuasan Pelanggan</h2>
                <p>
                    Pengelompokan feedback berdasarkan rating yang diberikan pelanggan.
                </p>
            </div>

            <div class="sentiment-grid">

                <div class="sentiment-card positive">
                    <h3>Positif</h3>

                    <strong>
                        <?= number_format($positivePercentage, 1); ?>%
                    </strong>

                    <p>
                        <?= $positiveFeedback; ?> feedback
                    </p>
                </div>

                <div class="sentiment-card neutral">
                    <h3>Netral</h3>

                    <strong>
                        <?= number_format($neutralPercentage, 1); ?>%
                    </strong>

                    <p>
                        <?= $neutralFeedback; ?> feedback
                    </p>
                </div>

                <div class="sentiment-card negative">
                    <h3>Negatif</h3>

                    <strong>
                        <?= number_format($negativePercentage, 1); ?>%
                    </strong>

                    <p>
                        <?= $negativeFeedback; ?> feedback
                    </p>
                </div>

            </div>

            <?php
            if ($totalFeedback === 0) {
                $sentimentMessage = 'Belum ada feedback untuk dianalisis.';
            } elseif ($negativeFeedback > $positiveFeedback) {
                $sentimentMessage = 'Feedback negatif lebih banyak daripada feedback positif. Perhatikan kembali aspek layanan yang perlu ditingkatkan.';
            } elseif ($positiveFeedback > $negativeFeedback) {
                $sentimentMessage = 'Feedback positif lebih banyak daripada feedback negatif. Pertahankan kualitas layanan dan pengalaman pelanggan.';
            } else {
                $sentimentMessage = 'Jumlah feedback positif dan negatif sama. Evaluasi feedback pelanggan untuk menemukan area perbaikan.';
            }
            ?>

            <div class="sentiment-insight">
                <strong>Ringkasan Analisis</strong>
                <p><?= htmlspecialchars($sentimentMessage); ?></p>
            </div>

        </section>


        <!-- MONTHLY FEEDBACK TREND -->
        <section class="monthly-section">

            <div class="monthly-heading">
                <div>
                    <span class="section-label">PERFORMANCE OVERVIEW</span>
                    <h2>Monthly Feedback Trend</h2>
                    <p>Perkembangan jumlah feedback dan rata-rata rating pelanggan.</p>
                </div>
            </div>

            <?php if (count($monthlyData) > 0): ?>

            <div class="monthly-chart">

                <?php foreach ($monthlyData as $month): ?>

                <div class="monthly-item">

                    <div class="monthly-info">
                        <span><?= htmlspecialchars($month['month_label']); ?></span>

                        <div>
                            <strong>
                                <?= (int)$month['total_feedback']; ?> feedback
                            </strong>

                            <small>
                                <?= number_format((float)$month['average_rating'], 1); ?> / 5 ★
                            </small>
                        </div>
                    </div>

                    <div class="monthly-bar-background">
                        <div class="monthly-bar-fill"
                            style="width: <?= ((int)$month['total_feedback'] / $maxMonthlyFeedback) * 100; ?>%">
                        </div>
                    </div>

                </div>

                <?php endforeach; ?>

            </div>

            <?php else: ?>

            <p class="empty-insight">
                Belum ada data feedback dalam enam bulan terakhir.
            </p>

            <?php endif; ?>

        </section>

        <!-- INFORMATION -->
        <section class="insight-note">

            <div>
                <span class="section-label">
                    INSIGHTLY ANALYTICS
                </span>

                <h2>
                    Data membantu memahami pelanggan.
                </h2>

                <p>
                    Gunakan ringkasan rating dan kategori feedback
                    untuk mengetahui aspek yang paling banyak
                    mendapatkan perhatian pelanggan.
                </p>
            </div>

            <a href="../feedback/index.php" class="button button-dark">
                Lihat Semua Feedback
            </a>

        </section>

    </div>

</main>


<?php include '../includes/footer.php'; ?>

</body>

</html>