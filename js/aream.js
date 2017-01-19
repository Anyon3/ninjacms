/* Menu profil */
//On click aream (menu icon)
aream.on('click', function() {

	//Remove any previous container if existing in DOM
	if(profile.length > 0) profile.empty().remove();

	//html content
	body.append('<div id="profile">\
					<div class="optuser">\
						<ul class="listopt list-group">\
						<li id="optinfos" class="list-group-item" data-title="Afficher mes informations"><i class="fa fa-file-text-o fa-fw fa-2x"></i></li>\
						<li id="optpassword" class="list-group-item" data-title="Changer le mot de passe"><i class="fa fa-key fa-fw fa-2x"></i></li>\
						<li id="optcontact" class="list-group-item" data-title="Gestion de contact"><i class="fa fa-envelope-o fa-fw fa-2x"></i></li>\
						<li id="optbadge" class="list-group-item" data-title="Gestion des badges"><i class="fa fa-graduation-cap fa-fw fa-2x"></i></li>\
						<li id="optscheme" class="list-group-item" data-title="Thème"><i class="color fa fa-flask fa-fw fa-2x"></i></li>\
						<li id="optavatar" class="list-group-item" data-title="Mon avatar"><i class="fa fa-picture-o fa-fw fa-2x"></i></li>\
						<li id="optdisco" class="list-group-item" data-title="Déconnexion"><i class="fa fa-sign-out fa-fw fa-2x"></i></li>\
						</ul>\
					</div>\
					<div class="optset">\
						<h4>Informations</h4>\
						<div class="containerset">\
						<p>Bienvenue dans votre profil, selectionnez les options disponibles dans le menu situé à gauche.</p>\
						<p>Si vous rencontrez des difficultés avec l\'utilisation de celui-ci, contactez l\'équipe du forum.</p>\
						</div>\
					</div>\
				</div>');

	//Refresh selector
	profile 	 = cacheSel('#profile');
	trigPop 	 = cacheSel('#trigPop');

	//Get the username of login
	loguser = trigPop.attr('data-login');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max 1200px
	if(parseInt(sizecal) > 800) var sizecal = 800;

	//Modal
	profile.dialog({
		width:sizecal,
		modal:true,
		resizable: true,
		title:'Profil',
		dialogClass:'modalNinja',
		show:{effect:'fold', duration: 320},
		hide:{effect:'fold', duration: 320}
	});

});

/* End menu profil */

