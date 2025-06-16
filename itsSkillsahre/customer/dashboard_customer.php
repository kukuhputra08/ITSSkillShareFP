<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Dashboard Customer | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <!-- Google Font: SF Pro Text (Apple-style font) -->
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Text&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        /* Reset & Base */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'SF Pro Text', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen,
                Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f0f2f5 0%, #ffffff 100%);
            color: #1c1c1e;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            padding: 48px 28px 32px;
            background: rgba(255 255 255 / 0.95);
            box-shadow: 6px 0 24px rgb(55 78 180 / 0.08);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            user-select: none;
            backdrop-filter: saturate(180%) blur(12px);
        }

        .sidebar .brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: #007aff;
            letter-spacing: 0.05em;
            margin-bottom: 56px;
            text-align: center;
            text-shadow: 0 0 3px rgb(0 122 255 / 0.4);
        }

        .sidebar a.nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 1.15rem;
            color: #007aff;
            text-decoration: none;
            margin-bottom: 24px;
            transition: background-color 0.25s ease, box-shadow 0.25s ease;
            box-shadow: inset 0 0 0 0 transparent;
        }

        .sidebar a.nav-link:hover,
        .sidebar a.nav-link.active {
            background: #cce4ff;
            box-shadow: inset 0 0 12px 3px rgb(0 122 255 / 0.3);
            color: #004db3;
            text-decoration: none;
        }

        .btn-logout {
            background: #ff3b30;
            border-radius: 32px;
            padding: 14px 0;
            font-weight: 700;
            font-size: 1.1rem;
            color: #fff;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 16px rgb(255 59 48 / 0.6);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-logout:hover {
            background: #cc2b22;
            box-shadow: 0 6px 20px rgb(204 43 34 / 0.8);
        }

        /* Main content */
        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 60px 48px;
            background: #fefefe;
            min-height: 100vh;
        }

        .glass-card {
            background: #fff;
            border-radius: 32px;
            padding: 56px 64px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 24px 60px rgb(55 78 180 / 0.16);
            text-align: center;
            user-select: none;
            backdrop-filter: saturate(180%) blur(14px);
            transition: box-shadow 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 0 36px 96px rgb(55 78 180 / 0.3);
        }

        h2 {
            font-weight: 800;
            font-size: 3rem;
            color: #007aff;
            margin-bottom: 36px;
            letter-spacing: 0.03em;
        }

        p.welcome-text {
            font-weight: 600;
            font-size: 1.4rem;
            color: #222;
            margin-bottom: 60px;
        }

        /* Summary cards */
        .summary-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 36px;
            margin-bottom: 80px;
        }

        .summary-card {
            background: #f0f5ff;
            border-radius: 26px;
            padding: 36px 28px;
            box-shadow: 0 8px 26px rgb(0 122 255 / 0.15);
            cursor: pointer;
            transition: box-shadow 0.3s ease, background-color 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            user-select: none;
        }

        .summary-card:hover {
            box-shadow: 0 12px 38px rgb(0 122 255 / 0.3);
            background: #d5e5ff;
        }

        .summary-card i {
            font-size: 44px;
            color: #007aff;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .summary-card:hover i {
            transform: scale(1.15);
        }

        .summary-card b {
            font-size: 1.4rem;
            margin-bottom: 12px;
            color: #004db3;
        }

        .summary-card small {
            color: #3470cc;
            font-weight: 600;
            font-size: 1rem;
            line-height: 1.3;
            max-width: 160px;
        }

        /* Footer text */
        .footer-text {
            font-size: 1.1rem;
            color: #4b5563;
            max-width: 760px;
            margin: 0 auto;
            font-weight: 500;
            line-height: 1.5;
            user-select: none;
        }

        /* Responsive */
        @media (max-width: 720px) {
            .glass-card {
                padding: 42px 36px;
            }

            .summary-row {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                gap: 28px;
                margin-bottom: 48px;
            }

            .summary-card small {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .main-content {
                margin-left: 0;
                padding: 40px 24px;
            }

            .sidebar {
                display: none;
            }

            body {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar" role="navigation" aria-label="Sidebar Navigation">
        <span class="brand">ITS SkillShare</span>
        <a href="dashboard_customer.php" class="nav-link active" aria-current="page"><i data-feather="home"></i> Dashboard</a>
        <a href="cari_jasa.php" class="nav-link"><i data-feather="search"></i> Cari Jasa</a>
        <a href="riwayat_order.php" class="nav-link"><i data-feather="clipboard"></i> Riwayat Order</a>
        <a href="review_saya.php" class="nav-link"><i data-feather="star"></i> Review Saya</a>
        <a href="profil.php" class="nav-link"><i data-feather="user"></i> Profil</a>
        <a href="ajukan_provider.php" class="nav-link"><i data-feather="briefcase"></i> Ajukan Provider</a>
        <button class="btn btn-logout mt-auto" onclick="window.location.href='/itsSkillsahre/login.php'" aria-label="Logout dari aplikasi ITS SkillShare">Logout</button>
    </aside>


    <main class="main-content" role="main" aria-label="Dashboard Customer">
        <section class="glass-card">
            <h2>Dashboard Customer</h2>
            <p class="welcome-text">Selamat datang, <b><?= htmlspecialchars($_SESSION['nama']) ?></b>!<br>Anda login sebagai <b><?= htmlspecialchars($_SESSION['role']) ?></b>.</p>
            <div class="summary-row" role="list">
                <article class="summary-card" role="listitem" tabindex="0" onclick="location.href='cari_jasa.php';" aria-label="Cari dan pesan jasa mahasiswa ITS">
                    <i data-feather="search" aria-hidden="true"></i>
                    <b>Cari & Pesan Jasa</b>
                    <small>Temukan layanan mahasiswa ITS</small>
                </article>
                <article class="summary-card" role="listitem" tabindex="0" onclick="location.href='riwayat_order.php';" aria-label="Riwayat order dan status pemesanan">
                    <i data-feather="clipboard" aria-hidden="true"></i>
                    <b>Riwayat Order</b>
                    <small>Pantau status dan histori pemesanan</small>
                </article>
                <article class="summary-card" role="listitem" tabindex="0" onclick="location.href='review_saya.php';" aria-label="Review dan rating jasa">
                    <i data-feather="star" aria-hidden="true"></i>
                    <b>Review & Rating</b>
                    <small>Beri ulasan untuk jasa yang sudah selesai</small>
                </article>
                <article class="summary-card" role="listitem" tabindex="0" onclick="location.href='profil.php';" aria-label="Profil dan pengaturan akun">
                    <i data-feather="user" aria-hidden="true"></i>
                    <b>Profil & Akun</b>
                    <small>Lihat dan edit data diri kamu</small>
                </article>
            </div>
            <p class="footer-text" aria-live="polite">
                <b>ITS SkillShare</b> memudahkan mahasiswa menemukan dan memesan jasa mahasiswa ITS, serta memantau order dan memberi review jasa secara online.
            </p>
        </section>
    </main>

    <script>
        feather.replace();
    </script>
</body>

</html>