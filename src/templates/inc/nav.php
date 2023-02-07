<nav>
	<?php if ($_SESSION['username'] ?? false): ?>
		<a href="/logout"><button>Выйти</button></a>
	<?php else: ?>
		<a href="/registration"><button>Регистрация</button></a>
		<a href="/login"><button>Войти</button></a>	
	<?php endif; ?>
</nav>