/* Display infos users  */
$(document).off('click', '#optinfos');
$(document).on('click', '#optinfos', function() {

	//Refresh cache selector
	containerset = cacheSel('.containerset');
	optset 		 = cacheSel('.optset');
	optinfos	 = cacheSel('#optinfos');

	//Check if the lock isn't active and the target ctn
	if(optinfos.hasClass('act'))
		return;

	//Check is the lock on the DOM
	if($('.act').length > 0)
		$('.act').removeClass();

	//Add the lock to the target ctn
	optinfos.addClass('act');

	//Empty any content
	containerset.empty().hide();

	//Redefine the box title
	optset.children('h4').text('Chargement en cours...');

	//Get infos user
	$.ajax({
		type: 'POST',
		url: 'php/getter.php',
		data: 'optinfos=1',
		dataType: 'json'
	}).done(function(json) {

		//Get the number of badge
		badges = json['us_badges'].split('-');
		nbadges = 0;

		//Count how many badge the user have
		$.each(badges, function(idx, value) {
			nbadges++;
		});

		//Transform the date on human read format
		regConv = timeConverter(json['us_registered']);

	//Display infos
	$('<ul id="displayinf" class="fa-ul">\
		   <li class=infav><img onerror=\'this.style.display = none\' src="https://avatar.wawa-mania.ec/images/'+ json['us_avatar'] +'" alt="avatar"></li>\
		   <li data-title="Username"><i class="fa-li fa fa-user fa-lg"></i> '+ json['username'] +'</li>\
		   <li data-title="Date d\'inscription"><i class="fa-li fa fa-clock-o fa-lg"></i> '+ regConv +'</li>\
		   <li data-title="Jabber id"><i class="fa-li fa fa-lightbulb-o fa-lg"></i>'+ ((json['us_jabber'] === '') ? 'Non renseigné' : json['us_jabber']) +'</li>\
		   <li data-title="Icq id"><i class="fa-li fa fa-commenting-o fa-lg"></i> '+ ((json['us_icq'] === '') ? 'Non renseigné' : json['us_icq']) +'</li>\
		   <li data-title="Email"><i class="fa-li fa fa-envelope fa-lg"></i> '+ ((json['us_email'] === '') ? 'Non renseigné' : json['us_email']) +'</li>\
		   <li data-title="Badges"><i class="fa-li fa fa-graduation-cap fa-lg"></i> '+ nbadges +' badge(s)</li>\
		   <li data-title="Point(s)"><i class="fa-li fa fa-trophy fa-lg"></i> '+ json['us_pts'] +' point(s)</li>\
		   <li data-title="Vote(s) disponible"><i class="fa-li fa fa-thumbs-up fa-lg"></i> '+ json['us_vote_left'] +' vote(s)</li>\
		   <li '+ ((json['us_avertissement'] > 0) ? 'id=srcgwa class='+ json['us_id'] +'': '') +' data-title="Avertissement(s)"><i class="fa-li fa fa-exclamation fa-lg"></i> '+ json['us_avertissement'] +' averto(s)</li>\
	   </ul>').appendTo(containerset);

	}).promise().done(function() {

		//Redefine the box title
		optset.children('h4').text('Vos informations');

		//Show content with effect
		containerset.show('clip','slow');
	});

});
/* End display infos users */

//Get details warning
$(document).off('click', '#srcgwa');
$(document).on('click', '#srcgwa', function(e) {

	if($('.detailswa').length > 0) return;

	uid = $(this).attr('class');

	$.ajax({
		type:'POST',
		url:'../php/getter.php',
		data:'target='+ uid +'&warning=1',
		dataType:'json'
		}).done(function(json) {

		$.each(json, function(idx, value) {
			$('<li class=detailswa><i class="fa fa-exclamation-triangle"></i> '+ json[idx][3] +' <br><br> '+ json[idx][1] +' <br><br> <a href="https://forum.wawa-mania.ec/pid-'+ json[idx][5] +'">Post concerné</a></li>').insertAfter('#srcgwa');
		});

	}).promise().done(function() {
		//$('#srcgwa').attr('id', '#srcgopn');

	});

});

