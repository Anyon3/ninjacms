
//Function - spawnLoad
function spawnLoad(TrigLoad) {

	loadGen = $('<div id="loadGen"><p><i class="fa fa-cog fa-spin"></i>Chargement en cours</p></div>');

	if(TrigLoad === 'load') {
	TrigLoad = loadGen.insertAfter('body');
	return TrigLoad;
	}

	else if(TrigLoad === 'kill') $('#loadGen').remove();
}

//Show report
$('#admrpt').on('click', function() {
$.ajax({
	type:'POST',
	url:'../php/getter.php',
	data:'admrpt=1',
	dataType: 'json'
	}).done(function(json) {

		/* Empty the right ctn*/
		$('#admback').empty();

		/* Check if there is new report */
		if(json === null) {
		$('<ul class=nurpt><li>Aucun nouveau signalement</li></ul>').appendTo('#admback');
		return;
		}

		/* subject, tid, pid, poster, report, reason */
		$.each(json, function(idx, value) {
		cbyrpt = (json[idx][3] === 9 || json[idx][3] === 47 || json[idx][3] === 48 || json[idx][3] === 49) ? 'porn' : 'noporn';
		$('<ul id=r'+ json[idx][7] +' class="byrpt '+ cbyrpt +'">\
			<li><i id='+ json[idx][7] +' class="rptck fa fa-check-circle fa-2x"></i></li>\
			<li class=titlerpt><a href="https://forum.wawa-mania.ec/topic-'+ json[idx][1] +'">'+ json[idx][0] +'</a></li>\
			<li class=whorpt><i class="fa fa-commenting-o"></i> Reporté par '+ json[idx][5] +'</li>\
			<li class=secrpt><i class="fa fa-map-pin"></i> Section '+ json[idx][3] +'</li>\
			<li class=rearpt><i class="fa fa-bullhorn"></i> '+ json[idx][6] +' </li>\
			<li class=directrpt><a href="https://forum.wawa-mania.ec/pid-'+ json[idx][2] +'" target=_blank>Accès direct -> Post-'+ json[idx][2] +'</a></li>\
			</ul>').appendTo('#admback');
		});
	});
});


$(document).off('click', '.rptck');
$(document).on('click', '.rptck', function(e) {

	//Id report
	idr = $(e.target).attr('id');

	$.ajax({
	type:'POST',
	url:'../php/getter.php',
	data:'admck='+ idr,
	}).done(function(msg) {
	$('#'+ idr).parent().remove();
	$('#r'+ idr).css('background-color', '#34A853');
	});
});
//End report

$('#showmv').on('click', function() {

	$.ajax({
	type:'POST',
	url:'../php/getter.php',
	data:'showmv=1',
	dataType:'json'
	}).done(function(json) {
	$('#admback').empty();
	$('<div id="log">').appendTo('#admback');
		/* subject, tid, pid, poster, report, reason */
		$.each(json, function(idx, value) {
		$('<ul id=mv'+ json[idx][0] +' class="showlog">\
			<li><a href="https://forum.wawa-mania.ec/topic-'+ json[idx][1] +'">Topic '+ json[idx][1] +'</a></li>\
			<li>Vers '+ json[idx][2] +'</li>\
			<li>Vers '+ json[idx][3] +'</li>\
			<li>Incriminé '+ json[idx][4] +'</li>\
			</ul>').appendTo('#admback');
		});

	});

});

$('#showsp').on('click', function() {

	$.ajax({
	type:'POST',
	url:'../php/getter.php',
	data:'showsp=1',
	dataType:'json'
	}).done(function(json) {
	$('#admback').empty();
	$('<div id="log">').appendTo('#admback');
		/* subject, tid, pid, poster, report, reason */
		$.each(json, function(idx, value) {
		$('<ul id=sp'+ json[idx][0] +' class="showlog">\
			<li><a href="https://forum.wawa-mania.ec/topic-'+ json[idx][1] +'">Topic '+ json[idx][1] +'</a></li>\
			<li>Date '+ json[idx][2] +'</li>\
			<li>Incriminé '+ json[idx][3] +'</li>\
			<li>(0 retiré, 1 épinglé) '+ json[idx][4] +'</li>\
			</ul>').appendTo('#admback');
		});

	});

});

