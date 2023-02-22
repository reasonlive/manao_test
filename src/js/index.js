const errClosers = document.querySelectorAll('.error-closer');
errClosers.forEach = Array.prototype.forEach;

// click to hide the input error
errClosers.forEach(elem => {
	elem.addEventListener('click', () => {
		elem.parentElement.style.display = 'none';
	})
})

// shows error messages above target field
/**
 * Form validation error
 * @param field input field (id)
 * @param message Error message
 */
function showError(field, message) {
	const elem = document.querySelector(`#${field}`);
		
	elem.focus();
	elem.previousElementSibling.children[0].innerHTML = message;
	elem.previousElementSibling.style.display = 'flex';
}

/**
 * Validates all inputs in the form
 * @param {FormData} data - Form inputs
 * @returns {Object} - Error message and target field
 */
function validateForm(data) {
	const namePattern = /^[a-zа-я]{2,50}$/i;
	const emailPattern = /^[a-z0-9]{4,100}@[a-z]{3,15}\.[a-z]{2,15}$/i;

	// all fields must be filled
	for (let pair of data.entries()) {
		if (!pair[1].trim()) {
			return {field: pair[0], message: 'Поле должно быть заполнено:'};
		}
	}

	//then check the passwords
	if (data.get('password').trim().length < 6) {
		return {field: 'password', message: 'Пароль должен содержать не менее 6 знаков:'};
	}

	if (
		data.get('password')
		&& data.get('confirm_password')
		&& data.get('password') !== data.get('confirm_password')
	) {
		return {field: 'password', message: 'Пароли не совпадают:'};
	}

	// check an email
	if (data.get('email') && !data.get('email').trim().match(emailPattern)) {
		return {field: 'email', message: 'Почта имеет неправильный формат'};
	}

	// check username length
	if (data.get('username')?.trim()?.length < 2) {
		return {field: 'username', message: 'Длина имени должна быть больше 2 букв:'}; 
	}

	// check username by pattern
	if (data.get('username') && !data.get('username')?.trim()?.match(namePattern)) {
		return {field: 'username', message: 'Имя должно состоять только из букв:'};
	}

	// check the login
	if (data.get('login').trim().length < 6) {
		return {field: 'login', message: 'Логин должен содержать не менее 6 знаков:'};
	}

	if (! data.get('login').match(namePattern)) {
		return {field: 'login', message: 'Логин должен состоять только из букв:'};
	}

	return null;
}

/**
 * Validates the form and then sends it to the server
 * @param {HTMLElement} form
 * @returns {Promise} server response
 */
async function postDataToServer(form) {
	const data = new FormData(form);
	const inputError = validateForm(data);

	if (inputError) {
		showError(inputError.field, inputError.message)
		return null;
	}

	return await fetch('index.php', {
		method: 'POST',
		/*headers: {
    	'Content-Type': 'application/json;charset=utf-8'
  	},*/
		body: data,
	})
}

/**
 * Takes response and handle at frontend
 * @param form {HTMLElement} - Form element
 * @param callback {Function} - final handler
 * @returns {Promise<void>}
 */
async function handleResponseFromServer(form, callback) {
	let result = await postDataToServer(form);
	if (! result) {
		return;
	}

	let responseData;

	try {
		responseData = await result.json();
	} catch (err) { // for errors in the console if json is invalid
		console.log(err)
		postDataToServer(form)
			.then(result => result.text().then(result => console.log(result)));
		return;
	}

	if (responseData.error) {
		showError(responseData.field, responseData.message);
	} else {
		callback(responseData);
	}
}

/**
 * Handler form registration form
 * @param form
 * @returns {Promise<void>}
 */
async function registerUser(form) {
	await handleResponseFromServer(form, (data) => {
		document.body.getElementsByTagName('main')[0].innerHTML = data.message;
		setTimeout(() => document.location.href = '/', 2000);
	})
}

/**
 * Handler for login form
 * @param form
 * @returns {Promise<void>}
 */
async function loginUser(form) {
	await handleResponseFromServer(form, (data) => {
		document.location.href = data.redirect;
	})
}