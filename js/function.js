//Selector caching for better jQuery perf
/* Menu */
header 		 = $('header');
home	     = $('#home');
login		 = $('#login');
lost		 = $('.lost');
register	 = $('.register');
search 		 = $('#search');
scheme     	 = $('.scheme');
color	   	 = $('.color');
/* Container central */
central    	 = $('#central');
resultDb   	 = $('.resultDb');
/* Search function */
hSearch		 = $('#hSearch');
hMain		 = $('.hMain');
subSearch  	 = $('.subSearch'); //Button submit the search
sub			 = $('#sub'); //Select with options
searchSel  	 = $('#searchSel');
optSearch  	 = $('#optSearch');
optBy      	 = $('#optBy');
optSort    	 = $('#optSort');
author		 = $('.author'); //Look last_topics of the target user
autModal	 = $('.autModal'); //
nbrow      	 = $('#nbrow'); //Wrote the number of result found (used for scroll more result)
getmore		 = $('#getmore'); //Get the word(s)
/* Login */
loginForm 	 = $('.loginForm'); //Form of the login connection members
lostForm 	 = $('.lostForm'); //Form of the login connection members
loginu		 = $('.loginu'); //Field username
loginp		 = $('.loginp'); //Field password
clog		 = $('#clog'); //Submit
secup		 = $('.secup'); //Captcha
/* Members area */
aream		 = $('.aream'); //Icon user in top menu
profile		 = $('#profile'); //Modal profile (option user)
containerset = $('.containerset'); //Container content of selected opt
optset	 	 = $('.optset'); //Right block profile
optpassword  = $('#optpassword'); //ctn right block change password profile
optcontact	 = $('#optcontact'); //ctn right block change contact infos
optinfos     = $('#optinfos'); //ctn right block about me
optavatar	 = $('#optavatar');
sendpa		 = $('#sendpa'); //Button send change password
optpas		 = $('.optpas'); //Input form change password
actual		 = $('#actualp'); //Actual password (input)
newp		 = $('#newp'); //New password (input)
confirmp	 = $('#confirmp'); //Confirm password (input)
/* Home */
lefthome	   = $('.lefthome'); //Go to the target sub
/* Topic */
recent	   = $('.recent');
viewtopic  	   = $('.viewtopic');
pidPost   	   = $('.pidPost');
pageTop    	   = $('.pageTop');
pageSub	   	   = $('.pageSub');
historySet 	   = $('#historySet');
/* Make topic */
modalAsk	   = $('#ctnask'); //Container form ask
message_to 		= $('#message_to');
title_to 		= $('#title_to');
/* Reply */
message_post   = $('#message_post');
bb_bold	   	   = $('#bb_bold'); //bbcode bold
replycap		= $('#replycap');
topiccap		= $('#topiccap');
/* ShortLink */
backUp		   = $('.backUp'); //Icons arrow up
wmStats	 	   = $('.wmStats');  //Icons stats
look4user	   = $('.look4user'); //Link for search user
lookTuser	   = $('.lookTuser');
modalStats	   = $('#modalStats'); //Modal stats
searchUser 	   = $('#searchUser'); //Modal search user
iptsu		   = $('#iptsu');
resultUser	   = $('#resultUser'); //Result of search user
ucnt		   = $('.ucnt'); //On click Hide/show infos hidden by default
toprank		   = $('.toprank'); //Button Top rank users
ctnrank		   = $('#ctnrank'); //Container top rank users
donation	   = $('.donation');
donate		   = $('#donate');
/* Footer */
firefoot	   = $('#firefoot');
contact		   = $('#contact');
/* Misc */
trigSc		   = true; //Avoid scroll event to fire (more result search function)
disable		   = $('.disable');
faq			   = $('.faq'); //Display the FAQ
report		   = $('#report'); //Container report
reportv		   = $('#reportv'); //Textarea report
hide		   = $('.hide'); // = display: none;
recovery	   = $('#recovery'); //Recevery lost password
accrec		   = $('#accrec');
mailrec		   = $('#mailrec');
sndcook		   = $('#sndcook');
trigPop 	   = $('#trigPop');
shortLink	   = $('#shortLink');
head		   = $('head');
body 		   = $('body');
wninja		   = $('.wninja');
loadTag 	   = '<div id="loadGen"><p><i class="fa fa-cog fa-spin"></i>Chargement en cours</p></div>';
endTag 		   = '<div id="endresult"><i class="fa fa-exclamation-triangle"></i>Il n\'y a plus de résultats concernant votre recherche<br />Cliquez sur ce bandeau pour remonter !</div>';