$('#showlc').on('click', function() {

	$.ajax({
	type:'POST',
	url:'../php/getter.php',
	data:'showlc=1',
	dataType:'json'
	}).done(function(json) {
	$('#admback').empty();
	$('<div id="log">').appendTo('#admback');
		/* subject, tid, pid, poster, report, reason */
		$.each(json, function(idx, value) {
		$('<ul id=lc'+ json[idx][0] +' class="showlog">\
			<li><a href="https://forum.wawa-mania.ec/topic-'+ json[idx][1] +'">Topic '+ json[idx][1] +'</a></li>\
			<li>Date '+ json[idx][2] +'</li>\
			<li>Incriminé '+ json[idx][3] +'</li>\
			<li>(0 ouvert, 1 fermé) '+ json[idx][4] +'</li>\
			</ul>').appendTo('#admback');
		});

	});

});

$('#showet').on('click', function() {

	$.ajax({
	type:'POST',
	url:'../php/getter.php',
	data:'showet=1',
	dataType:'json'
	}).done(function(json) {
	$('#admback').empty();
	$('<div id="log">').appendTo('#admback');
		/* subject, tid, pid, poster, report, reason */
		$.each(json, function(idx, value) {
		$('<ul id=et'+ json[idx][0] +' class="showlog">\
			<li><a href="https://forum.wawa-mania.ec/pid-'+ json[idx][1] +'">post '+ json[idx][1] +'</a></li>\
			<li>Date '+ json[idx][2] +'</li>\
			<li>Incriminé '+ json[idx][3] +'</li>\
			</ul>').appendTo('#admback');
		});

	});

});

//Field and button search for a members

$('#srcus').on('click', function() {
	$('#admback').empty();
	$('<input type="text" id=srcusername maxlength="25" placeholder="Par pseudo..." /><span id=srcsend>Recherche</span>').appendTo('#admback');
});

//Display user information
$(document).off('click', '#srcsend');
$(document).on('click', '#srcsend', function() {

	//Get the pseudo
	pseudo = $('#srcusername').val();

	//At least 3 chars
	if(pseudo.length < 3) return;

	//Load
	spawnLoad('load');

	$.ajax({
	type:'POST',
	url:'../php/getter.php',
	data:'admsrcus='+ pseudo,
	dataType:'json'
	}).done(function(json) {

		//Kill loader
		spawnLoad('kill');

		//Not found
		if(json === null) {
		$('#srcusername').val('').attr('placeholder', 'introuvable');
		return;
		}

		//Clean the container
		$('#admback').empty();

		//Keep warning + id for callback details
		details = json[0]["avertissement"];
		target = json[0]["id"];

		//Spawn the user information
		$('#admback').append('<ul id=displaysrcus>\
							  <li id=admban title="bannir"><i class="fa fa-minus-circle"></i></li>\
							  <li id=admwa title="Ajouter un avertissement"><i class="fa fa-exclamation-triangle"></i></li>\
							  <li id=suid data-idus="'+ json[0]["id"] +'"><i class="fa fa-user"></i> '+ pseudo +'</li>\
							  <li id=srcwa><i class="fa fa-exclamation-triangle"></i> Avertissement : '+ json[0]["avertissement"] +'</li>\
							  '+ ((details > 0) ? '<li id=srcgwa class='+ target +'><i class="fa fa-plus"></i><i> Afficher détails</i></li>' : '') +'\
							  <li class=srcpts> <i class="fa fa-star"></i> Point(s) : '+ json[0]["pts"] +'</li>\
							  <li class=srcpdis> <i class="fa fa-hand-pointer-o"></i> Pts dis : '+ json[0]["vote_left"] +'</li>\
							  <li class=srcjabber><i class="fa fa-lightbulb-o"></i> Jabber : '+ json[0]["jabber"] +'</li>\
							  <li class=srcicq><i class="fa fa-commenting-o"></i> Icq : '+ json[0]["icq"] +'</li>\
							  </ul>');

		//Explode badges string
		idbadge = json[0]["badges"].split('-');

		$('#admback').append('<ul id=srcusba>\
							  <li id=batitle><i id=admbdg class="fa fa-plus"></i> Liste des badges</li>');
		//Loop badges tag
		badges = '';
		$.getJSON('../cachejs/badges.json', function(data) {
			$.each(idbadge, function(idx, value) {
			badges = badges + '<li id="ba'+ value +'" class="brm groupe-'+ data[value].groupe +'" data-title="<span class=substy>'+ data[value].subtitle +'</span><br>'+ data[value].description +'"><i class="fa fa-'+ data[value].icon +'"></i> '+ data[value].name +'</li>';
			});//End each

			$('#admback').append(badges + '</ul>').promise().done(function() {
				//Loop vote given
				cfint = 0;
				$('#admback').append('<ul id=srcvote>\
								  <li id=votitle><i id=admvt></i> Liste des votes</li>');

				$.each(json, function(idx, value) {

				if(cfint === 0) cfint = 1;

				else if(json[idx][0] === "idto") {

				$('#admback').append('<li class="'+ ((cfint > 5) ? 'more ' : '') +'ctnvo brm '+ ((json[idx][9] === 1) ? 'pos' : 'neg') +'">\
									<p>Commentaire : '+ json[idx][11] +'</p>\
									 <a href="https://forum.wawa-mania.ec/pid-'+ json[idx][5] +'" target=_blank>Voir le post</a>\
									  </li>');
				cfint++;

				}

				else if(json[idx][0] === "idvo")
					return false;

				});

				$('#admvt').text(cfint - 1 +'vote(s)');

				$('#admback').append(((cfint > 5) ? '<i class="smore fa fa-arrow-down"> More</i>' : '') +'</ul>').promise().done(function() {

				$('#admback').append('<ul id=srcgvote>\
								  <li id=gvotitle>Liste des votes reçu </li>');

					$.each(json, function(idx, value) {

					if(json[idx][0] === "idvo") {
					$('#admback').append('<li class="ctnvo brm '+ ((json[idx][7] === 1) ? 'pos' : 'neg') +'">\
										<p>Par :'+ json[idx][5] +' </p>\
										<p>Commentaire : '+ json[idx][9] +'</p>\
										 <a href="https://forum.wawa-mania.ec/pid-'+ json[idx][3] +'" target=_blank>Voir le post</a>\
										  </li>');
					}

					});

				});

			});

		});

	//End Ajax
	});

});

