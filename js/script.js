$(document).ready(function(){
	$('.scroll_top').click(function(){
		$('html, body').stop(true,true).animate({
			scrollTop: 0
		}, 1000);
		return false;
	});

	$('.scroll_bottom').click(function(){
		$('html, body').stop(true,true).animate({
			scrollTop: 5000
		}, 1000);
		return false;
	});
	// $('.depth03').masonry({
	// 	// options
	// 	itemSelector: '.grid-item',
	// 	columnWidth: 300
	// });

	$('.depth01 > li').mouseover(function(){
		var $this = $(this);
		$(this).find('.depth02').addClass('on');
		// $('.gnb_bg').fadeIn(0);
	});
	$('.depth01 > li').mouseleave(function(){
		var $this = $(this);
		$(this).find('.depth02').removeClass('on');
		$('.gnb_bg').fadeOut(0);
	});
	$('.depth02 > li').mouseover(function(){
		$(this).find('.depth03').addClass('on');
	});
	$('.depth02 > li').mouseleave(function(){
		$(this).find('.depth03').removeClass('on');
	});

	$('.visual_txt li').click(function(){
		$(this).addClass('on');
		$('.visual_txt li').not(this).removeClass('on');
	});

	$('.clfix li').click(function() {
		$(this).addClass('on');
		$('.clfix li').not(this).removeClass('on');
		$(this).parent().find('.inner').stop(true,true).slideToggle();
	});

	$('.visual_txt li:nth-child(1)').click(function(){
		$('.visual_img li:nth-child(1)').addClass('on');
		$('.visual_img li').not('.visual_img li:nth-child(1)').removeClass('on');
	});
	$('.visual_txt li:nth-child(2)').click(function(){
		$('.visual_img li:nth-child(2)').addClass('on');
		$('.visual_img li').not('.visual_img li:nth-child(2)').removeClass('on');
	});
	$('.visual_txt li:nth-child(3)').click(function(){
		$('.visual_img li:nth-child(3)').addClass('on');
		$('.visual_img li').not('.visual_img li:nth-child(3)').removeClass('on');
	});
	$('.visual_txt li:nth-child(4)').click(function(){
		$('.visual_img li:nth-child(4)').addClass('on');
		$('.visual_img li').not('.visual_img li:nth-child(4)').removeClass('on');
	});
	$('.visual_txt li:nth-child(5)').click(function(){
		$('.visual_img li:nth-child(5)').addClass('on');
		$('.visual_img li').not('.visual_img li:nth-child(5)').removeClass('on');
	});
	// var swiper = new Swiper('.swiper-container', {
	// 	slidesPerView: 4,
	// 	spaceBetween: 20,
	// 	pagination: {
	// 		el: '.swiper-pagination',
	// 		clickable: true,
	// 	},
	// 	navigation: {
	// 		nextEl: '.swiper-button-next',
	// 		prevEl: '.swiper-button-prev',
	// 	},
	// 	breakpoints: {
	// 		1025: {
	// 			slidesPerView: 3,
	// 			spaceBetween: 10,
	// 		},
	// 		768: {
	// 			slidesPerView: 2,
	// 			spaceBetween: 9,
	// 		},
	// 	}
	// });


	var numSlide = $('.visual_img li').length;
	var slideNow = 0;
	var slidePrev = 0;
	var slideNext = 0;
	var slideFirst = 1;
	var timerId = '';
	var isTimerOn = true;
	var timerSpeed = 5000;


	showSlide(slideFirst);

	$('.visual_txt li').on('click', function() {
		var index = $('.visual_txt li').index($(this));
		showSlide(index + 1);
		timerId = setTimeout(function() {showSlide(slideNext);}, timerSpeed);
		$(this).addClass('on');
		isTimerOn = false;
	});


	function showSlide(n) {
		clearTimeout(timerId);
		$('.visual_img li').removeClass('on');
		$('.visual_img li:eq(' + (n - 1) + ')').addClass('on');
		$('.visual_txt li').removeClass('on');
		$('.visual_txt li:eq(' + (n - 1) + ')').addClass('on');
		slideNow = n;
		slidePrev = (n <= 1) ? numSlide : (n - 1);
		slideNext = (n >= numSlide) ? 1 : (n + 1);
		//console.log(slidePrev + ' / ' + slideNow + ' / ' + slideNext);
		if (isTimerOn === true) {
			timerId = setTimeout(function() {showSlide(slideNext);}, timerSpeed);
		}
	}


	$('.tab_gnb li').click(function(){
		$('.layer_menu').addClass('on');
		var target_id = $(this).attr("target_id");
		$('.layer_menu').hide();
		$('#'+target_id).show();
		$('#'+target_id).addClass('on');
	});
	$('#header .nav_btn').click(function(){
		$('.layer_menu').addClass('on');
	});
	$('.layer_2dep ul li').click(function(){
		$('.layer_3dep').addClass('on');
	});
	$('.layer_3dep ul li').click(function(){
		$('.layer_4dep').addClass('on');
	});
	$('.layer_menu .back').click(function(){
		$(this).parent().parent().removeClass('on');
	});
	$('.layer_menu .layer_close').click(function(){
		$('.layer_menu').show();
		$('.layer_menu .layer_close').parent().parent().removeClass('on');
		$('.layer_menu').removeClass('on');
	});

	$('.sub_menu_top').click(function(){
		$('.sub_menu').removeClass('on');
		$('#sub_contents').removeClass('on');
		$('.location').addClass('on');
	});
	$('.sub_menu_top_tab').click(function(){
		$('.sub_menu').removeClass('on_tab');
		$('.sub_menu_bg').fadeOut();
	});

	$('.sub_menu_btn').click(function(){
		$('.sub_menu').addClass('on');
		$('#sub_contents').addClass('on');
		$('.location').removeClass('on');
	});
	$('.sub_menu_btn_tab').click(function(){
		$('.sub_menu').addClass('on_tab');
		$('.sub_menu_bg').fadeIn();
	});

	var winW = $(window).width();
	if(winW < 1025){
		$('.sub_menu').removeClass('on');
		$('#sub_contents').removeClass('on');
		$('.location').addClass('on');
	}
	if(winW > 1025){
		$('.sub_menu').addClass('on');
		$('#sub_contents').addClass('on');
		$('.location').removeClass('on');
	}
	$(window).resize(function(){
		winW = $(window).width();
		if(winW < 1024){
			$('.sub_menu').removeClass('on');
			$('#sub_contents').removeClass('on');
			$('.location').addClass('on');
		}
		if(winW > 1024){
			$('.sub_menu').addClass('on');
			$('.sub_menu').removeClass('on_tab');
			$('#sub_contents').addClass('on');
			$('.location').removeClass('on');
			$('.sub_menu_bg').fadeOut();
		}
	});

	//추가 201222------------------------------------------------------
	$('.mcu_con02 ul li em').click(function(){
		$(this).find('span').toggleClass('on');
		$(this).parent().find('.edit_wrap').stop(true,true).slideToggle();
	});
	$('.mcu_con03 ul li em').click(function(){
		$(this).find('span').toggleClass('on');
		$(this).parent().find('.edit_wrap').stop(true,true).slideToggle();
	});
	$('.tab_menu_mob em').click(function(){
		$(this).toggleClass('on');
		$(this).parent().find('ul').stop(true,true).slideToggle();
	});


	var cnt = 0;
	$('#section01 .sec01_con').hide().eq(0).show();
	$('#section01 .sec01_btn ul li').click(function() {
		cnt = $(this).index()
		$('#section01 .sec01_btn ul li').removeClass('on').eq(cnt).addClass('on');
		$('#section01 .sec01_con').hide().eq(cnt).fadeIn();
	});

	var cnt = 0;
	$('.sub0402_2_wrap .sec02_con').hide();
	$('.sub0402_2_wrap .sec01_con').hide().eq(0).show();
	$('.sub0402_2_wrap .sub0402_2_btn .tr02 td a').removeClass('on').eq(0).addClass('on');
	$('.sub0402_2_wrap .sub0402_2_btn .button').click(function() {
		cnt = $(this).index();
		if($(this).parent().attr('class') == 'tr03') {
			cnt -= 1;
			if($('.sub0402_2_wrap .sec02_con').eq(cnt).css('display') == 'block')
				return;
			$('.sub0402_2_wrap .sec01_con').hide();
			$('.sub0402_2_wrap .sub0402_2_btn .tr02 td a').removeClass('on');
			$('.sub0402_2_wrap .sub0402_2_btn .tr03 td a').removeClass('on').eq(cnt).addClass('on');
			$('.sub0402_2_wrap .sec02_con').hide().eq(cnt).fadeIn();
		}
		else {
			if($('.sub0402_2_wrap .sec01_con').eq(cnt).css('display') == 'block')
				return;
			$('.sub0402_2_wrap .sec02_con').hide();
			$('.sub0402_2_wrap .sub0402_2_btn .tr03 td a').removeClass('on');
			$('.sub0402_2_wrap .sub0402_2_btn .tr02 td a').removeClass('on').eq(cnt).addClass('on');
			$('.sub0402_2_wrap .sec01_con').hide().eq(cnt).fadeIn();
		}
	});

	var itcdelay = (function () {
		// Function
		var itcTimer = 0;
		return function (callback, ms) {
			clearTimeout(itcTimer);
			itcTimer = setTimeout(callback, ms);
		};
	})();
	// jQuery Code
	$('.unifiedInputSearch').keyup(function () {
		itcdelay(function () {
			Search_result()
		}, 500);
	});
});



