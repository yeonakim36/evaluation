<?php
include "./head.php";
?>

<!----------------------------------------PC--------------------------------------------->
	<div style = "background-color:#084897">
        <div class="topMenu" style = "max-width:1720px;">
			<div class="topMenu_in">
				<ul class="menu01">
					<li><a href="./introduction.php"><span>인사평가 제도 소개</span></a></li>
					<li><a href="./mypage.php"><span>나의 인사평가</span></a></li>
					<?php if($_SESSION['sess_grade'] > 0) { ?>
						<li><a href="./management.php"><span>조직인사평가</span></a></li>
					<? } ?>
					<li><a href="./myeducation.php"><span>AEMS(나의 교육)</span></a></li>
					<?php if($_SESSION['sess_grade'] == 1) { ?>
					<li><span>APEC관리</span>
						<ul class="dept01">
							<li id="nop"><a href="./search_result.php?search_option=user_id&search_query=&user_use1=1">사원 관리</a></li>
							<li id="nop"><a href="./eval_upload.php">엑셀 업로드</a></li>
							<li id="nop"><br></li>
						</ul>
					</li>
					<? } ?>
					<?php if($_SESSION['sess_manage'] == 1) { ?>
					<li><span>AEMS관리</span>
						<ul class="dept01">
							<li id="nop"><a href="./edu_manage.php">교육과정 관리</a></li>
							<li id="nop"><a href="./edu_user_list.php">교육활동 관리</a></li>
							<li id="nop"><a href="./edu_user_manage.php">사원관리</a></li>
						</ul>
					</li>
					<? } ?>
				</ul>
			</div>
			<?php if(is_null($_SESSION['sess_username'])) { //if($_SESSION[user_grade] == 0)?>
				<div class = "topMenu2">
					<div class = "login"><a href="./login.php">Login</a></div>
					<!-- <div class = "join"><a href="/recruit/login/general_conditions.php">Join</a></div> -->
				</div>
			<?} else { ?>
				<div class = "topMenu2">
					<div class = "login"><span style = "color:white;"><? echo $_SESSION['sess_username']?> 님</span></div>
					<div class = "login"><a href="./adlogout.php">Logout</a></div>
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
.menu01>li {float: left; width: 13%; line-height: 50px;}
.menu01 span {font-size: 17px; font-weight: 600; color:white; font-family:'Noto Sans KR';}      
.dept01 {position: absolute; display: none; width: 13%; padding: 20px 0; background: #fff;z-index:999;}  
#nop {float: none; font-size:15px;}  
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
