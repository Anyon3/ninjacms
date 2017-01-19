//Timer
timer = false;

//Avoid multiple firing reply, vote, login
sl = true;

//Avoid multiple firing faq container
prevdb = true;

$.ajax({
type:'POST',
url:'php/getter.php',
 data:'js=true'
})

//Clean the timer if still 'running', even on popstate
window.onpopstate = function(event) {
	//Do not allow scroll event (search)
	trigSc = true;
    clearTimeout(timer);
    spawnLoad('kill');
}

//Default placeholder input search
placeSearch = searchSel.attr('placeholder');

//Hide some div on load
central.hide();
scheme.hide();

/* Menu */

//Home
$(document).off('click', '#home, .backHome');
$(document).on('click', '#home, .backHome', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	      logoSwitch('ninja');

	//Cancel anchor (if any)
	e.preventDefault();

	//Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'GET',
        url: 'php/home.php'
    }).done(function(msg) {

    	if (data === undefined) history.pushState({}, null, '/');

        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

			//PopState replace
            if(data === undefined) history.replaceState({}, null, '/');

			//Change title (browser)
            document.title = 'Wawa-Mania - Téléchargement direct';

          //Show shortLink
			if(shortLink.is(':hidden'))
				shortLink.show();

			//Do not allow scroll event (search)
			trigSc = true;
            spawnLoad('kill');

        });

    });

});

//Login
$(document).off('click', '#login');
$(document).on('click', '#login', function(e, data) {

  //Check if the lock is set or no (avoid double spawn captcha)
  if(!sl)
	  return false;

  //Set on the lock for reject any other firing until the captcha get full load
  sl = false;

  if($('.logoNinja').hasClass('ddg'))
      logoSwitch('ninja');

	//Cancel anchor (if any)
	e.preventDefault();

	//Clean/hide previous container
	clean('hidden');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'GET',
        url: 'php/login.php'
    }).done(function(msg) {

	if(data === undefined) history.pushState({}, null, 'login');

        resultDb.html(msg);
        $('	<p class=waitcaptcha><i class="fa fa-cog fa-spin fa-2x"></i> Chargement</p>').insertAfter('.loginp');

        central.fadeIn().promise().done(function() {

			//PopState replace
            if(data === undefined) history.replaceState({}, null, 'login');
			//Change title (browser)
            document.title = 'Connexion - Identifiez-vous';

            //Get random captcha and insert it
            $.ajax({
            type:'POST',
            data:'randomcc=gen',
            url:'php/getter.php'
            }).done(function(msg) {

            	//Remove loader captcha
            	$('.waitcaptcha').remove();

            	//Spawn the security question
            	$(msg).insertAfter('.loginp').promise().done(function() {
	            	//Release the lock
	            	sl = true;
            	});
            });

			//Do not allow scroll event (search)
			trigSc = true;
            spawnLoad('kill');
        });
    });
});

//Faq
$(document).off('click', '.faq');
$(document).on('click', '.faq', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	      logoSwitch('ninja');

    	//Do not allow scroll event (search)
	trigSc = true;

	//Cancel anchor (if any)
	e.preventDefault();

	//Do not redirect if contain other class
	if($(e.target).hasClass('donation')) return;

	if($('.faq').hasClass('fload')) return;

	//Clean/hide previous container
	clean('hidden');

	spawnLoad('load'); //Start the display loading
	if(data === undefined) history.pushState({}, null, 'faq');

	$.get('html/faq.html', function(data) {

	    	resultDb.html(data);
		if(data === undefined) history.replaceState({}, null, 'faq')
		document.title = 'Foire aux questions - Aide à l\'utilisation de Wawa-Mania';
		central.fadeIn();

		$('.faq dd').hide();
		$('.faq').addClass('fload');
		$('.nop').hide();

		spawnLoad('kill');
	});

});

$(document).off('click', '#sms, #phone, #cash');
$(document).on('click', '#sms, #phone, #cash', function() {

	if($(this).next('div').hasClass('nop')) {
		$(this).next('div').removeClass('nop');
		$(this).next('div').show();
	}

	else {
		$(this).next('div').addClass('nop');
		$(this).next('div').hide();
	}
});

//Redirect link of FAQ
$(document).off('click', '.falink');
$(document).on('click', '.falink', function(e) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

	//Cancel anchor (if any)
	e.preventDefault();

	link = $(e.target).attr('href');

	window.location.href = link;
});

//Open or hide selected question on FAQ
$(document).off('click', '.faq dt');
$(document).on('click', '.faq dt', function(e) {

	//Cancel anchor (if any)
	e.preventDefault();

	if(prevdb === false) return;

	if($(e.target).next().hasClass('ldown')) {
		prevdb = false;
		$(e.target).next().slideUp().removeClass('ldown').promise().done(function() {
		prevdb = true;
		});
	}

	else {
		prevdb = false;
		$(e.target).next().slideDown().addClass('ldown').promise().done(function() {
		prevdb = true;
		});
	}
});

//Lost
$(document).off('click', '.lost');
$(document).on('click', '.lost', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

	//Cancel anchor (if any)
	e.preventDefault();

	//Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'GET',
        url: 'php/lost.php'
    }).done(function(msg) {

	if (data === undefined) history.pushState({}, null, 'lost');

        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

			//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();

			//PopState replace
            if(data === undefined)
            	history.replaceState({}, null, 'lost');

			//Change title (browser)
            document.title = 'Mot de passe perdu';

			//Do not allow scroll event (search)
			trigSc = true;
            spawnLoad('kill');
        });
    });
});

//disable
$(document).off('click', '.disable');
$(document).on('click', '.disable', function(e, data) {

	//Cancel anchor (if any)
	e.preventDefault();

	//Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'GET',
        url: 'html/disable.html'
    }).done(function(msg) {

	if (data === undefined) history.pushState({}, null, 'disable');

        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();

			//PopState replace
            if(data === undefined)
            	history.replaceState({}, null, 'disable');

			//Change title (browser)
            document.title = 'Section supprimée';

			//Do not allow scroll event (search)
			trigSc = true;
            spawnLoad('kill');
        });
    });
});

//Register
$(document).off('click', '.register');
$(document).on('click', '.register', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

	//Cancel anchor (if any)
	e.preventDefault();

	//Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'GET',
        url: 'php/register.php'
    }).done(function(msg) {

	if (data === undefined) history.pushState({}, null, 'register');

        resultDb.html(msg);
        //Spawn loader waiting captcha
        $('	<p class=waitcaptcha><i class="fa fa-cog fa-spin fa-2x"></i> Chargement</p>').insertAfter('#pwreg');

        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();

			//PopState replace
            if(data === undefined)
            	history.replaceState({}, null, 'register');

			//Change title (browser)
            document.title = 'Créer un compte';

            	//Get random captcha and insert it
	            $.ajax({
	            type:'POST',
	            data:'randomcc=gen',
	            url:'php/getter.php'
	            }).done(function(msg) {
	            	$('.waitcaptcha').remove(); //Remove the loader
	            	$(msg).insertAfter('#pwreg'); //Spawn the security question on the DOM
	            });

			//Do not allow scroll event (search)
			trigSc = true;
            spawnLoad('kill');
        });

    });
});

//Search
$(document).off('click', '#search');
$(document).on('click', '#search', function(e, data) {

	logoSwitch('ddg');

	if(sl === false) return;

	sl = false;

	//Cancel anchor (if any)
	e.preventDefault();

	//Push history (popstate)
    if (data === undefined)  {
    	history.pushState({}, null, 'search');
    	clean('hidden');
    }

    else {
    	inf = data.split('-');
    	central.fadeIn();
    }

	spawnLoad('load'); //Start the display loading

	$.get('html/search.html', function(msg) {

		$(msg).appendTo('header');

		hSearch = cacheSel('#hSearch');

		hSearch.show();

		if(data === undefined)
			history.replaceState({}, null, 'search');
		else {
			optSearch = cacheSel('#optSearch');
    		optSearch.attr('data-filter', inf[1]);
		}

		document.title = 'Recherchez un fichier';

		trigSc = true;

		spawnLoad('kill');
		sl = true;
	});
});


