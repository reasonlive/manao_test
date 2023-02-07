<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= $data['title'] ?? ''; ?></title>
</head>
<body>
	<?php if ($data['content'] ?? false): ?>
		<?php include $data['content'] ?? ''; ?>
	<?php endif; ?>

	<?php if ($data ?? false): ?>
		<?php foreach($data as $param): ?>
			<?= $param; ?>
		<?php endforeach; ?>
	<?php endif; ?>
</body>
</html>