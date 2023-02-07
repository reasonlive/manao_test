<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $data['title'] ?? ''; ?></title>
	<link rel="stylesheet" href="./src/css/index.css<?= $GLOBALS['css_filetime'] ?? ''; ?>">
</head>
<body>
	<header><?php include './src/templates/inc/nav.php'; ?></header>
	<main>
		<?php if ($data['content'] ?? false): ?>
			<?php include $data['content']; ?>
		<?php endif; ?>
	</main>
	<footer class="flex-around">
		&copy;reasonlive
		<div class="breadcrumb">
			<a href="/">На главную</a>
			<?php if ($_SESSION['username'] ?? false): ?>
				/<a href="/account">Аккаунт</a>
			<?php endif; ?>
		</div>
	</footer>
	<script src="./src/js/index.js<?= $GLOBALS['js_filetime'] ?? ''; ?>"></script>
</body>
</html>