$('.scheme > li').on('click', function() {

	var trigScheme = $(this).attr('class').split(' ')[0];
    var scheme = (trigScheme === 'dark') ? 'dark' : 'blue';


        if (scheme === 'dark') {

			body.css('background-color', '#2F2F2F');
			head.append('<link rel="stylesheet" href="css/dark.css?v=1.160" type="text/css">');
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

/* End menu */

/*
   ################### START ###################
   ################### sub   ###################
   ###################	     ###################
*/

//Go to the target section
$(document).off('click', '.lefthome, .backSub');
$(document).on('click', '.lefthome, .backSub', function(e, data) {

    trigSc = true;

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

    //Cancel anchor (if any)
    if(e.target.tagName.toLowerCase() === 'a')
	e.preventDefault();

    var fid = ((data === undefined) ? $(this).attr('id') : data);

	//Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'POST',
        url: 'php/sub.php',
        data: 'fid=' + fid
    }).done(function(msg) {

	if (data === undefined)
	    history.pushState({}, null, 'sub-' + fid);

        resultDb.html(msg);

        central.fadeIn().promise().done(function() {

        //Show shortLink
	if(shortLink.is(':hidden'))
	    shortLink.show();

        //Change title and push history
        if (data === undefined)
            history.replaceState({}, null, 'sub-' + fid);

       if(parseInt(fid) === 6)
	   titleTohtml = 'Séries Tv - Les derniers épisodes de vos show favoris';
       else if(parseInt(fid) === 27)
	   titleTohtml = 'E-book en vrac';
       else if(parseInt(fid) === 45)
	   titleTohtml = 'Exclue - Les films du moment';
       else if(parseInt(fid) === 5)
	   titleTohtml = 'Films DVDrip - Qualité dvdrip en lien http/ftp';
       else if(parseInt(fid) === 58)
	   titleTohtml = 'Dessins animés / Animés / Mangas';
       else
	   titleTohtml = $('.labelsub > h1').text();

       document.title = titleTohtml;

       //Change some element depends on the lvl
       bid = aream.attr('data-jslvl');
       displayperm(bid, fid);

       spawnLoad('kill');

        });
    });
});

/* Section (aka sub or subCat) */

//Preview - Get first post or last post (preview post)
$(document).off('click', '.preview');
$(document).on('click', '.preview', function() {

    var to_id = $(this).attr('id');

    spawnLoad('load');

    $.ajax({
        type: 'POST',
        url: 'php/getPost.php',
        data: 'to_id=' + to_id
    }).done(function(msg) {

        //Recursive bbQuote & convert plain url
        msg = quoteRecursive(msg);
        msg = linkify(msg);

		var prevTag = '\
					<div class="containerPreview">\
						<span class="closePrev fa-stack fa-lg">\
							<i class="fa fa-circle fa-stack-2x"></i>\
							<i class="fa closePrev fa fa-times  fa-stack-1x fa-inverse"></i>\
						</span>\
						<div class="messagePreview">'+ msg +'</p>\
					</div>';

		body.append(prevTag).promise().done(function() {
			$('.containerPreview').fadeIn();
		});

        spawnLoad('kill');
    });
});

//Section
//Next & Prev hand trigger with offset
$(document).off('click', '.nextSub, .prevSub');
$(document).on('click', '.nextSub, .prevSub', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

	//Cancel anchor
	e.preventDefault();

    //get Offsc sub and fid
    var setSub = ((data === undefined) ? $(this).attr('data-idn').split('-') : data.split('-'));
    var fid = setSub[0];
    var roff = setSub[1] % 50;
    offset = setSub[1] - roff;

    //Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'POST',
        url: 'php/sub.php',
        data: 'fid=' + fid + '&offset=' + offset
    }).done(function(msg) {

        if (data === undefined) history.pushState({}, null, 'sub-' + fid + '-' + offset);

        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();

            history.replaceState({}, null, 'sub-' + fid + '-' + offset);

			//Change some element depends on the lvl
		    bid = aream.attr('data-jslvl');
		    displayperm(bid, fid);

			//Do not allow scroll event (search)
			trigSc = true;
            spawnLoad('kill');
        });
    });
});

//Select page (section)
$(document).off('change', '.pageSub');
$(document).on('change', '.pageSub', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

	//Cancel anchor
	e.preventDefault();

    if ($(this).attr('name') !== 'sub') return false;

    var fid = ((data === undefined) ? $(this).attr('data-idt') : data.split('-')[0]);
    var offset = ((data === undefined) ? $(this).val() : data.split('-')[1]);

    var roff = offset % 50;
    offset = offset - roff;

	//Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'POST',
        url: 'php/sub.php',
        data: 'fid=' + fid + '&offset=' + offset
    }).done(function(msg) {

        if (data === undefined) history.pushState({}, null, 'sub-' + fid + '-' + offset);

        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();

            //Change title and push history
            if (data === undefined) history.replaceState({}, null, 'sub-' + fid + '-' + offset);

			//Change some element depends on the lvl
		    bid = aream.attr('data-jslvl');
		    displayperm(bid, fid);

			//Do not allow scroll event (search)
			trigSc = true;
            spawnLoad('kill');
        });
    });
});

//Go topic
$(document).off('click', '.viewtopic');
$(document).on('click', '.viewtopic', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

	//Prevent anchor
	e.preventDefault();

	//Do not allow scroll event (search)
	trigSc = true;

	//Check if the link have page selection
	if(data === undefined)

		if($(this).attr('id').split('-')[2] === undefined) {
			var tid =  $(this).attr('id').split('-')[1];
			var hstate = 'topic-'+ tid;
			var dataajax = 'tid='+ tid;
		}

		else {
			var tid =  $(this).attr('id').split('-')[1];
			var offset =   $(this).attr('id').split('-')[2];
			var hstate = 'topic-'+ tid +'-'+ offset;
			var dataajax = 'tid='+ tid +'&offset='+ offset;
		}

	else {
		//Get topic ID
		var tid =  data;
		var hstate = 'topic-'+ tid;
		var dataajax = 'tid='+ tid;
	}

    //Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
    type: 'POST',
    url: 'php/topic.php',
    data:dataajax
    }).done(function(msg) {

        //Recursive bbQuote & convert plain url & avoid bbcode hidden container to be linkify
        msg = quoteRecursive(msg);
        msg = rttp(msg);
        msg = linkify(msg);

        //Display
        if (data === undefined) history.pushState({}, null, hstate);
        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();

            //Change title and push history
            if (data === undefined) history.replaceState({}, null,  hstate);
            var titleUpdate = $('.titleTohtml').text();
            document.title = titleUpdate;

			//Fetch each edit button, check if must be display or no
			$('.editpa').each(function() {
			if(aream.attr('id').slice(2) != $(this).attr('id').slice(2) && aream.attr('id') != 'adm') $(this).hide();
			});

			//Set the text for close button information
			$('#closed').empty().text('Fermé');
            spawnLoad('kill');
        });

	});

});

//Form ask
$(document).off('click', '#ask');
$(document).on('click', '#ask', function() {

	if(modalAsk.length > 0)
		modalAsk.remove();

	body.append('<div id=ctnask>\
					<ul>\
						<li><input type="text" class=asktitle placeholder="Titre du fichier"></li>\
						<select class=askwh>\
							<option value="Film">Film</option>\
							<option value="Musique">Musique</option>\
							<option value="Ebook">E-book</option>\
							<option value="Jeux-Vidéo">Jeux vidéo</option>\
							<option value="Série-TV">Série TV</option>\
							<option value="Logiciel">Logiciel</option>\
			                <option value="Os">Système D\'exploitation</option>\
							<option value="app-smartphone">App Smartphone</option>\
							<option value="Autre">Autre</option>\
						</select>\
						<li><input type="text" class=askext placeholder="Format désiré (mp3, rar, avi...)"></li>\
						<li><input type="text" class=askwhere placeholder="Hébergeur désiré (uplea, 1fichier...)"></li>\
						<textarea class=askcom placeholder="Message supplémentaire (je cherche ce jeu sur mac...)"></textarea>\
					</ul>\
				</div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	if(parseInt(sizecal) > 500)
		var sizecal = 500;

	modalAsk = cacheSel('#ctnask');

	modalAsk.dialog({
		width:sizecal,
		resizable: false,
		draggable: false,
		title: 'Formulaire de demande',
		position: { my:'right top', at:'right top', of:'#container_to' },
					show: {effect:'blind', duration: 320},
					hide: {effect:'blind', duration: 320},
		buttons:[{
			text:'Confirmer',
			click: function(e, data) {

					//Set all value field
					var title = $('.asktitle').val();
					var wtype = $('.askwh').val();
					var wext = $('.askext').val();
					var whost = $('.askwhere').val();
					var sup = $('.askcom').val();

					//If title not empty then
					if(title !== undefined && title !== '')
						$('#title_to > input').val('['+ wtype +'] '+ title);

					//Build for each field not empty the bbcode
					stype = '[b]Type de fichier[/b] :' + ((wtype !== undefined && wtype !== '') ?  wtype + '\r' : '\r');
					sext = '[b]Format[/b] :' + ((wext !== undefined && wext !== '') ?  wext + '\r' : '\r');
					shost = '[b]Hébergeur(s)[/b] :' + ((whost !== undefined && whost !== '') ? whost + '\r' : '\r');
					ssup = '[b]Exra[/b] :' + ((sup !== undefined && sup !== '') ? sup + '\r' : '\r');

					$('#message_to').val(stype + sext + shost + ssup);

					modalAsk.hide('blind').promise().done(function() {
						modalAsk.remove();
					});
			}
		}]
	});
});



//Next & Prev hand
$(document).off('click', '.nextTop, .prevTop');
$(document).on('click', '.nextTop, .prevTop', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

	//Prevent anchor
	e.preventDefault();

    //get Offset sub and fid
    var setTop = ((data === undefined) ? $(this).attr('id').split('-') : data.split('-'));
    var tid = setTop[0];
    var offset = setTop[1];

    //Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'POST',
        url: 'php/topic.php',
        data: 'tid=' + tid + '&offset=' + offset
    }).done(function(msg) {

        //Recursive bbQuote & convert plain url
        msg = quoteRecursive(msg);
        msg = linkify(msg);
		msg = rttp(msg);

		offset = (offset < 1) ? 1 : offset;

        //Display
        if (data === undefined) history.pushState({}, null, 'topic-' + tid + '-' + offset);

        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();

            //Change title and push history
            if (data === undefined) history.replaceState({}, null, 'topic-' + tid + '-' + offset);
            var titleUpdate = $('.titleTohtml').text();
            document.title = titleUpdate;

			//Fetch each edit button, check if must be display or no
			$('.editpa').each(function() {
			if(aream.attr('id').slice(2) != $(this).attr('id').slice(2) && aream.attr('id') != 'adm') $(this).hide();
			});

			//Set the text for close button information
			$('#closed').empty().text('Fermé');

			//Do not allow scroll event (search)
			trigSc = true;
            spawnLoad('kill');
        });
    });
});