/* Change password */
$(document).off('click', '#optpassword');
$(document).on('click', '#optpassword', function() {

	//Refresh cache selector
	containerset = cacheSel('.containerset');
	optset 		 = cacheSel('.optset');
	optpassword  = cacheSel('#optpassword');

	//Check if the lock isn't active for this action
	if(optpassword.hasClass('act'))
		return;

	if($('.act').length > 0)
		$('.act').removeClass();

	optpassword.addClass('act');

	//Empty the actual content
	containerset.empty().hide();

	$('<div id="formchangepa">\
	   <i class="optkey fa fa-key fa-lg"></i><input id="actualp" class="optpas" type="password" placeholder="Mdp actuel" />\
	   <i class="optkey fa fa-key fa-lg"></i><input id="newp" class="optpas" type="password" placeholder="Nouveau mdp" />\
	   <i class="optkey fa fa-key fa-lg"></i><input id="confirmp" class="optpas" type="password" placeholder="Confirmation" />\
	   <span id="sendpa">Envoyer</span>\
	   </div>').appendTo(containerset);

	//Redefine title rightbox
	optset.children('h4').text('Changer le mdp');

	containerset.show('clip','slow');

	//Refresh cached selector
	sendpa   = cacheSel('#sendpa');
	optpas   = cacheSel('.optpas');
	actual   = cacheSel('#actualp');
	newp     = cacheSel('#newp');
	confirmp = cacheSel('#confirmp');

	//On click send password value
	sendpa.on('click', function() {

		//Bool - on false, return false
		bool = true;

		//For each input, check they got at least 5 chars
		optpas.each(function() {

		if($(this).val().length < 5) {

			$(this).val('').css('border-color','red').css('color','red').attr('placeholder','5 caractères minimum').prev('i').css('color','red');

			bool = false;

			return false;
		}

		});//End loop

		//If the loop did fail
		if(bool === false) return

		//Check that new password and confirmation are the same
		if(newp.val() !== confirmp.val()) {

			confirmp.val('').css('border-color','red').css('color','red').attr('placeholder','Différent du mdp...').prev('i').css('color','red');
			return;
		}

		//Update password
		//Fetch information
		$.ajax({
		type: 'POST',
		url: 'php/getter.php',
		data: 'optpassword=1&op='+ actual.val() +'&np='+ encodeURIComponent(newp.val()) +'&co='+ encodeURIComponent(confirmp.val())
		}).done(function(msg) {

			//Actual password failed
			if(msg == '1') {

				actual.val('').css('border-color','red').css('color','red').attr('placeholder','Invalide').prev('i').css('color','red');
				return;
			}

			//Success
			if(msg == '2') {

				actual.val('').css('border-color','green').css('color','green').attr('placeholder','Mot de passe').prev('i').css('color','green');
				newp.val('').css('border-color','green').css('color','green').attr('placeholder','mis à jour').prev('i').css('color','green');
				confirmp.val('').css('border-color','green').css('color','green').attr('placeholder','avec succès !').prev('i').css('color','green');

				return;
			}
		});

	});

	//On click input, reset css (if needed)
	optpas.on('click', function() {
		if(placeholderReset(this) === true) $(this).css('border-color','').css('color','').attr('placeholder','').prev('i').css('color','');
	});

});

/* End change password */

