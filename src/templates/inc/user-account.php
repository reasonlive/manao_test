<div class='user-account'>
	<?php if ($username = $_SESSION['username'] ?? false): ?>
		<h3>Hello <?= $username ?></h3>
	<?php endif; ?> 
</div>