//Select page select (topic)
$(document).off('change', '.pageTop');
$(document).on('change', '.pageTop', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

    if ($(this).attr('name') !== 'top') return false;

	//Dom or Popstate (data from pop)
    var tid = ((data === undefined) ?
		$(this).attr('id') : data.split('-')[0]);

	//Dom or Popstate (data from pop)
    var offset = ((data === undefined) ?
		$(this).val() : data.split('-')[1]);

	//Keep container_post if spawn
	replyspawn = false;
	if($('#container_post').length > 0) {
	nowq = $('#message_post').val();
	replyspawn = true;
	}

	//Clean/hide previous container
	clean('visible');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'POST',
        url: 'php/topic.php',
        data: 'tid=' + tid + '&offset=' + offset
    }).done(function(msg) {

        //Recursive bbQuote & convert plain url
        msg = quoteRecursive(msg);
        msg = linkify(msg);
		msg = rttp(msg);

		offt = (offset < 1) ? 1 : offset;

        //Display
        if (data === undefined) history.pushState({}, null, 'topic-' + tid + '-' + offt);
        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();


		//Change title and push history
        if (data === undefined) history.replaceState({}, null, 'topic-' + tid + '-' + offt);
        var titleUpdate = $('.titleTohtml').text();
        document.title = titleUpdate;

			//Spawn the container reply if requiered, refresh some selector (else won't work)
			if(replyspawn === true) {

			//Simulate click #reply
			$('#reply').trigger('click').promise().done(function() {
				//Add to the textarea (wait 1,8 seconds make sure #message_post is spawn)
				setTimeout(function(){
				$('#message_post').val(nowq);
				}, 1800);
			});

			}

			//Fetch each edit button, check if must be display or no
			$('.editpa').each(function() {
			if(aream.attr('id').slice(2) != $(this).attr('id').slice(2) && aream.attr('id') != 'adm') $(this).hide();
			});

			//Set the text for close button information
			$('#closed').empty().text('Fermé');

				//Scroll to target post (set in URL)
				timer = setTimeout(function() {

				npclass = $('a:contains("#'+ offt +'")').attr('class');

				var container = $("html,body");
				scrollTo = $('.'+ npclass);

				container.animate({scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()});

				//Do not allow scroll event (search)
				trigSc = true;
				spawnLoad('kill');

				}, 1800);
        });
    });
});

//Go to the lastPost
$(document).off('click', '.pidPost');
$(document).on('click', '.pidPost', function(e, data) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

	e.preventDefault();

	if(data === undefined)
		pid = $(this).attr('id');
	else
		pid = data;

	clean('visible'); 	//Clean/hide previous container
	spawnLoad('load'); //Start the display loading

    $.ajax({
    type: 'POST',
    url: 'php/topic.php',
    data:'pid='+ pid
    }).done(function(msg) {

        //Recursive bbQuote & convert plain url
        msg = quoteRecursive(msg);
        msg = linkify(msg);
		msg = rttp(msg);

        //push history -
        if (data === undefined) history.pushState({}, null, 'pid-'+ pid);

        resultDb.html(msg);
        central.show().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();

            //Change title and push history
            if(data === undefined) history.replaceState({}, null, 'pid-'+ pid);
            var titleUpdate = $('.titleTohtml').text();
            document.title = titleUpdate;

            //Scroll to last post, wait with timer for the load
            timer = setTimeout(function() {

    			var container = $("html,body");
    			    scrollTo = $('.p'+ pid);

    			container.animate({scrollTop: scrollTo.offset().top - container.offset().top, scrollLeft: 0},300);

    			//Do not allow scroll event (search)
    			spawnLoad('kill');

            }, 1800);

            //Fetch each edit button, check if must be display or no
    		$('.editpa').each(function() {
    		if(aream.attr('id').slice(2) != $(this).attr('id').slice(2) && aream.attr('id') != 'adm') $(this).hide();
    		});

    		//Set the text for close button information
    		$('#closed').empty().text('Fermé');

        });

    });

});

/* Search */

// Select/define options - By author or By keyword(s)
$(document).off('click', '#optSearch i');
$(document).on('click', '#optSearch i', function(e) {

    var getFilter = $(e.target).attr('class').split(' ')[2];

    $(e.target).parent().attr('data-filter', getFilter);

    $('#optSearch i').each(function() {
		var temp = $(this).attr('class').split(' ')[2];
		if(temp != getFilter)
			$(this).removeClass().addClass('fa fa-circle ' + temp);
    });

    $(e.target).removeClass().addClass('fa fa-check-circle ' + getFilter);
});

// Select/define options - By last message or last new topic
$(document).off('click', '#optBy i');
$(document).on('click', '#optBy i', function(e) {

    var getBy = $(e.target).attr('class').split(' ')[2];

    $(e.target).parent().attr('data-by', getBy);

    $('#optBy i').each(function() {
		var temp = $(this).attr('class').split(' ')[2];
		if(temp != getBy)
			$(this).removeClass().addClass('fa fa-square-o ' + temp);
    });

    $(e.target).removeClass().addClass('fa fa-check-square-o ' + getBy);
});

// Select/define options - ASC / DESC
$(document).off('click', '#optSort i');
$(document).on('click', '#optSort i', function(e) {

    var getSort = $(e.target).attr('class').split(' ')[2];

    $(e.target).parent().attr('data-sort', getSort);

    $('#optSort i').each(function() {
		var temp = $(this).attr('class').split(' ')[2];
		if(temp != getSort)
			$(this).removeClass().addClass('fa fa-square-o ' + temp);
	});

	$(e.target).removeClass().addClass('fa fa-check-square-o ' + getSort);
});

//Different display for small device (* < 1000px)
$(window).width(function() {
    if ($($(document)).width() < 1000) $('#optctn fieldset i').hide();
});

$(document).on('click', '#optctn > fieldset > legend', function() {

    //Exit if screen greater than 1000px
    if ($($(document)).width() > 1000) return false;

	//Show or hide
    ($(this).next().is(':visible') ? $(this).nextAll().slideUp() : $(this).nextAll().slideDown());
});

//Search by author (automatic by url match & search by user 'Modal jUi')
$(document).off('click', '.author');
$(document).on('click', '.author', function(e, data) {

    $('.logoNinja').attr('src','').promise().done(function() {
	$(this).attr('src','svg/ddg.svg').addClass('ddg').attr('data-title','Sur Duckduckgo, recherche directement un post sur le forum en utilisant le bang !wawa');

    });

	//Cache selector
	trigPop = cacheSel('#trigPop');

	//On match url on load
	//On search 'Show recent activity' of the target user
	if(data === undefined) {
		ausername = $('.author.uAuthor').children('i').attr('data-user');
	    inf = ['author',ausername,'all','0'];
		resultUser.dialog('close');
		resultUser.dialog('destroy');
		resultUser.remove();
	}

	else  inf =  data.split('-');

	//Clean/hide previous container
	clean('hidden');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'POST',
        url: 'php/search.php',
        data: 'search='+ inf[1] +'&sub='+ inf[2] +'&startfrom='+ inf[3] +'&filter=poster&by=to_last_post_id&sort=desc'
    }).done(function(msg) {

		//Push to history
    	if(!popped) history.pushState({}, null, 'author-'+ inf[1] +'-'+ inf[2] +'-'+ inf[3]);

        //Recursive bbQuote & convert plain url
        msg = quoteRecursive(msg);
        msg = linkify(msg);

        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();

			//If !popped (can't detect by data attribute trigger here
			if(!popped) history.replaceState({}, null, 'author-'+ inf[1] +'-'+ inf[2] +'-'+ inf[3]);
            spawnLoad('kill');

            //Update arg search
        	optSearch = cacheSel('#optSearch');
        	optBy = cacheSel('#optBy');
        	optSearch.attr('data-filter', 'poster');
        	optBy.attr('data-by', 'to_last_post_id');

			//Wrote the new result startfrom
			numItems = trigPop.attr('data-nrow');
			trigPop.attr('data-nrow', parseInt(inf[3]) + 20);
			trigPop.attr('data-sub', inf[2]);
		})

    }).promise().done(function() {

        // Load script after getting the new class/id in the DOM
        $.getScript('js/getbody.js', function() {

			if($('#hSearch').length <= 0) {
				search = cacheSel('#search');
				search.trigger('click', ['bypass-author']);
			}

			//Release the lock scroll (need a short moment  for avoid multiple firing)
			setTimeout(function(){
				trigSc = false;
			}, 2000);

		}); //End $.get

    }); //End callback  ajax

}); //End .author

//Search by recent (automatic by url match)
$(document).off('click', '.recent');
$(document).on('click', '.recent', function(e, data) {

	logoSwitch('ddg');

	//Cancel anchor (if any)
	e.preventDefault();

	trigPop = cacheSel('#trigPop');

	if(trigPop.hasClass('lck'))
		return;

	trigPop.addClass('lck');

	//Which target section
	if(data === 'recent')
		var sec = 'all';
	else if(data === 'detente')
		var sec = '4';
	else if(data === 'informatique')
		var sec = '57';
	else if(data === 'jeuxvideo')
		var sec = '60';
	else if(data === 'mac')
		var sec = '59';
	else if(data === 'tuto')
		var sec = '29';
	else if(data === 'films')
		var sec = '45';
	else if(data === 'seriestv')
		var sec = '6';
	else
		var sec = 'all';

	//Clean/hide previous container
	clean('hidden');

	$.get('html/search.html', function(msg) {

		$(msg).appendTo('header');

		hSearch = cacheSel('#hSearch');

		if(hSearch.not(':visible'))
			hSearch.show();

		//Update used opt
		optSearch = cacheSel('#optSearch');
		optBy 	  = cacheSel('#optBy');

        optSearch.attr('data-filter', 'subject');
        optBy.attr('data-by', 'to_id');

	});

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'POST',
        url: 'php/search.php',
        data: 'search=&sub='+ sec +'&startfrom=0&filter=subject&by=to_id&sort=desc'
    }).done(function(msg) {

        //Push to history
        history.pushState({}, null, 'search--'+ sec +'-0');

        //Recursive bbQuote & convert plain url
        msg = quoteRecursive(msg);
        msg = linkify(msg);

        resultDb.html(msg);

        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':visible'))
				shortLink.hide();

			history.replaceState({}, null, 'search--'+ sec +'-0');

			trigPop.attr('data-nrow', 20);

			//Insert in the DOM the target sub
	    	trigPop.attr('data-sub',sec);

            spawnLoad('kill');

            $.getScript('js/getbody.js', function() {});

          //Release the lock scroll (need a short moment  for avoid multiple firing)
			setTimeout(function(){

				trigPop.removeClass('lck');
				trigSc = false;

			}, 2000);

        }); //End show central

    }); //End callback for ajax

});

