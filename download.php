<?php
$target_Dir = "./download/";
if($_POST['file_type'])
$file = $_POST['file_type'];
$down = $target_Dir.$file;
$filesize = filesize($down);

if(file_exists($down)){
    header("Content-Type:application/octet-stream");
    header("Content-Disposition:attachment;filename=$file");
    header("Content-Transfer-Encoding:binary");
    header("Content-Length:".filesize($target_Dir.$file));
    header("Cache-Control:cache,must-revalidate");
    header("Pragma:no-cache");
    header("Expires:0");
    if(is_file($down)){
        $fp = fopen($down,"r");
        while(!feof($fp)){
            $buf = fread($fp,8096);
            $read = strlen($buf);
            print($buf);
            flush();
        }
        fclose($fp);
    }
} else{
    ?><script>
        alert("파일이 존재하지 않습니다. 관리자에게 문의해주시기 바랍니다.");
        location.replace("main.php");
    </script><?
}