<?php
/**
 * Created by PhpStorm.
 * User: park029
 * Date: 2017-04-04
 * Time: 오후 1:47
 */

header("Content-Type:application/json; charset=UTF-8");
define('BASE_PATH', dirname(__file__));
// include_once "../include/config/header.php";
include "/var/www/web/evaluation/conndb.php";

$id = $_POST['id'];

$return_value = array(
    'code' => 0,
    'msg' => '파일업로드를 완료하였습니다.'
);

$file_count = count($_FILES);
// 업로드된 파일이 있는지 체크
if (!isset($_FILES) || $file_count == 0) {
    $return_value['code'] = -1;
    $return_value['msg'] = '첨부된 파일이 없습니다.';
    echo json_encode($return_value);
    return;
}

if ($file_count > 5) {
    $return_value['code'] = -1;
    $return_value['msg'] = '파일은 한번에 최대5개 까지만 첨부할 수 있습니다.';
    echo json_encode($return_value);
    return;

}

$allow_type = NULL;
$thumbnail_flag = 0;
$category = $_POST['category'];
switch ($id) {
    case "upload_images":
        $type = 1;
        if ($category === "product_manage") {
            $thumbnail_flag = 1;
        }
        $allow_type = array("jpeg", "jpg", "png");
        break;
    case "upload_thumbnail":
    case "upload_thumbnails":
        $type = 2;
        $re_width = 210;
        $re_height = 150;
        if ($id === "upload_thumbnails") {
            $type = 4;
            $re_width = 500;
            $re_height = 310;
        }
        $thumbnail_flag = 1;
        $allow_type = array("jpeg", "jpg", "png");
        break;
    case "upload_file":
        $type = 3;
        $allow_type = array("pdf", "hwp", "pptx", "docx", "xlsx", "doc", "ppt", "xls");
        if ($category === "as_movie") {
            $allow_type = array("mp4");
        }
        if ($category === "unpaid_upload") {
            $allow_type = array("xlsx");
        }
        break;
    default :
        $return_value['code'] = -1;
        $return_value['msg'] = 'It is not a normal approach.';
        echo json_encode($return_value);
        return;
        break;
}

if (count($allow_type) < 1) {
    $return_value['code'] = -1;
    $return_value['msg'] = 'It is not a normal approach.';
    echo json_encode($return_value);
    return;
}

// 확장자 체크 및 에러 체크
foreach ($_FILES AS $key => $file) {
    $name = explode('.', $file['name']);
    $file_size = $file['size'];
    $file_type = '';
    if (count($name) > 1) {
        $file_type = strtolower(array_pop($name));
    }
    $name = join('.', $name);

    // 확장자 체크
    if (!in_array($file_type, $allow_type)) {
        $return_value['code'] = -2;
        $return_value['msg'] = json_encode($allow_type) . " 확장자만 업로드 됩니다.";
        $return_value['file_name'] = $name;
        $return_value['file_type'] = $file_type;
        echo json_encode($return_value);
        return;
    }

    if ($file_size > FILE_UPLOAD_MAX_SIZE) {
        $return_value['code'] = -2;
        $return_value['msg'] = "파일이 너무 큽니다(" . PHP_FILE_MAX_SIZE . "이하 업로드 가능)";
        $return_value['file_name'] = $name;
        $return_value['file_type'] = $file_type;
        echo json_encode($return_value);
        return;
    }

    // 에러 체크
    if ($file['error']) {
        $return_value['code'] = -2;

        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $return_value['msg'] = "파일이 너무 큽니다.(" . PHP_FILE_MAX_SIZE . "이하 업로드 가능)";
                $return_value['file_name'] = $name;
                $return_value['file_type'] = $file_type;
                break;
            case UPLOAD_ERR_NO_FILE:
                $return_value['msg'] = "파일이 첨부되지 않았습니다.";
                $return_value['file_name'] = $name;
                $return_value['file_type'] = $file_type;
                break;
            default:
                $return_value['msg'] = "파일이 제대로 업로드되지 않았습니다.";
                $return_value['file_name'] = $name;
                $return_value['file_type'] = $file_type;
        }

        echo json_encode($return_value);
        return;
    }

    // 확장자 정보 및 확장자 없는 이름 셋팅
    $_FILES[$key]['file_type'] = $file_type;
    $_FILES[$key]['file_name'] = $name;
}
$year_folder = date('Y/');
$md_folder = date('md/');

