<?php
    include "./gnb.php";

	if(!$_SESSION['sess_userid']) {
		?>
			<script>
				location.replace("index.php");
			</script>
		<?
		exit;
	}
?>
<!DOCTYPE html>
<html>
<!-- <body> -->
	<div style = "background-image:url(/evaluation/images/main_background.jpg);height:1000px;margin:0 auto;background-size: cover;">
		<div>
			<h3 style = "text-align:center; font-size:36px; padding-top:125px; margin-bottom:125px; color:white;margin-top:0px;">
				성과와 실력중심의 인사제도로<br>조직의 목표달성과 구성원의 성장을 지향합니다.
			</h3>
		</div>
		<div style = "text-align:center; background-color:#b1aeae45; width:100%; padding-top:35px; padding-bottom:35px; font-size:30px; color:white; border-radius:23px;">
			"실력X성과"에 기반한 인사제도 <br> 성과평가와 역량평가 점수를 종합하여 임금 및 승진에 반영
		</div>
		<div style = "text-align:center; background-color:#b1aeae45; width:100%; padding-top:35px; padding-bottom:35px; font-size:30px; color:white; border-radius:23px;margin-top:30px;margin-bottom:30px;">
			성과에 따른 승진제도 운영 <br> 인사평가 결과에 따른 승진누적포인트 기반 직급체계 운영
		</div>
		<div style = "text-align:center; width:100%; padding-top:50px; padding-bottom:50px; font-size:35px; color:white; border-radius:23px;margin-top:30px;margin-bottom:30px;">
			<img src="/evaluation/images/rank.png" alt="" style = "width:60%;margin:auto;display:block;padding-bottom:45px;"/>
		</div>
	<!-- <img src="/evaluation/images/eval_main.png" alt="" style = "width:60%;margin:auto;display:block;padding-top:45px;padding-bottom:45px;"/> -->
	</div>
<!-- </body> -->
</html>
<? include "./foot.php";?>