/* Contact infos */
$(document).off('click', '#optcontact');
$(document).on('click', '#optcontact', function() {

	//Refresh cache selector
	containerset = cacheSel('.containerset');
	optset 		 = cacheSel('.optset');
	optcontact	 = cacheSel('#optcontact');

	//Check if the lock isn't active and the actual ctn is loaded, else activate it
	if(optcontact.hasClass('act'))
		return;

	if($('.act').length > 0)
		$('.act').removeClass();

	optcontact.addClass('act');

	//Redefine the box title
	optset.children('h4').text('Chargement en cours...');

	//Empty the actual content
	containerset.empty().hide();

	//Fetch information
	$.ajax({
	type: 'POST',
	url: 'php/getter.php',
	data: 'optcontact=1',
	dataType: 'json'
	}).done(function(json) {

		//Set variable info - (1 jv | 2 j | 3 iv | 4 i | 5 e)
		jabber = (json[2] !== '') ? '<i id="rem_jabber" class="con_ic fa fa-trash-o" data-title="Supprimer"></i> <i id="edit_jabber" class="con_ic fa fa-pencil-square" data-title="Editer"></i>' : '<i id="add_jabber" class="con_ic fa fa-plus-square" data-title="Ajouter"></i>';
		icq    = (json[4] !== '') ? '<i id="rem_icq" class="con_ic fa fa-trash-o" data-title="Supprimer"></i> <i id="edit_icq" class="con_ic fa fa-pencil-square" data-title="Editer"></i>' : '<i id="add_icq" class="con_ic fa fa-plus-square" data-title="Ajouter"></i>';
		email  = (json[5] !== '') ? '<i id="rem_email" class="con_ic fa fa-trash-o" data-title="Supprimer"></i> <i id="edit_email" class="con_ic fa fa-pencil-square" data-title="Editer"></i>' : '<i id="add_email" class="con_ic fa fa-plus-square" data-title="Ajouter"></i>';

		//Class color (public/private
		jabcolor = (json[2] !== '') ? 'status'+ json[1] : '';
		icqcolor = (json[4] !== '') ? 'status'+ json[3] : '';
		emailcolor = (json[5] !== '') ? 'status0' : '';

		//Text title for the icon method
		jabtext = (json[2] !== '') ? ((json[1] === 0) ? 'Privé' : 'Public') : 'Aucun';
		icqtext = (json[4] !== '') ? ((json[3] === 0) ? 'Privé' : 'Public') : 'Aucun';
		emailtext = (json[5] !== '') ? 'Privé' : 'Aucun';

		//Im id/address or nothing
		jabid 	= (json[2] !== '') ? json[2] : 'Aucun';
		icqid 	= (json[4] !== '') ? json[4] : 'Aucun';
		emailid = (json[5] !== '') ? json[5] : 'Aucun';


		//Display contact html
		$('<p class="mth_display" data-title="Status : '+ jabtext +'<br>Jabber : '+ jabid +'"><i class="contactd jc '+ jabcolor +' fa fa-lightbulb-o fa-lg"></i> Jabber '+ jabber +'</p>\
		   <p id="jabberfield" class="add_jabber edit_jabber hide"><input id="jabberval" class="contactput" type="text" /><i class="fieldsend fa fa-check-square-o"></i></p>').appendTo(containerset);

		$('<p class="mth_display" data-title="Status : '+ icqtext +'<br>Icq : '+ icqid +'"><i class="contactd iq '+ icqcolor +' fa fa-commenting-o fa-lg"></i> Icq '+ icq +'</p>\
		   <p id="icqfield" class="add_icq edit_icq hide"><input id="icqval" class="contactput" type="text" /><i class="fieldsend fa fa-check-square-o"></i></p>').appendTo(containerset);

		$('<p class="mth_display" data-title="Status : '+ emailtext +'<br>Email : '+ emailid +'"><i class="ec '+ emailcolor +' fa fa-envelope-o fa-lg"></i> Email '+ email +'</p>\
		   <p id="emailfield" class="add_email edit_email hide"><input id="emailval" class="contactput" type="text" /><i class="fieldsend fa fa-check-square-o"></i></p>').appendTo(containerset);

		//Redefine the box title
		optset.children('h4').text('Gestion des contacts');

		//Show content with effect
		containerset.show('clip','slow');

		//Refresh selector
		hide 	 = cacheSel('.hide');
		contactd = cacheSel('.contactd');

		//Reset css input on click (if needed)
		$(document).off('click', '.contactput');
		$(document).on('click', '.contactput', function() {
			if(placeholderReset('.contactput') === true) $('.contactput').css('border-color','').css('color','').attr('placeholder','');
		});

		//Update public /private
		$(document).off('click', '.contactd');
		$(document).on('click', '.contactd', function(e) {

			//Test the class empty method contact
			if(!$(e.target).hasClass('status0') && !$(e.target).hasClass('status1')) return;

			fclass = ($(e.target).hasClass('status0')) ? 'status0' : 'status1';

			//Get method
			wh = $(this).attr('class').split(' ')[1];

			//Text
			if(wh === 'jc') {
			atext = 'Jabber';
			id = jabid;
			}

			else if(wh === 'iq') {
			atext = 'Icq';
			id = icqid;
			}

			else if(wh === 'ec') {
			atext = 'Email';
			id = emailid;
			}

			//Get the bool value
			bool = fclass.slice(6);

			//update
			$.ajax({
			type: 'POST',
			url: 'php/getter.php',
			data: wh +'='+ bool
			}).done(function(msg) {

				//Caching selector
				wh = $('.'+ wh);

				if(msg === '0') {
				wh.parent().attr('data-title', 'Status : Privé<br>'+ atext +' : '+ id)
				wh.css('color','red');
				}

				else {
				wh.parent().attr('data-title', 'Status : Public<br>'+ atext +' : '+ id)
				wh.css('color','green');
				}

			});
	   });

		//On click add / edit / delete
		$(document).off('click', '.con_ic');
		$(document).on('click', '.con_ic', function(e) {

			//Hide any input active
			if(hide.length !== 0) hide.fadeOut();

			//Jabber - Add / Edit / Delete
			if(e.target.id === 'add_jabber') $('.' + e.target.id).fadeIn();

			else if(e.target.id === 'edit_jabber') $('.' + e.target.id).fadeIn().children('input').val(jabid);

			else if(e.target.id === 'rem_jabber') {

			$.ajax({
			type: 'POST',
			url: 'php/getter.php',
			data: e.target.id +'=1'
			}).done(function(msg) {
				$('.jc').replaceWith('<i class="contactd jc fa fa-lightbulb-o fa-lg"></i>');
				$('#edit_jabber').remove();
				$('#rem_jabber').replaceWith('<i id="add_jabber" class="con_ic fa fa-plus-square" data-title="Ajouter"></i>');
				$('.jc').parent().attr('data-title', 'Status : Aucun<br>Jabber : Aucun');
			});

			}

			//Icq - Add / Edit / Delete
			if(e.target.id === 'add_icq') $('.' + e.target.id).fadeIn();

			else if(e.target.id === 'edit_icq') $('.' + e.target.id).fadeIn().children('input').val(icqid);

			else if(e.target.id === 'rem_icq') {

			$.ajax({
			type: 'POST',
			url: 'php/getter.php',
			data: e.target.id +'=1'
			}).done(function(msg) {
				$('.iq').replaceWith('<i class="contactd iq fa fa-commenting-o fa-lg"></i>');
				$('#edit_icq').remove();
				$('#rem_icq').replaceWith('<i id="add_icq" class="con_ic fa fa-plus-square" data-title="Ajouter"></i>');
				$('.iq').parent().attr('data-title', 'Status : Aucun<br>Icq : Aucun');
			});

			}

			//Email - Add / Edit / Delete
			if(e.target.id === 'add_email') $('.' + e.target.id).fadeIn();

			else if(e.target.id === 'edit_email') $('.' + e.target.id).fadeIn().children('input').val(emailid);

			else if(e.target.id === 'rem_email') {

			$.ajax({
			type: 'POST',
			url: 'php/getter.php',
			data: e.target.id +'=1'
			}).done(function(msg) {
				$('.ec').replaceWith('<i class="contactd ec fa fa-envelope-o fa-lg"></i>');
				$('#edit_email').remove();
				$('#rem_email').replaceWith('<i id="add_email" class="con_ic fa fa-plus-square" data-title="Ajouter"></i>');
				$('.ec').parent().attr('data-title', 'Status : Aucun<br>Email : Aucun');
			});

			}

		});

		//On send input
		$(document).off('click', '.fieldsend');
		$(document).on('click', '.fieldsend', function(e) {

			//Send the action to do add / edit / del with their id and value
			actcon = $(this).parent().attr('id');
			valcon = $(this).prev().val();

			//Answer text
			if(actcon === 'jabberfield') {
			atext = 'Jabber';
			perm  = (jabtext === 'Aucun') ? 'Privé' : ((jabtext === 'Privé') ? 'Public' : 'Privé');
			}

			else if(actcon === 'icqfield') {
			atext = 'Icq';
			perm  = (icqtext === 'Aucun') ? 'Privé' : ((icqtext === 'Privé') ? 'Public' : 'Privé');
			}

			else if(actcon === 'emailfield') {
			atext = 'Email';
			perm  = 'Privé';
			}

			//Get result
			$.ajax({
			type: 'POST',
			url: 'php/getter.php',
			data: actcon +'='+ valcon
			}).done(function(msg) {

				//Caching selector
				actcon = $('#' + actcon);

				//Invalid format
				if(msg === '1')
				actcon.children('input').val('').css('border-color', 'red').css('color', 'red').attr('placeholder','Invalide');

				//Im already registered
				else if(msg === '2')
				actcon.children('input').val('').css('border-color', 'red').css('color', 'red').attr('placeholder', atext +' id déjà utilisé');

				//Success
				else if(msg === '3') {
				actcon.prev().children('.contactd').addClass('status0');
				actcon.prev().attr('data-title', 'Status : '+ perm +'<br>'+ atext +' : '+ valcon);
				actcon.children('input').val('').css('border-color', 'green').css('color', 'green').attr('placeholder', atext +' ajouté');
				}

				else
				alert('Error');

			});

		});

	});

});

