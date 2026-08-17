<?php $assetPath = str_repeat('../', substr_count(trim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? ''))), '/'), '/')) . 'assets'; ?>
<link rel="icon" href="<?= e($assetPath) ?>/images/iconkai.png" type="image/png">
<link rel="shortcut icon" href="<?= e($assetPath) ?>/images/iconkai.png" type="image/png">
<link rel="apple-touch-icon" href="<?= e($assetPath) ?>/images/iconkai.png">
