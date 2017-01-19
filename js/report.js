//If container already in DOM, remove it
if(report.length > 0) report.remove();

//Container report
/*report = $('<div id="report">\
			<textarea id="reportv" maxlength="150" placeholder="Attention, les signalements abusifs, sans intérêt ou encore pour dire merci seront passible de sanction allant d\'un simple avertissement au ban."></textarea>\
			</div>');*/

	
	body.append('<div id=report> </div>');
	
	report = cacheSel('#report');
	
	//Container, select subject / textarea 
	report.html('<p>Selectionner le sujet de votre signalement</p>\
				<ul>\
					 <li id=reDead><i class="fa fa-unlink"> Liens morts</i></li>\
					 <li id=reForb><i class="fa fa-ban"> Hors charte</i></li>\
					 <li id=reMove><i class="fa fa-share"> A déplacer</i></li>\
					 <li id=reSecu><i class="fa fa-crosshairs"> Virus/Trojan/Scam...</i></li>\
					 <li id=reHate><i class="fa fa-globe"> Insulte envers un peuple/religion/minorité...</i></li>\
					 <li id=rePers><i class="fa fa-eye-slash"> Violation vie privée</i></li>\
					 <li id=reProt><i class="fa fa-child"> Contenu pédophile</i></li>\
					 <li id=reOthr><i class="fa fa-random"> Réouverture, résolu, autres...</i></li>\
				</ul>\
				<textarea id=reportv placeholder="default"></textarea>');
	
	//Refresh caching
	report = cacheSel('#report');
	reportv = cacheSel('#reportv');
	
	//Hide the reportv until a subject get select
	reportv.hide();

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;
	
	//Max width
	if(parseInt(sizecal) > 600) var sizecal = 600;

	//Pop the container vote
	report.dialog({
		width:sizecal,
		modal:true,
		resizable: true,
		title:'Signalement',
		dialogClass:'modalNinja',
		show:{effect:'scale', duration: 320},
		buttons: [{
			text: 'Confirmation',
			click: function() {

				//Subject 
				reps = reportv.attr('class'); 
					
				//Value textarea
				rept = reportv.val();
				
				//Less 10 chars return
				if(rept.length < 10) {
					coloredInput(reportv,'errorSe','Minimum 10 caractères');
					return;
				}
				
				 //Rewrite the subject of the report list
				 if(reps === 'reDead')
					 reps = 'LIENS MORTS<br>';
				 
				 else if(reps === 'reForb')
					 reps = 'POST/TOPICS HORS CHARTE<br>';
				 
				 else if(reps === 'reMove')
					 reps = 'TOPIC A DEPLACER<br>';
				 
				 else if(reps === 'reSecu')
					 reps = 'SECURITE VIRUS/TROJAN<br>';
				 
				 else if(reps === 'reHate')
					 reps = 'HAINE/RACISME<br>';
				 
				 else if(reps === 'rePers')
					 reps = 'VIOLATION VIE PRIVEE<br>';
				 
				 else if(reps === 'reProt')
					 reps = 'PEDOPHILIE OU ATTEINTE ENFANT<br>';
				 
				 else if(reps === 'reOthr')
					 reps = 'AUTRES<br>'
						 
				 else
					 reps = 'ERROR';
				 
				//Send report			
				$.ajax({
					type: 'POST',
					url: 'php/getter.php',
					data:'postid='+ postid +'&report='+ reps + rept
					}).done(function(msg) {
	
					//Already reported
					if(msg === 'f1') {
						coloredInput(reportv,'errorSe','Ce post a déjà été reporté à l\'équipe de modération et n\'a pas encore été traité.');
						return;
					}
				
					//No more vote left 
					else if(msg === 'f2') {
						coloredInput(reportv,'errorSe','Vous n\'avez pas l\'autorisation d\'ajouter de nouveau signalement pour le moment.');
						return;
					}
	
					//Ok
					else if(msg === 'fok') {
						reportv.val("").css('color','green').css('border-color','green').attr('placeholder', 'Votre signalement sera traité rapidement. Merci.');
						return;
					}
					
					//Error unknown
					else
						coloredInput(reportv,'errorSe','ERROR');
	
				//End Ajax
				});
			
		//End click
		},
		
	//End button		 
	}]
		
	//End dialog jUi
	});
	