$(document).off('click', '#srcgwa');
$(document).on('click', '#srcgwa', function(e) {

	uid = $(this).attr('class');

	$.ajax({
		type:'POST',
		url:'../php/getter.php',
		data:'target='+ uid +'&warning=1',
		dataType:'json'
		}).done(function(json) {

		$.each(json, function(idx, value) {
			$('#srcwa').append('<li class=detailswa><i class="fa fa-exclamation-triangle"></i> Par '+ json[idx][3] +' : '+ json[idx][1] +' <a href="https://forum.wawa-mania.ec/pid-'+ json[idx][5] +'">Post visé</a>  </li>');
		});

	}).promise().done(function() {
		$('#srcgwa').remove();
	});

});

//End display user information
$(document).off('click', '.srcemail');
$(document).on('click', '.srcemail', function(e) {

	$('.srcemail').children('i:first-child').remove();
	classcheck = $(e.target).attr('class').split(' ')[0];

	if($('#admuemail').length > 0 || classcheck === 'admca' || classcheck === 'admk') {
		$('.srcemail').prepend('<i class="fa fa-envelope-o"></i>');
		return;
	}

	bck = $(e.target).html();

	$(e.target).children().remove();
	$(e.target).empty();
	$(e.target).html('<input type="text" id=admuemail class=admk  data-org="'+ bck +'" /> <i class="admup fa fa-check-circle fa-lg"></i> <i class="admca fa fa-times-circle fa-lg"></i>');

});

$(document).off('click', '.srcicq');
$(document).on('click', '.srcicq', function(e) {

	$('.srcicq').children('i:first-child').remove();
	classcheck = $(e.target).attr('class').split(' ')[0];

	if($('#admuicq').length > 0 || classcheck === 'admca' || classcheck === 'admk') {
		$('.srcicq').prepend('<i class="fa fa-commenting-o"></i>');
		return;
	}

	bck = $(e.target).html();

	$(e.target).children().remove();
	$(e.target).empty();
	$(e.target).html('<input type="text" id=admuicq data-org="'+ bck +'" class=admk /> <i class="admup fa fa-check-circle fa-lg"></i> <i class="admca fa fa-times-circle fa-lg"></i>');

});

$(document).off('click', '.srcjabber');
$(document).on('click', '.srcjabber', function(e) {

	$('.srcjabber').children('i:first-child').remove();
	classcheck = $(e.target).attr('class').split(' ')[0];

	if($('#admujabber').length > 0 || classcheck === 'admca' || classcheck === 'admk') {
		$('.srcjabber').prepend('<i class="fa fa-lightbulb-o"></i>');
		return;
	}
	bck = $(e.target).html();

	$(e.target).children().remove();
	$(e.target).empty();
	$(e.target).html('<input type="text" id=admujabber class=admk  data-org="'+ bck +'" /> <i class="admup fa fa-check-circle fa-lg"></i> <i class="admca fa fa-times-circle fa-lg"></i>');

});

