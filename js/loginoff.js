/* Login */
//On send input login/password (connect)
$(document).off('click', '#clog');
$(document).on('click', '#clog', function(e) {

	e.preventDefault();

	//Regen loginForm & parameters
	loginForm 	 = cacheSel('.loginForm');
	loginu		 = cacheSel('.loginu'); //Field username
	loginp		 = cacheSel('.loginp'); //Field password
	captcha   	 = $('#answercap').val();

	//Get input pseudo / password
	uval = loginu.val();
	pval = loginp.val();

	//Check login
	if(uval.length < 3) {
		coloredInput('.loginu','errorVal','3 caractères minimum');
		return;
	}

	if(uval.length > 25) {
		coloredInput('.loginu','errorVal','25 caractères maximumm');
		return;
	}

	//Check password
	if(pval < 5) {
		coloredInput('.loginp','errorVal','5 caractères minimum');
		return;
	}

	//Captcha
	if(captcha.length > 20) {
		coloredInput('#answercap','errorVal','20 caractères maximum');
		return false;
	}

	//Get answer from the captcha
	//Will be check in php with verify function

	spawnLoad('load');

	$.ajax({
	type: 'POST',
	url: 'php/getter.php',
	data: 'login=1&username='+ uval +'&password='+ encodeURIComponent(pval) +'&security='+ encodeURIComponent(captcha)
	}).done(function(msg) {

		spawnLoad('kill');

		//Invalid chars
		if(msg === '2')
			coloredInput('.loginu','errorVal','Caractères invalide');

		//Ban
		else if(msg === '7')
			coloredInput('.loginu','errorVal','Compte banni');

		//User not found
		else if(msg === '3')
			coloredInput('.loginu','errorVal','Compte inexistant');

		//Captcha failed
		else if(msg === 'badcap')
			coloredInput('#answercap','errorVal','Captcha incorrect');

		//Success login
		else if(msg === '4') {
			loginu.val('').attr('placeholder','Succès').css('border-color','green').css('color','green').prev().css('color','green');
			loginp.val('').attr('placeholder','Redirection...').css('border-color','green').css('color','green').prev().css('color','green');

			trigPop.attr('data-login',uval);

			setTimeout(function() {
			window.location.replace('https://forum.wawa-mania.ec/home');
			}, 1500);
		}

		//password not correct
		else if(msg === '5')
			coloredInput('.loginp','errorVal','Mot de passe incorrect');

		//Unknow error
		else
			coloredInput('.loginu','errorVal','Error server');
	});
});
/* End login */

//Block - Password recoveryrecovery send
$(document).off('click', '#crec');
$(document).on('click', '#crec', function(e) {

	e.preventDefault();

	//Regen loginForm
	loginForm = cacheSel('.loginForm');

	//Get value username, email and the captcha
	accrec = cacheSel('#accrec');
	mailrec = cacheSel('#mailrec');

	//Start the load
	spawnLoad('load');
	$.ajax({
	type: 'POST',
	url: 'php/getter.php',
	data:'username='+ accrec.val() +'&email='+ mailrec.val() +'&recovery=1'
	}).done(function(msg) {

		spawnLoad('kill');

		//Format email incorrect
		if(msg === 'email') {
		mailrec.val("").addClass('fail').attr('placeholder','Incorrect').css('border-color','red').css('color','red').prev().css('color','red');
		return;
		}

		//Too long or the too short
		else if(msg === 'euser') {
		accrec.val("").addClass('fail').attr('placeholder','Incorrect').css('border-color','red').css('color','red').prev().css('color','red');
		return;
		}

		//Account ban or security catch this user
		else if(msg === 'enow' || msg === 'ealrea' || msg === 'eban') {
		mailrec.val("").addClass('fail').attr('placeholder','refusée').css('border-color','red').css('color','red').prev().css('color','red');
		accrec.val("").addClass('fail').attr('placeholder','Autorisation').css('border-color','red').css('color','red').prev().css('color','red');
		return;
		}

		//Email send with sucess
		else if(msg === 'eok') {
		mailrec.val("").attr('placeholder','Succès').css('border-color','green').css('color','green').prev().css('color','green');
		accrec.val("").attr('placeholder','Consultez votre boite').css('border-color','green').css('color','green').prev().css('color','green');
		}

	});//End Ajax

});//End function

//Block, send registration form
$(document).off('click', '#creg');
$(document).on('click', '#creg', function(e) {

	e.preventDefault();

	//Check field
	pseudo    = $('#accreg').val();
	password  = $('#pwreg').val();
	captcha   = $('#answercap').val();

	//Regex
	var chacc = new RegExp(/^([\d\w]*)$/i);

	//Failed chars
	if(!chacc.test(pseudo)) {
		coloredInput('#accreg','errorVal','Caractères 1-9 et A-Z uniquement');
		return false;
	}

	//3 minimum, 25 maximum
	if(pseudo.length < 3) {
		coloredInput('#accreg','errorVal','3 caractères minimum, 25 maximum');
		return false;
	}

	//5 minimum
	if(password.length < 5) {
		coloredInput('#pwreg','errorVal','5 caractères minimum');
		return false;
	}

	//20 maximum
	if(captcha.length > 20) {
		coloredInput('#answercap','errorVal','20 caractères maximum');
		return false;
	}

	//Ajax
	spawnLoad('load'); //Start the loader

	$.ajax({
	type: 'POST',
	url: 'php/getter.php',
	data:'username='+ encodeURI(pseudo) +'&password='+ encodeURI(password) +'&security='+ encodeURI(captcha)
	}).done(function(msg) {

		spawnLoad('kill'); //Kill the loader

		//Permission denied (WAF...)
		if(msg === 'perm') {
			coloredInput('#accreg','errorVal','Permission');
			coloredInput('#pwreg','errorVal','Refusée');
		}

		//Wrong captcha
		else if(msg === 'badcap')
			coloredInput('#answercap','errorVal','Le captcha est incorrect');

		//Account name already taken
		else if(msg === 'utaken')
			coloredInput('#accreg','errorVal','Pseudo déjà enregistré');

		else if(msg === 'ok') {
			$('#formReg').empty();
			$('#formReg').append('<p class=registrationok>Votre compte '+ pseudo +' est désormais enregistré, le compte sera actif dans quelques minutes.</p>');
		}

	});

});

//Clean registration form on click
$(document).off('click','#accreg');
$(document).on('click','#accreg', function() {

	if(placeholderReset(this) === true)
		coloredInput(this, 'errorClean', 'Pseudo désiré');
});

$(document).off('click','#pwreg');
$(document).on('click','#pwreg', function() {

	if(placeholderReset(this) === true)
		coloredInput(this, 'errorClean', 'Mot de passe');
});

$(document).off('click','#answercap');
$(document).on('click','#answercap', function() {

	if(placeholderReset(this) === true)
		coloredInput(this, 'errorClean', 'Réponse à la question');
});