<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>ITS SkillShare</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4.1/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="bg-gray-50 min-h-screen">
  <nav class="bg-blue-700 text-white px-8 py-4 flex justify-between items-center shadow">
    <div class="font-bold text-xl tracking-wide">ITS SkillShare</div>
    <?php if (isset($_SESSION['nama'])): ?>
      <div>
        <span class="mr-4">👤 <?= $_SESSION['nama']; ?></span>
        <a href="/logout.php" class="bg-red-500 hover:bg-red-600 px-3 py-2 rounded transition">Logout</a>
      </div>
    <?php endif; ?>
  </nav>