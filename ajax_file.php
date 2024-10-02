<?php
header("Content-Type: text/html; charset=UTF-8");

if (!empty($_FILES['fileToUpload']['name'][0])) {
    $edu_id = $_POST["edu_id"];
    // $base_path = $_SERVER['DOCUMENT_ROOT']."/evaluation_test/upload_survey/".$edu_id."/"; //local
    $base_path = $_SERVER['DOCUMENT_ROOT']."/evaluation/upload_survey/".$edu_id."/"; //live

    if(!is_dir($base_path)){
        if(!mkdir($base_path, 0777, true)){
            echo "error: Failed to create directory";
            exit;
        }
    }
    
    $success = true;
    $paths = array();

    foreach ($_FILES['fileToUpload']['name'] as $i => $name) {
        if ($_FILES['fileToUpload']['error'][$i] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES['fileToUpload']['tmp_name'][$i];
            $name = "attach".$name; // 한글 파일명 처리
            $name = urldecode($name);
            $name = mb_convert_encoding($name, 'UTF-8', 'auto');
            $name = str_replace(' ', '_', $name);
            $name = preg_replace('/[^가-힣a-zA-Z0-9_. -]/u', '', $name);
            
            $target_file = $base_path . basename($name);
            // $path = "/evaluation_test/upload_survey/".$edu_id."/" . basename($name); //local
            $path = "/evaluation/upload_survey/".$edu_id."/" . basename($name); //live
            
            if (move_uploaded_file($tmp_name, $target_file)) {
                $paths[] = $path;
            } else {
                $success = false;
                break;
            }
        } else {
            $success = false;
            break;
        }
    }

    if ($success) {
        echo implode("|", $paths);
    } else {
        echo "error";
    }
} else {
    echo "empty_error";
}
?>