//Search by keywords (user defined)
var startSearch = function(e, data) {

    logoSwitch('ddg');

    if(data === undefined)
    	searchSel = cacheSel('#searchSel');

    //Get the result startfrom
    trigPop = cacheSel('#trigPop');
    numItems = ((data === undefined) ? 0 : trigPop.attr('data-nrow'));

    //Split inf from data
    var inf = ((data === undefined) ? 0 : data.split('-'));

    //Words
    var search = ((data === undefined) ? searchSel.val() : inf[1]);

    //Sub target
    var sub = ((data === undefined) ? $('#sub').val() : inf[2]);

    //offset
    var startfrom = ((data === undefined) ? numItems : inf[3]);


	//If the request come from the input search
	if(inf === '0') {
		//Exit on empty or k < 2 or k > 30 length
		if (search.length < 2 || search.length > 30) {
		coloredInput('#search', 'errorSe', 'Minimum 2 caractères, maximum 30');
		return false;
		}
	}

	//By another way (url match, lastopics, target user last topic...), replace search
	if(inf !== '0')
		if (search.length < 2 || search.length > 30) search = '';

	//Refresh caching
	optSearch = cacheSel('#optSearch');
	optBy 	  = cacheSel('#optBy');
	optSort   = cacheSel('#optSort');

    //Get opt
	if(data === undefined)
		var filter = (optSearch.attr('data-filter') !== 'poster') ? 'subject' : 'poster'; //by author or keyword
	else
		var filter = 'subject';

    var by       = (optBy.length <= 0) ? 'to_id' : optBy.attr('data-by'); //by post or by topic
    var sendSort = (optSort.length <= 0) ? 'desc' : optSort.attr('data-sort'); //ASC or DESC


	//For the rewrite
	pa = (filter === 'subject') ? 'search' : 'author';

    //Clean/hide previous container
	clean('cse');

	spawnLoad('load'); //Start the display loading
    $.ajax({
        type: 'POST',
        url: 'php/search.php',
        data: 'search=' + search + '&sub=' + sub + '&startfrom=' + parseInt(startfrom) + '&filter=' + filter + '&by=' + by + '&sort=' + sendSort
    }).done(function(msg) {

        //Push to history
        if (data === undefined) history.pushState({}, null, pa + '-' + search + '-' + sub + '-' + startfrom);

        //Recursive bbQuote & convert plain url
        msg = quoteRecursive(msg);
        msg = linkify(msg);

        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

        	//Show shortLink & announcement
			if(shortLink.is(':hidden'))
				shortLink.show();


            if (data === undefined) history.replaceState({}, null, pa + '-' + search + '-' + sub + '-' + startfrom);
			//Write new 'nrow' and 'sub'
			trigPop.attr('data-nrow', parseInt(startfrom) + 20);
			trigPop.attr('data-sub', sub);
        });

        // Load script after getting the new class/id in the DOM
        $.getScript('js/getbody.js', function() {

		//Allow scroll event (search)
		trigSc = false;
		spawnLoad('kill');
        });

		if($('#hSearch').length <= 0) {
			search = cacheSel('#search');
			search.trigger('click', ['bypass-subject']);
		}
    });
};

//On click subsearch (start a new search, with opt define by the user)
$(document).off('click', '.subSearch');
$(document).on('click', '.subSearch', startSearch);


$(document).keypress('.subSearch', function(e) {

    if (e.which == 13 && $('#searchSel').length > 0)
    	startSearch();
});

//Remove css add by Jquery (error...) on click input search
searchSel.on('click', function() {
    if (placeholderReset(this) === true) coloredInput(this, 'errorClean', placeSearch);
});

//Detect click outside container preview and close it
$(document).mouseup(function(e) {
    var container = $('.containerPreview');
	if(container.length === 0) return;
	if (!container.is(e.target) && container.has(e.target).length === 0) container.remove();
});

//Remove container preview on click close FA
$(document).on('click', '.closePrev', function() {
	$('.containerPreview').remove();
});

//On scroll, get 20 results more when scroll almost on footer
$(window).off('scroll');
$(window).on('scroll', function() {

	if(trigSc !== false) return;

    if ($(window).scrollTop() >= central.offset().top - 100 + resultDb.outerHeight() - window.innerHeight && trigSc === false) {

	trigPop   = cacheSel('#trigPop');
	nbrow	  = cacheSel('#nbrow');
	numItems  = trigPop.attr('data-nrow');
	cnrow = nbrow.children('p').text();

		//Check (more content)
        if (parseInt(numItems) >= 200 || parseInt(numItems) >= parseInt(cnrow)) {

            //No more result from sphinx
            if ($('#endresult').length === 0) {
			resultDb.append(endTag);

			//On click, go to the top of the window (scroll effect, going back to the top)
			$('#endresult').on('click', function() {
				$('html, body').animate({ scrollTop: 0 }, 'slow');
			});

			//Remove the bind scroll attached event
			$(window).unbind('scroll');
			return false;
            }
        }

        //If first firing, trigSc will be equal to false
        if (trigSc === false) {

			trigSc = true; //Set to true directly for avoid multiple firing
			spawnLoad('load'); //Show loading animation

            if (numItems > 0) {

			getmore = cacheSel('#getmore');

			var getSub = trigPop.attr('data-sub');
			var search = getmore.text();
			var filter   = (optSearch.attr('data-filter') === 'subject') ? 'subject' : 'poster';
			var by = (optBy.length <= 0) ? 'to_id' : optBy.attr('data-by');
			var sendSort = (optSort.length <= 0) ? 'desc' : optSort.attr('data-sort');

			//Ajax parameters
            var reqPost = 'search=' + encodeURI(search) + '&resu=n&startfrom=' + numItems + '&sub=' + getSub + '&filter=' + filter + '&by=' + by + '&sort=' + sendSort;

            	$.ajax({
				type: 'POST',
				url: 'php/search.php',
				data: reqPost
				}).done(function(msg) {

						//Search by author match or words
						var pa = ((filter === 'subject') ? 'search' : 'author');

						//Push
						history.pushState({}, null, pa + '-' + search + '-' + getSub + '-' + numItems);

						msg = quoteRecursive(msg)
						msg = linkify(msg);

						resultDb.append(msg);

						history.replaceState({}, null, pa + '-' + search + '-' + getSub + '-' + numItems);

						//Write the new 'nrow'
						trigPop.attr('data-nrow', parseInt(numItems) + 20);
						spawnLoad('kill');

						//ReLoad  new class/id in the DOM
						$.getScript('js/getbody.js', function() {});

					}).promise().done(function() {

						//Release the lock scroll (need a short moment  for avoid multiple firing)
						setTimeout(function(){
							trigSc = false;
						}, 2000);

					});


            }
        }
    }
});

/* End search */

/* Shortlink  */

//backUp (back to top)
backUp.on('click', function() {
$('html, body').animate({ scrollTop: 0 }, 'slow');
});

//Show stats : members, topics, posts (jUi modal)
wmStats.on('click', function() {
	//If the modal is not already open
	if(modalStats.dialog('isOpen') !== true) {
		$.ajax({
		type: 'POST',
		url: 'php/getter.php',
		data:'stats=1'
		}).done(function(msg) {
			body.append(msg).promise().done(function() {
				modalStats = cacheSel('#modalStats');
				modalStats.dialog({
					width:290,
					resizable: false,
					draggable: false,
					title: 'Stats du forum',
					position: { my:'right top', at:'right top', of:window },
					show: {effect:'blind', duration: 320},
					hide: {effect:'blind', duration: 320}
				});
			});
		});
	}
});

