<?php
session_start();
include 'config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'customer') {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];

$q = mysqli_query($conn, "SELECT * FROM Provider WHERE User_user_id = '$id_user'");
$isProvider = mysqli_num_rows($q) > 0;

if (isset($_POST['ajukan'])) {
    $namaUsaha = mysqli_real_escape_string($conn, $_POST['namaUsaha']);
    $deskripsiUsaha = mysqli_real_escape_string($conn, $_POST['deskripsiUsaha']);

    $providerID = uniqid('P');

    $insert = mysqli_query($conn, "INSERT INTO Provider (providerID, namaUsaha, deskripsiUsaha, ratingRata, User_user_id) 
                  VALUES ('$providerID', '$namaUsaha', '$deskripsiUsaha', 0, '$id_user')");

    if ($insert) {
        mysqli_query($conn, "UPDATE User SET role = 'provider' WHERE user_id = '$id_user'");
        $_SESSION['role'] = 'provider';
        header("Location: dashboard_provider.php");
        exit();
    } else {
        $error = mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Ajukan Provider | ITS SkillShare</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://fonts.googleapis.com/css2?family=SF+Pro+Text&display=swap" rel="stylesheet" />
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            margin: 0;
            font-family: 'SF Pro Text', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #f0f2f5 0%, #ffffff 100%);
            color: #1c1c1e;
            display: flex;
            min-height: 100vh;
        }

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
            backdrop-filter: saturate(180%) blur(12px);
        }

        .sidebar .brand {
            font-size: 1.8rem;
            font-weight: 700;
            color: #007aff;
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
        }

        .sidebar a.nav-link:hover,
        .sidebar a.nav-link.active {
            background: #cce4ff;
            box-shadow: inset 0 0 12px 3px rgb(0 122 255 / 0.3);
            color: #004db3;
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
        }

        .main-content {
            margin-left: 260px;
            flex-grow: 1;
            padding: 60px 48px;
            background: #fefefe;
        }

        .glass-card {
            background: #fff;
            border-radius: 32px;
            padding: 56px 64px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 24px 60px rgb(55 78 180 / 0.16);
            text-align: center;
        }

        form input,
        form textarea {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }

        button {
            padding: 14px 32px;
            background: linear-gradient(90deg, #2563eb 0%, #60a5fa 100%);
            color: #fff;
            border: none;
            border-radius: 20px;
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
        }

        button:hover {
            background: linear-gradient(90deg, #1746a0 0%, #4894ff 100%);
        }
    </style>
</head>

<body>
    <aside class="sidebar" role="navigation">
        <a href="dashboard_customer.php" class="btn btn-outline-primary mt-3" role="link">&larr; Kembali ke Dashboard</a>
    </aside>

    <main class="main-content">
        <section class="glass-card">
            <h2>Ajukan Menjadi Provider</h2>
            <?php if ($isProvider): ?>
                <div class="alert alert-info">Anda sudah menjadi Provider.</div>
            <?php else: ?>
                <form method="post">
                    <input type="text" name="namaUsaha" placeholder="Nama Usaha" required>
                    <textarea name="deskripsiUsaha" placeholder="Deskripsi Usaha" rows="4" required></textarea>
                    <button type="submit" name="ajukan">Ajukan Provider</button>
                </form>
                <?php if (isset($error)) echo "<p style='color:red;'>Error: $error</p>"; ?>
            <?php endif; ?>
        </section>
    </main>

    <script>
        feather.replace();
    </script>
</body>

</html>