$(document).off('click', '.srcpdis');
$(document).on('click', '.srcpdis', function(e) {

	$('.srcpdis').children('i:first-child').remove();
	classcheck = $(e.target).attr('class').split(' ')[0];

	if($('#admudis').length > 0 || classcheck === 'admca' || classcheck === 'admk') {
		$('.srcpdis').prepend('<i class="fa fa-hand-pointer-o"></i>');
		return;
	}

	bck = $(e.target).html();

	$(e.target).children().remove();
	$(e.target).empty();
	$(e.target).html('<input type="text" id=admudis class=admk data-org="'+ bck +'" /> <i class="admup fa fa-check-circle fa-lg"></i> <i class="admca fa fa-times-circle fa-lg"></i>');

});

$(document).off('click', '.srcpts');
$(document).on('click', '.srcpts', function(e) {

	$('.srcpts').children('i:first-child').remove();
	classcheck = $(e.target).attr('class').split(' ')[0];

	if($('#admupts').length > 0 || classcheck === 'admca' || classcheck === 'admk') {
		$('.srcpts').prepend('<i class="fa fa-star"></i>');
		return;
	}

	bck = $(e.target).html();

	$(e.target).children().remove();
	$(e.target).empty();
	$(e.target).html('<input type="text" id=admupts class=admk data-org="'+ bck +'" /> <i class="admup fa fa-check-circle fa-lg"></i> <i class="admca fa fa-times-circle fa-lg"></i>');

});

$(document).off('click', '.admup');
$(document).on('click', '.admup', function(e) {

	upval = $(e.target).prev().val();
	upid = $(e.target).prev().attr('id');

	uid = $('#suid').attr('data-idus');

	$.ajax({
	type:'POST',
	url:'../php/getter.php',
	data:upid +'='+ upval +'&uid='+ uid
	}).done(function(msg) {

		if(msg !== 'ok') {
		 $('#'+ upid).val('').attr('placeholder', 'Incorrect');
		 return;
		}

		else {
		rpl = $('#'+ upid).attr('data-org').split(':');
		$('#'+ upid).next().next().remove();
		$('#'+ upid).next().remove();
		$('#'+ upid).replaceWith(rpl[0] +': '+ upval);
		}

	});
});

$(document).off('click', '.admca');
$(document).on('click', '.admca', function(e) {
	ca = $(e.target).prev().prev().attr('data-org');
	rid = $(e.target).prev().prev().attr('id');

	$(e.target).prev().prev().replaceWith(ca);
	$('#'+ rid).remove();
	$(e.target).prev().remove();
	$(e.target).remove();
});

//Warning
$(document).off('click', '#admwa');
$(document).on('click', '#admwa', function(e) {

	if($('#ctnadmwa').length > 0) ctnadmban.remove();

	ctnadmwa = $('<div id=ctnadmwa>\
					  <p> Raison de l\'avertissement </p>\
					 <input type="text" class=valaverto />\
					<p>Id du post concerné</p>\
			        <input type="text" class=valpost />\
					</div>');

		//Get 80% in px
		var sizecal = $(window).width() * 0.8;

		//Max 1200px
		if(parseInt(sizecal) > 400) var sizecal = 400;

		ctnadmwa.dialog({
			width:sizecal,
			modal:false,
			resizable: true,
			position: { my: "center top", at: "center top", of: "#admctn" },
			title:'Avertissement',
			show:{effect:'scale', duration: 320},
			buttons: {
	        'Avertir': function() {

				uid = $('#suid').attr('data-idus');
				reason = $('.valaverto').val();
				post = $('.valpost').val();

				if(reason === "" || post === "") {
					alert('Merci de renseigner tous les champs');
					return;
				}

				$.ajax({
				type: 'POST',
				url: '../php/getter.php',
				data:'admaverto='+ reason +'&uid='+ uid +'&post='+ post
				}).done(function(msg) {

					if(msg === 'ok') {
					alert('Avertissement ajouté');
					location.reload();
					}

				});
			},
		  }
		});
 });

