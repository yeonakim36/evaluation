<?php
include_once 'include/class/PHPExcel-1.8/PHPExcel/IOFactory.php';
include_once "/var/www/html/include/config/header.php";

$file = 'CODIP_MP.xlsx';
try {
    $inputFileType = PHPExcel_IOFactory::identify($file);
    $objReader = PHPExcel_IOFactory::createReader($inputFileType);
    $objPHPExcel = $objReader->load($file);
} catch(Exception $e) {
    die('Error loading file "'.pathinfo($file, PATHINFO_BASENAME).'" : '.$e->getMessage());
    echo "error";
}
$sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

//print_r($sheetData);
//return;

$upload_codip_column = "( CODiP_Number, cd_status, cd_sub_status, 
                                cd_opp_reg_date, cd_d_in_reg_date, cd_lost_reg_date, cd_pending_reg_date, cd_d_win_reg_date, cd_mp_reg_date,
                                cd_ex1, cd_ex2, cd_ex3, cd_ex4, cd_ex5, 
                                cd_ex6, cd_ex7, cd_ex8, cd_ex9, cd_ex10, 
                                cd_ex11, cd_ex12, cd_ex13, cd_ex14, cd_ex15, cd_ex16, cd_ex17, 
                                cd_ex18, cd_ex19, cd_ex20, cd_ex21, 
                                
                                cd_ex22, cd_ex23, 
                                
                                cd_ex24, cd_ex25, cd_ex26, cd_ex27, 
                                cd_ex28, cd_ex29, cd_ex30, 
                                cd_ex31, cd_ex32, cd_ex33, cd_ex34, cd_ex35, cd_ex36, cd_ex37, cd_ex38, cd_ex39, cd_ex40, 
                                cd_ex41, cd_ex42, cd_ex43, cd_ex44, cd_ex45, cd_ex46, cd_ex47, cd_ex48 ) VALUES";