/* End contact infos */

/* Badges */
//Setting badges
$(document).off('click', '#optbadge');
$(document).on('click', '#optbadge', function() {

	//Refresh cache selector
	containerset = cacheSel('.containerset');
	optset 		 = cacheSel('.optset');
	optbadge	= cacheSel('#optbadge');

	//Check if the lock isn't active and the actual ctn is loaded, else activate it
	if(optbadge.hasClass('act'))
		return;

	if($('.act').length > 0)
		$('.act').removeClass();

	optbadge.addClass('act');

	//Redefine title rightbox
	optset.children('h4').text('Chargement en cours...');

	//Empty the actual content
	containerset.empty().hide();

	//Fetch information
	$.ajax({
	type: 'POST',
	url: 'php/getter.php',
	data: 'optbadge=1',
	dataType: 'json'
	}).done(function(json) {

		//split every id badge
		idbadge = json[1].split('-');
		acbadge = json[2];

		//Get info of badge
		$.getJSON('../cachejs/badges.json', function(data) {

			$('<p class="sep">Badge(s) affiché(s)</p>\
			   <p class="act groupe-'+ data[acbadge].groupe +'" data-id="'+ acbadge +'" data-title="'+ data[acbadge].subtitle +'<br>'+ data[acbadge].description +'"><i class="fa fa-'+ data[acbadge].icon+'"></i> '+ data[acbadge].name +'</p>').appendTo(containerset);

			//For each badge that the user got
			$('<p class="sep">Vos badges</p>').appendTo(containerset);
			$.each(idbadge, function(idx, value) {
				$('<p id="ba'+ value +'" class="bdg groupe-'+ data[value].groupe +'" data-title="'+ data[value].subtitle +'<br>'+ data[value].description +'"><i class="fa fa-'+ data[value].icon +'"></i> '+ data[value].name +'</p>').appendTo(containerset);
			});//End each

		}).promise().done(function() {

			//Redefine the box title
			optset.children('h4').text('Gestion des badges');

			//Show content with effect
			containerset.show('clip','slow');

			});

	//End json ajax
	});
});

