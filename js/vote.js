if($('#covote').length > 0) $('#covote').remove();

sigle = (actvote === 'votep') ? '1' : '0';
stc = (sigle == '1') ? '+' : '-';

//Container vote
covote = $('<div id="covote">\
			<p> Afin de valider votre vote, merci de laisser un court commentaire sur la raison de celui-ci. Un vote injustifié peut mener au ban définitif de votre compte.</p>\
			<textarea id="commentv" maxlength="150" placeholder="150 caractères maximum, merci d\'être précis sur les raisons de votre vote par exemple, pour un vote +1 : Bon upload, information utile, bonne idée car...  Vote -1 : Ne respect pas les règles, insultant, raciste, flood..."></textarea>\
			</div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max width
	if(parseInt(sizecal) > 600) var sizecal = 600;

	//Pop the container vote
	covote.dialog({
		width:sizecal,
		modal:true,
		resizable: true,
		title:'Vote '+ stc +' 1',
		dialogClass:'modalNinja',
		show:{effect:'scale', duration: 320},
		buttons: [{
			text: 'Vote '+ stc +' 1',
			click: function() {


			//Get textarea value
			com  = $('#commentv');
			comv = $('#commentv').val();

			//Check if css com must be reset
			com.on('click', function() {
			if(placeholderReset(com) === true)
			coloredInput(com,'errorClean', '150 caractères maximum, merci d\'être précis sur les raisons de votre vote par exemple : Bon upload, post bien présenté, information utile, bonne idée car...');
			});

			//Check textarea, minimum 8
			if(comv.length < 8) {
			coloredInput(com,'errorSe','Minimum 8 caractères');
			return;
			}

			//Send vote
			$.ajax({
			type: 'POST',
			url: 'php/getter.php',
			data:'postid='+ postid +'&act='+ sigle +'&com='+ com.val()
			}).done(function(msg) {
				console.log(msg);
				//Not authorized to vote
				if(msg === 'f1') {
				coloredInput(com,'errorSe','Vous n\'avez pas l\'autorisation de voter, renseignez-vous auprès de la FAQ de Wawa-Mania sur l\'acquisition de ce privilège.');
				return;
				}

				//No more vote left
				else if(msg === 'f2') {
				coloredInput(com,'errorSe','Vous n\'avez plus de vote disponible pour aujourd\'hui.');
				return;
				}

				//Already voted this post
				else if(msg === 'f3') {
				coloredInput(com,'errorSe','Vous avez déjà voté pour ce post');
				return;
				}

				//Own post
				else if(msg === 'f4') {
				coloredInput(com,'errorSe','Vous ne pouvez pas voter pour votre propre post !');
				return;
				}

				//Post has not vote (cancel -1 on 0 vote post
				else if(msg === 'f5') {
				coloredInput(com,'errorSe','Ce post n\'a actuellement aucun vote à son actif. Si celui-ci ne respect pas les règles du forum, signalez le.');
				return;
				}

				else if(msg === 'f6') {
					coloredInput(com,'errorSe','Un badge de niveau 4 est nécessaire pour effectuer un vote.');
					return;
					}

				//Ok
				else if(msg === 'fok') {
				com.val("").css('color','green').css('border-color','green').attr('placeholder', 'Votre vote a correctement été enregistré');
				return;
				}

			});/* End Ajax */
		}, /* End function button */
	}] /* Close button */
});/* End dialog */