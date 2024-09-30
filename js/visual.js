var $jj = jQuery.noConflict();

$jj(document).ready(function() {
    // pngfix for msie6
    if ($jj.browser.msie && + $jj.browser.version == 6) $jj(function() {
        DD_belatedPNG.fix("img, .pngbg");
        $jj("#gnb").addClass("pngbg");
    });

    // gnb
    initGnb();

    // snb
    initSnb();

    // 이미지 경로 바꾸기
    // img.imgSrcToggle({ orgSrc: 'on.gif', newSrc:'off.gif' });
    $jj.fn.imgSrcToggle = function(settings) {
        var config = {
            orgSrc : '_off', newSrc : '_on'
        };
        if (settings) $jj.extend(config, settings);
        this.each(function() {
            if($jj(this).attr('src')){
                $jj(this).attr('src',$jj(this).attr('src').replace(config.orgSrc, config.newSrc));
            }
        });
    };

// 이미지 롤 오버
    // img.imgRollOver({ orgSrc: 'on.gif', newSrc:'off.gif' });
    $jj.fn.imgRollOver = function(settings) {
        var config = {
            orgSrc : '_off', newSrc : '_on'
        };
        if (settings) $jj.extend(config, settings);
        this.each(function() {
            $jj(this).bind('mouseenter',function() {
                if ($jj(this).attr('src') && $jj(this).attr('src').match(config.orgSrc)) {
                    $jj(this).attr('src', $jj(this).attr('src').replace(config.orgSrc, config.newSrc));
                    $jj(this).bind('mouseleave',function() {
                        $jj(this).attr('src', $jj(this).attr('src').replace(config.newSrc, config.orgSrc));
                    });
                }
            });
        });
    };
    $jj('img.rollover, .rollover img, .gnb li a img, div.target-service ul.toggle img, div.link-service img, #modeMenu img').imgRollOver({ orgSrc: '_off', newSrc: '_on' });

    // input box 스타일링
    $jj('input[type=text], input[type=password]').addClass('text');
    $jj('input[type=file]').addClass('file');
    $jj('input[type=image]').addClass('image');
    $jj('input[type=submit]').addClass('button');
    $jj('input[type=checkbox]').addClass('check');
    $jj('input[type=radio]').addClass('radio');
    $jj('textarea').addClass('textarea');

    $jj('input[type=text], input[type=password], textarea').focusin(function(){
        $jj(this).addClass('focus_style');
    });
    $jj('input[type=text], input[type=password], textarea').focusout(function(){
        $jj(this).removeClass('focus_style');
    });

    // login_layer, family_site toggle
    $jj('a.login_open').click(function(){
        $jj('.login_layer').show();
    });
    $jj('a.login_close').click(function(){
        $jj('.login_layer').hide();
    });

    // family_site
    $jj('a.family_open').click(function(){
        $jj('.family_site').show();
    });
    $jj('.family_close a').click(function(){
        $jj('.family_site').hide();
    });

    //테이블 더보기
    $jj("table.ocean tr:gt(10)").hide();
    $jj('div.btn_cell_more a').bind('click', function(event){
        event.preventDefault();
        $jj("table.ocean tr:gt(10)").toggle();
        $jj(this).children('img').imgSrcToggle({ orgSrc: 'btn_close', newSrc:'btn_more2' });

        if($jj("table.ocean tr:gt(10)").is(':visible')){
            $jj(this).children('img').imgSrcToggle({ orgSrc: 'btn_more2', newSrc:'btn_close' });
        }

    });

//main
    $jj('ul.link_section > li').bind('mouseenter', function(){
        var ban_obj = $jj(this).attr('id');

        if($jj(this).children('div.on').is(':visible')){
            $jj(this).children('div.on').css({"opacity":"1"});
        }else{
            $jj(this).children('div.on').css({"opacity":"0"});
        }
        $jj(this).children('div.on').stop(true , true).animate({"opacity":"1"},"fast").show();
        $jj(this).children('div.off').stop(true , true).animate({"opacity":"0"},"fast").hide();
        $jj(this).siblings().children('div.off').stop(true , true).animate({"opacity":"1"},"fast").show();
        $jj(this).siblings().children('div.on').stop(true , true).animate({"opacity":"0"},"fast").hide();

        $jj('div.banner_section > div').each(function(){
            if($jj(this).attr('id') ==(ban_obj)){
                $jj(this).show();
            }else{
                $jj(this).hide();
            }
        });
    });

    $jj('ul.link_section > li.link_a').bind('mouseenter', function(){
        $jj('.top h3 img').attr('src','/img/front/main/h3_reserve.gif');
    });

    $jj('ul.link_section > li.link_b').bind('mouseenter', function(){
        $jj('.top h3 img').attr('src','/img/front/main/h3_reserve.gif');
    });

    $jj('ul.link_section > li.link_c').bind('mouseenter', function(){
        $jj('.top h3 img').attr('src','/img/front/main/h3_info.gif');
    });

    /*var c8 = $jj('ul.link_section > li:nth-child(2) ul li:first-child a');

    $jj('ul.link_section > li:nth-child(2)').bind('mouseenter', function(){
        $jj(c8).addClass('current');
        $jj(c8).parent().siblings().children().removeClass('current');
        $jj('.ban_b').show();
        $jj('.ban_b1,.ban_b2,.ban_b3').hide();
    });

    $jj('ul.link_section > li:nth-child(1),ul.link_section > li:nth-child(3)').bind('mouseenter', function(){
        $jj('.ban_b,.ban_b1,.ban_b2,.ban_b3').hide();
    });*/

    $jj('ul.link_section > li:nth-child(2) ul li a').click(function(){
        $jj(this).addClass('current');
        $jj(this).parent().siblings().children().removeClass('current');
    });

    /* 2013-04-17 하단의 4개로 변경
        $jj('ul.link_section > li:nth-child(2) ul li:nth-child(1) a,ul.link_section > li:nth-child(2) ul li:nth-child(3) a').click(function(){
            $jj('.check_set_resort').show();
            $jj('.check_set_hotel').hide();
        });
        $jj('ul.link_section > li:nth-child(2) ul li:nth-child(2) a,ul.link_section > li:nth-child(2) ul li:nth-child(4) a').click(function(){
            $jj('.check_set_hotel').show();
            $jj('.check_set_resort').hide();
        });
    */
    // 리조트 패키지
    $jj('ul.link_section > li:nth-child(2) ul li:nth-child(1) a').click(function(){
        // 리조트 패키지 사업장 리스트 = 보임
        $jj('.check_set_resort').show();
        // 호텔 패키지 사업장 리스트
        $jj('.check_set_hotel').hide();
        // 리조트 이벤트 사업장 리스트
        $jj('.check_set_eve_resort').hide();
        $jj('.check_set_eve_hotel').hide();
    });
    // 호텔 패키지
    $jj('ul.link_section > li:nth-child(2) ul li:nth-child(2) a').click(function(){
        // 리조트 패키지 사업장 리스트
        $jj('.check_set_resort').hide();
        // 호텔 패키지 사업장 리스트 = 보임
        $jj('.check_set_hotel').show();
        // 리조트 이벤트 사업장 리스트
        $jj('.check_set_eve_resort').hide();
        // 호텔 이벤트 사업장 리스트
        $jj('.check_set_eve_hotel').hide();
    });
    // 리조트 이벤트
    $jj('ul.link_section > li:nth-child(2) ul li:nth-child(3) a').click(function(){
        // 리조트 패키지 사업장 리스트
        $jj('.check_set_resort').hide();
        // 호텔 패키지 사업장 리스트
        $jj('.check_set_hotel').hide();
        // 리조트 이벤트 사업장 리스트 = 보임
        $jj('.check_set_eve_resort').show();
        // 호텔 이벤트 사업장 리스트
        $jj('.check_set_eve_hotel').hide();
    });
    // 호텔 이벤트
    $jj('ul.link_section > li:nth-child(2) ul li:nth-child(4) a').click(function(){
        // 리조트 패키지 사업장 리스트
        $jj('.check_set_resort').hide();
        // 호텔 패키지 사업장 리스트
        $jj('.check_set_hotel').hide();
        // 리조트 이벤트 사업장 리스트
        $jj('.check_set_eve_resort').hide();
        // 호텔 이벤트 사업장 리스트 = 보임
        $jj('.check_set_eve_hotel').show();
    });


    $jj('div.right_link ul.tab li.ban_link a').bind('click', function(event){
        event.preventDefault();
        var link_obj  = $jj(this).attr('href').replace('#','');

        $jj(this).children('img').imgSrcToggle({ orgSrc: '_off', newSrc:'_on' });
        $jj(this).parents().siblings().children().children('img').each(function(){
            $jj(this).imgSrcToggle({ orgSrc: '_on', newSrc:'_off' });
        });

        if($jj('ul#ban2').is(':hidden')){
            $jj('div.sidebg').addClass('ban2_bg');
        }else{
            $jj('div.sidebg').removeClass('ban2_bg');
        }

        $jj('div.sidelist ul.ban').each(function(){
            if($jj(this).attr('id').match(link_obj)){
                $jj(this).show();
            } else {
                $jj(this).hide();
            }
        });

    });

    //language
    $jj('div.util ul li:nth-child(5)').click(function(){
        $jj('div.lang').slideToggle('fast');
    });


    // family site
    /*
    $jj(".openlayer").click(function(){
        $jj(".familySite .box,.familySite_main .box").animate({"opacity":"1"},"fast").show();
        $jj(".liner li:first .view").animate({"opacity":"1"});
        return false;
    });
    $jj(".familySite .close,.familySite_main .close").click(function(){
        $jj(".familySite .box,.familySite_main .box").animate({"opacity":"0"},"fast").hide();
        $jj(".fm").removeClass("on");
        $jj(".liner li .view").animate({"opacity":"0"});
        $jj(".liner li:first a").addClass("on");
        $jj(".liner li:first .view").animate({"opacity":"1"});
        return false;
    });

    $jj(".fm").click(function(){
        $jj(".fm").removeClass("on");
        $jj(this).addClass("on");
        $jj(".view").animate({"opacity":"0"});
        $jj(this).next().animate({"opacity":"1"});
        return false;
    });
    */
    $jj(".openlayer").click(function(){
        $jj(".familySite .box,.familySite_main .box").animate({"opacity":"1"},"fast").show();
        //$jj(".liner li:first .view").animate({"opacity":"1"});
        return false;
    });
    $jj(".familySite .close,.familySite_main .close").click(function(){
        $jj(".familySite .box,.familySite_main .box").hide();
        $jj(".fm").removeClass("on");
        $jj(".liner li .view").hide();
        $jj(".liner li:first a").addClass("on");
        $jj(".liner li:first .view").show();
        return false;
    });

    $jj(".familySite_main .fm,.familySite .fm").click(function(){
        $jj(".familySite_main .on,.familySite .on").next('.view').show();
        $jj(this).next('.view').show();
        $jj(this).parent().siblings().children('.view').hide();
        $jj(this).addClass("on");
        $jj(this).parent().siblings().children().removeClass("on");
        return false;
    });
    $jj(".familySite_main ul:nth-child(1) .fm,.familySite ul:nth-child(1) .fm").click(function(){
        $jj('.familySite_main ul:nth-child(2),.familySite ul:nth-child(2)').children().children('a').removeClass("on");
        $jj('.familySite_main ul:nth-child(2),.familySite ul:nth-child(2)').children().children('.view').hide();
    });
    $jj(".familySite_main ul:nth-child(2) .fm,.familySite ul:nth-child(2) .fm").click(function(){
        $jj('.familySite_main ul:nth-child(1),.familySite ul:nth-child(1)').children().children('a').removeClass("on");
        $jj('.familySite_main ul:nth-child(1),.familySite ul:nth-child(1)').children().children('.view').hide();
    });


    //layer_popup
    /*$jj('span[class*=btn] a , [class*=state] a , [class*=subject] a , a.layer , input.layer').bind('click', function(event){
        event.preventDefault();
        var layer_obj = $jj(this).attr('href').replace('#','');*/

    //layer_popup 2013-02-26 수정
    $jj('[class*=state] a , [class*=subject] a , a.layer , input.layer').bind('click', function(event){
        event.preventDefault();
        var layer_obj = $jj(this).attr('href').replace('#','');

        $jj('.link_site').css('z-index','-1');
        $jj('.util').css('z-index','-1');
        $jj('h1').css('z-index','-1');
        $jj('.visual').css('z-index','-10');
        $jj('#gnb').css('z-index','-10');
        $jj('#footer').css('z-index','-6');

        if(this.id == 'mms_send' || this.id == 'email_send'){
            var frm = document.frm;
            var cnt = 0;
            if(typeof(frm.chkCoupon) == 'undefined' || frm.chkCoupon.length < 1 ){
                return;
            }

            if(frm.chkCoupon.length > 1) {
                for (i=0; i<frm.chkCoupon.length ; i++)
                {
                    if(frm.chkCoupon[i].checked == true) {
                        cnt++;
                    }
                }
            }

            if(cnt == 0) {
                return false;
            }

        }
        $jj('div.popup').each(function(){
            if($jj(this).attr('id').match(layer_obj)){
                $jj(this).parent().css({height:$jj('window').height()});
                $jj(this).parent().fadeIn('fast');
                $jj(this).show();
                $jj(this).css({marginTop:- + $jj(this).height()/2});
            } else {
                $jj(this).hide();
            }
        });
    });

    $jj('div.popup a.btn_close, div.popup div.btn_ok span a').bind('click', function(){
        $jj('div.layer_bg').fadeOut('fast');

        $jj('.link_site').css('z-index','12');
        $jj('.util').css('z-index','11');
        $jj('h1').css('z-index','11');
        $jj('.visual').css('z-index','3');
        $jj('#gnb').css('z-index','10');
        $jj('#footer').css('z-index','6');
    });

    //사이드메뉴
    var w_body = $jj('body').width();
    $jj(window).resize(function(){
        if ( $jj(window).width() < 1225) {
            $jj('.tab li a').toggle(function(){
                $jj('.right_link, .sidebg, .sidemenu').css('right','0px');
            }, function(){
                $jj('.right_link, .sidebg, .sidemenu').css('right','-190px');
            });
        }
    });

    //고객센터 FAQ

    $jj('div.faq_list ul.category li a').live('click', function(event){
        event.preventDefault();
        var faq_obj = $jj(this).attr('href').replace('#','');
        $jj(this).parent().addClass('current');
        $jj(this).parent().siblings().removeClass('current');

        $jj('div.faq_list ul.con').each(function(){
            if($jj(this).attr('id').match(faq_obj)){
                $jj(this).show();
            } else {
                $jj(this).hide();
            }
        });
    });

    $jj('div.faq_list ul.con li').live('mouseenter', function(event){
        $jj(this).addClass('current');
        $jj(this).find('img').attr('src', $jj(this).find('img').attr('src').replace('_off', '_on'));

    });
    $jj('div.faq_list ul.con li').live('mouseleave', function(event){
        $jj(this).removeClass('current');
        $jj(this).find('img').attr('src', $jj(this).find('img').attr('src').replace('_on', '_off'));
    });


    //FAQ
    $jj(".faq_type01 dt").click(function(){
        var count =1;
        if(count == 1){
            $jj(".faq_type01 dt").removeClass("current");
            $jj(".faq_type01 dt").next().css("display","block");
            $jj(".faq_type01 dd").css("display","none");
            $jj(this).addClass("current");
            $jj(this).next().css("display","block");
            count = 0;
        }else{
            $jj(".faq_type01 dt").removeClass("current");
            $jj(".faq_type01 dd").css("display","none");
            count = 1;
        }
    });

    // login_layer
    $jj('.id-label, .pass-label').animate({ opacity: "0.4" })
        .click(function() {
            var thisFor	= $jj(this).attr('for');
            $jj('.'+thisFor).focus();
        });
    $jj('.layer_id').focus(function() {
        $jj('.id-label').animate({ opacity: "0" }, "fast");
        if($jj(this).val() == "layer_id")
            $jj(this).val() == "";
    }).blur(function() {
        if($jj(this).val() == "") {
            $jj(this).val() == "layer_id";
            $jj('.id-label').animate({ opacity: "0.4" }, "fast");
        }
    });
    $jj('.layer_pass').focus(function() {
        $jj('.pass-label').animate({ opacity: "0" }, "fast");
        if($jj(this).val() == "layer_pass") {
            $jj(this).val() == "";
        }
    }).blur(function() {
        if($jj(this).val() == "") {
            $jj(this).val() == "layer_pass";
            $jj('.pass-label').animate({ opacity: "0.4" }, "fast");
        }
    });

    // gnb

    function initGnb() {
        $jj('#gnb > ul > li').bind('mouseenter focusin',function(){
            $jj(this).children().show();
            $jj('.sidemenu').css('z-index','1');
            $jj(this).siblings().children('.sub_wrap').hide();
            //$jj('#gnb').css({'background':'#fff','height':'454px','border-bottom':'1px solid #d3d3d3'});
            //$jj('.gnb_menu > li').css('padding-bottom','390px');
            // IE6 width 100%
            if(navigator.userAgent.match('MSIE 6')) {
                var winw = document.documentElement.offsetWidth - 17;
                $jj(this).find('.sub_wrap').css('width',winw);
            }
            $jj(this).addClass('on');
            //$jjobj = $jj(this).find('a > img');
            $jj('#gnb li.on .subinfo').show();
        });
        $jj('#gnb > ul > li').bind('mouseleave',function(){
            $jj(this).removeClass('on');
            $jj('#gnb .sub_wrap').hide();
            $jj('.sidemenu').css('z-index','11');
            $jj('#gnb').css({'background':'none','height':'80px','border-bottom':'none'});
            $jj('.gnb_menu > li').css('padding-bottom','13px');
            $jj('#gnb li .subinfo').hide();
        });
    }
    /*
    $jj('.m6').focusin(function(){
        $jj('.sub_wrap').hide();
    });
    */
    // snb
    function initSnb() {
        $jj('#snb ul').hide();
        //$jj('#snb a').focus(function() {$jj(this).blur();});
        $jj('#snb li.current ul').slideDown('slow');
        $jj('#snb li > a').click(function(){
            //$jj(this).parent().siblings().find('ul').hide();
            $jj(this).parent().siblings().removeClass('current');
            //$jj(this).siblings().slideDown();
            $jj(this).parent().addClass('current');
        });

        $jj('#snb li > a').click(function(){
            $jj(this).parent().siblings().find('ul').hide();
            $jj(this).parent().siblings().removeClass('current');
            $jj(this).siblings().slideDown('slow');
            $jj(this).parent().addClass('current');
        });
    }

//리조트 선택
    $jj('div.resort div.depth2').hide();
    $jj('div.resort ul.depth1 li a').bind('click', function(event){
        event.preventDefault();
        var r_obj = $jj(this).attr('href').replace('#','');

        $jj('div.resort ul.depth1 li.current div').show();
        $jj(this).parent().siblings().removeClass('current');
        $jj(this).parent().addClass('current');

        $jj('div.resort_detail').each(function(){
            if($jj(this).attr('id') ==(r_obj)){
                $jj(this).show();
            } else {
                $jj(this).hide();
            }
        });

    });

    $jj('div.resort ul.depth1 li a').click(function(){
        $jj(this).parent().siblings().find('div').hide();
        $jj(this).parent().siblings().removeClass('current');
        $jj(this).siblings().show();
        $jj(this).parent().addClass('current');
    });

    //link_site
    $jj('div.link_site div.link_select').bind('mouseenter focusin',function(){
        $jj('ul.link_list').show();
    });
    $jj('ul.link_list').bind('mouseleave',function(){
        $jj(this).hide();
    });
    $jj('#header h1 a, .r_site li:last-child').bind('focusin',function(){
        $jj('ul.link_list').hide();
    });


//family tab
    $jj('.family_site ul li > a').bind('click', function(){
        var obj = $jj(this).attr('href').replace('#','');

        $jj(this).parent().siblings().removeClass('current');
        $jj(this).parent().addClass('current');
        $jj(this).next().each(function(){
            if($jj(this).attr('id').match(obj)){
                $jj(this).show();
            } else {
                $jj(this).hide();
            }
        });
    });


    //tabtoggle
    $jj('.event_tab ul li > a, .map_tab').bind('click', function(event){
        var tab_togg = $jj(this).attr('href').replace('#','');
        $jj(this).parent().addClass('current');
        $jj(this).parent().siblings().removeClass('current');

        $jj('div.tab_con').each(function(){

            if($jj(this).attr('id').match(tab_togg)){
                $jj(this).show();
            } else {
                $jj(this).hide();
            }
        });
    });

    $jj('.map_tab').click(function(){
        $jj('.event_tab ul li').siblings().removeClass('current');
    });


    //popup 홀
    $jj('.w100 li a').bind('click',function(){
        $jj(this).parent().addClass('current');
        $jj(this).parent().siblings().removeClass('current');
    });

    $jj('.w100 li:nth-child(1) a').click(function(){
        $jj('.hall_img img:nth-child(1)').addClass('view');
        $jj('.hall_img img:nth-child(1)').siblings().removeClass('view');
    });

    $jj('.w100 li:nth-child(2) a').click(function(){
        $jj('.hall_img img:nth-child(2)').addClass('view');
        $jj('.hall_img img:nth-child(2)').siblings().removeClass('view');
    });

    $jj('.w100 li:nth-child(3) a').click(function(){
        $jj('.hall_img img:nth-child(3)').addClass('view');
        $jj('.hall_img img:nth-child(3)').siblings().removeClass('view');
    });

    $jj('.w100 li:nth-child(4) a').click(function(){
        $jj('.hall_img img:nth-child(4)').addClass('view');
        $jj('.hall_img img:nth-child(4)').siblings().removeClass('view');
    });

    //
    var close = $jj('a.btn_close');
    $jj(close).attr('href','#none');

    //qrv 맵
    $jj('ul.depth1 li:nth-child(1)').click(function(){
        $jj('.thum_del').show();
        $jj('.thum_del').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(2)').click(function(){
        $jj('.thum_viv').show();
        $jj('.thum_viv').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(3)').click(function(){
        $jj('.thum_sono').show();
        $jj('.thum_sono').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(4)').click(function(){
        $jj('.thum_yp').show();
        $jj('.thum_yp').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(5)').click(function(){
        $jj('.thum_gj').show();
        $jj('.thum_gj').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(6)').click(function(){
        $jj('.thum_dy').show();
        $jj('.thum_dy').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(7)').click(function(){
        $jj('.thum_sol').show();
        $jj('.thum_sol').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(8)').click(function(){
        $jj('.thum_jj').show();
        $jj('.thum_jj').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(9)').click(function(){
        $jj('.thum_bs').show();
        $jj('.thum_bs').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(10)').click(function(){
        $jj('.thum_geo').show();
        $jj('.thum_geo').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(11)').click(function(){
        $jj('.thum_ys').show();
        $jj('.thum_ys').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(12)').click(function(){
        $jj('.thum_kin').show();
        $jj('.thum_kin').siblings().hide();
    });
    $jj('ul.depth1 li:nth-child(13)').click(function(){
        $jj('.thum_ch').show();
        $jj('.thum_ch').siblings().hide();
    });

    // 객실예약 갤러리
    $jj('.rv_gal li a').bind('click',function(){
        $jj(this).prev('.ab').fadeIn('fast');
        $jj(this).parent().siblings().children('.ab').fadeOut('fast');
        $jj(this).after('<div class="on_frame"></div>');
        $jj(this).parent().addClass('on');
        $jj(this).parent().siblings().removeClass('on');
    });
    $jj('.rv_gal li.on a').after('<div class="on_frame"></div>');



    //quick_login
    $jj('#t1').click(function(){
        $jj('.quick_login').show();
    });
    $jj('.quick_login a.btn_close').click(function(){
        $jj('.quick_login').hide();
    });

    //단체행사
    $jj('.banquet_map').click(function(){
        $jj('.right_imgbox').css('overflow','visible');
        $jj('.right_imgbox ul').hide();
        $jj('.right_map').show();
        $jj('.right_map img').show();
    });
    $jj('.map_x').click(function(){
        $jj('.right_map').hide();
        $jj('.right_map img').hide();
        $jj('.right_imgbox ul').show();
        $jj('.right_imgbox').css('overflow','hidden');
    });

    $jj('.right_map_right').click(function(){
        $jj('.right_imgbox ul li:first').appendTo('.right_imgbox ul');
    });
    $jj('.right_map_left').click(function(){
        $jj('.right_imgbox ul li:last').prependTo('.right_imgbox ul');
    });

    var standard = $jj('.cal_table_a td').height();
    $jj('.cal_table_b td').css('height',standard);

    $jj('.mem_use_tab li a').click(function(){
        var tab_li = $jj(this).attr('class');
        $jj('div.'+tab_li).show();
        $jj('div.'+tab_li).siblings('div').hide();
        $jj('div.'+tab_li).siblings('div.tab_wrap').show();
        $jj(this).parent().addClass('current');
        $jj(this).parent().siblings().removeClass('current');
    });

    var w_width = $jj('html body').width(),
        l_margin = (w_width / 2) - 120,
        nomore = $jj('.nomore').css('margin-left',l_margin);
    $jj(window).resize(function(){
        var w_width = $jj('html body').width(),
            l_margin = (w_width / 2) - 120;
        nomore.unbind();
        $jj('.nomore').css('margin-left',l_margin);

    });

    //트위터로 질문하기
    $jj('.btn_tqst').click(function(){
        $jj('.t_qst').fadeIn('fast');
        $jj('.t_qst').css('z-index','400');
        $jj('.link_site').css('z-index','1');
        $jj('.util').css('z-index','-1');
        $jj('h1').css('z-index','-1');
        $jj('.visual').css('z-index','-10');
        $jj('#gnb').css('z-index','-10');
        $jj('#footer').css('z-index','-6');
    });
    $jj('.t_pop a').click(function(){
        $jj('.t_qst').fadeOut('fast');
        $jj('.t_qst').css('z-index','0');
        $jj('.link_site').css('z-index','12');
        $jj('.util').css('z-index','11');
        $jj('h1').css('z-index','11');
        $jj('.visual').css('z-index','3');
        $jj('#gnb').css('z-index','10');
        $jj('#footer').css('z-index','6');
    });

    //기업소개, 연혁 탭
    $jj('ul.his_tab li a').click(function(){
        alert(0);
        //$jj(this).parent().addClass('current');
        //$jj(this).parent().siblings().removeClass('current');
    });

    var w_width = $jj('html body').width(),
        Lmargin = 1259 - w_width,
        r_cl =  $jj('.lr_btns .right_btn').click(function(){
            $jj('.abslider').animate({marginLeft: -Lmargin},300);
        }),
        l_cl =  $jj('.lr_btns .left_btn').click(function(){
            $jj('.abslider').animate({marginLeft:'0'},300);
        });


    if(w_width < 1259){
        $jj('.lr_btns').show();
    };
    if(w_width > 1259){
        $jj('.lr_btns').hide();
        $jj('.abslider').css('margin','0 auto');
    };

    $jj(window).resize(function(){
        var wy_width = $jj('html body').width(),
            Lymargin = 1259 - wy_width;
        if(wy_width < 1259){
            $jj('.lr_btns').show();
            $jj('.abslider').css('margin','0');
        };
        if(wy_width > 1259){
            $jj('.lr_btns').hide();
            $jj('.abslider').css('margin','0 auto');
        };
        $jj(r_cl).unbind();
        $jj(l_cl).unbind();
        $jj('.lr_btns .right_btn').click(function(){
            $jj('.abslider').animate({marginLeft: -Lymargin},300);
        });
        $jj('.lr_btns .left_btn').click(function(){
            $jj('.abslider').animate({marginLeft:'0'},300);
        });
    });

});

