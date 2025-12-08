<?php
require_once __DIR__ . '/../backend/helpers.php';

$criteria = $_GET['criteria'] ?? 'NBA Criterion';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= h($criteria) ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 p-6">

<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">

  <a href="index.php?tab=nba" class="text-blue-600">&larr; Back to NBA</a>

  <h1 class="text-3xl font-bold mt-4"><?= h($criteria) ?></h1>

  <p class="text-gray-600 mb-6">
    Upload documents, fill data, and maintain records for:
    <strong><?= h($criteria) ?></strong>
  </p>

  <form method="post" action="../backend/upload_nba.php" enctype="multipart/form-data" class="space-y-4">
    
    <input type="hidden" name="criteria" value="<?= h($criteria) ?>">

    <div>
      <label class="font-semibold">Upload Document</label>
      <input type="file" name="file" class="border p-2 w-full" required>
    </div>

    <button class="bg-blue-600 text-white px-4 py-2 rounded w-full">Upload</button>
  </form>

</div>

</body>
</html>
