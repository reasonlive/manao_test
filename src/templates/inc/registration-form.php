<form
	id="data" 
	method="POST"
	action="index.php"
	enctype="multipart/form-data" 
	class="flex flex-col"
	onsubmit="return false;"
>
	<span>Форма регистрации</span>
	
	<div class="form-item">
		<span class="error-message">
			<span></span>
			<span class="error-closer">x</span>
		</span>
		<input type="text" name="username" id="username" placeholder="Имя*" maxlength="50">	
	</div>
	
	<div class="form-item">
		<span class="error-message">
			<span></span>
			<span class="error-closer">x</span>
		</span>
		<input type="email" name="email" id="email" placeholder="Почта*">
	</div>

	<div class="form-item">
		<span class="error-message">
			<span></span>
			<span class="error-closer">x</span>
		</span>
		<input type="text" name="login" id="login" placeholder="Логин*" maxlength="50">
	</div>

	<div class="form-item">
		<span class="error-message">
			<span></span>
			<span class="error-closer">x</span>
		</span>
		<input type="password" name="password" id="password" placeholder="Пароль*" maxlength="200">
	</div>

	<div class="form-item">
		<span class="error-message">
			<span></span>
			<span class="error-closer">x</span>
		</span>
		<input type="password" name="confirm_password" id="confirm_password" placeholder="Подтверждение пароля*" maxlength="200">
	</div>
	
	<div class="form-item">
		<button onclick="registerUser(document.querySelector('#data'))">Зарегистрироваться</button>
	</div>
</form>
<script>
	
</script>