$base_path = BASE_PATH . '/';
$year_folder_path = $base_path . $year_folder;

// 업로드할 디렉토리가 존재하는지 확인하여 없으면 생성
if (!is_dir($year_folder_path)) {
    mkdir($year_folder_path);
}
$folder_name = $year_folder . $md_folder;
$dir_path = $year_folder_path . $md_folder; // 저장 경로

// 업로드할 디렉토리가 존재하는지 확인하여 없으면 생성
if (!is_dir($dir_path)) {
    mkdir($dir_path);
}

// 트랜잭션 시작
$return_data = array();
$save_files = array();

// 파일 업로드
foreach ($_FILES AS $key => $file) {
    $img_tag = "";
    $size = ceil($file['size'] / 1024);
    $re_file_name = md5($_FILES[$key]['file_name'] . date('His') . rand(0, 100));

    // 파일 저장 및 배열에 저장경로 임시 저장 ( 임시저장 데이터로 에러 발생시 업로드된 파일들을 삭제)
    $image_info = getimagesize($_FILES[$key]["tmp_name"]);
    $image_width = $image_info[0];
    $image_height = $image_info[1];

    if (empty($image_width) && empty($image_height)) {
        $image_width = 0;
        $image_height = 0;
    }
    $file_type = $_FILES[$key]['file_type'];

    if ($thumbnail_flag) {
        $resize_name = $dir_path . "t_" . $re_file_name . '.' . $file_type;
        resize_image($_FILES[$key]["tmp_name"], $image_info, $resize_name, $re_width, $re_height);
        $save_thumbnail[] = $dir_path . "t_" . $re_file_name . '.' . $file_type;
    }

    move_uploaded_file($_FILES[$key]['tmp_name'], $dir_path . $re_file_name . '.' . $file_type);
    $save_files[] = $dir_path . $re_file_name . '.' . $file_type;
    // 저장
    $query = "INSERT INTO tb_evaluation 
                           ( user_no, user_id, user_name, year, half, total_grade, score, comment, etc )
                      VALUES
                           ('{$folder_name}', '{$_FILES[$key]['file_name']}', '{$re_file_name}', '{$file_type}', $image_width, $image_height, {$size})";
    if (!$Db->select_query($query)) {
        for ($i = 0; $i < count($save_files); $i++) {
            unlink($save_files[$i]);
            if ($thumbnail_flag) {
                unlink($save_thumbnail[$i]);
            }
        }
        $Db->trans_rollback();
        return false;
    }

    $insert_id = $Db->insert_id();

    $site_url = URL;
    $enter = "";
    if ($file_count > 1) {
        $enter = "<p>&nbsp;</p>";
    }
    $upload_folder = UPLOAD_FOLDER;
    $return_value['result']['type'] = $type;
    if ($type == 1) {
        $img_tag = "<br><img src='{$site_url}{$upload_folder}{$folder_name}{$re_file_name}.{$file_type}' width='$image_width' height='$image_height' style='margin-bottom:10px;max-width:100%;'>";
    }
    if ($type == 2 || $type == 4) {
        $img_tag[0] = "{$upload_folder}{$folder_name}{$re_file_name}.{$file_type}";
        if ($type == 4) {
            $img_tag[0] = "{$upload_folder}{$folder_name}t_{$re_file_name}.{$file_type}";
        }
        $img_tag[1] = $insert_id;
    }

    if ($type == 3) {
        $img_tag[0] = "{$_FILES[$key]['file_name']}.{$_FILES[$key]['file_type']}";
        $img_tag[1] = $size;
        $img_tag[2] = $insert_id;
    }

    // 반환할 배열에 셋팅
    $return_data[$key] = $img_tag;
}

// 트랜잭션 커밋
$Db->trans_commit();

// 저장 완료
$return_value['result']['file_info'] = $return_data;
echo json_encode($return_value);