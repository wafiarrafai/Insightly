<?php
require_once '../config/database.php';

// Ambil filter dari URL
$search = trim($_GET['search'] ?? '');
$category = $_GET['category'] ?? '';
$rating = $_GET['rating'] ?? '';

$categories = ['Produk', 'Pelayanan', 'Harga', 'Pengiriman'];

$where = [];
$params = [];
$types = '';

// Filter pencarian
if ($search !== '') {
    $where[] = "(
        customers.customer_name LIKE ?
        OR feedback.comment LIKE ?
    )";

    $keyword = "%{$search}%";
    $params[] = $keyword;
    $params[] = $keyword;
    $types .= 'ss';
}

// Filter kategori
if (in_array($category, $categories, true)) {
    $where[] = "feedback.category = ?";
    $params[] = $category;
    $types .= 's';
}

// Filter rating
if (in_array($rating, ['1', '2', '3', '4', '5'], true)) {
    $where[] = "feedback.rating = ?";
    $params[] = (int) $rating;
    $types .= 'i';
}

// Query data
$sql = "
    SELECT
        feedback.id,
        customers.customer_name,
        feedback.rating,
        feedback.category,
        feedback.comment,
        feedback.feedback_date
    FROM feedback
    JOIN customers
        ON feedback.customer_id = customers.id
";

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY feedback.feedback_date DESC";

// Eksekusi query
$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Header download CSV
$filename = 'insightly-feedback-' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output
$output = fopen('php://output', 'w');

// BOM agar karakter Indonesia terbaca di Excel
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Judul kolom
fputcsv($output, [
    'ID',
    'Nama Pelanggan',
    'Rating',
    'Kategori',
    'Komentar',
    'Tanggal Feedback'
]);

// Hindari formula injection di Excel
function safeCsvValue($value)
{
    $value = (string) $value;

    if (preg_match('/^[\s]*[=+\-@]/u', $value)) {
        return "'" . $value;
    }

    return $value;
}

// Isi data CSV
while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, [
        $row['id'],
        safeCsvValue($row['customer_name']),
        $row['rating'],
        safeCsvValue($row['category']),
        safeCsvValue($row['comment']),
        $row['feedback_date']
    ]);
}

fclose($output);
mysqli_stmt_close($stmt);
exit;