/*$jj(document).ready(function() {
	// right layer resize
	var bHeight = $jj("body").height();
	var layerHeight = $jj(".layerCnt");

	if(bHeight < 850){
		layerHeight.css({"height":"850px"});
	}else{
		layerHeight.height(bHeight - 45);
	}

	// right layer tab
	$jj(".tab a").click(function(){
		$jj(".tab a").removeClass("on");
		$jj(this).addClass("on");
	});

	// tabBox tab
	$jj(".tabBox a").click(function(){
		$jj(this).parent().addClass("current");
		$jj(this).parent().siblings().removeClass("current");
		return false;
	});


});*/


function btnLayer(num){

    var boxlayer = $jj("#movLayer");

    if($jj('#layerTab'+num).css("display")=="block"){
        boxlayer.stop().animate({"margin-right":"-380px"},'easeInOutBack', function(){
            $jj(".vlayer").css({"display":"none"});
            $jj(".tab a").removeClass("on");
        });
    }else{
        if($jj('#layerTab1').css("display")=="none" && $jj('#layerTab2').css("display")=="none"){
            boxlayer.stop().animate({"margin-right":"0"},'easeInOutElastic');
            $jj("#layerTab"+num).css({"display":"block"});
        }else{
            boxlayer.stop().animate({"margin-right":"-380px"},'easeInOutBack', function(){
                $jj(".vlayer").css({"display":"none"});
                boxlayer.stop().animate({"margin-right":"0"},'easeInOutElastic');
                $jj("#layerTab"+num).css({"display":"block"});
            });
        }
    }
}

var $ = jQuery.noConflict();