/*
 *  Document   : op_auth_signin.js
 *  Author     : pixelcave
 *  Description: Custom JS code used in Sign In Page
 */

// Form Validation, for more examples you can check out https://github.com/jzaefferer/jquery-validation
class pageAuthSignIn {
	/*
	 * Init Sign In Form Validation
	 *
	 */
	static initValidationSignIn() {
		// Load default options for jQuery Validation plugin
		Codebase.helpers("jq-validation");

		// Init Form Validation
		jQuery(".js-validation-signin").validate({
			rules: {
				"email": {
					required: true,
					minlength: 3,
				},
				"password": {
					required: true,
					minlength: 5,
				},
			},
			messages: {
				"email": {
					required: "Ingresa tu correo electrónico",
					minlength: "Tu correo electrónico debe tener al menos 3 caracteres",
				},
				"password": {
					required: "Ingresa tu contraseña",
					minlength: "Tu contraseña debe tener al menos 5 caracteres",
				},
			},
		});

	}

	/*
	 * Init functionality
	 *
	 */
	static init() {
		this.initValidationSignIn();
	}
}

// Initialize when page loads
Codebase.onLoad(() => pageAuthSignIn.init());