//ban
$(document).off('click', '#admban');
$(document).on('click', '#admban', function(e) {

if($('#ctnadmban').length > 0) ctnadmban.remove();

ctnadmban = $('<div id=ctnadmban>\
				  <p> Raison du ban </p>\
				 <input type="text" class=valban />\
				</div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max 1200px
	if(parseInt(sizecal) > 400) var sizecal = 400;

	ctnadmban.dialog({
		width:sizecal,
		modal:false,
		resizable: true,
		position: { my: "center top", at: "center top", of: "#admctn" },
		title:'Ban',
		show:{effect:'scale', duration: 320},
		buttons: {
        'Bannir': function() {

			uid = $('#suid').attr('data-idus');
			reason = $('.valban').val();

			$.ajax({
			type: 'POST',
			url: '../php/getter.php',
			data:'admban='+ reason +'&uid='+ uid
			}).done(function(msg) {
				if(msg === 'ok') {
				alert('Compte banni !');
				location.reload();
				}

			});
		},
	  }
	});
});

//Change pass
$(document).off('click', '#admpw');
$(document).on('click', '#admpw', function(e) {

if($('#admpw').length > 0) $('#changepass').remove();

changepass = $('<div id=changepass>\
				  <p> Change le mdp </p>\
				 <input type="text" class=valps /> \
				</div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max 1200px
	if(parseInt(sizecal) > 400) var sizecal = 400;

	changepass.dialog({
		width:sizecal,
		modal:false,
		resizable: true,
		position: { my: "center top", at: "center top", of: "#admctn" },
		title:'Changer pass',
		show:{effect:'scale', duration: 320},
		buttons: {
        'Ok': function() {

			uid = $('#suid').attr('data-idus');
			pass = $('.valps').val();

			$.ajax({
			type: 'POST',
			url: '../php/getter.php',
			data:'changepass='+ encodeURIComponent(pass) +'&uid='+ uid
			}).done(function(msg) {
				if(msg === 'ok') {
				alert('Password changé');
				location.reload();
				}

			});
		},
	  }
	});
});

//Add badges
$(document).off('click', '#admbdg');
$(document).on('click', '#admbdg', function(e) {

	if($('#admbdg').length > 0) $('#addbdg').remove();

		addbdg = $('<div id=addbdg>\
				<ul>\
				<li id="ba28" class="bsu groupe-4"><i class="fa fa-male"></i> Fantôme</li>\
				<li id="ba27" class="bsu groupe-4"><i class="fa fa-male"></i> Approuvé</li>\
				<li id="ba38" class="bsu groupe-3"><i class="fa fa-google"></i> Google</li>\
				<li id="ba2" class="bsu groupe-2"><i class="fa fa-star"></i> V.I.P</li>\
				<li id="ba3" class="bsu groupe-5"><i class="fa fa-Upload"></i> SuperUploader</li>\
				<li id="ba4" class="bsu groupe-5"><i class="fa fa-Upload"></i> Megauploader</li>\
				<li id="ba8" class="bsu groupe-7"><i class="fa fa-pencil"></i> Graphiste</li>\
				<li id="ba9" class="bsu groupe-7"><i class="fa fa-pencil"></i> Super Graphiste</li>\
				<li id="ba11" class="bsu groupe-8"><i class="fa fa-laptop"></i> Citoyen</li>\
				<li id="ba12" class="bsu groupe-8"><i class="fa fa-laptop"></i> Intervenant</li>\
				<li id="ba23" class="bsu groupe-6"><i class="fa fa-venus"></i> Ca chauffe</li>\
				<li id="ba24" class="bsu groupe-6"><i class="fa fa-venus"></i> En feu</li>\
				<li id="ba15" class="bsu groupe-9"><i class="fa fa-venus"></i> Helper</li>\
				<li id="ba16" class="bsu groupe-9"><i class="fa fa-"></i> Oeil de chat</li>\
				<li id="ba19" class="bsu groupe-10"><i class="fa fa-laptop"></i> Dépanneur</li>\
				<li id="ba20" class="bsu groupe-10"><i class="fa fa-laptop"></i> Bidouiller</li>\
				</ul>\
				</div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max 1200px
	if(parseInt(sizecal) > 400) var sizecal = 400;

	addbdg.dialog({
	width:sizecal,
	modal:false,
	resizable: true,
	title:'Ajouter un badge',
	show:{effect:'scale', duration: 320},

	});
});

//On click badge | Add
$(document).off('click', '.bsu');
$(document).on('click', '.bsu', function(e) {

	//Get the badge ID, remove the first 2 chars as we want only the num value | get the target userid
	gbid = $(e.target).attr('id').slice(2);
	uid = $('#suid').attr('data-idus');

	$.ajax({
	type: 'POST',
	url: '../php/getter.php',
	data:'addbadge='+ encodeURIComponent(gbid) +'&uid='+ uid
		}).done(function(msg) {

				if(msg === 'ok') {
					$('#addbdg').dialog('close');
					alert('Badge ajouté avec succès');
					location.reload();
				}

				else
					alert('Une erreur est survenue');
		});
});

//On click badge | Remove
$(document).off('click', '.brm');
$(document).on('click', '.brm', function(e) {

	//Get the badge ID, remove the first 2 chars as we want only the num value | get the target userid
	gbid = $(e.target).attr('id').slice(2);
	uid = $('#suid').attr('data-idus');

	$.ajax({
	type: 'POST',
	url: '../php/getter.php',
	data:'rmbadge='+ encodeURIComponent(gbid) +'&uid='+ uid
		}).done(function(msg) {

				if(msg === 'ok') {
					alert('Badge retiré avec succès');
					location.reload();
				}

				else if(msg === 'only')
					alert('2 badges minimum pour effectuer un retrait');

				else
					alert('Une erreur est survenue');
		});
});

$(document).on('click', '.smore', function() {
	$('.more').show();
	$('.smore').hide();
});

//Cleantools
$(document).off('click', '#srctool');
$(document).on('click', '#srctool', function(e) {

	//Empty ctn
	$('#admback').empty();

	ctntool = '<input type="text" id=lnkpanth placeholder="ID topic pantheon">\
			  <p id=sndlnk>Confirmer</p>';

	$(ctntool).appendTo('#admback');

});

$(document).off('click', '#sndlnk');
$(document).on('click', '#sndlnk', function(e) {

	if(/^\d*(\.\d{0,2})?$/.test($('#lnkpanth').val())) {

		lnkpa = $('#lnkpanth').val();

		spawnLoad('load');

		$.ajax({
			type: 'POST',
			url: '../php/getPost.php',
			data:'lnkpa='+ lnkpa,
			dataType: 'json'
		}).done(function(data) {
			
			console.log(data);
			
			if(data === null) {
				alert('error');
				return;
			}

			atic = 0;
			wtic = 0;

			$('#admback').append('<br>');

			$.each(data, function(idx, value) {

				if(data[idx] === '01\n') {
					$('#admback').append('<span> '+ (parseInt(atic) + 1) +' </span> - <span style="color:red"> Syntaxe incorrect</span><br>');
					wtic++;
				}

				else if(data[idx] === "02\n") {
					$('#admback').append('<span> '+ (parseInt(atic) + 1) +' </span> - <span style="color:red"> Topic épinglé interdit</span><br>');
					wtic++;
				}

				else if(data[idx] === "03\n") {
						$('#admback').append('<span> '+ (parseInt(atic) + 1) +' </span> - <span style="color:red"> Section non classé upload</span><br>');
						wtic++;
					}

				else if(data[idx] === "04\n") {
					$('#admback').append('<span> '+ (parseInt(atic) + 1) +' </span> - <span style="color:red"> Topic supprimé</span><br>');
					wtic++;
				}

				else
					$('#admback').append('<span>'+ (parseInt(atic) + 1) +'</span>'+ data[idx] +'<br>');

				atic++;
			});

		}).promise().done(function() {

				totaldel = parseInt(atic) - parseInt(wtic);

				ctnstatus = '<div id=status>\
								<p class=glink>'+ totaldel +' topics prêt à supprimer</p>\
								<p class=blink>'+ wtic +' lien(s) sont incorrect</p>\
								<p class=cdel>Confirmer la suppression</p>\
							</div>';

				$('#admback').prepend(ctnstatus);
				spawnLoad('kill');

			});

	}

	else
		$('#lnkpanth').val('').attr('placeholder', 'Erreur, N\'insérez que l\'id du topic panthéon à extraire');
});

$(document).off('click', '.cdel');
$(document).on('click', '.cdel', function() {

	rmlnk = $('.rmlnk');
	spawnLoad('load');

	rmlnk.each(function() {

		$.ajax({
			type: 'POST',
			url: '../php/getter.php',
			data:'cltool='+ this.id
		}).done(function(data) {

			if(data != false)
				$('#'+ data).replaceWith('[Supprimé avec succès]');
			else
				$('#'+ data).css('color', 'red');

		});
	});
	spawnLoad('kill');
});