//Color scheme
$(document).off('click', '#optscheme');
$(document).on('click', '#optscheme', function() {

	//Refresh cache selector
	containerset = cacheSel('.containerset');
	optset 		 = cacheSel('.optset');
	optscheme = cacheSel('#optscheme');

	//Check if the lock isn't active for this action
	if(optscheme.hasClass('act'))
		return;

	if($('.act').length > 0)
		$('.act').removeClass();

	optscheme.addClass('act');

	//Empty the actual content
	containerset.empty().hide();

	$('<ul class="fa-ul scheme">\
			<li class="style fa fa-square fa-2x"></li>\
			<li class="dark fa fa-square fa-2x"></li>\
		</ul>').appendTo(containerset).promise().done(function() {

			//Redefine the box title
			optset.children('h4').text('Thème');

			//Show content with effect
			containerset.show('clip','slow');
		});
});

$(document).off('click', '.scheme > li');
$(document).on('click', '.scheme > li', function() {

	var trigScheme = $(this).attr('class').split(' ')[0];
    var scheme = (trigScheme === 'dark') ? 'dark' : 'blue';


        if (scheme === 'dark') {
			body.css('background-color', '#2F2F2F');
			head.append('<link rel="stylesheet" href="css/dark.css" type="text/css">');
			color.trigger('click');
        }

        else {
            body.css('background-color', 'white');
    		head.append('<link rel="stylesheet" href="css/style.css" type="text/css">');
    		color.trigger('click');
        }

		//Update in db
		$.ajax({
		type:'POST',
		url:'php/getter.php',
		data:'scheme='+ scheme}).done(function(msg) { });

});


