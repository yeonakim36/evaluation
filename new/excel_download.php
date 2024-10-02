<?php
// include "./head.lib.php";
if(!$_SESSION['sess_userid']) { //로그인하지 않았다면 로그인 페이지로 이동
	?>
		<script>
			location.replace("index.php");
		</script>
	<?
	exit;
}
if($_SESSION['sess_grade'] != 1) { //관리자 권한확인
	?>
		<script>
			location.replace("index.php");
		</script>
	<?
	exit;
}
$file = "./upload/evaluation_excel_upload.xlsx";

if (file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
    echo("<script>location.replace('./excel_download.php');</script>");
} else {
    echo "none";
}

?>
