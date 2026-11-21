<?php
require_once '../config/database.php';

$error = '';

$customers = mysqli_query($conn, "
    SELECT id, customer_name
    FROM customers
    ORDER BY customer_name ASC
");

if (isset($_POST['submit'])) {
    $customer_id = (int) $_POST['customer_id'];
    $rating = (int) $_POST['rating'];
    $category = trim($_POST['category']);
    $comment = trim($_POST['comment']);
    $feedback_date = $_POST['feedback_date'];

    if (
        $customer_id <= 0 ||
        $rating < 1 ||
        $rating > 5 ||
        $category === '' ||
        $comment === '' ||
        $feedback_date === ''
    ) {
        $error = "Semua data wajib diisi dengan benar.";
    } else {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO feedback
            (customer_id, rating, category, comment, feedback_date)
            VALUES (?, ?, ?, ?, ?)
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "iisss",
            $customer_id,
            $rating,
            $category,
            $comment,
            $feedback_date
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal menyimpan feedback.";
            mysqli_stmt_close($stmt);
        }
    }
}

// Pengaturan halaman
$pageTitle = 'Tambah Feedback';
$basePath = '../';
$currentPage = 'feedback';
$headerActionText = 'Kembali ke Feedback';
$headerActionLink = 'feedback/index.php';
$extraCss = 'assets/css/form.css';

include '../includes/header.php';
?>

<main class="form-page">
    <div class="form-container">

        <div class="form-heading">
            <h1>Add Feedback</h1>
            <p>Masukkan informasi feedback pelanggan.</p>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label for="customer_id">Customer</label>

                <select name="customer_id" id="customer_id" required>
                    <option value="">Select Customer</option>

                    <?php while ($customer = mysqli_fetch_assoc($customers)): ?>
                        <option
                            value="<?= $customer['id'] ?>"
                            <?= (isset($_POST['customer_id']) && $_POST['customer_id'] == $customer['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($customer['customer_name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="rating">Rating</label>

                <select name="rating" id="rating" required>
                    <option value="">Select Rating</option>

                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option
                            value="<?= $i ?>"
                            <?= (isset($_POST['rating']) && $_POST['rating'] == $i) ? 'selected' : '' ?>>
                            <?= $i ?> Star
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="category">Category</label>

                <select name="category" id="category" required>
                    <option value="">Select Category</option>

                    <?php
                    $categories = ['Produk', 'Pelayanan', 'Harga', 'Pengiriman'];

                    foreach ($categories as $categoryOption):
                    ?>
                        <option
                            value="<?= $categoryOption ?>"
                            <?= (isset($_POST['category']) && $_POST['category'] === $categoryOption) ? 'selected' : '' ?>>
                            <?= $categoryOption ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="comment">Comment</label>

                <textarea
                    name="comment"
                    id="comment"
                    rows="5"
                    placeholder="Write customer feedback..."
                    required><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="feedback_date">Feedback Date</label>

                <input
                    type="date"
                    name="feedback_date"
                    id="feedback_date"
                    value="<?= htmlspecialchars($_POST['feedback_date'] ?? date('Y-m-d')) ?>"
                    required>
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-back">
                    Cancel
                </a>

                <button type="submit" name="submit" class="btn">
                    Save Feedback
                </button>
            </div>

        </form>

    </div>
</main>

<?php include '../includes/footer.php'; ?>