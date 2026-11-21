<?php
$pageTitle = $pageTitle ?? 'Insightly';
$basePath = $basePath ?? '';
$currentPage = $currentPage ?? '';

$headerActionText = $headerActionText ?? '+ Tambah Feedback';
$headerActionLink = $headerActionLink ?? 'feedback/create.php';
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= htmlspecialchars($pageTitle); ?> — Insightly</title>

    <link rel="stylesheet" href="<?= $basePath; ?>assets/css/base.css">
    <link rel="stylesheet" href="<?= $basePath; ?>assets/css/buttons.css">
    <link rel="stylesheet" href="<?= $basePath; ?>assets/css/navbar.css">
    <link rel="stylesheet" href="<?= $basePath; ?>assets/css/footer.css">
    <link rel="stylesheet" href="<?= $basePath; ?>assets/css/responsive.css">
    <link rel="stylesheet" href="<?= $basePath; ?>assets/css/form.css">

    <?php if (!empty($extraCss)): ?>
        <link rel="stylesheet" href="<?= $basePath . $extraCss; ?>">
    <?php endif; ?>
</head>

<body>
    <header class="navbar">
        <div class="container nav-wrapper">
            <a href="<?= $basePath ?>index.php" class="logo">
                <img
                    src="<?= $basePath ?>assets/images/insightly-logo.png"
                    alt="Insightly Logo"
                    class="logo-image">
                <span>insightly</span>
            </a>
            <nav class="nav-menu">
                <a href="<?= $basePath; ?>index.php"
                    class="<?= $currentPage === 'home' ? 'active' : ''; ?>">
                    Beranda
                </a>
                <a href="<?= $basePath; ?>customers/index.php"
                    class="<?= $currentPage === 'customers' ? 'active' : ''; ?>">
                    Pelanggan
                </a>
                <a href="<?= $basePath; ?>feedback/index.php"
                    class="<?= $currentPage === 'feedback' ? 'active' : ''; ?>">
                    Feedback
                </a>
                <a href="<?= $basePath; ?>insights/index.php"
                    class="<?= $currentPage === 'insights' ? 'active' : ''; ?>">
                    Insights
                </a>
            </nav>
            <a href="<?= $basePath . $headerActionLink; ?>" class="nav-button">
                <?= htmlspecialchars($headerActionText); ?>
            </a>
        </div>
    </header>