<?php
require_once '../config/database.php';

$error = '';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "
    SELECT * FROM feedback WHERE id = ?
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$feedback = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$feedback) {
    die("Data feedback tidak ditemukan.");
}

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
            UPDATE feedback
            SET customer_id = ?,
                rating = ?,
                category = ?,
                comment = ?,
                feedback_date = ?
            WHERE id = ?
        ");

        mysqli_stmt_bind_param(
            $stmt,
            "iisssi",
            $customer_id,
            $rating,
            $category,
            $comment,
            $feedback_date,
            $id
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            header("Location: index.php");
            exit;
        } else {
            $error = "Gagal memperbarui feedback.";
            mysqli_stmt_close($stmt);
        }
    }
}

$customers = mysqli_query($conn, "
    SELECT id, customer_name
    FROM customers
    ORDER BY customer_name ASC
");

$current = $_POST ?: $feedback;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Feedback | Insightly</title>

    <link rel="stylesheet" href="../assets/css/base.css">
    <link rel="stylesheet" href="../assets/css/buttons.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
</head>

<body>

    <main class="form-page">
        <div class="form-container">

            <h1>Edit Feedback</h1>
            <p>Perbarui informasi feedback pelanggan.</p>

            <?php if ($error !== ''): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">

                <div class="form-group">
                    <label for="customer_id">Customer</label>

                    <select name="customer_id" id="customer_id" required>
                        <?php while ($customer = mysqli_fetch_assoc($customers)): ?>
                            <option value="<?= (int) $customer['id']; ?>"
                                <?= $current['customer_id'] == $customer['id'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($customer['customer_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="rating">Rating</label>

                    <select name="rating" id="rating" required>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i; ?>"
                                <?= $current['rating'] == $i ? 'selected' : ''; ?>>
                                <?= $i; ?> / 5
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>

                    <select name="category" id="category" required>
                        <?php
                        $categories = ['Produk', 'Pelayanan', 'Harga', 'Pengiriman'];
                        foreach ($categories as $category):
                        ?>
                            <option value="<?= $category; ?>"
                                <?= $current['category'] === $category ? 'selected' : ''; ?>>
                                <?= $category; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comment">Comment</label>

                    <textarea name="comment" id="comment"
                        rows="4" required><?= htmlspecialchars($current['comment']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="feedback_date">Feedback Date</label>

                    <input type="date" name="feedback_date"
                        id="feedback_date"
                        value="<?= htmlspecialchars($current['feedback_date']); ?>"
                        required>
                </div>

                <button type="submit" name="submit">
                    Update Feedback
                </button>

                <a href="index.php" class="btn btn-back">
                    Cancel
                </a>

            </form>
        </div>
    </main>

</body>

</html>