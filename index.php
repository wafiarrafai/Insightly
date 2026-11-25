<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Insightly — Kelola Feedback Pelanggan</title>


    <!-- CSS -->

    <link rel="stylesheet" href="assets/css/base.css">

    <link rel="stylesheet" href="assets/css/navbar.css">

    <link rel="stylesheet" href="assets/css/buttons.css">

    <link rel="stylesheet" href="assets/css/landing.css">

    <link rel="stylesheet" href="assets/css/footer.css">

    <link rel="stylesheet" href="assets/css/responsive.css">

</head>


<body>


    <!-- =========================
         NAVBAR
    ========================== -->

    <?php
    require_once './config/database.php';

    $pageTitle = 'Pelanggan';
    $basePath = './';
    $currentPage = 'home';

    $headerActionText = '+ Tambah Pelanggan';
    $headerActionLink = 'customers/create.php';

    $extraCss = 'assets/css/crud.css';

    include './includes/header.php';
    ?>



    <!-- =========================
         HERO
    ========================== -->

    <main>


        <section class="hero">

            <div class="container">


                <span class="section-label">
                    CUSTOMER FEEDBACK MANAGEMENT
                </span>


                <h1>
                    Dengarkan pelanggan.
                    <br>
                    Temukan yang penting.
                </h1>


                <p>
                    Insightly membantu kamu mengelola feedback
                    pelanggan dengan lebih sederhana, terstruktur,
                    dan mudah dipahami.
                </p>


                <div class="hero-actions">

                    <a href="feedback/create.php" class="button button-dark">
                        Tambah Feedback
                    </a>


                    <a href="customers/index.php" class="button button-light">
                        Lihat Pelanggan
                    </a>

                </div>


            </div>

        </section>



        <!-- =========================
             OVERVIEW
        ========================== -->

        <section class="overview">

            <div class="container">

                <div class="overview-header">

                    <div>

                        <span class="section-label">
                            INSIGHTLY
                        </span>

                        <h2>
                            Satu tempat untuk
                            memahami pelanggan.
                        </h2>

                    </div>


                    <p>
                        Catat feedback, kelola pelanggan,
                        dan gunakan data yang terkumpul
                        sebagai dasar untuk mengambil keputusan.
                    </p>

                </div>


                <div class="feature-grid">


                    <div class="feature-card">

                        <span class="feature-number">
                            01
                        </span>

                        <h3>
                            Kelola Pelanggan
                        </h3>

                        <p>
                            Simpan dan kelola informasi pelanggan
                            dalam satu tempat yang terstruktur.
                        </p>

                        <a href="customers/index.php">
                            Lihat pelanggan →
                        </a>

                    </div>



                    <div class="feature-card">

                        <span class="feature-number">
                            02
                        </span>

                        <h3>
                            Kumpulkan Feedback
                        </h3>

                        <p>
                            Catat pengalaman pelanggan lengkap
                            dengan rating, kategori, dan komentar.
                        </p>

                        <a href="feedback/index.php">
                            Lihat feedback →
                        </a>

                    </div>



                    <div class="feature-card">

                        <span class="feature-number">
                            03
                        </span>

                        <h3>
                            Temukan Insight
                        </h3>

                        <p>
                            Gunakan data feedback untuk melihat
                            pola dan hal yang perlu diperbaiki.
                        </p>

                        <a href="insights/index.php">
                            Lihat Insight →
                        </a>

                    </div>


                </div>

            </div>

        </section>


    </main>



    <!-- =========================
         FOOTER
    ========================== -->

    <?php include './includes/footer.php'; ?>


</body>

</html>