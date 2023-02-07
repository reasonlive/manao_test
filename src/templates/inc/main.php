<div class="main">
	<h2>Это главная страница нашего сайтика</h2>
	<?php if ($username = $_SESSION['username'] ?? false): ?>
		<h3>Hello <?= $username; ?></h3>
	<?php endif; ?>
</div>