//On click subject, appear the textarea
$(document).off('click', '#reDead, #reForb, #reMove, #reSecu, #reHate, #rePers, #reProt, #reOthr');
$(document).on('click', '#reDead, #reForb, #reMove, #reSecu, #reHate, #rePers, #reProt, #reOthr', function() {
		
		//Subject ID
		wsub = $(this).attr('id');
		
		//Show the textarea
		reportv.fadeIn();
		
		//Assign subjectId to a class reportv
		reportv.addClass(wsub);

		//Dead link
		if(wsub == 'reDead')
			coloredInput(reportv, 'errorClean', 'Merci de ne signaler que les topics dont tous les liens sont morts ! Si vous signalez un topic ne contenant un hébergeur encore actif, vous risquez d\'être banni.');
		
		//No rules
		else if(wsub === 'reForb') 
			coloredInput(reportv, 'errorClean', 'Décrivez pourquoi ce topic est hors charte dans un court commentaire.');
		
		//Move
		else if(wsub ==='reMove')
			coloredInput(reportv, 'errorClean', 'Expliquez rapidement pourquoi ce topic doit être déplacé et dans quelle section si possible.');
		
		//Secu (virus/trojan)
		else if(wsub === 'reSecu')
			coloredInput(reportv, 'errorClean', 'Ne signalez que si le topic présente un danger, comme la diffusion d\'un virus/trojan ou une page de phishing.');
			
		//Hate
		else if(wsub === 'reHate')
			coloredInput(reportv, 'errorClean', 'Post/topic insultant, diminuant un peuple ou une religion / à caractères racistes, homophobes, irrespecteux... Pouvant de façon générale, blesser ou visant à la haine de l\'autre');
		
		//Private life
		else if(wsub === 'rePers')
			coloredInput(reportv, 'errorClean', 'Violation de la vie privée d\'une personne, diffusion d\'informations, harcélement, photo(s)/vidéo(s) à caractères pornographique... Révélant de manière générale une information non souhaité par celui-ci en public');
		
		//Child protection
		else if(wsub === 'reProt')
			coloredInput(reportv, 'errorClean', 'Affichage explicite ou implicite, de photos/vidéos d\'un enfant ou d\'un mineur (-18 ans) ou toute autre information, même partielle. Le staff s\'engage à agir en urgence face à ce genre de requête.');
		
		//Other
		else if(wsub === 'reOthr')
			coloredInput(reportv, 'errorClean', 'Pour une raison différentes (réouverture de topic...)');
			
		else
			coloredInput(reportv, 'errorSe', 'ERROR');
});
	
//Check if css com must be reset
reportv.on('click', function() {
	
	//Reset css 
	if(placeholderReset(reportv) === true) {
		
		//Subject 
		wsub = reportv.attr('class');
		
		//Dead link
		if(wsub === 'reDead')
			coloredInput(reportv, 'errorClean', 'Merci de ne signaler que les topics dont tous les liens sont morts ! Si vous signalez un topic ne contenant un hébergeur encore actif, vous risquez d\'être banni.');
		
		//No rules
		else if(wsub === 'reForb') 
			coloredInput(reportv, 'errorClean', 'Décrivez pourquoi ce topic est hors chartre dans un court commentaire.');
		
		//Move
		else if(wsub ==='reMove')
			coloredInput(reportv, 'errorClean', 'Expliquez rapidement pourquoi ce topic doit être déplacé et dans quelle section si possible.');
		
		//Secu (virus/trojan)
		else if(wsub === 'reSecu')
			coloredInput(reportv, 'errorClean', 'Ne signalez que si le topic présente un danger, comme la diffusion d\'un virus/trojan ou une page de phishing.');
		
		//Hate
		else if(wsub === 'reHate')
			coloredInput(reportv, 'errorClean', 'Post/topic insultant, diminuant un peuple ou une religion / à caractères racistes, homophobes, irrespecteux... Pouvant de façon générale, blesser ou visant à la haine de l\'autre');
		
		//Private life
		else if(wsub === 'rePers')
			coloredInput(reportv, 'errorClean', 'Violation de la vie privée d\'une personne, diffusion d\'informations, harcélement, photo(s)/vidéo(s) à caractères pornographique... Révélant de manière générale une information non souhaité par celui-ci en public');
		
		//Child protection
		else if(wsub === 'reProt')
			coloredInput(reportv, 'errorClean', 'Affichage explicite ou implicite, de photos/vidéos d\'un enfant ou d\'un mineur (-18 ans) ou toute autre information, même partielle. Le staff s\'engage à agir en urgence face à ce genre de requête.');
		
		//Other
		else if(wsub === 'reOthr')
			coloredInput(reportv, 'errorClean', 'Pour une raison différentes (réouverture de topic...)');
		
		else
			coloredInput(reportv, 'errorSe', 'ERROR');
	}
	
});
