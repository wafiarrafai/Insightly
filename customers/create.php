<?php
require_once '../config/database.php';

$pageTitle = 'Tambah Pelanggan';
$currentPage = 'customers';
$basePath = '../';

$error = '';

if (isset($_POST['submit'])) {
    $customer_name = trim($_POST['customer_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    if ($customer_name === '' || $email === '') {
        $error = 'Nama dan email wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO customers (customer_name, email, phone)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $customer_name,
            $email,
            $phone
        );

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            header("Location: index.php");
            exit;
        } else {
            $error = 'Gagal menyimpan data pelanggan.';
        }

        mysqli_stmt_close($stmt);
    }
}

include '../includes/header.php';
?>

<main class="form-page">
    <div class="form-card">

        <div class="form-header">
            <span class="section-label">CUSTOMER MANAGEMENT</span>
            <h1>Add Customer</h1>
            <p>Masukkan informasi pelanggan baru ke dalam Insightly.</p>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label for="customer_name">Customer Name</label>
                <input
                    type="text"
                    id="customer_name"
                    name="customer_name"
                    placeholder="Enter customer name"
                    value="<?= htmlspecialchars($_POST['customer_name'] ?? ''); ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Enter email address"
                    value="<?= htmlspecialchars($_POST['email'] ?? ''); ?>"
                    required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input
                    type="text"
                    id="phone"
                    name="phone"
                    placeholder="Enter phone number"
                    value="<?= htmlspecialchars($_POST['phone'] ?? ''); ?>">
            </div>

            <div class="form-actions">
                <a href="index.php" class="btn btn-secondary">
                    Cancel
                </a>

                <button type="submit" name="submit" class="btn btn-primary">
                    Save Customer
                </button>
            </div>

        </form>
    </div>
</main>

<?php include '../includes/footer.php'; ?>