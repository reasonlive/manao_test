<form 
	id="data"
	action="index.php"
	method="POST"
	enctype="multipart/form-data" 
	class="flex flex-col"
	onsubmit="return false;"
>
	<span>Форма входа</span>
	
	<div class="form-item">
		<span class="error-message">
			<span></span>
			<span class="error-closer">x</span>
		</span>
		<input type="text" name="login" id="login" placeholder="Логин" maxlength="50">
	</div>

	<div class="form-item">
		<span class="error-message">
			<span></span>
			<span class="error-closer">x</span>
		</span>
		<input type="password" name="password" id="password" placeholder="Пароль" maxlength="200">
	</div>
	
	<div class="form-item">
		<button onclick="loginUser(document.querySelector('#data'))">Войти</button>
	</div>
</form>