for($i = 1; $i < count($sheetData)+1; $i++) {
    if($i >= 3) {
        if(empty($sheetData[$i][A]))
            return;
        $Data[$i][B] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][B]));
        $Data[$i][B] = str_replace("\n", " ", $Data[$i][B]);
        $Data[$i][C] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][C]));
        $Data[$i][C] = str_replace("\n", " ", $Data[$i][C]);
        $Data[$i][D] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][D]));
        $Data[$i][D] = str_replace("\n", " ", $Data[$i][D]);
        $Data[$i][E] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][E]));
        $Data[$i][E] = str_replace("\n", " ", $Data[$i][E]);
        $Data[$i][F] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][F]));
        $Data[$i][F] = str_replace("\n", " ", $Data[$i][F]);
        $Data[$i][G] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][G]));
        $Data[$i][G] = str_replace("\n", " ", $Data[$i][G]);
        $Data[$i][H] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][H]));
        $Data[$i][H] = str_replace("\n", " ", $Data[$i][H]);
        $Data[$i][I] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][I]));
        $Data[$i][I] = str_replace("\n", " ", $Data[$i][I]);
        $Data[$i][J] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][J]));
        $Data[$i][J] = str_replace("\n", " ", $Data[$i][J]);
        $Data[$i][K] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][K]));
        $Data[$i][K] = str_replace("\n", " ", $Data[$i][K]);
        $Data[$i][L] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][L]));
        $Data[$i][L] = str_replace("\n", " ", $Data[$i][L]);
        $Data[$i][M] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][M]));
        $Data[$i][M] = str_replace("\n", " ", $Data[$i][M]);
        $Data[$i][N] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][N]));
        $Data[$i][N] = str_replace("\n", " ", $Data[$i][N]);
        $Data[$i][O] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][O]));
        $Data[$i][O] = str_replace("\n", " ", $Data[$i][O]);
        $Data[$i][P] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][P]));
        $Data[$i][P] = str_replace("\n", " ", $Data[$i][P]);
        $Data[$i][Q] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][Q]));
        $Data[$i][Q] = str_replace("\n", " ", $Data[$i][Q]);
        $Data[$i][R] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][R]));
        $Data[$i][R] = str_replace("\n", " ", $Data[$i][R]);
        $Data[$i][S] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][S]));
        $Data[$i][S] = str_replace("\n", " ", $Data[$i][S]);
        $Data[$i][T] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][T]));
        $Data[$i][T] = str_replace("\n", " ", $Data[$i][T]);
        $Data[$i][U] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][U]));
        $Data[$i][U] = str_replace("\n", " ", $Data[$i][U]);
        $Data[$i][V] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][V]));
        $Data[$i][V] = str_replace("\n", " ", $Data[$i][V]);
        $Data[$i][W] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][W]));
        $Data[$i][W] = str_replace("\n", " ", $Data[$i][W]);
        $Data[$i][X] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][X]));
        $Data[$i][X] = str_replace("\n", " ", $Data[$i][X]);
        $Data[$i][Y] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][Y]));
        $Data[$i][Y] = str_replace("\n", " ", $Data[$i][Y]);
        $Data[$i][Z] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][Z]));
        $Data[$i][Z] = str_replace("\n", " ", $Data[$i][Z]);

        $Data[$i][AA] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AA]));
        $Data[$i][AA] = str_replace("\n", " ", $Data[$i][AA]);
        $Data[$i][AB] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AB]));
        $Data[$i][AB] = str_replace("\n", " ", $Data[$i][AB]);
        $Data[$i][AC] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AC]));
        $Data[$i][AC] = str_replace("\n", " ", $Data[$i][AC]);
        $Data[$i][AD] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AD]));
        $Data[$i][AD] = str_replace("\n", " ", $Data[$i][AD]);
        $Data[$i][AE] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AE]));
        $Data[$i][AE] = str_replace("\n", " ", $Data[$i][AE]);
        $Data[$i][AF] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AF]));
        $Data[$i][AF] = str_replace("\n", " ", $Data[$i][AF]);
        $Data[$i][AG] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AG]));
        $Data[$i][AG] = str_replace("\n", " ", $Data[$i][AG]);
        $Data[$i][AH] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AH]));
        $Data[$i][AH] = str_replace("\n", " ", $Data[$i][AH]);
        $Data[$i][AI] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AI]));
        $Data[$i][AI] = str_replace("\n", " ", $Data[$i][AI]);
        $Data[$i][AJ] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AJ]));
        $Data[$i][AJ] = str_replace("\n", " ", $Data[$i][AJ]);
        $Data[$i][AK] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AK]));
        $Data[$i][AK] = str_replace("\n", " ", $Data[$i][AK]);
        $Data[$i][AL] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AL]));
        $Data[$i][AL] = str_replace("\n", " ", $Data[$i][AL]);
        $Data[$i][AM] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AM]));
        $Data[$i][AM] = str_replace("\n", " ", $Data[$i][AM]);
        $Data[$i][AN] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AN]));
        $Data[$i][AN] = str_replace("\n", " ", $Data[$i][AN]);
        $Data[$i][AO] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AO]));
        $Data[$i][AO] = str_replace("\n", " ", $Data[$i][AO]);
        $Data[$i][AP] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AP]));
        $Data[$i][AP] = str_replace("\n", " ", $Data[$i][AP]);
        $Data[$i][AQ] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AQ]));
        $Data[$i][AQ] = str_replace("\n", " ", $Data[$i][AQ]);
        $Data[$i][AR] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AR]));
        $Data[$i][AR] = str_replace("\n", " ", $Data[$i][AR]);
        $Data[$i]['AS'] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i]['AS']));
        $Data[$i]['AS'] = str_replace("\n", " ", $Data[$i]['AS']);
        $Data[$i][AT] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AT]));
        $Data[$i][AT] = str_replace("\n", "<br>", $Data[$i][AT]);
        $Data[$i][AU] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AU]));
        $Data[$i][AU] = str_replace("\n", "<br>", $Data[$i][AU]);
        $Data[$i][AV] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AV]));
        $Data[$i][AV] = str_replace("\n", "<br>", $Data[$i][AV]);
        $Data[$i][AW] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AW]));
        $Data[$i][AW] = str_replace("\n", "<br>", $Data[$i][AW]);
        $Data[$i][AX] = preg_replace("/[#&\"']/i", "", trim($sheetData[$i][AX]));
        $Data[$i][AX] = str_replace("\n", " ", $Data[$i][AX]);

        if (empty($sheetData[$i][D]))
            $sheetData[$i][D] = '2000-01-01';

        if ($sheetData[1][A] == 'OPP')
            $identification_date = " '{$sheetData[$i][D]}', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', ";
        else if ($sheetData[1][A] == 'D-IN')
            $identification_date = " '2000-01-01 00:00:00', '{$sheetData[$i][D]}', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', ";
        else if ($sheetData[1][A] == 'LOST')
            $identification_date = " '2000-01-01 00:00:00', '2000-01-01 00:00:00', '{$sheetData[$i][D]}', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', ";
        else if ($sheetData[1][A] == 'PENDING')
            $identification_date = " '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '{$sheetData[$i][D]}', '2000-01-01 00:00:00', '2000-01-01 00:00:00', ";
        else if ($sheetData[1][A] == 'D-WIN')
            $identification_date = " '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '{$sheetData[$i][D]}', '2000-01-01 00:00:00', ";
        else if ($sheetData[1][A] == 'MP')
            $identification_date = " '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '2000-01-01 00:00:00', '{$sheetData[$i][D]}', ";

        $log_sql .= "(  '{$sheetData[$i][A]}', '{$sheetData[1][A]}', '{$Data[$i][AP]}',
                            $identification_date
                            '{\"update\":\"0\",\"define\":\"1\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"color\",\"value\":\"{$Data[$i][B]}\"}',
                            '{\"update\":\"0\",\"define\":\"2\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"date\",\"value\":\"{$Data[$i][C]}\"}', 
                            '{\"update\":\"0\",\"define\":\"3\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"date\",\"value\":\"{$Data[$i][D]}\"}', 
                            '{\"update\":\"0\",\"define\":\"4\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][E]}\"}',
                            '{\"update\":\"0\",\"define\":\"5\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][F]}\"}',
                            '{\"update\":\"0\",\"define\":\"6\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][G]}\"}',
                            '{\"update\":\"0\",\"define\":\"7\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][H]}\"}', 
                            '{\"update\":\"0\",\"define\":\"8\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][I]}\"}', 
                            '{\"update\":\"0\",\"define\":\"9\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][J]}\"}',
                            '{\"update\":\"0\",\"define\":\"10\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][K]}\"}',
                            '{\"update\":\"0\",\"define\":\"11\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][L]}\"}',
                            '{\"update\":\"0\",\"define\":\"12\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][M]}\"}', 
                            '{\"update\":\"0\",\"define\":\"13\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][O]}\"}', 
                            '{\"update\":\"0\",\"define\":\"14\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][P]}\"}',
                            '{\"update\":\"0\",\"define\":\"15\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][Q]}\"}',
                            '{\"update\":\"0\",\"define\":\"16\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][R]}\"}',
                            '{\"update\":\"0\",\"define\":\"17\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][S]}\"}',
                            '{\"update\":\"0\",\"define\":\"18\",\"date\":\"2000-01-01\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$Data[$i][T]}\"}',
                            '{\"update\":\"0\",\"define\":\"19\",\"date\":\"2000-01-01\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$Data[$i][U]}\"}',
                            '{\"update\":\"0\",\"define\":\"20\",\"date\":\"2000-01-01\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$Data[$i][V]}\"}',
                            '{\"update\":\"0\",\"define\":\"21\",\"date\":\"2000-01-01\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$Data[$i][W]}\"}',
                            '{\"update\":\"0\",\"define\":\"22\",\"date\":\"2000-01-01\",\"status\":\"OPP\",\"type\":\"date\",\"value\":\"{$Data[$i][X]}\"}',
                            '{\"update\":\"0\",\"define\":\"23\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"date\",\"value\":\"{$Data[$i][Y]}\"}',
                            '{\"update\":\"0\",\"define\":\"24\",\"date\":\"2000-01-01\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$Data[$i][Z]}\"}',
                            '{\"update\":\"0\",\"define\":\"25\",\"date\":\"2000-01-01\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$Data[$i][AA]}\"}', 
                            '{\"update\":\"0\",\"define\":\"26\",\"date\":\"2000-01-01\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$Data[$i][AB]}\"}',
                            '{\"update\":\"0\",\"define\":\"27\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"date\",\"value\":\"{$Data[$i][AC]}\"}',
                            '{\"update\":\"0\",\"define\":\"28\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AD]}\"}',
                            '{\"update\":\"0\",\"define\":\"29\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AE]}\"}',
                            '{\"update\":\"0\",\"define\":\"30\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AF]}\"}',
                            '{\"update\":\"0\",\"define\":\"31\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AG]}\"}',
                            '{\"update\":\"0\",\"define\":\"32\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AH]}\"}',
                            '{\"update\":\"0\",\"define\":\"33\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AI]}\"}',
                            '{\"update\":\"0\",\"define\":\"34\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AJ]}\"}',
                            '{\"update\":\"0\",\"define\":\"35\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AK]}\"}',
                            '{\"update\":\"0\",\"define\":\"36\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AL]}\"}',
                            '{\"update\":\"0\",\"define\":\"37\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AM]}\"}',
                            '{\"update\":\"0\",\"define\":\"38\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AN]}\"}',
                            '{\"update\":\"0\",\"define\":\"39\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AO]}\"}',
                            '{\"update\":\"0\",\"define\":\"40\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][AP]}\"}',
                            '{\"update\":\"0\",\"define\":\"41\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$Data[$i][AQ]}\"}',
                            '{\"update\":\"0\",\"define\":\"42\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][AR]}\"}',
                            '{\"update\":\"0\",\"define\":\"43\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i]['AS']}\"}',
                            '{\"update\":\"0\",\"define\":\"44\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][AT]}\"}',
                            '{\"update\":\"0\",\"define\":\"45\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][AU]}\"}',
                            '{\"update\":\"0\",\"define\":\"46\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][AV]}\"}',
                            '{\"update\":\"0\",\"define\":\"47\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][AW]}\"}',
                            '{\"update\":\"0\",\"define\":\"48\",\"date\":\"2000-01-01\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$Data[$i][AX]}\"}' ),";

    }
}
// SET UPDATE 항목 끝에 , 제거
$end_length = strlen($log_sql) - 1;
$log_sql = substr($log_sql, 0, $end_length);

$query = " INSERT INTO CODiP_INFO $upload_codip_column $log_sql";
echo $query;
//$codip_log_update = $Db->game->query($query);
//if ($codip_log_update == false) {
//    echo "$query error";
//    return false;
//}
//$Db->trans_commit();

echo "성공";

?>
