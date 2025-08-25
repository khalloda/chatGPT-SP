<?php
use App\Core\Helpers;
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= Helpers::e(\App\Config\Config::app('name')) ?></title>
<link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
  <main class="container">
    <?php if ($msg = Helpers::flash('success')): ?>
      <div class="alert success"><?= Helpers::e($msg) ?></div>
    <?php endif; ?>
    <?php if ($msg = Helpers::flash('error')): ?>
      <div class="alert error"><?= Helpers::e($msg) ?></div>
    <?php endif; ?>
    <?php $content(); ?>
  </main>
  <script src="/assets/js/app.js"></script>
</body>
</html>