//Rebuild selector (if element spawn after caching)
function cacheSel(name) {
	name = $(name);
	return name;
}

//Function - spawnLoad
function spawnLoad(TrigLoad) {

	if(TrigLoad === 'load') {
	TrigLoad = $(loadTag).insertAfter('#wrapper');
	return TrigLoad;
	}

	else if(TrigLoad === 'kill') {
	loadGen = $('#loadGen');
	loadGen.remove();
	}
}

//Function - coloredInputifaq
function coloredInput(TrigSelector, TrigColor, TrigText) {

	if (TrigColor === 'error')
	$(TrigSelector).css('border-color','red').css('color','red').attr('placeholder',TrigText).parent().css('color','red');

	else if(TrigColor === 'errorSe')
	$(TrigSelector).css('border-color','red').css('color','red').attr('placeholder',TrigText).val('');

	else if(TrigColor === 'errorVal')
	$(TrigSelector).css('border-color','red').css('color','red').attr('placeholder',TrigText).val('').prev().css('color','red');

	else if (TrigColor === 'errorClean')
	$(TrigSelector).css('color','').css('border-color','').attr('placeholder',TrigText)
}

$('img').error(function () {
    $(this).hide();
});

//Semantic tag time (datetime=year-month-day)
function sem_time(time) {
	time = time.split('/');
	time = time[2] +'-'+ time[1] +'-'+ time[0];
	return time;
}

function timeConverter(UNIX_timestamp){

	var a = new Date(UNIX_timestamp * 1000);
	var months = ['Jan','Fev','Mars','Avr','Mai','Juin','Jui','Aout','Sep','Oct','Nov','Dec'];
	var year = a.getFullYear();
	var month = months[a.getMonth()];
	var date = a.getDate();
	var hour = a.getHours();
	var min = a.getMinutes();
	var sec = a.getSeconds();
	var time = date + ' ' + month + ' ' + year;
	return time;
}

