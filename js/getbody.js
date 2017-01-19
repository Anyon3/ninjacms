// On click, show or hide container-body
$(document).off('click','.container-link');
$(document).on('click','.container-link', function(e) {

	//Prevent the search body message to open on rightInfo container
	//On shref true only
	if($('.rightInfo').has(e.target).length > 0 || $(e.target).hasClass('shref')) return;

	if(!$(this).next('article').hasClass('loaded')) {

		$(this).addClass('trigCont');
		$(this).next('article').addClass('loaded').slideDown().prev('section').addClass('loaded');
		return;
	}

	else 
		$(this).next('article').removeClass('loaded').slideUp();
});