//On clic badge, update to showed badge
$(document).off('click', '.bdg');
$(document).on('click', '.bdg', function(e) {

	//Check if the user do not select the actual badge
	if(e.target.id.slice(2) === $('.act').attr('data-id')) {
	alert('Ce badge est déjà actif.');
	return false;
	}

	//Check if the confirmation for this badge wasn't already open
	if($(e.target).next().attr('class') === 'confirmba') {
	$(e.target).next().slideUp().remove();
	return false;
	}

	//Delete any previous confirmation, spawn new confirm and fadeIn (as display:none css)
	$('.confirmba').remove();
	$('<span class="confirmba">Utiliser le badge '+ $(e.target).text() +' ? <i id="ok'+ e.target.id.slice(2) +'" class="changeok fa fa-check-square-o data-valid="Valider"></i></span>').insertAfter('#' + e.target.id).fadeIn();
	});

		//On clic changeok (confirmation change badge)
		$(document).off('click', '.changeok');
		$(document).on('click','.changeok', function() {

			//Get the badge id
			badgeid = $('.changeok').attr('id').slice(2);

			//Fetch the information
			$.ajax({
			type: 'POST',
			url: 'php/getter.php',
			data: 'showbadge='+ badgeid,
			}).done(function(msg) {
				//Success
				if(msg == 1)  {

				//cache selector
				optset = $('.optset');

				optset.empty().html('<h4>Succès</h4>\
									<div class="containerset">\
									<p>Votre badge s\'est changé avec succès, il sera disponible comme affichage primaire dans 1 minute.</p>\
									<p>Toutefois, il peut mettre jusqu\'à 24 heures pour se mettre à jour sur l\'enssemble des topics du forum, consultez notre centre d\'aide pour plus d\'informations</p>\
									</div>');
				}

				else alert('Problème inconnu');
			});

		});

/* End Badges */

/* Upload avatar */
//https://github.com/Anyon3/simplehtml5upload

$(document).off('click', '#optavatar');
$(document).on('click', '#optavatar', function(e) {

	//Refresh cache selector
	containerset = cacheSel('.containerset');
	optset 		 = cacheSel('.optset');
	optinfos	 = cacheSel('#optavatar');

	//Check if the lock isn't active for this action
	if(optinfos.hasClass('act'))
		return;

	if($('.act').length > 0)
		$('.act').removeClass();

	//Empty the actual content
	containerset.empty().hide;

	optset.empty().html('<h4>Avatar</h4>\
						<div class="containerset">\
						<form enctype="multipart/form-data" method="post" name="fileinfo" id=fileinfo>\
						<label for=filesend class=filezone>\
						<img src="" alt="" class="ish">\
						<input id=filesend type="file" name="file">\
						</label>\
						<input type="button" id=reset class=ish value="Reset">\
						<input type="submit" id=fileup class=ish value="Upload">\
						</form>\
						<p class=msgUp>Glisses ton avatar ou clic dans le carré</p>\
						</div>');

	//Show content with effect
	containerset.show('clip','slow');


	//Settings
	actype = ['image/png','image/jpeg','image/jpg','image/gif']; /* Accepted mime type */
	maxweight = 419200; /* Max file size in octets */
	maxwidth = 150; /* Max width of the image */
	maxheight = 150; /* Max height*/

	//Caching variable selector
	ish = $('.ish'); /* On attach element hide or show */
	msgUp = $('.msgUp'); /* container message, infos, error... */
	filezone = $('.filezone'); /* Selector filezone label */
	fileprev = $('.filezone').children('img'); /* Selector img element */
	filesend = $('#filesend'); /* Selector input file (children label) */
	fileup = $('#fileup'); /* Selector button submit */
	reset = $('#reset'); /* Selector button reset */

	ish.hide(); /* Initial hide */

});

//Settings
actype = ['image/png','image/jpeg','image/jpg','image/gif']; /* Accepted mime type */
maxweight = 419200; /* Max file size in octets */
maxwidth = 150; /* Max width of the image */
maxheight = 150; /* Max height*/