//Change plain url to hyperlink
function linkify(text) {
	var urlRegex = /(\s\b(https|http):\/\/[-A-ZÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
	return text.replace(urlRegex, function(url) {
	return '<a href="'+ url +'" class="isLink" target=_blank>'+ url +'</a>';
	})
}

//Fix the href in html tags hidden (for edit)
function rttp(text) {
	return text.replace(/r(ttps?)/gim, 'h$1');
}

//Function - placeholderReset
function placeholderReset(select) {
	//Browser natively return color in rbg
	return ($(select).css('borderTopColor') == 'rgb(255, 0, 0)' || $(select).css('borderTopColor') == 'rgb(51, 191, 144)' ? true : false);
}

//Bbcode recursive
function quoteRecursive(output) {
	//ruote at the last for avoid quote tag to be convert inside bcc_* class
	output = output.replace(/\[quote=?(.*?)\]/gi,"<div class=\"recursiveQ\"><p><i class=\"fa fa-quote-left\"></i> $1</p>\
												  <span>")
	output = output.replace(/\[\/quote\]/gi,"</span></div>")
	output = output.replace(/\[([\/])?ruote/gi,"[$1quote");

	return output;
}

//Change logo
function logoSwitch($logo) {

    if($logo === 'ddg')
	$('.logoNinja').attr('src','').promise().done(function() {
	    $(this).hide().attr('src','svg/ddg.svg?0.5').addClass('ddg').attr('data-title','<small>Astuce</small> <span style="color:#55B045">Duckduckgo</span>, Recherchez rapidement un topic en utilisant le bang <b>!wawa</b>').show('scale','1500');
	});

    else
	$('.logoNinja').attr('src','').promise().done(function() {
	    $(this).hide().attr('src','svg/ninja.svg?0.5').removeClass('ddg').attr('data-title','').show('scale','1500');
	});

}

function displayperm(level, fid) {

	//28 Unknown (ghost) no level at all
	if(parseInt(level) === 28)
	return $('#button_newto').css('background-color', '#EA4335').addClass('capost');
	//End Level28

	//Level 5
	else if(parseInt(level) === 27) {

		/*4 : coffee | 83 : Discover | 8 appz W | 36 Appz L | 44 No Virus |
		16 Gamez PC | 37 Gamez C | 38 Gamez portable | 17 Ask	*/
		var lv5ck = [4, 83, 8, 36, 44, 16, 37, 38];

		if($.inArray(parseInt(fid), lv5ck) != -1)
		return $('#button_newto').css('background-color', '#EA4335').addClass('capost');

	}
	//End Level 5

	//Not allowed to create a topic in exclue if no badge uploader categorie
	 else if(parseInt(fid) === 45) {
	  if(parseInt(level) !== 1 && parseInt(level) !== 29 && parseInt(level) !== 3 && parseInt(level) !== 4 && parseInt(level) !== 5 && parseInt(level) !== 6)
	  return $('#button_newto').css('background-color', '#EA4335').addClass('capost');
	 }

	//Section information
	if(parseInt(fid) === 1) {
		if(parseInt(level) !== 1 && parseInt(level) !== 29)
		return $('#button_newto').css('background-color', '#EA4335').addClass('capost');
	}
}

//Clean body, hide or show container div
/* Container affected : hMain | resultDb | central */

function clean(action) {

	//Hide empty target div on action
	if(action === 'visible') {

		//Hide the content of header
		if($('#hSearch').length > 0)
			$('#hSearch').remove();

		//empty any content on resultDb
		if(resultDb.not(':empty'))
			resultDb.empty();

		//Remove the lock / load FAQ
		if($('.faq').hasClass('fload'))
			$('.faq').removeClass('fload');

		//Hide the principal middle container
		central.hide();
	}

	else if(action === 'hidden') {

		//Hide the content of header
		if($('#hSearch').length > 0)
			$('#hSearch').remove();

		//empty any content on resultDb
		if(resultDb.not(':empty'))
			resultDb.empty();

		//Remove the lock / load FAQ
		if($('.faq').hasClass('fload'))
			$('.faq').removeClass('fload');

		//Hide the shortLink block
		if(shortLink.length > 0)
			shortLink.hide();

		//Hide the principal middle container
		central.hide();
	}

	else if(action === 'cse') {

		 //empty any content on resultDb
		if(resultDb.not(':empty'))
			resultDb.empty();

		//Hide the principal middle container
		central.hide();
	}
}

//Popstat function
function pop() {

	if($('#hSearch').length > 0)
		$('#hSearch').remove();

	//Regex url
	var regUrl = /^https:\/\/forum\.wawa\-mania\.ec\/(([\w]*)-?([^-\^\°\<\>\*\$\~\|\;\={}\(\)\§\€\£\¬]*)-?([\w]*)-?([\d]*))$/gi;

	//Retrieve actual URL and get the target substring
	subUrl = regUrl.exec(location.href);

	//If null then set default page (actual default page : search)
	gTo = (subUrl === undefined || subUrl === null) ? "" : subUrl[2];

	trigPop = cacheSel('#trigPop');

	//Login/Home display
	if(gTo === "" || gTo === 'home' || gTo === 'index')
		home.trigger('click', '/');

	//Login
	else if(gTo === 'login')
		login.trigger('click');

	//Faq
	else if(gTo === 'faq') {
		trigPop.removeClass().addClass('faq');
		popSel = cacheSel('.faq');
		popSel.trigger('click');
	}

	//Donation
	else if(gTo === 'donation') {
		trigPop.removeClass().addClass('donation');
		popSel = cacheSel('.donation');
		popSel.trigger('click');
	}

	//Trade
	else if(gTo === 'trade') {
		trigPop.removeClass().addClass('trade');
		popSel = cacheSel('.trade');
		popSel.trigger('click');
	}

	//MP
	else if(gTo === 'mp') {
		trigPop.removeClass().addClass('mp');
		popSel = cacheSel('.mp');
		popSel.trigger('click');
	}

	//lost
	else if(gTo === 'lost') {
		trigPop.removeClass().addClass('lost');
		popSel = cacheSel('.lost');
		popSel.trigger('click');
	}

	//register
	else if(gTo === 'register') {
		trigPop.removeClass().addClass('register');
		popSel = cacheSel('.register');
		popSel.trigger('click');
	}

	//Disable (porn photos)
	else if(gTo === 'disable') {
		trigPop.removeClass().addClass('disable');
		popSel = cacheSel('.disable');
		popSel.trigger('click');
	}

	//Search no opt
	else if(gTo === 'search' && subUrl[3] === '')
		search.trigger('click', [subUrl[2]]);

	//Search default (launch by user)
	else if(gTo === 'search' && subUrl[3] !== '') {

		trigPop.removeClass().addClass('subSearch');
		popSel = cacheSel('.subSearch');
		popSel.trigger('click', [subUrl[2] +'-'+ subUrl[3] +'-'+ subUrl[4] +'-'+ subUrl[5]]);
	}

	//Search by author (automatic by url match)
	else if(gTo === 'author') {

		trigPop.removeClass().addClass('author');
		popSel = cacheSel('.author');
		popSel.trigger('click', ['author-'+ subUrl[3] +'-'+ subUrl[4] +'-'+ subUrl[5]]);
	}

	//Search by last_topics (automatic by url match)
	else if(gTo === 'recent' || gTo === 'detente' || gTo === 'mac' || gTo === 'informatique' || gTo === 'tuto' || gTo === 'films' || gTo === 'seriestv') {

		trigPop.removeClass().addClass('recent');
		popSel = cacheSel('.recent');
		popSel.trigger('click', [gTo]);
	}

	//Sub (first page)
	else if(gTo === 'sub' && subUrl[4] === "") {

		trigPop.removeClass().addClass('lefthome');
		popSel = cacheSel('.lefthome');
		popSel.trigger('click', [subUrl[3]]);
	}

	//Sub seconds page or more (hands fic & select page)
	else if(gTo === 'sub' && subUrl[4] !== "") {

		trigPop.removeClass().addClass('pageSub').attr('name', 'sub');
		popSel = cacheSel('.pageSub');
		popSel.trigger('change', [subUrl[3] +'-'+ subUrl[4]]);

	}

	//Topic (first page)
	else if(gTo === 'topic' && subUrl[4] === "") {

		trigPop.removeClass().addClass('viewtopic');
		popSel = cacheSel('.viewtopic');
		popSel.trigger('click', [subUrl[3]]);
	}

	//Topic with page
	else if(gTo === 'topic' && subUrl[4] !== "") {

		trigPop.removeClass().addClass('pageTop').attr('name', 'top');
		popSel = cacheSel('.pageTop');
		popSel.trigger('change', [subUrl[3] +'-'+ subUrl[4]]);
	}

	//Topic last post
	else if(gTo === 'pid') {

		trigPop.removeClass().addClass('pidPost');
		popSel = cacheSel('.pidPost');
		popSel.trigger('click', [subUrl[3]]);
	}

}

//On load (first 'real') load, popped = true and avoid popstate
popped = ('state' in window.history), initialURL = location.href;

//Event on back/next popstate (except on load)
$(window).bind('popstate', pop);

/* On load */
$(document).ready(function() {
	//Push the url load (popstate)
	if(popped) pop();
});

//after fully load
$(window).load(function() {

	//if gTo = password load form, show resultDb as he will be hidden
	if(gTo === 'lost' || gTo === 'faqtest') resultDb.show();

});