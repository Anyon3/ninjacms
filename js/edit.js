bbchidden = $('.bbc_'+ postid);
bbctn = $('.ctn'+ postid);

//Get current HL highlight
function getHigh() {
	var textComponent = document.getElementById('message_edit');
	var startPos = textComponent.selectionStart;
	var endPos = textComponent.selectionEnd;
	hltext = textComponent.value.substring(startPos, endPos);
	return hltext;
}

//Fonction - replace highlight text with bbcode
function replaceIt(txtarea, newtxt) {
	$(txtarea).val($(txtarea).val().substring(0, txtarea.selectionStart)+ newtxt + $(txtarea).val().substring(txtarea.selectionEnd));
}

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
containeredit = '<div id="container_edit">\
					'+ ((edtitle === 'firstmsg') ? '<input id="valtitle" type="text" value="'+ vtitle +'"/>' : '') +'\
					 <div id="bar_bbedit">\
					 <span id="ed_bold" class="fa-stack fa-lg" data-title="Gras"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-bold fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_italic" class="fa-stack fa-lg" data-title="Italic"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-italic fa-stack-1x fa-inverse"></i></span>\
					 <span id=ed_large class="fa-stack fa-lg" data-title="Grand"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-text-height fa-stack-1x fa-inverse"></i></span>\
					 <span id=ed_small class="fa-stack fa-lg" data-title="Petit"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-text-width fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_underline" class="fa-stack fa-lg" data-title="Souligné"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-underline fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_strikethrough" class="fa-stack fa-lg" data-title="Barré"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-strikethrough fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_code" class="fa-stack fa-lg" data-title="Code"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-code fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_quote" class="fa-stack fa-lg" data-title="Quote"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-quote-left fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_left" class="fa-stack fa-lg" data-title="Gauche"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-left fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_center" class="fa-stack fa-lg" data-title="Centre"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-center fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_right" class="fa-stack fa-lg" data-title="Droite"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-right fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_justify" class="fa-stack fa-lg" data-title="Pre"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-align-justify fa-stack-1x fa-inverse"></i></span>\
					 <span id=ed_paragraph class="fa-stack fa-lg" data-title="Paragraph"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-paragraph fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_image" class="fa-stack fa-lg" data-title="Image"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-picture-o fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_url" class="fa-stack fa-lg" data-title="Lien"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-link fa-stack-1x fa-inverse"></i></span>\
					 <span id="ed_color" class="spect fa-stack fa-lg" data-title="Couleur"><i class="fa fa-square fa-stack-2x"></i><i class="white fa fa-eyedropper fa-stack-1x fa-inverse"></i></span>\
					 <span id=ed_emo class="fa-stack fa-lg" data-title="Smiley"><i class="yellow fa fa-square fa-stack-2x"></i><i class="white fa fa-smile-o fa-stack-1x fa-inverse"></i></span>\
					 <span id="close_post" class="fa-stack fa-lg" data-title="Annuler"><i class="red fa fa-square fa-stack-2x fa-inverse"></i><i class="white fa fa-times fa-stack-1x fa-inverse"></i></span>\
					 </div>\
					 <ul id=ctn-smiley-ed>\
						'+(lisml)+'\
						</ul>\
					<textarea id="message_edit"></textarea>\
				 </div>';

//insert content to DOM
bbctn.empty().promise().done(function() {
bbctn.append(containeredit);
});

//Fix newline
textedit = bbchidden.html();
//$('#message_edit').val(textedit.replace(/<br>/gi,'\r'));
$('#message_edit').val(textedit.replace(/<br>/gi,'\r').replace(/<a href="([https?\:\/\s\?\=\w\d\.\-]+)"([\_\.\:\/\s\"\=\w\d\<)\s?]*)>([\.\:\/\s\?\"\=\w\d\<\-]+)>/gmi,"$1").replace(/\&gt;/gmi,'>').replace(/\&lt;/gmi,'<'));

//Selector textarea
message_edit = $('#message_edit');

//Title
if(edtitle === 'firstmsg') uptitle = $('#valtitle');

//Check css style error
message_edit.on('click', function() {
	if($(this).css('borderTopColor') == 'rgb(255, 0, 0)')
	coloredInput(message_edit,'errorClean','');
});

if(edtitle === 'firstmsg') {

	uptitle.on('click', function() {
	if($(this).css('borderTopColor') == 'rgb(255, 0, 0)')
	coloredInput(message_edit,'errorClean','');
	});
}

//On click spect, show spectrum
$('.spect').spectrum({color:'#055698'});

//click bold
$('#ed_bold').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[b]'+ getHigh() +'[/b]');
});

//click italic
$('#ed_italic').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[i]'+ getHigh() +'[/i]');
});

//click large
$('#ed_large').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[large]'+ getHigh() +'[/large]');
});

//click small
$('#ed_small').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[small]'+ getHigh() +'[/small]');
});

//click underline
$('#ed_underline').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[u]'+ getHigh() +'[/u]');
});

