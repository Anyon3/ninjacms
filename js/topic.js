if($('#container_to').length > 0) $('#container_to').remove();

//Get current HL highlight
function getHigh() {
	var textComponent = document.getElementById('message_to');
	var startPos = textComponent.selectionStart;
	var endPos = textComponent.selectionEnd;
	hltext = textComponent.value.substring(startPos, endPos);
	return hltext;
}

//Fonction - replace highlight text with bbcode
function replaceIt(txtarea, newtxt) {
	$(txtarea).val($(txtarea).val().substring(0, txtarea.selectionStart)+ newtxt + $(txtarea).val().substring(txtarea.selectionEnd));
}

//Hide the button reply
$('#button_newto').hide();

//generate <li> smiley
nbsml = 40;
stsml = 1;

while(parseInt(stsml) < parseInt(nbsml)) {

	if(stsml === 1)
		lisml = '<li id=sm'+ stsml +' class=smemo><img src="../img/em/'+ stsml +'.gif" alt="Smiley"></li>';
	else
		lisml = lisml + '<li id=sm'+ stsml +' class=smemo><img src="../img/em/'+ stsml +'.gif" alt="Smiley"></li>';

	stsml++;
}

//On load spawn the post tag
containerto = $('<div id="container_to">\
		'+((sectionid == 17) ? '<p id=ask><i class="fa fa-file-text-o fa-lg"></i> Générateur</p>' : "")+'\
					 <div id=bar_bbcode>\
					 <span id=bb_bold class="fa-stack fa-lg" data-title="Gras"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-bold fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_italic class="fa-stack fa-lg" data-title="Italic"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-italic fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_large class="fa-stack fa-lg" data-title="Grand"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-text-height fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_small class="fa-stack fa-lg" data-title="Petit"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-text-width fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_underline class="fa-stack fa-lg" data-title="Souligné"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-underline fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_strikethrough class="fa-stack fa-lg" data-title="Barré"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-strikethrough fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_code class="fa-stack fa-lg" data-title="Code"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-code fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_quote class="fa-stack fa-lg" data-title="Quote"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-quote-left fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_left class="fa-stack fa-lg" data-title="Gauche"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-left fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_center class="fa-stack fa-lg" data-title="Centre"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-center fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_right class="fa-stack fa-lg" data-title="Droite"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-right fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_justify class="fa-stack fa-lg" data-title="Pre"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-justify fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_paragraph class="fa-stack fa-lg" data-title="Paragraph"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-paragraph fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_image class="fa-stack fa-lg" data-title="Image"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-picture-o fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_url class="fa-stack fa-lg" data-title="Lien"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-link fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_color class="spect fa-stack fa-lg" data-title="Couleur"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-eyedropper fa-stack-1x fa-inverse"></i></span>\
					 <span id=bb_emo class="fa-stack fa-lg" data-title="Smiley"><i class="yellow fa fa-square fa-stack-2x"></i><i class="white fa fa-smile-o fa-stack-1x fa-inverse"></i></span>\
					 <span id=close_to class="fa-stack fa-lg" data-title="Annuler"><i class="red fa fa-square fa-stack-2x fa-inverse"></i><i class="white fa fa-times fa-stack-1x fa-inverse"></i></span>\
					 </div>\
					<ul id=ctn-smiley>\
					'+(lisml)+'\
					</ul>\
					<p id="title_to"><input type="text" placeholder="Titre de votre topic..."/><p>\
					<textarea id="message_to" placeholder="Avant de poster, lisez les règles du forum et de la section dans laquelle vous vous apprétez à poster. Un post qui ne respect pas les règles peut conduire au banissement de votre compte définitivement."></textarea>\
					<div id=ctn-captcha></div>\
					<div id="bar_to"><p id="preview_to"><i class="fa fa-eye fa-lg"></i> Prévisualisation</p><p id="send_to"><i class="fa fa-paper-plane"></i> Envoyer</p>\
				 </div>').hide();

//insert content to DOM
containerto.insertBefore('.seSub:first').show('slide').show('slide').promise().done(function() {

	//Setup captcha for level 5
	var lvl = aream.attr('data-jslvl');

	if(parseInt(lvl) === 27) {

	$.ajax({
		type:'POST',
		data:'randomcc=gen',
		url:'php/getter.php'
		}).done(function(msg) {

		$('#ctn-captcha').append(msg); //Spawn the security question on the DOM
		$('#ctn-captcha').append('<input id=topiccap type="text" name="topiccap" placeholder="Réponse à la question" data-title="Réponse" />');

		});
	}

});

//Refresh selector
message_to = cacheSel('#message_to');
title_to = cacheSel('#title_to');

