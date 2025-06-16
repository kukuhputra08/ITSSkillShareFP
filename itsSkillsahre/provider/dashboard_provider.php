<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'provider') {
    header("Location: login.php");
    exit();
}

// ambil id user yang login
$id_user = $_SESSION['user_id'];

// ambil total order provider
$sql = "SELECT 
            p.providerID, 
            p.namaUsaha, 
            COUNT(o.orderId) AS total_order
        FROM provider p
        JOIN service_provider sp ON p.providerID = sp.Provider_providerID
        JOIN service s ON sp.Service_serviceId = s.serviceId
        LEFT JOIN `Order` o ON s.serviceId = o.Service_serviceId
        WHERE p.User_user_id = ?
        GROUP BY p.providerID, p.namaUsaha";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$total_order = $data ? $data['total_order'] : 0;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Dashboard Provider | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background: linear-gradient(120deg, #f0f4ff 0%, #e8efff 100%);
            font-family: 'Inter', Arial, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            height: 100vh;
            width: 240px;
            background: #fff;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 5px 0 30px rgba(55, 78, 180, 0.12);
            padding: 40px 24px 32px 24px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .sidebar .brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2563eb;
            letter-spacing: 0.04em;
            margin-bottom: 48px;
            text-align: center;
        }

        .sidebar .nav-link {
            color: #2563eb;
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 22px;
            border-radius: 12px;
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background-color 0.3s, color 0.3s;
            text-decoration: none;
        }

        .sidebar .nav-link.active,
        .sidebar .nav-link:hover {
            background-color: #dbeafe;
            color: #1e40af !important;
            text-decoration: none;
        }

        .main-content {
            margin-left: 240px;
            padding: 60px 32px 40px 32px;
            min-height: 100vh;
            background-color: #f9fafb;
        }

        .glass-card {
            background: #fff;
            box-shadow: 0 16px 48px rgba(55, 78, 180, 0.15);
            border-radius: 28px;
            max-width: 720px;
            margin: 0 auto;
            padding: 48px 48px 40px 48px;
            text-align: center;
        }

        .summary-card {
            background: #e3edfd;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(55, 78, 180, 0.1);
            padding: 28px 32px;
            min-height: 130px;
            text-align: center;
            margin-bottom: 28px;
            transition: box-shadow 0.3s ease;
        }

        .summary-card:hover {
            box-shadow: 0 12px 36px rgba(55, 78, 180, 0.2);
            cursor: pointer;
        }

        .summary-card i {
            font-size: 36px;
            color: #2563eb;
            margin-bottom: 14px;
        }

        .summary-card b {
            display: block;
            font-size: 1.22rem;
            margin-bottom: 8px;
        }

        .summary-card div {
            font-size: 0.95rem;
            color: #374151;
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
            margin-top: 16px;
        }

        .btn-logout:hover {
            background: #ef4444;
            color: white;
            box-shadow: 0 6px 20px #fca5a5;
            border-color: #ef4444;
        }

        @media (max-width: 900px) {
            .main-content {
                margin-left: 0;
                padding: 40px 20px 20px 20px;
            }

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
                box-shadow: none;
                padding: 24px 12px;
            }

            .glass-card {
                padding: 28px 20px 24px 20px;
            }

            .summary-card {
                padding: 20px 16px;
                font-size: 0.9rem;
                min-height: 120px;
                margin-bottom: 22px;
            }
        }
    </style>
</head>

<body>
    <aside class="sidebar d-flex flex-column justify-content-between">
        <div>
            <span class="brand mb-5">ITS SkillShare</span>
            <a href="dashboard_provider.php" class="nav-link active"><i data-feather="home"></i> Dashboard</a>
            <a href="manage_jasa.php" class="nav-link"><i data-feather="briefcase"></i> Kelola Jasa</a>
            <a href="manage_orders.php" class="nav-link"><i data-feather="clipboard"></i> Daftar Pesanan</a>
            <a href="statistik_rating.php" class="nav-link"><i data-feather="star"></i> Statistik Rating</a>
        </div>
        <a href="/itsSkillsahre/login.php" class="btn btn-logout"><i data-feather="log-out" style="margin-top:-2px"></i> Logout</a>
    </aside>

    <div class="main-content">
        <div class="glass-card">
            <h2 class="mb-4 fw-bold" style="color:#2563eb; font-size:2.5rem; letter-spacing:.02em;">Dashboard Provider</h2>
            <p class="mb-4" style="font-size:1.25rem; color:#4b5563;">
                Selamat datang, <b><?= htmlspecialchars($_SESSION['nama']) ?></b>!<br />
                Anda login sebagai <b><?= htmlspecialchars($_SESSION['role']) ?></b>.
            </p>

            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="summary-card" onclick="window.location.href='manage_jasa.php'">
                        <i data-feather="briefcase"></i>
                        <b>Kelola Jasa</b>
                        <div>Atur jasa yang Anda sediakan di platform</div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="summary-card" onclick="window.location.href='manage_orders.php'">
                        <i data-feather="clipboard"></i>
                        <b>Daftar Pesanan</b>
                        <div>Total Pesanan: <b><?= $total_order ?></b></div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="summary-card" onclick="window.location.href='statistik_rating.php'">
                        <i data-feather="star"></i>
                        <b>Statistik Rating</b>
                        <div>Lihat dan evaluasi rating jasa Anda</div>
                    </div>
                </div>
            </div>

            <p class="mt-5" style="color:#6b7280; font-size:.95rem;">
                <b>ITS SkillShare</b> membantu provider untuk mengelola layanan dan memantau pesanan dengan mudah dan efisien.
            </p>
        </div>
    </div>
    <script>
        feather.replace()
    </script>
</body>

</html>