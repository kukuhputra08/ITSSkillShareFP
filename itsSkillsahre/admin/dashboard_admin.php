<?php
session_start();
include 'config.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background: linear-gradient(120deg, #f9fafb 0%, #e2e8f0 100%);
            font-family: 'Inter', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
            color: #1e293b;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: #fff;
            box-shadow: 5px 0 30px rgba(55, 78, 180, 0.12);
            padding: 40px 24px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar .brand {
            font-size: 1.75rem;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: 0.05em;
            margin-bottom: 56px;
            text-align: center;
            user-select: none;
        }

        .sidebar .nav-link {
            color: #2563eb;
            font-weight: 600;
            font-size: 1.15rem;
            margin-bottom: 22px;
            border-radius: 12px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            transition: background-color 0.3s ease, color 0.3s ease;
            user-select: none;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: #dbeafe;
            color: #1e40af !important;
            text-decoration: none;
        }

        .main-content {
            margin-left: 260px;
            padding: 64px 48px 48px;
            min-height: 100vh;
            background-color: #f9fafb;
        }

        .glass-card {
            background: #fff;
            box-shadow: 0 18px 48px rgba(55, 78, 180, 0.15);
            border-radius: 28px;
            max-width: 800px;
            margin: 0 auto;
            padding: 48px 48px 40px;
            text-align: center;
            user-select: none;
            transition: box-shadow 0.3s ease;
        }

        .glass-card:hover {
            box-shadow: 0 28px 70px rgba(55, 78, 180, 0.25);
        }

        h2 {
            color: #2563eb;
            font-weight: 800;
            font-size: 2.8rem;
            margin-bottom: 24px;
            letter-spacing: 0.02em;
        }

        p.welcome-text {
            font-weight: 600;
            font-size: 1.3rem;
            color: #475569;
            margin-bottom: 48px;
        }

        .summary-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 36px;
        }

        .summary-card {
            background: #e0e7ff;
            border-radius: 22px;
            padding: 32px 28px;
            box-shadow: 0 6px 26px rgba(38, 57, 254, 0.1);
            cursor: pointer;
            transition: box-shadow 0.3s ease, background-color 0.3s ease;
            user-select: none;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .summary-card:hover {
            background-color: #c7d2fe;
            box-shadow: 0 12px 36px rgba(38, 57, 254, 0.25);
        }

        .summary-card i {
            font-size: 42px;
            color: #2563eb;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .summary-card:hover i {
            transform: scale(1.15);
        }

        .summary-card b {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: #1e40af;
            user-select: text;
        }

        .summary-card div {
            font-size: 1rem;
            color: #4b5563;
            user-select: text;
        }

        .btn-logout {
            border-radius: 35px;
            border: 3px solid #ef4444;
            color: #ef4444;
            font-weight: 700;
            padding: 14px 0;
            transition: all 0.3s ease;
            background: none;
            width: 100%;
            text-align: center;
            margin-top: 24px;
            user-select: none;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .btn-logout:hover {
            background: #ef4444;
            color: white;
            box-shadow: 0 8px 26px #fca5a5;
            border-color: #ef4444;
        }

        @media (max-width: 900px) {
            .main-content {
                margin-left: 0;
                padding: 40px 24px 32px;
            }

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                box-shadow: none;
                padding: 24px 16px;
                display: flex;
                flex-direction: row;
                justify-content: space-around;
            }

            .sidebar .brand {
                margin-bottom: 0;
                font-size: 1.25rem;
            }

            .sidebar .nav-link {
                margin-bottom: 0;
                font-size: 1rem;
                padding: 8px 12px;
            }

            .glass-card {
                padding: 32px 24px 28px;
                max-width: 100%;
            }

            .summary-row {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 24px;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar" role="navigation" aria-label="Sidebar Admin ITS SkillShare">
        <span class="brand">ITS SkillShare</span>
        <a href="dashboard_admin.php" class="nav-link active" aria-current="page"><i data-feather="home"></i> Dashboard</a>
        <a href="manage_users.php" class="nav-link"><i data-feather="users"></i> Kelola Pengguna</a>
        <a href="manage_jasa.php" class="nav-link"><i data-feather="briefcase"></i> Kelola Jasa</a>
        <a href="manage_orders.php" class="nav-link"><i data-feather="clipboard"></i> Kelola Pesanan</a>
        <a href="reports.php" class="nav-link"><i data-feather="bar-chart-2"></i> Laporan</a>
        <button class="btn btn-logout" onclick="window.location.href='/itsSkillsahre/login.php'" aria-label="Logout admin">Logout</button>
    </aside>

    <main class="main-content" role="main" aria-label="Dashboard Admin ITS SkillShare">
        <section class="glass-card">
            <h2>Dashboard Admin</h2>
            <p class="welcome-text">
                Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>!<br />
                Anda login sebagai <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>.
            </p>
            <div class="summary-row" role="list">
                <article class="summary-card" role="listitem" tabindex="0" onclick="location.href='manage_users.php';" aria-label="Kelola pengguna">
                    <i data-feather="users" aria-hidden="true"></i>
                    <b>Kelola Pengguna</b>
                    <div>Tambah, edit, atau hapus user</div>
                </article>
                <article class="summary-card" role="listitem" tabindex="0" onclick="location.href='manage_jasa.php';" aria-label="Kelola jasa">
                    <i data-feather="briefcase" aria-hidden="true"></i>
                    <b>Kelola Jasa</b>
                    <div>Atur jasa yang tersedia di platform</div>
                </article>
                <article class="summary-card" role="listitem" tabindex="0" onclick="location.href='manage_order_admin.php';" aria-label="Kelola pesanan">
                    <i data-feather="clipboard" aria-hidden="true"></i>
                    <b>Kelola Pesanan</b>
                    <div>Pantau dan kelola semua pesanan</div>
                </article>
            </div>
            <p style="color:#64748b; font-size:1rem; margin-top: 40px;">
                <strong>ITS SkillShare</strong> memberikan kontrol penuh kepada admin untuk memantau seluruh aktivitas pengguna dan jasa di platform.
            </p>
        </section>
    </main>

    <script>
        feather.replace()
    </script>
</body>

</html>