$('#ed_strikethrough').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[s]'+ getHigh() +'[/s]')
});

//click underline
$('#ed_code').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[code]'+ getHigh() +'[/code]');
});

//click quote
$('#ed_quote').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[quote]'+ getHigh() +'[/quote]');
});

//click left
$('#ed_left').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[left]'+ getHigh() +'[/left]');
});

//click center
$('#ed_center').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[center]'+ getHigh() +'[/center]');
});

//click right
$('#ed_right').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[right]'+ getHigh() +'[/right]');
});

//click justify
$('#ed_justify').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[justify]'+ getHigh() +'[/justify]');
});

//paragraph
$('#ed_paragraph').on('click', function() {
	//Replace the highlight text
	replaceIt(message_edit[0], '[paragraph]'+ getHigh() +'[/paragraph]');
});

//click image
$('#ed_image').on('click', function() {
	if(getHigh() === '') {
		var linkU = prompt('Lien vers l\'image', 'http://');
		var walg  = prompt('Pour l\'alignement à gauche, écrivez L, à droite R, vide pour centrer', '');
		    walg  = (walg === 'L') ? ' align=L' : ((walg !== 'R' ) ? ' align=C' : ' align=R');
		if (linkU != null) {replaceIt(message_edit[0], '[img'+ walg +']' + linkU + '[/img]'); }
	}
	//Replace the highlight text
	else replaceIt(message_edit[0], '[img]'+ getHigh() +'[/img]');
});

//click url
$('#ed_url').on('click', function() {
	if(getHigh() === '') {
		var linkU = prompt('Lien de l\'url', 'http://');
		if (linkU != null) {replaceIt(message_edit[0], '[url]' + linkU + '[/url]'); }
	}
	//Replace the highlight text
	else replaceIt(message_edit[0], '[url]'+ getHigh() +'[/url]');
});

//click pickup color
$('.sp-container button').on('click', function() {
	//Get the current color selected
	var colorPick =	$('.spect').spectrum('get').toHexString();

	//Replace the highlight text
	replaceIt(message_edit[0], '[color='+ colorPick +']'+ getHigh() +'[/color]');
});

//click smiley button
$('#ed_emo').on('click', function() {

	ctn_smiley = $('#ctn-smiley-ed');

	if(ctn_smiley.is(':hidden'))
		ctn_smiley.slideDown();
	else
		ctn_smiley.slideUp();
});

//Click on the smiley (emoticon)
$('.smemo').on('click', function() {
	replaceIt(message_edit[0], getHigh() +'[emo='+ $(this).attr('id') +']');
});

$(document).off('click', '.editfire');
$(document).on('click', '.editfire', function() {

	//Less 10 chars
	if(message_edit.val().length < 10) {
	coloredInput(message_edit, 'errorSe', '10 caractères minimum');
	return;
	}

	//Title required ?
	if(edtitle === 'firstmsg') {
		if(uptitle.val().length < 10 || uptitle.val().length > 80) {
		coloredInput(uptitle, 'errorSe', '10 caractères minimum, 80 maximum');
		return;
		}
	}

	//Data (with or without title
	if(edtitle === 'firstmsg')
		var edupt = 'editr='+ encodeURIComponent(message_edit.val()) +'&postid='+ postid +'&edtitle='+ encodeURIComponent(uptitle.val());
	else
		var edupt = 'editr='+ encodeURIComponent(message_edit.val()) +'&postid='+ postid;

	//Insert the new reply
	spawnLoad('load');
	$.ajax({
	type:'POST',
	url:'php/getter.php',
	data: edupt
	}).done(function(msg) {

		//Remove loading
		spawnLoad('kill');

		//Keep the message
		keepmess  = message_edit.val();
		keeptitle = (edtitle === 'firstmsg') ? uptitle.val() : false;

		//Flood protection
		if(msg == 'flood') {

			coloredInput(message_edit, 'errorSe', 'Flood protection, merci d\'attendre au moins 1 minute avant d\'éditer. Réapparition de votre message dans 5 secondes...');
			setTimeout(function(){
			coloredInput(message_edit, 'errorClean', '');
			message_edit.val(keepmess);
			}, 5000);

		} //End flood

		//Not authorized
		else if(msg == 'p1') {

			coloredInput(message_edit, 'errorSe', 'Vous n\'êtes pas autoriser à editer ce topic. Réapparition de votre message dans 5 secondes...');
			setTimeout(function(){
			coloredInput(message_edit, 'errorClean', '');
			message_edit.val(keepmess);
			}, 5000);
		} //End Not authorized

		else {

			//If title update, change val in DOM
			if(edtitle === 'firstmsg')
			$('.artitle').text(keeptitle);

			//Add the new bbcode to the hidden container
			bbchidden.empty();
			bbchidden.append(keepmess);

			//Remove the green class (button will come back to his normal color
			$('.editp').removeClass('editfire').prev().removeClass('green');

			//Empty and append the new message to the container
			bbctn.empty();
			msg = linkify(msg);
			bbctn.append(msg);
		}
	});
});