//Caching variable selector
ish = $('.ish'); /* On attach element hide or show */
msgUp = $('.msgUp'); /* container message, infos, error... */
filezone = $('.filezone'); /* Selector filezone label */
fileprev = $('.filezone').children('img'); /* Selector img element */
filesend = $('#filesend'); /* Selector input file (children label) */
fileup = $('#fileup'); /* Selector button submit */
reset = $('#reset'); /* Selector button reset */

ish.hide(); /* Initial hide */

$(document).on('change',':file', function(e) {

	//Cancel the default execution
	e.preventDefault();

	//Full file
	file = this.files[0];

    var filer = new FileReader;

    filer.onload = function() {

    	//Get size and type
    	var aType = file.type;
        var aSize = file.size;

        //Check the file size
        if(aSize > maxweight) {
        	msgUp.text('Trop lourd, maximum 400 ko');
        	return;
        }

        //Check the file type
        if($.inArray(aType, actype) === -1) {
        	msgUp.text('Type de fichier non autorisé');
        	return;
        }

        //Set src / preview
    	fileprev.attr('src', filer.result);

        //Make new Image for get the width / height
    	var image = new Image();
		image.src = filer.result;

			image.onload = function() {

				//Get width / height
				aWidth  = image.width;
				aHeight = image.height;

				//Check width
				if(aWidth > maxwidth) {
					msgUp.text('Maximum' + maxwidth +' de largeur autorisé');
	        		return;
				}

				//Check height
				if(aHeight > maxheight) {
					msgUp.text('Maximum' + maxheight +' de hauteur autorisé');
	        		return;
				}

				//Success of every check, display infos about the image and show up the <img> tag
				msgUp.html('Poid :'+ aSize +' bytes<br>Type : '+ aType +'<br>Largeur :'+ aWidth +' px<br>Hauteur: '+ aHeight +' px');
				ish.show();
				filesend.addClass('lock').css('height','0%');

			//End image
			};

	//End filer
    };

    //File is up
    filer.readAsDataURL(file);

});

//input file prevent on lock
$(document).off('click', '#filesend');
$(document).on('click', '#filesend',  function(e) {

	//Cancel the default execution if img ready to be send to php
	if($(this).hasClass('lock'))
		e.preventDefault();

});

//On reset
$(document).off('click', '#reset');
$(document).on('click', '#reset',  function(e) {

	//Cancel the default execution
	e.preventDefault();

	//Remove the href link
	fileprev.attr('src', '');

	//Set default message
	msgUp.text('Glissez votre avatar ou clic dans le carré');

	//Remove the lock of the input file
	if(filesend.hasClass('lock'))
		filesend.css('height','100%').removeClass();

	//Set default  reset value
	$(this).val('Reset');

	//Back to hide
	ish.hide();

});

//On fileup
$(document).off('click', '#fileup');
$(document).on('click', '#fileup', function(e) {

	//Cancel the default execution
	e.preventDefault();

	//Set variable which contain the entiere form / field
	var filesfm = new FormData(document.querySelector('#fileinfo'));

    $.ajax({
        url: 'php/getter.php',  //Server side script (php...)
        type: 'POST',
        data:filesfm,
        processData: false,  //Avoid jquery process
        contentType: false   //Avoid set content type (done by var filesfm)
    		}).done(function(msg) {

    		//Hide the button upload
    		fileup.hide();

    		//Change the text reset button  (make as reinitialize the form)
    		reset.val('Changer mon avatar');

    		//On success upload
    		if(msg === 'err')
    			msgUp.text('Erreur inconnue'); //That should happen !
    		else if(msg === 'lvl')
    			msgUp.text('Un badge minimum de level 4 est requis');
    		else
    			msgUp.text('Succès, votre nouvel avatar sera effectif d\'ici 10 minutes');

    }); //End ajax

});
/* End Avatar */

/* Disconnect */
$(document).off('click', '#optdisco');
$(document).on('click', '#optdisco', function() {

	$.ajax({
	type: 'POST',
	url: 'php/getter.php',
	data: 'disconnect=1',
	}).done(function(msg) {
	location.reload();
	});
});