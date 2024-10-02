<?php
include "/var/www/html/evaluation/head.php";
?>

<!----------------------------------------PC--------------------------------------------->
	<div style = "background-color:#084897">
        <div class="topMenu" style = "max-width:1720px;">
			<div class="topMenu_in">
				<ul class="menu01">
                    <!-- <li style = "background-color:white;">
                        <a href="/evaluation/introduction.php"><img src="/evaluation/images/head_logo_pc.png" class="pc_img" alt="ABOV" /></a>
                    </li> -->
					<li><a href="/evaluation/introduction.php"><span>인사평가 제도 소개</span></a>
						<!-- <ul class="dept01">
                            <li id="nop"><br></li>
                            <li id="nop"><br></li>
						</ul> -->
					</li>
					<li><a href="/evaluation/mypage.php"><span>나의 인사평가</span></a>
						<!-- <ul class="dept01">
							<li id="nop"><br></li>
							<li id="nop"><br></li>
						</ul> -->
					</li>
					<?php if($_SESSION['sess_grade'] == 1) { //관리자세션 ?>
					<li><span>관리자</span>
						<ul class="dept01">
							<li id="nop"><a href="/evaluation/search_result.php?search_option=user_id&search_query=&user_use1=1">사원 관리</a></li>
							<li id="nop"><a href="/evaluation/eval_upload.php">엑셀 업로드</a></li>
						</ul>
					</li>
					<? } ?>
				</ul>
			</div>
			<?php if(is_null($_SESSION['sess_username'])) { //if($_SESSION[user_grade] == 0)?>
				<div class = "topMenu2">
					<div class = "login"><a href="/evaluation/login.php">Login</a></div>
					<!-- <div class = "join"><a href="/recruit/login/general_conditions.php">Join</a></div> -->
				</div>
			<?} else { ?>
				<div class = "topMenu2">
					<div class = "login"><span style = "color:white;"><? echo $_SESSION['sess_username']?> 님</span></div>
					<div class = "login"><a href="/evaluation/adlogout.php">Logout</a></div>
					<!-- <div class = "join"><a href="/recruit/user/mypage.php">My page</a></div> -->
				</div>
			<? } ?>
        </div>
	</div>
<style>
li {list-style: none; cursor: pointer;}
.topMenu {position: relative; width: 100%; text-align: center; height: 50px; background-color:#084897; margin:0 auto;}
.topMenu:after {content: ""; display: block; clear: both;}
.menu01>ul{z-index: 999;}
.menu01>li {float: left; width: 16%; line-height: 50px;}
.menu01 span {font-size: 18px; font-weight: 600; color:white; font-family:'Noto Sans KR';}      
.dept01 {position: absolute; display: none; width: 16%; padding: 20px 0; background: #fff;z-index:999;}  
#nop {float: none;}  
.none:after {content: ""; display: block; clear: both;}
.topMenu2 {float:right; padding-right:50px; line-height: 50px; font-size: 17px; font-weight: 600;}
.join, .login {float:left;padding-left:50px;}
.login>a, .join>a {color:white;}
</style>

<script>
	$(document).on('mouseover', '.topMenu_in span', function() {
    	$('.dept01').slideDown(300);
	});
	$(document).on('mouseleave', '.topMenu_in', function () {
		$('.dept01').slideUp(300);
	});	
</script>
