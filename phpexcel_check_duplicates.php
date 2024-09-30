<?php
include_once "include/class/PHPExcel-1.8/PHPExcel/IOFactory.php";
include_once "/var/www/html/evaluation/conndb.php";

// 사용안함

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWD, $DB_SNAME, $db_port);
    if ($conn->connect_error) {
        die('데이터베이스 연결 실패: ' . $conn->connect_error);
    }
} catch (Exception $e) {
    echo '데이터베이스 연결 오류: ' . $e->getMessage();
}

// 엑셀에서 가져온 데이터
$excelData = json_decode($_POST['excelData'], true);
echo $excelData;
// 중복된 데이터를 담을 배열 초기화
$duplicates = array();

foreach ($excelData as $rowIndex => $row) {
    // 이 부분에서 DB와 비교하여 중복 여부를 확인
    // 예제로 userid를 기준으로 중복을 확인하도록 설정
    $user_id = $row[1];
    $year = $row[3];
    $half = $row[4];

    $sql = "SELECT * FROM tb_evaluation WHERE user_id = '$user_id' AND year = '$year' AND half = '$half'";
    $result = mysqli_query($conn, $sql);

    if ($result->num_rows > 0) {
        // 중복된 데이터가 있다면 해당 셀의 정보를 저장
        $duplicates[] = array(
            'rowIndex' => $rowIndex + 1, // Excel 행 번호는 1부터 시작하므로 +1
            'columnIndex' => 1 // 예제에서는 userid가 첫 번째 열에 위치하므로 1
        );
    }
}

// JSON 형태로 중복된 데이터를 반환
echo json_encode($duplicates);

$conn->close();

?>