//Search user modal form (jUi modal)
$(document).off('click', '.look4user, lookTuser');
$(document).on('click', '.look4user, .lookTuser', function() {

	//Trig the display user info
	trigu = ($(this).attr('class') === 'look4user') ? '' : $(this).text();

	openw = (trigu === '') ? 400 : 800;

	//If the modal is not already open
	if(searchUser.dialog('isOpen') === true) return;

	//Remove any previous container if existing in DOM
	if(searchUser.length > 0) searchUser.remove();

	body.append('<div id="searchUser"><i class="inssu fa fa-search fa-lg"></i><input id="iptsu" type="text" placeholder="Utilisateur" data-holder="Utilisateur" /></div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max 1200px
	if(parseInt(sizecal) > 800) var sizecal = 800;

	searchUser = cacheSel('#searchUser');

	searchUser.dialog({
		width:sizecal,
		resizable: false,
		modal:false,
		title:'Rechercher un membre',
		dialogClass: 'seusModal',
		show:{effect:'scale', duration: openw},
		hide:{effect:'explode', duration: 500},
		buttons:[{
			//Start button send
			text:'Envoyer',
			class:'sendsu',
			click: function(e, data) {

				//Cache selector and add function clean input
				iptsu = cacheSel('#iptsu');

				//Get the value of the input
				user = (data === undefined) ? $(this).children('input').val() : data;

				//If less 3 chars
				if(user.length < 3) {
				iptsu.val("").addClass('fail').attr('placeholder','Incorrect').css('border-color','red').css('color','red').prev().css('color','red');
				return false;
				}

				//Fetch the information
				$.ajax({
				type: 'POST',
				url: 'php/getter.php',
				data: 'look4user='+ encodeURIComponent(user),
				dataType: 'json'
				}).done(function(json) {

				//Array null (no user found)
				if(json === null) {
				iptsu.val("").addClass('fail').attr('placeholder','Incorrect').css('border-color','red').css('color','red').prev().css('color','red');
				return false;
				}

				//Unshow contact method if empty or private
				if(json.us_icq_visible === 0 || json.us_icq === null) json.us_icq = 'non renseigné';
				if(json.us_jabber_visible === 0 || json.us_jabber === null) json.us_jabber = 'non renseigné';

				//Empty container and display result user
				searchUser.empty();

				//Display temporary loading, waiting the end timeout
				$('<p id="tempwa">Chargement en cours...</p>').appendTo(searchUser);

				$('.sendsu').hide(); //hide button send
				$('.backsu').show(); //Show button back

				//Explode badges string
				idbadge = json.us_badges.split('-');

					//Loop badges tag
					badges = '';
					$.getJSON('../cachejs/badges.json', function(data) {
						$.each(idbadge, function(idx, value) {
						badges = badges + '<li id="ba'+ value +'" class="bsu groupe-'+ data[value].groupe +'" data-title="<span class=substy>'+ data[value].subtitle +'</span><br>'+ data[value].description +'"><i class="fa fa-'+ data[value].icon +'"></i> '+ data[value].name +'</li>';
						});//End each
					});

					//Container result of search user (need a little time for badges)
					setTimeout(function(){

					$('#tempwa').remove();

					//account not ban
					if(json.us_reason === "")

					$('<ul>\
						'+ ((json.us_avatar !== undefined && json.us_avatar !== '0') ? '<li class=jumpa><img onerror=\'this.style.display = "none"\' src="https://avatar.wawa-mania.ec/images/'+ json.us_avatar +'" class=avatar />\</li>' : '') +'\
						<li class=jump>Information</li>\
						<li><i class="fa fa-user fa-fw fa-lg"></i> '+ json.username +'</li>\
						<li><i class="fa fa-file-o fa-fw fa-lg"></i> '+ json.us_num_posts + ((json.us_num_posts >= 1) ? ' posts' : ' post') +'</li>\
						<li><i class="fa fa-calendar fa-fw fa-lg"></i> Inscrit le '+ timeConverter(json.us_registered) +'</li>\
						<li><i class="fa fa-circle fa-fw fa-lg"></i> '+ json.us_pts +' point(s)</li>\
						<li class=jump>Contact</li>\
						<li><i class="fa fa fa-commenting-o fa-fw fa-lg" data-title="ICQ"></i> '+ json.us_icq +'</li>\
						<li><i class="fa fa-lightbulb-o fa-fw fa-lg" data-title="Jabber IM"></i> '+ json.us_jabber +'</li>\
						<li class=jump>Badge(s)</li>\
						'+ badges + '\
						<li class="author uAuthor"><i class="fa fa-list-alt fa-fw fa-lg" data-user="'+ json.username +'"></i> Afficher ses messages</li>\
						</ul>').appendTo(searchUser);

					 else $('<p>Utilisateur banni</p>').appendTo(searchUser);

					//Close dialog (will be redirect to search)
					$('.uAuthor').on('click', function() {
					searchUser.dialog('close');
					});

					}, 2000);

				});//End Ajax result

			}, //End trigger click send

			}, //End button send

			//Start button back
			{text:'Nouvelle recherche',
			 class:'backsu',
			 show:false,
			 click: function() {

				//Empty container and display the search form
				searchUser.empty();
				$('<i class="inssu fa fa-search fa-lg"></i><input id="iptsu" type="text" placeholder="Utilisateur" />').appendTo(searchUser);
				$('.backsu').hide(); //hide button send
				$('.sendsu').show(); //Show button send

				//Reload fun + cache selector (function + selector must be load again for work)
				iptsu = cacheSel('#iptsu');
				iptsu.on('click', function() {
				if(placeholderReset(this) === true) coloredInput(this, 'errorClean', 'Utilisateur');
				});
			 },
			},

		], //Close button

		//Hide the button new search on load dialogs (first time)
		open: function(){

			  $('.backsu').hide();

			  if(trigu !== '') {
				  $('.sendsu').trigger('click', [trigu]);
				  searchUser.empty();
			  }

		}

		}); //End dialog opt

}); //End modal look4user

//Top rank users
toprank.on('click', function() {

	//Return in case previous request aren't fully load
	if($('.loadrank').length > 0) return;

	//Remove any previous container if existing in DOM
	if(ctnrank.length > 0) ctnrank.remove();

	body.append('<div id=ctnrank>\
					<p class=loadrank>\
						<i class="fa fa-cog fa-spin fa-lg"></i> Chargement</p>\
					</p>\
				</div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max 1200px
	if(parseInt(sizecal) > 700) var sizecal = 700;

	ctnrank = cacheSel('#ctnrank');

	ctnrank.dialog({
		width:sizecal,
		resizable: true,
		modal:false,
		title:'Top 10 de Wawa-Mania',
		dialogClass: 'seusModal',
		position: { my: "center top", at: "center top", of: "#central" },
		show:{effect:'scale', duration: 300},
		hide:{effect:'explode', duration: 300}
		}).promise().done(function() {

		$.ajax({
		type:'POST',
		url:'php/getter.php',
		data:'toprank=1',
		dataType:'json'
		}).done(function(json) {
			$('.loadrank').remove();
			$.each(json, function(idx, value) {
			ctnrank.append('<p class="nickcolor-'+ json[idx][5] +'">'+ json[idx][1] +' '+ json[idx][3] +' points</p>');
			});

		});

	});

});
//End Top rank users

//Donate (loading main page)
$(document).off('click', '.donation');
$(document).on('click', '.donation', function(e) {

	//Cancel anchor (if any)
	e.preventDefault();
	trigSc = true;

	if($('.logoNinja').hasClass('ddg'))
	    logoSwitch('ninja');

	//Clean/hide previous container
	clean('hidden');

	spawnLoad('load'); //Start the display loading

    $.ajax({
        type: 'GET',
        url: 'php/donation.php'
    }).done(function(msg) {

        history.pushState({}, null, 'donation');

        resultDb.html(msg);
        central.fadeIn().promise().done(function() {

        	//PopState replace
            history.replaceState({}, null, 'donation');
            document.title = 'Donation à Wawa-Mania';
            spawnLoad('kill');
      });

    });

});

$(document).off('click', '.ctn-method');
$(document).on('click', '.ctn-method', function(e) {

	//Follow anchor webmoney
	if(this.id === 'ctn-donwebmoney')
		return;

	//Cancel anchor (if any)
	e.preventDefault();

	//Check the element of the target
	if($(e.target).is(':input') || $(e.target).hasClass('help') ||  $(e.target).hasClass('nohelp') || $(e.target).parent().attr('id') === 'doth')
		return;

	//If target element is not hide, hide it only
	if($(this).children('div').hasClass('showed')) {
		$('.showed').children('i').css('color','transparent');
		$('.showed').slideUp().removeClass('showed');
	}

	//Otherwise hide any other (if any) showed method and show the targeted one
	else {
		$('.showed').children('i').css('color','transparent');
		$('.showed').slideUp().removeClass('showed');
		$(this).children('div').addClass('showed').slideDown().promise().done(function() {
			$('.showed').children('i').css('color','#055698');
		});
	}
});

//Wire transfer
$(document).off('click','.bank');
$(document).on('click', '.bank', function(e) {

		//Prevent submit form
		e.preventDefault();

		if($(e.target).hasClass('help'))
			return

		//Check value of the field
		var amount = $('.mtbank').val();

		if(!amount.match(/^\d+$/) || amount > 100 || amount < 1) {
			$('.mtbank').val('').attr('placeholder','Incorrect (1 - 100)').css('color','#CE1126').addClass('er');
			$('.mtbank').prev().css('color','#CE1126');
			return;
		}

		//Loading
		spawnLoad('load');

		$.ajax({
		type:'POST',
		url:'php/getter.php',
		data:'system=trustpay&amount='+ encodeURIComponent(amount),
		dataType:'json'
		}).done(function(json) {

			//Remove loading
			spawnLoad('kill');

			//Get the reponse code
			reponse = json['result_code'];

			//if the answer from the API do not match with 200
			if(parseInt(reponse) !== 200) {
				$('.mtbank').val('').attr('placeholder','Error server').css('color','#CE1126').addClass('er');
				$('.mtbank').prev().css('color','#CE1126');
				return;
			}

			$('.mtbank').val('Succès, redirection').css('color','#34A853').addClass('er');
			$('.mtbank').prev().css('color','#34A853');

			//Get the redirect url
			url = json['redirect_url'];

			//Remove every espace chars (\)
			rurl = url.replace(/\\/, '');

			//Redirect to the donation (pay)
			window.location.href = rurl;

			});
});

//Phone
$(document).off('click','.phone');
$(document).on('click', '.phone', function(e) {

		//Prevent submit form
		e.preventDefault();

		if($(e.target).hasClass('help'))
			return

		//Check value of the field
		var amount = $('.mtphone').val();

		if(!amount.match(/^\d+$/) || amount > 100 || amount < 1) {
			$('.mtphone').val('').attr('placeholder','Incorrect (1 - 100)').css('color','#CE1126').addClass('er');
			$('.mtphone').prev().css('color','#CE1126');
			return;
		}

		//Loading
		spawnLoad('load');

		$.ajax({
		type:'POST',
		url:'php/getter.php',
		data:'system=paybycall&amount='+ encodeURIComponent(amount),
		dataType:'json'
		}).done(function(json) {

			//Remove loading
			spawnLoad('kill');

			//Get the reponse code
			reponse = json['result_code'];

			//if the answer from the API do not match with 200
			if(parseInt(reponse) !== 200) {
				$('.mtphone').val('').attr('placeholder','Error server').css('color','#CE1126').addClass('er');
				$('.mtphone').prev().css('color','#CE1126');
				return;
			}

			$('.mtphone').val('Succès, redirection').css('color','#34A853').addClass('er');
			$('.mtphone').prev().css('color','#34A853');

			//Get the redirect url
			url = json['redirect_url'];

			//Remove every espace chars (\)
			rurl = url.replace(/\\/, '');

			//Redirect to the donation (pay)
			window.location.href = rurl;

			});
});

//Sms
$(document).off('click','.sms');
$(document).on('click', '.sms', function(e) {

		//Prevent submit form
		e.preventDefault();

		if($(e.target).hasClass('help'))
			return

		//Check value of the field
		var amount = $('.mtsms').val();

		if(!amount.match(/^\d+$/) || amount > 100 || amount < 1) {
			$('.mtsms').val('').attr('placeholder','Incorrect (1 - 100)').css('color','#CE1126').addClass('er');
			$('.mtsms').prev().css('color','#CE1126');
			return;
		}

		//Loading
		spawnLoad('load');

		$.ajax({
		type:'POST',
		url:'php/getter.php',
		data:'system=paybysms&amount='+ encodeURIComponent(amount),
		dataType:'json'
		}).done(function(json) {

			//Remove loading
			spawnLoad('kill');

			//Get the reponse code
			reponse = json['result_code'];

			//if the answer from the API do not match with 200
			if(parseInt(reponse) !== 200) {
				$('.mtsms').val('').attr('placeholder','Error server').css('color','#CE1126').addClass('er');
				$('.mtsms').prev().css('color','#CE1126');
				return;
			}

			$('.mtsms').val('Succès, redirection').css('color','#34A853').addClass('er');
			$('.mtsms').prev().css('color','#34A853');

			//Get the redirect url
			url = json['redirect_url'];

			//Remove every espace chars (\)
			rurl = url.replace(/\\/, '');

			//Redirect to the donation (pay)
			window.location.href = rurl;

			});
});

//Cash
$(document).off('click','.cash');
$(document).on('click','.cash', function(e) {

		//Prevent submit form
		e.preventDefault();

		if($(e.target).hasClass('help'))
			return

		//Check value of the field
		var amount = $('.mtcash').val();

		if(!amount.match(/^\d+$/) || amount > 100 || amount < 1) {
			$('.mtcash').val('').attr('placeholder','Incorrect (1 - 100)').css('color','#CE1126').addClass('er');
			$('.mtcash').prev().css('color','#CE1126');
			return;
		}

		//Loading
		spawnLoad('load');

		$.ajax({
		type:'POST',
		url:'php/getter.php',
		data:'system=paysafecard&amount='+ encodeURIComponent(amount),
		dataType:'json'
		}).done(function(json) {

			//Remove loading
			spawnLoad('kill');

			//Get the reponse code
			reponse = json['result_code'];

			//if the answer from the API do not match with 200
			if(parseInt(reponse) !== 200) {
				$('.mtcash').val('').attr('placeholder','Error server').css('color','#CE1126').addClass('er');
				$('.mtcash').prev().css('color','#CE1126');
				return;
			}

			$('.mtcash').val('Succès, redirection').css('color','#34A853').addClass('er');
			$('.mtcash').prev().css('color','#34A853');

			//Get the redirect url
			url = json['redirect_url'];

			//Remove every espace chars (\)
			rurl = url.replace(/\\/, '');

			//Redirect to the donation (pay)
			window.location.href = rurl;

			});
});

//Crypto
$(document).off('click','.crypto');
$(document).on('click','.crypto', function() {

	var wcry =  $(this).attr('id');

	if(wcry === 'sbtc')
		$('#rcpa').val('1Jip3U2Ugi7brm6fPB6hswcizsrfSU3Xu5').show();
	else if(wcry === 'sltc')
		$('#rcpa').val('LRti1doNXuiwZcc2e6sEkvtYJhBq9ZDCVC').show();
	else
		alert('error');
});

$(document).off('change', '#doth');
$(document).on('change', '#doth', function() {

	var pys = $(this).val();

	if($('.ctn-doth').length > 0)
		$('.ctn-doth').remove();

	$('#ctn-donother').append('<div class=ctn-doth>\
    		<i class="fa fa-eur fa-lg"></i>\
    		<input id='+ pys +' type="text" name="amount" class="amount moth" placeholder="Montant">\
    		<input type="submit" value="&hearts; Confirmation" class="btncn soth">\
    	</div>');
});

$(document).off('click', '.soth');
$(document).on('click', '.soth', function(e) {

	//Prevent submit form
	e.preventDefault();

	var amount = $('.moth').val();
	var sys = $('.moth').attr('id');

		if(!amount.match(/^\d+$/) || amount > 100 || amount < 1) {
			$('.moth').val('').attr('placeholder','Incorrect (1 - 100)').css('color','#CE1126').addClass('er');
			$('.moth').prev().css('color','#CE1126');
			return;
		}

	//Loading
	spawnLoad('load');

	$.ajax({
	type:'POST',
	url:'php/getter.php',
	data:'system='+ sys +'&amount='+ encodeURIComponent(amount),
	dataType:'json'
	}).done(function(json) {

		//Remove loading
		spawnLoad('kill');

		//Get the reponse code
		reponse = json['result_code'];

		//if the answer from the API do not match with 200
		if(parseInt(reponse) !== 200) {
			$('.moth').val('').attr('placeholder','Error server').css('color','#CE1126').addClass('er');
			$('.moth').prev().css('color','#CE1126');
			return;
		}

		$('.moth').val('Succès, redirection').css('color','#34A853').addClass('er');
		$('.moth').prev().css('color','#34A853');

		//Get the redirect url
		url = json['redirect_url'];

		//Remove every espace chars (\)
		rurl = url.replace(/\\/, '');

		//Redirect to the donation (pay)
		window.location.href = rurl;

		});

});

//Reset css input text donation (the error css)
$(document).off('click','.er');
$(document).on('click','.er', function() {

	$(this).attr('placeholder','Montant').css('color','').removeClass('er');
	$(this).prev().css('color','');
});



//Help donate
$(document).off('click', '.help');
$(document).on('click', '.help', function(e) {

	//Cancel anchor (if any)
	e.preventDefault();

	//Loading
	spawnLoad('load');

	//Structure ctn
	var ctnhp = '\
	<div class="containerPreview">\
		<span class="closePrev fa-stack fa-lg">\
			<i class="fa fa-circle fa-stack-2x"></i>\
			<i class="fa closePrev fa fa-times  fa-stack-1x fa-inverse"></i>\
		</span>\
		<div class="messagePreview"></p>\
	</div>';

	if($(e.target).hasClass('sms'))
		$.get('html/sms.html', function(data) {
			body.append(ctnhp).promise().done(function() {
				$('.messagePreview').html(data);
				$('.containerPreview').fadeIn();
				spawnLoad('kill');
			});
		});

	else if($(e.target).hasClass('phone'))
		$.get('html/phone.html', function(data) {
			body.append(ctnhp).promise().done(function() {
				$('.messagePreview').html(data);
				$('.containerPreview').fadeIn();
				spawnLoad('kill');
			});
		});

	else if($(e.target).hasClass('cash'))
		$.get('html/cash.html', function(data) {
			body.append(ctnhp).promise().done(function() {
				$('.messagePreview').html(data);
				$('.containerPreview').fadeIn();
				spawnLoad('kill');
			});
		});

	else if($(e.target).hasClass('bank'))
		$.get('html/wire.html', function(data) {
			body.append(ctnhp).promise().done(function() {
				$('.messagePreview').html(data);
				$('.containerPreview').fadeIn();
				spawnLoad('kill');
			});
		});
});

/* End donation */

/* Trade */
$(document).off('click', '.trade');
$(document).on('click', '.trade', function(e) {

    if($('.logoNinja').hasClass('ddg'))
	logoSwitch('ninja');

	//Lock scroll event
	trigSc = true;

	//Cancel anchor (if any)
	e.preventDefault();

	//Clean/hide previous container
	clean('visible');

	//Start the display loading
	spawnLoad('load');

    $.ajax({
    type: 'GET',
    url: 'php/trade.php'
    }).done(function(msg) {

    	history.pushState({}, null, 'trade');

        resultDb.html(msg);

        central.fadeIn().promise().done(function() {

			//PopState replace
            history.replaceState({}, null, 'trade');

			//Change title (browser)
            document.title = 'Bureau des échanges';

            //Show shortLink
			if(shortLink.is(':visible'))
				shortLink.hide();

			//Kill spawnload
            spawnLoad('kill');
        });

    });

});

//Trade verified pass
$(document).off('click', '.ck');
$(document).on('click', '.ck', function() {

	if($('.ctn-ck').hasClass('vsb-trade')) {
		$('.ctn-ck').hide('fold').removeClass('vsb-trade');
		return;
	}

	$('.vsb-trade').hide('slow').removeClass('vsb-trade');

	$('.ctn-ck').show('fold').addClass('vsb-trade');

});

$(document).off('click', '.cy');
$(document).on('click', '.cy', function() {

	if($('.ctn-cy').hasClass('vsb-trade')) {
		$('.ctn-cy').hide('fold').removeClass('vsb-trade');
		return;
	}

	$('.vsb-trade').hide('fold').removeClass('vsb-trade');

	$('.ctn-cy').show('fold').addClass('vsb-trade');
});

//Trade verified pass submit
$(document).off('click', '.sbm-ck');
$(document).on('click', '.sbm-ck', function() {

	var ckval = $('.trade-ck');

	if(ckval.val().length < 3) {
		ckval.val("Pseudo invalide").addClass('red');
		return;
	}

	spawnLoad('load');

	$.ajax({
	type:'POST',
	url:'php/getter.php',
	data:'trck=1&username=' + encodeURIComponent(ckval.val())
	}).done(function(msg) {

		spawnLoad('kill');

		if(msg === 'ptsless')
			ckval.val("5 points minimum").addClass('red');
		else if(msg === 'no')
			ckval.val("Compte incorrect").addClass('red');
		else if(msg === 'ok')
			ckval.val("Succès").addClass('green');
		else
			ckval.val("Error").addClass('red');
	});

});

//Trade citizen pass
$(document).off('click', '.trade-ck');
$(document).on('click', '.trade-ck', function() {

	if($(this).hasClass('red'))
		$(this).val('').removeClass('red');

	else if($(this).hasClass('green'))
		$(this).val('').removeClass('green');

	else
		return;
});

//Trade citizen pass submit
$(document).off('click', '.sbm-cy');
$(document).on('click', '.sbm-cy', function() {

	spawnLoad('load');

	$.ajax({
	type:'POST',
	url:'php/getter.php',
	data:'trcy=1'
	}).done(function(msg) {

		spawnLoad('kill');

		if(msg === 'ptsless')
			alert("10 points minimum");

		else if(msg === 'bad')
			alert("Vous ne pouvez assigner le badge citoyen à votre compte");

		else if(msg === 'ok')
			alert('Badge assigné avec succès');

		else
			alert('error');
	});

});
/* Trade End */
/* Toolbar post (quote, report, vote up... */
//On click #button_newto  (new topic)
$(document).off('click', '#button_newto, #button_newto_bis');
$(document).on('click', '#button_newto, #button_newto_bis', function(e) {

	//Don't follow the href
	e.preventDefault();

	//is locked ?
	if(sl === false) return;

	//Set lock
	sl = false;

	//Check if must spawn error
	if($(this).attr('class').split(' ')[1] === 'capost') {


	ctnperm = $('<div id="noperm">\
			<i class="fa fa-info fa-3x fa-pull-left fa-border"></i>\
			<p>Votre compte n\'est pas autorisé à créer un nouveau topic dans cette section du forum.</p>\
			<p>L\'accès à la totalité du forum est ouvert à tous, la création de nouveau topic ou répondre dans certaines sections vous sont limités. \
			   cette mesure permet ainsi de réguler les robots, les multi-comptes et autre fauteurs de troubles.</p>\
			<p>Nous vous invitons à lire notre FAQ pour prendre connaissance du mode de fonctionnement de Wawa-Mania depuis la version Ninja </p>\
			<p id=permfaq><i class="fa fa-life-ring"></i><a href="https://forum.wawa-mania.ec/faq"> La FAQ de Wawa-Mania</a></p>\
		</div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max 1200px
	if(parseInt(sizecal) > 400) var sizecal = 400;

	ctnperm.dialog({
	width:sizecal,
	modal:true,
	resizable: true,
	title:'Permission refusée',
	dialogClass:'modalNinja',
	show:{effect:'scale', duration: 320}
	});

	sl = true;
	return false;

	}//End capost

	$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'css/spectrum.css'));

	//Get the section id
	sectionid = $(e.target).attr('data-bnt');

	//Loading
	spawnLoad('load');

	//Load the js new topic
	$.getScript('js/topic.js', function() {

		//Kill loader and reset the lock
		spawnLoad('kill');
		sl = true;
	});

});

//On click #reply (reply in topic)
$(document).off('click', '#reply');
$(document).on('click', '#reply', function(e, data) {

	//Avoid multi firing
	if(sl === false) return;
	sl = false;

	//Get the post id, action and load js file
	$('head').append($('<link rel="stylesheet" type="text/css" />').attr('href', 'css/spectrum.css'));

	//Get the topic id and load js file
	topicid = $(this).attr('class').split(' ')[0];

	//Loading
	spawnLoad('load');

	$.getScript('js/reply.js', function() {

	spawnLoad('kill');

	if(data !== undefined)
		$('#message_post').val(data);

	sl = true;

	});

});

//On click .vo (p = +1 | n = -2) load js
$(document).off('click', '.vo');
$(document).on('click', '.vo', function(e) {

	//Avoid multi firing
	if(sl === false) return;
	sl = false;
	head.append($('<link rel="stylesheet" type="text/css" />').attr('href', 'css/vote.css'));

	//Get the post id, action and load js file
	postid	= $(e.target).attr('class').split(' ')[2];
	actvote = $(e.target).attr('class').split(' ')[1];

	//Loading
	spawnLoad('load');

	$.getScript('js/vote.js', function() {
	sl = true;

	//End loading
	spawnLoad('kill');

	});
});

//On click #report load js
$(document).off('click', '.report');
$(document).on('click', '.report', function(e) {

	//Avoid multi firing
	if(sl === false) return;
	sl = false;

	//Get the post id
	postid	= $(e.target).attr('class').split(' ')[2];

	//Loading
	spawnLoad('load');

	$.getScript('js/report.js', function() {
		spawnLoad('kill');
		sl = true;
	});
});

//On click .editp
$(document).off('click', '.editp');
$(document).on('click', '.editp', function(e) {

	if(!$(e.target).hasClass('editfire')) {

	//Avoid multi firing
	if(sl === false) return;
	sl = false;

	if($('#container_edit').length > 0) {
	alert('Vous avez déjà un edit en cours, désélectionner le avant de sélectionner celui-ci');
	return false;
	}

	head.append($('<link rel="stylesheet" type="text/css" />').attr('href', 'css/spectrum.css'));

	//Get the post id
	postid	= $(e.target).attr('class').split(' ')[2];

	//Edit title on first msg
	edtitle = $(e.target).attr('id');
    vtitle  = $('.titleTohtml').text();
    vtitle  = vtitle.replace(/"/g, '&quot;');

	//Change the color to green (open)
	$(e.target).prev().addClass('green');

	//add trigger for next even click (send textarea)
	$(e.target).addClass('editfire');

	//Loading
	spawnLoad('load');
	$.getScript('js/edit.js', function() {
		spawnLoad('kill');
		sl = true;
	});

	}
});

//On click .quotec
$(document).off('click', '.quotec');
$(document).on('click', '.quotec', function(e) {

	//Avoid multi firing
	if(sl === false) return;
	sl = false;
	sl = true;

	//Get the post id
	postid	= $(e.target).attr('class').split(' ')[2];

	//Get the bbcode
	bbtext = $('.bbc_'+ postid).text();

	//Get the username
	qus = $(e.target).attr('class').split(' ')[3].slice('3');

	//New quote to add
	newq = '[quote='+ qus +']'+ bbtext +'[/quote]';

	//Open the reply block if needed
	if($('#container_post').length == '0')
		//Simulate click #reply
		$('#reply').trigger('click', [newq]);

	//To be remove
	if($(e.target).prev('i').hasClass('green')) {
		//Get the string to remove
		remq = '[quote='+ qus +']'+ bbtext +'[/quote]';

		$('#message_post').val(function(i, v) { //index, current value
		return v.replace(remq +"\n\n",'');
		});

		//Remove the color green on the button quote, then exit
		$(e.target).prev('i').removeClass('green')
		return;
	}

	//Or to be add
	//Get the actual val and insert it after the previous text
	nowq = $('#message_post').val();
	$('#message_post').val(nowq + newq +"\n\n");

	//Change the color of the button (in green)
	$(e.target).prev('i').addClass('green');

});

//On click .delp
$(document).off('click', '.delp');
$(document).on('click', '.delp', function(e) {

	//Avoid multi firing
	if(sl === false) return;
	sl = false;

	//Delete any potential containerdelp
	//if($('#containerdelp').length > 0) $('#containerdelp');

	//Get the post id
	postid	  = $(e.target).attr('class').split(' ')[2];
	sectionid = $(e.target).attr('class').split(' ')[3];

	//Container delete warning
	containerdelp = $('<div id="container_delp">\
					   Le post que vous avez selectionné va être supprimé, si celui est le premier du topic, tous les messages seront automatiquement supprimé. Voulez-vous continuer ?</div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max width
	if(parseInt(sizecal) > 600) var sizecal = 600;

	sl = true;
	//Pop the containerdelp vote
	containerdelp.dialog({
		width:sizecal,
		modal:true,
		resizable: true,
		title:'Supprimez',
		dialogClass:'modalNinja',
		show:{effect:'scale', duration: 320},
	    buttons: {
        'Confirmer': function() {
			$.ajax({
			type: 'POST',
			url: 'php/getter.php',
			data:'postid='+ postid +'&sectionid='+ sectionid +'&delp=1'
			}).done(function(msg) {

			//If post deleted, reload the page
			if(msg == 'reload') location.reload();

			//If the topic is deleted, redirect to section of this topic
			else window.location.href = '/sub-'+ msg;

			});
		},
			Cancel: function() {
			$(this).dialog('close');
        }
	   }
	});
});

//Lock topic
$(document).off('click', '.lockt, .unlockt');
$(document).on('click', '.lockt, .unlockt', function(e) {

	//Avoid multi firing
	if(sl === false) return;
	sl = false;

	//Get the post id
	topicid = $(e.target).attr('class').split(' ')[1];

	//Lock or Unlock
	actlck = $(e.target).attr('class').split(' ')[2];

	lck = (actlck === 'lockt') ? '0' : '1';

	//Ajax
	spawnLoad('load');
	$.ajax({
	type:'POST',
	url:'php/getter.php',
	data:'lck='+ lck +'&topicid='+ topicid
	}).done(function(msg) {

		sl = true;
		spawnLoad('kill');

		//Red + lockt
		if(msg === '0') {
		$('.unlockt').prev().removeClass('green').addClass('red');
		$('.unlockt').addClass('lockt fa-lock').removeClass('unlockt fa-unlock');
		}

		//Green + unlockt
		else if(msg === '1') {
		$('.lockt').prev().removeClass('red').addClass('green');
		$('.lockt').addClass('unlockt fa-unlock').removeClass('lockt fa-lock');
		}

		else return false;

	});
});

//Spin topic
$(document).off('click', '.stickyt, .unstickyt');
$(document).on('click', '.stickyt, .unstickyt', function(e) {

	//Avoid multi firing
	if(sl === false) return;
	sl = false;

	//Get the topic id
	topicid = $(e.target).attr('class').split(' ')[1];

	//Spin or Unspin
	actspn = $(e.target).attr('class').split(' ')[2];

	spn = (actspn === 'stickyt') ? '0' : '1';

	//Ajax
	spawnLoad('load');
	$.ajax({
	type:'POST',
	url:'php/getter.php',
	data:'spn='+ spn +'&topicid='+ topicid
	}).done(function(msg) {

		sl = true;
		spawnLoad('kill');

		//Red + stickyt
		if(msg == '0') {
		$('.unstickyt').prev().removeClass('green').addClass('red');
		$('.unstickyt').addClass('stickyt').removeClass('unstickyt');
		}

		//Green + unstickyt
		else if(msg == '1') {
		$('.stickyt').prev().removeClass('red').addClass('green');
		$('.stickyt').addClass('unstickyt').removeClass('stickyt');
		}

		else return false;

	});
});

//Move topic

$(document).off('click', '.movet');
$(document).on('click', '.movet', function(e) {

	//Avoid multi firing
	if(sl === false)
		return;

	sl = false;

	//Check if was not previously open
	if($('#movesel').length > 0) {
	$('#movesel').remove();
	sl = true;
	return;
	}

	//Get the topic id
	topicid = $(this).attr('data-topid');

	//Get option cat / sec
	$.ajax({
    type:'POST',
    url:'php/getter.php',
    data:'selector=1'
    }).done(function(seltag) {

	//Get the list of categorie / section already spawn in DOM (for the search function)
	$('<select name="movesel" id="movesel">'+ seltag +'</select>').insertAfter('.atopbar:first');

	$('#movesel').selectmenu();

	//Replace the text first option
	$('.ui-selectmenu-text').text('Déplacez vers');

	  //Selected section
	  $('#movesel').selectmenu({
		change: function(event, data) {

			//ID section target
			sectionid = data.item.value;

			$.ajax({
			type:'POST',
			url:'php/getter.php',
			data:'movet=1&sectionid='+ sectionid +'&topicid='+ topicid
			}).done(function(msg) {
				//Redirect to the section
				window.location.href = 'https://forum.wawa-mania.ec/sub-'+ msg;
			});
		}

     });

	sl = true;

	});
});

/* Misc */
//Tooltip Jquery-ui
$(function() {

	if($(window).width() < 1000) return; //Less 1000, exit (bug on small device)
    $(document).tooltip({
	position: {my: "right bottom-30" },

	items: '[data-title]',
	track: true,
	content: function() {
		var element = $(this);
		if(element.is('[data-title]')) return element.attr('data-title');
	}
    });
});

//footer
if($(window).width() < 700)
	firefoot.children('a').text('Firefox mobile');
else
	firefoot.children('a').text('Optimisé pour Firefox');

//Block, reset input field
$(document).off('click', '.fail');
$(document).on('click', '.fail', function(e) {
	//Get the placeholder saved into data-holder
	valholder = $(e.target).attr('data-holder');

	//Clean target
	$(e.target).val('').attr('placeholder', valholder).css('border-color','').css('color','').prev().css('color','');
});

//Open link in new tab, bypass prevent.default
$(document).off('click', '.go');
$(document).on('click', '.go', function(e) {
	e.preventDefault();

	var glink = $(this).attr('href');
	window.location.href = glink;

});

//Prevent only wished element
$(document).off('click', '.donot');
$(document).on('click', '.donot', function(e){
	e.preventDefault();
});

//Toolclean
$(document).off('click', '#cltool');
$(document).on('click', '#cltool', function(e) {

	if($('#ctncl').length > 0)
		$('#ctncl').remove();

	ctncl = $('<div id="ctncl">\
			<span>Chargement en cours...</span>\
			</div>');

	//Get 80% in px
	var sizecal = $(window).width() * 0.8;

	//Max width
	if(parseInt(sizecal) > 900)
		var sizecal = 900;

	//Pop the containerdelp vote
	ctncl.dialog({
		width:sizecal,
		modal:true,
		resizable: true,
		title:'CleanTool',
		dialogClass:'modalNinja',
		show:{effect:'scale', duration: 320},
		open:function() {

		spawnLoad('load');

		$.ajax({
			type: 'POST',
			url: 'php/getPost.php',
			data:'lnkpa=cleantool',
			dataType: 'json'
		}).done(function(data) {

			console.log(data);

			$('#ctncl').empty();

			if(data === null || data === undefined || data === '') {
				$('<p>ToolClean n\'a trouvé aucun panthéon attaché à ce compte.</p>').appendTo('#ctncl');
			return;
			}

			atic = 0;
			wtic = 0;

			$('#ctncl').append('<br>');

			$.each(data, function(idx, value) {

				if(data[idx] === '01\n') {
					$('#ctncl').append('<span> '+ (parseInt(atic) + 1) +' </span> - <span style="color:red"> Syntaxe incorrect</span><br>');
					wtic++;
				}

				else if(data[idx] === "02\n") {
					$('#ctncl').append('<span> '+ (parseInt(atic) + 1) +' </span> - <span style="color:red"> Topic épinglé interdit</span><br>');
					wtic++;
				}

				else if(data[idx] === "03\n") {
						$('#ctncl').append('<span> '+ (parseInt(atic) + 1) +' </span> - <span style="color:red"> Section non classé upload</span><br>');
						wtic++;
					}

				else if(data[idx] === "04\n") {
					$('#ctncl').append('<span> '+ (parseInt(atic) + 1) +' </span> - <span style="color:red"> Topic supprimé</span><br>');
					wtic++;
				}

				else
					$('#ctncl').append('<span>'+ (parseInt(atic) + 1) +'</span>'+ data[idx] +'<br>');

					atic++;
			});

		}).promise().done(function() {

				totaldel = parseInt(atic) - parseInt(wtic);

				ctnstatus = '<div id=status>\
							<p class=glink>'+ totaldel +' topics prêt à supprimer</p>\
							<p class=blink>'+ wtic +' lien(s) sont incorrect</p>\
							</div>';

				$('#ctncl').prepend(ctnstatus);
				spawnLoad('kill');

			});
	}
	});
});

//Private message
$(document).off('click', '.mp');
$(document).on('click' ,'.mp', function() {

    if($('.logoNinja').hasClass('ddg'))
	      logoSwitch('ninja');

	history.pushState({}, null,'/mp');
	history.replaceState({}, null, '/mp');

	clean('hidden');

	spawnLoad('load');

	$.get('php/mp.php', function(data) {

    	spawnLoad('kill');
		resultDb.html(data);
		central.fadeIn();
	});

});

//Write new message / Write new message with sender (auto fill receipt) / Read new message
$(document).off('click', '#mp_wrn');
$(document).on('click' ,'#mp_wrn', function() {

	var url = $(this).attr('href');

	history.pushState({}, null,url);
	history.replaceState({}, null, url);

	clean('hidden');

	spawnLoad('load');

	$.get('php/mp.php', function(data) {

    	spawnLoad('kill');
		resultDb.html(data);
		central.fadeIn();
	});

});

//Send message
$(document).off('click', '#mp_snewt');
$(document).on('click' ,'#mp_snewt', function() {

	var receipt   = $('#mp_twho').val();
	var title = $('#mp_title').val();
	var message = $('#mp_message').val();

	if(receipt.length < 3) {
		$('#mp_twho').val('Ne peut pas être vide').css('color','red').addClass('fail');
		return;
	}

	if(title.length < 3) {
		$('#mp_title').val('Ne peut pas être vide').css('color','red').addClass('fail');
		return;
	}

	if(message.length < 3) {
		$('#mp_message').val('Ne peut pas être vide').css('color','red').addClass('fail');
		return;
	}

	spawnLoad('load'); //Start the display loading

    $.ajax({
    type: 'POST',
    url: 'php/getter.php',
    data:'sendmp=1&receipt='+ receipt +'&title='+ title +'&message='+ message
    }).done(function(msg) {

    	$('#mp_msg').empty().html('<p>'+ msg +'</p><a id=mp_wrn href="/mp-new" class="donot mp_answer"> Envoyer un message</a>');
    	spawnLoad('kill');
    });

});