//Check css style error
message_to.on('click', function() {
	if($(this).css('borderTopColor') == 'rgb(255, 0, 0)')
	coloredInput(message_to,'errorClean','Avant de poster, lisez les règles du forum et de la section dans laquelle vous vous apprétez à poster. Un topic qui ne respect pas les règles peut conduire au banissement définitif de votre compte.');
});

title_to.on('click', function() {
	if($(this).css('borderTopColor') == 'rgb(255, 0, 0)')
	coloredInput(message_to,'errorClean','Titre de votre topic...');
});

//On click spect, show spectrum
$('.spect').spectrum({color:'#055698'});

//click bold
$('#bb_bold').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[b]'+ getHigh() +'[/b]');
});

//click italic
$('#bb_italic').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[i]'+ getHigh() +'[/i]');
});
//click large
$('#bb_large').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[large]'+ getHigh() +'[/large]');
});

//click small
$('#bb_small').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[small]'+ getHigh() +'[/small]');
});

//click underline
$('#bb_underline').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[u]'+ getHigh() +'[/u]');
});

$('#bb_strikethrough').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[s]'+ getHigh() +'[/s]')
});

//click underline
$('#bb_code').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[code]'+ getHigh() +'[/code]');
});

//click quote
$('#bb_quote').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[quote]'+ getHigh() +'[/quote]');
});

//click left
$('#bb_left').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[left]'+ getHigh() +'[/left]');
});

//click center
$('#bb_center').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[center]'+ getHigh() +'[/center]');
});

//click right
$('#bb_right').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[right]'+ getHigh() +'[/right]');
});

//click justify
$('#bb_justify').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[justify]'+ getHigh() +'[/justify]');
});

//paragraph
$('#bb_paragraph').on('click', function() {
	//Replace the highlight text
	replaceIt(message_to[0], '[paragraph]'+ getHigh() +'[/paragraph]');
});

//click smiley button
$('#bb_emo').on('click', function() {

	ctn_smiley = $('#ctn-smiley');

	if(ctn_smiley.is(':hidden'))
		ctn_smiley.slideDown();
	else
		ctn_smiley.slideUp();
});

//Click on the smiley (emoticon)
$('.smemo').on('click', function() {
	replaceIt(message_to[0], getHigh() +'[emo='+ $(this).attr('id') +']');
});

//click image
$('#bb_image').on('click', function() {
	if(getHigh() === '') {
		var linkU = prompt('Lien vers l\'image', 'http://');
		var walg  = prompt('Pour l\'alignement à gauche, écrivez L, à droite R, vide pour centrer', '');
		    walg  = (walg === 'L') ? ' align=L' : ((walg !== 'R' ) ? 'align=C' : ' align=R');
		if (linkU != null) {replaceIt(message_to[0], '[img'+ walg +']' + linkU + '[/img]'); }
	}
	//Replace the highlight text
	else replaceIt(message_to[0], '[img]'+ getHigh() +'[/img]');
});

//click url
$('#bb_url').on('click', function() {
	if(getHigh() === '') {
		var linkU = prompt('Lien de l\'url', 'http://');
		if (linkU != null) {replaceIt(message_to[0], '[url]' + linkU + '[/url]'); }
	}
	//Replace the highlight text
	else replaceIt(message_to[0], '[url]'+ getHigh() +'[/url]');
});

//click pickup color
$('.sp-container button').on('click', function() {
	//Get the current color selected
	var colorPick =	$('.spect').spectrum('get').toHexString();

	//Replace the highlight text
	replaceIt(message_to[0], '[color='+ colorPick +']'+ getHigh() +'[/color]');
});

//Preview reply
$('#preview_to').on('click', function() {

	//Empty
	if(message_to.val() === '') {
	coloredInput(message_to, 'errorSe', 'Ne peut pas être vide...');
	return;
	}

	//Less 10 chars
	if(message_to.val().length < 5) {
	coloredInput(message_to, 'errorSe', '5 caractères minimum');
	return;
	}

	//bbcode to html
	$.ajax({
	type: 'POST',
	url: 'php/getter.php',
	data: 'textr=' + encodeURIComponent(message_to.val())
	}).done(function(msg) {

	//Prepare container
	textr = '\
		<div class="containerPreview">\
			<span class="closePrev fa-stack fa-lg">\
				<i class="fa fa-circle fa-stack-2x"></i>\
				<i class="fa closePrev fa fa-times  fa-stack-1x fa-inverse"></i>\
			</span>\
			<div class="messagePreview">'+ msg +'</p>\
		</div>';

	//Spawn it
	body.append(textr).promise().done(function() {
		$('.containerPreview').fadeIn();
	});

	});
});

