<?php
require_once '../config/database.php';

$error = '';
$categories = ['Produk', 'Pelayanan', 'Harga', 'Pengiriman'];

// Ambil ID feedback
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}

if (!$id || $id <= 0) {
    header("Location: index.php");
    exit;
}

// Ambil data feedback
$stmt = mysqli_prepare($conn, "
    SELECT * FROM feedback WHERE id = ?
");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$feedback = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

if (!$feedback) {
    header("Location: index.php?error=notfound");
    exit;
}

// Ambil data pelanggan
$customers = mysqli_query($conn, "
    SELECT id, customer_name
    FROM customers
    ORDER BY customer_name ASC
");

// Nilai awal form
$current = $feedback;

// Proses update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = (int) ($_POST['customer_id'] ?? 0);
    $rating = (int) ($_POST['rating'] ?? 0);
    $category = trim($_POST['category'] ?? '');
    $comment = trim($_POST['comment'] ?? '');
    $feedback_date = trim($_POST['feedback_date'] ?? '');

    $current = [
        'customer_id' => $customer_id,
        'rating' => $rating,
        'category' => $category,
        'comment' => $comment,
        'feedback_date' => $feedback_date
    ];

    // Validasi tanggal
    $dateObject = DateTime::createFromFormat('Y-m-d', $feedback_date);
    $validDate = $dateObject &&
        $dateObject->format('Y-m-d') === $feedback_date;

    // Pastikan pelanggan tersedia
    $checkCustomer = mysqli_prepare(
        $conn,
        "SELECT id FROM customers WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $checkCustomer,
        "i",
        $customer_id
    );

    mysqli_stmt_execute($checkCustomer);
    $customerResult = mysqli_stmt_get_result($checkCustomer);
    $customerExists = mysqli_num_rows($customerResult) > 0;

    mysqli_stmt_close($checkCustomer);

    if (
        !$customerExists ||
        $rating < 1 ||
        $rating > 5 ||
        !in_array($category, $categories, true) ||
        $comment === '' ||
        !$validDate
    ) {
        $error = "Periksa kembali data feedback. Pastikan semua data valid.";
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

            header("Location: index.php?updated=success");
            exit;
        } else {
            $error = "Gagal memperbarui feedback.";
            mysqli_stmt_close($stmt);
        }
    }
}
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

                <input type="hidden" name="id" value="<?= (int) $id; ?>">

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
                        <option value="<?= $i; ?>" <?= $current['rating'] == $i ? 'selected' : ''; ?>>
                            <?= $i; ?> / 5
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>

                    <select name="category" id="category" required>
                        <?php foreach ($categories as $categoryOption): ?>
                        <option value="<?= htmlspecialchars($categoryOption); ?>"
                            <?= $current['category'] === $categoryOption ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($categoryOption); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="comment">Comment</label>

                    <textarea name="comment" id="comment" rows="4"
                        required><?= htmlspecialchars($current['comment']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="feedback_date">Feedback Date</label>

                    <input type="date" name="feedback_date" id="feedback_date"
                        value="<?= htmlspecialchars($current['feedback_date']); ?>" required>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn btn-back">
                        Cancel
                    </a>

                    <button type="submit" name="submit" class="btn">
                        Update Feedback
                    </button>
                </div>

            </form>
        </div>
    </main>

</body>

</html>