//On send topic
$('#send_to').on('click', function() {

	topiccap = cacheSel('#topiccap');

	//Empty
	if(message_to.val() === '') {
	coloredInput(message_to, 'errorSe', 'Ne peut pas être vide...');
	return;
	}

	//Less 10 chars
	if(message_to.val().length < 5) {
	coloredInput(message_to, 'errorSe', '5 caractères minimum');
	return;
	}

	if(title_to.children().val().length < 10 || title_to.children().val().length > 80) {
	coloredInput(title_to.children(), 'errorSe', '10 caractères minimum, 80 maximum');
	return;
	}

	var lvl = aream.attr('data-jslvl');

	if(parseInt(lvl) === 27) {

		if(topiccap.val().length < 1) {
			coloredInput(replycap, 'errorSe', 'Ne peut pas être vide...');
			return;
		}

	}

	else
		topiccap = 'none';

	//Refresh selector
	message_to = cacheSel('#message_to');

	//Insert the new topic
	spawnLoad('load');
	$.ajax({
	type:'POST',
	url:'php/getter.php',
	data:'sendt='+ encodeURIComponent(message_to.val()) +'&title_to='+ encodeURIComponent(title_to.children().val()) +'&sectionid='+ sectionid +'&security='+ ((topiccap === 'none') ? 'none' : topiccap.val())
	}).done(function(msg) {

		//Remove loading
		spawnLoad('kill');

		//Keep the message
		keepmess = message_to.val();

		//Flood protection
		if(msg === 'flood' || msg === 'flood3' || msg === 'flood4' || msg === 'flood5' || msg === 'badcap') {

			if(msg === 'flood')
			coloredInput(message_to, 'errorSe', 'Flood protection, merci d\'attendre au moins 1 minute avant de poster. Réapparition de votre message dans 5 secondes...');

			else if(msg === 'flood3')
			coloredInput(message_to, 'errorSe', 'Flood protection, merci d\'attendre au moins 2 minutes avant de poster. Réapparition de votre message dans 5 secondes...');

			else if(msg === 'flood4')
			coloredInput(message_to, 'errorSe', 'Flood protection, merci d\'attendre au moins 5 minutes avant de poster. Réapparition de votre message dans 5 secondes...');

			else if(msg === 'flood5')
			coloredInput(message_to, 'errorSe', 'Flood protection, merci d\'attendre au moins 10 minutes avant de poster. Réapparition de votre message dans 5 secondes...');

			else if(msg === 'badcap') {

				coloredInput(message_to, 'errorSe', 'Captcha incorrect');

				$('#ctn-captcha').empty();

				$.ajax({
					type:'POST',
					data:'randomcc=gen',
					url:'php/getter.php'
					}).done(function(msg) {

					$('#ctn-captcha').append(msg); //Spawn the security question on the DOM
					$('#ctn-captcha').append('<input id=topiccap type="text" name="topiccap" placeholder="Réponse à la question" data-title="Réponse" />');

					});
			}

			setTimeout(function(){
			coloredInput(message_to, 'errorClean', '');
			message_to.val(keepmess);
			}, 5000);

		} //End flood

		//Not authorized
		else if(msg === 'p1' || msg === '') {

			coloredInput(message_to, 'errorSe', 'Vous n\'êtes pas autoriser à poster dans cette section. Réapparition de votre message dans 5 secondes...');

			setTimeout(function(){
			coloredInput(message_to, 'errorClean', '');
			message_to.val(keepmess);
			}, 5000);

		} //End Not authorized

		else {

			//Delete the form new topic
			containerto.remove();

			//Show the post button again
			$('#button_newto').show();

			//Container success
			su = $('<div id="su">\
					<p>Votre topic s\'est enregistré avec succès, merci de patienter et de recharger la page de la section pour y voir votre topic affiché d\'ici 1 minute. le lien vers celui-ci sera :</p>\
					<input type="text" class="glink" value="https://forum.wawa-mania.ec/topic-'+ msg +'" />\
					</div>');

			//Get 80% in px
			var sizecal = $(window).width() * 0.8;

			//Max width
			if(parseInt(sizecal) > 420) var sizecal = 600;

			//Dialog
			su.dialog({
			modal:'true',
			width:sizecal,
			maxHeight: 350,
			resizable: false,
			title:'Succès',
			dialogClass:'statsModal',
			show:{effect:'drop', duration: 320},
			hide:{effect:'drop', duration: 320}
			});
		}
	});
});

//On click close_post, cancel the reply
$(document).off('click', '#close_to');
$(document).on('click', '#close_to', function() {

	//Remove the container
	containerto.hide('slide').promise().done(function() {
		containerto.remove();
		$('#button_newto').show('explode');
	});

});