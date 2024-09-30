<?php
// defined('ALLOW_PAGE') OR exit('정상적인 접근이 아닙니다');
/**
 * Created by PhpStorm.
 * User: wohedong
 * Date: 2018-04-13
 * Time: 오후 11:53
 */
include "/var/www/web/evaluation/conndb.php";

header("Content-Type:application/json; charset=UTF-8");

if (!function_exists('excel_update')) {
    function excel_update()
    {
        //DB연결확인
        $db_link = db_conn();
        $SQL = " SELECT * FROM eval_user  ";
        $sql_query = mysqli_query($db_link, $SQL);
        while($row = mysqli_fetch_array($sql_query)) {
            echo $row[user_no];
        }
        return;

        include_once 'include/class/PHPExcel-1.8/PHPExcel/IOFactory.php';
        $db->trans_begin();
        ini_set('memory_limit','1024M');
        $return_value = array(
            'code' => 0,
            'msg' => "CODIP file has been uploaded."
        );

        $upload_id = $_POST['upload_id'];

        if (empty($upload_id)) {
            $return_value['code'] = 3;
            $return_value['msg'] = "No attachments found.";
            echo json_encode($return_value);
            return false;
        }

        $query = "SELECT * FROM tb_file_upload WHERE upload_id = $upload_id";
        $upload_file_select = $db->web->query($query);
        if ($upload_file_select == false) {
            $return_value['code'] = 3;
            $return_value['msg'] = "This file does not exist.";
            $db->error($upload_file_select, $db->web, $query);
            echo json_encode($return_value);
            return false;
        }

        foreach ($upload_file_select AS $key => $val) {
            $file_data = $val;
        }

        $base_path = $_SERVER['DOCUMENT_ROOT'] . "/";
        $upload_folder = "upload/";

        $file_path = $base_path.$upload_folder.$file_data['upload_path'].$file_data['re_name'].".".$file_data['file_type'];
//        $file_path = "/home/CODiP/upload/2023/1113/".$file_data['re_name'].".".$file_data['file_type'];
        try {
            $file_type = PHPExcel_IOFactory::identify($file_path);
            $obj_reader = PHPExcel_IOFactory::createReader($file_type);
            // 데이터만 읽기(서식을 모두 무시해서 속도 증가 시킴)
            $obj_reader->setReadDataOnly(true);
            // 업로드된 엑셀 파일 읽기
            $php_excel = $obj_reader->load($file_path);
            // 첫번째 시트로 고정
            $php_excel->setActiveSheetIndex(0);
            $excel_sheet = $php_excel->getSheet(0);
        } catch (Exception $e) {
            $return_value['code'] = 3;
            $return_value['msg'] = "The file cannot be read. Please contact the administrator.";
            echo json_encode($return_value);
            return false;
        }

        // 테이블 헤드
        $head_num = 4;
        $table_head = EXCEL_TABLE_HEAD;
        $table_head_end = count($table_head) - 1;
        $first_cell = $table_head[0]['cell'];
        $last_cell = $table_head[$table_head_end]['cell'];

        $data_arr = $php_excel->getActiveSheet()->toArray(null, true, true, true);
        $excel_cell = array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');

        $return_value['error_html'] = "";

        $error_count = 0;
        $total_update_count = 0;
        $codip_number_array = array();

        for ($i = 4; $i < count($data_arr)+1; $i++) {
            $td_error_html = "";
            $row_data = $data_arr[$i];

            // upload 양식 폼 check
            if($i == 4 ) {
                $check_num = 0;
                // FSCT 년도
                $query = " SELECT * FROM CODiP_FCST_year ORDER BY year ASC";
                $codip_fcst = $db->game->query($query);
                if ($codip_fcst == false)
                    $db->error($codip_fcst, $db->game, $query);

                $FCST_year = array();
                while ($fcst_year = $codip_fcst->fetch_assoc()) {
                    array_push($FCST_year, $fcst_year[year]);
                }

                $now_year = $FCST_year[0];
                $next_year = $FCST_year[1];
                $next_next_year = $FCST_year[2];

                // CODiP_Define
                $query = " SELECT * FROM CODiP_Define WHERE cd_use = 1 ORDER BY cd_order ASC ";
                $select = $db->game->query($query);
                if ($select == false)
                    $db->error($select, $db->game, $query);
                foreach ($select AS $key => $val) {
                    $define = $val[cd_name];
                    if( strcmp($define, $row_data[$excel_cell[$check_num]])) {
                        $return_value = array(
                            'code' => 0,
                            'msg' => "$define {$row_data[$excel_cell[$check_num]]} 업로드 양식이 잘못되었습니다. 다시 확인해주시기 바랍니다."
                        );
                        echo json_encode($return_value);
                        return true;
                    }
                    $check_num++;
                }
                // FCST Define
                $query = " SHOW COLUMNS FROM CODiP_FCST_INFO ";
                $select = $db->game->query($query);
                if ($select == false)
                    $db->error($select, $db->game, $query);
                foreach ($select AS $key => $val) {
                    if ($val['Field'] === 'jan_qty_fcst_nw')
                        $check_point = true;

                    if ($check_point) {
                        // 여기서 FCST EXCEL 이름 변경하기 cd_name 이 excel 이름
                        if(strpos($val[Field], 'fcst_nw') !== false) {
                            $year = $now_year;
                            if(strpos($val[Field], '_qty_') !== false || strpos($val[Field], 'ASP') !== false ) {
                                $val[Field] = str_replace('_fcst_nw', '',$val[Field]);
                            } else if(strpos($val[Field], '_billing_') !== false) {
                                $val[Field] = str_replace('billing_fcst_nw', 'Amt', $val[Field]);
                            }
                        }
                        if(strpos($val[Field], 'fcst_nxt') !== false) {
                            $year = $next_year;
                            if(strpos($val[Field], '_qty_') !== false || strpos($val[Field], 'ASP') !== false ) {
                                $val[Field] = str_replace( '_fcst_nxt', '', $val[Field]);
                            } else if(strpos($val[Field], '_billing_') !== false) {
                                $val[Field] = str_replace( 'billing_fcst_nxt', 'Amt', $val[Field]);
                            }
                        }
                        if(strpos($val[Field], 'fcst_nnxt') !== false) {
                            $year = $next_next_year;
                            if(strpos($val[Field], '_qty_') !== false || strpos($val[Field], 'ASP') !== false ) {
                                $val[Field] = str_replace('_fcst_nnxt', '', $val[Field]);
                            } else if(strpos($val[Field], '_billing_') !== false) {
                                $val[Field] = str_replace('billing_fcst_nnxt', 'Amt', $val[Field]);
                            }
                        }

                        $year = "Y".substr($year, 2, 2);

                        $define = $year."_".$val[Field];

                        if( strcmp($define, $row_data[$excel_cell[$check_num]])) {
                            $return_value = array(
                                'code' => 3,
                                'msg' => "$define 업로드 양식이 잘못되었습니다. 다시 확인해주시기 바랍니다."
                            );
                            echo json_encode($return_value);
                            return true;
                        }
                        $check_num++;
                    }
                }
            } else if($i > 4 ) {
                // 앞뒤 공백 제거 및 특수 문자 치환
                $Data['A'] = preg_replace("/[#\"']/i", "", trim($row_data['A']));$Data['A'] = str_replace("\n", " ", $Data['A']);
                $Data['B'] = preg_replace("/[#\"']/i", "", trim($row_data['B']));$Data['B'] = str_replace("\n", " ", $Data['B']);
                $Data['C'] = preg_replace("/[#\"']/i", "", trim($row_data['C']));$Data['C'] = str_replace("\n", " ", $Data['C']);
                $Data['D'] = preg_replace("/[#\"']/i", "", trim($row_data['D']));$Data['D'] = str_replace("\n", " ", $Data['D']);
                $Data['E'] = preg_replace("/[#\"']/i", "", trim($row_data['E']));$Data['E'] = str_replace("\n", " ", $Data['E']);
                $Data['F'] = preg_replace("/[#\"']/i", "", trim($row_data['F']));$Data['F'] = str_replace("\n", " ", $Data['F']);
                $Data['G'] = preg_replace("/[#\"']/i", "", trim($row_data['G']));$Data['G'] = str_replace("\n", " ", $Data['G']);
                $Data['H'] = preg_replace("/[#\"']/i", "", trim($row_data['H']));$Data['H'] = str_replace("\n", " ", $Data['H']);


                if( empty($Data['A']) && empty($Data['B']) && empty($Data['C']) && empty($Data['D']) && empty($Data['E']) && empty($Data['F']))
                    break;

                $row_error_count = 0;

                $session_table_name = session_table_name();

                // CODiP_INFO / CODiP_INFO_History 테이블 명
                $query = " SELECT * FROM CODiP_INFO$session_table_name WHERE CODiP_Number = '{$Data['A']}'";
                $select = $db->game->query($query);
                if ($select == false)
                    $db->error($select, $db->game, $query);

                foreach ($select AS $key => $val) {
                    $CODiP_info = $val;
                }

                // CODIP_Number
                if ( empty($Data['A']) ) {
                    // 앞에 CODIP Number가 없어서 생성했던 번호가 있는지 확인
                    if( empty($codip_number_array[$Data['C']][0]) ) {
                        $date = date("Y");
                        $end_length = strlen($date) - 2;
                        $codip_year = substr($date, 2, $end_length);

                        // Region 설정
                        if($Data['C'] == 'F Sales') $region = 'F';
                        else if($Data['C'] == 'S Sales') $region = 'S';
                        else if($Data['C'] == 'Overseas') $region = 'O';
                        else $region = $Data['C'];

                        // CODiP_INFO / CODiP_INFO_History 테이블 명
                        $query = " SELECT *
                            FROM CODiP_INFO$session_table_name
                            WHERE CODiP_Number LIKE ('%O$region$codip_year%')
                            ORDER BY CODiP_Number DESC
                            LIMIT 1";
                        $select = $db->game->query($query);
                        if ($select == false)
                            $db->error($select, $db->game, $query);

                        foreach ($select AS $key => $val) {
                            $select_CODiP = $val;
                        }

                        // SET UPDATE 항목 끝에 , 제거
                        $start_length = strlen($select_CODiP['CODiP_Number']) - 3;
                        $end_length = strlen($select_CODiP['CODiP_Number']);
                        (int)$codip_number = substr($select_CODiP['CODiP_Number'], $start_length, $end_length);

                        $codip_number += 1;
                        $codip_number_array[$Data['C']][0] = $codip_number;
                    } else {
                        // 이전에 CODIP Number가 없어서 생성했던 번호 다음을 가져와서 생성
                        $codip_number = $codip_number_array[$Data['C']][0] + 1;
                        $codip_number_array[$Data['C']][0] = $codip_number;
                    }
                    if(strlen($codip_number) == 1)
                        $number_pos = "00".$codip_number;
                    else if(strlen($codip_number) == 2)
                        $number_pos = "0".$codip_number;
                    else
                        $number_pos = $codip_number;

                    // codip number 생성
                    // OPP  -> O+Region+년도 ex) OSZ20-003 : OPP,  SZ지역, 20년도 생성 003번째
                    // D-IN -> D+Region+년도 ex) DCD21-008 : D-IN, CD지역, 21년도 생성 008번째
                    $codip_number = "O".$region.$codip_year."-".$number_pos;

                    $NEW = "<span style='color: yellow'> [New]</span>";
                    $NEW_background = "style=background:antiquewhite";
                } else {
                    // CODIP NUMBER가 있는건 UPDATE
                    $codip_number = $Data['A'];

                    $NEW = "";
                    $NEW_background = "";
                }
                $row_date = array();
                $row_date['A'] = $codip_number;

                $td_error_html .= "<td style=\"color: azure; cursor:pointer; position:sticky; left:-1px; background-color: rgb(67 121 182); font-weight: 700; text-decoration: underline;\">
                                        <div>
                                            $NEW {$codip_number}
                                        </div>
                                    </td>";

                // CODiP Define
                $query = "SELECT * FROM CODiP_Define WHERE cd_use = 1 ORDER BY cd_order ASC";
                $select = $db->game->query($query);
                if ($select == false)
                    $db->error($select, $db->game, $query);

                $Cell = 'B';
                $update_count = 0;
                while ($define = $select->fetch_assoc()) {
                    $codip_value = json_decode($CODiP_info[$define[cd_column]]);
                    $row_date[$Cell][0] = $Data[$Cell];

                    if( empty($NEW) ) {
                        if($define[cd_column] != 'cd_ex2' && $define[cd_column] != 'cd_ex3') {
                            if (strcmp($Data[$Cell], $codip_value->value)  ) {
                                $td_update_style = " color: blue; font-weight: 700; text-align: right; background:skyblue;";
                                $update_count++;
                                $total_update_count++;

                                $row_date[$Cell][1] = 1;
                                $row_date[$Cell][2] = DATE('Y-m-d H:i:s');
                            } else {
                                $row_date[$Cell][1] = 0;
                                $row_date[$Cell][2] = '2000-01-01 00:00:00';
                            }
                        }
                    } else {
                        $row_date[$Cell][1] = 0;
                        $row_date[$Cell][2] = '2000-01-01 00:00:00';
                    }

                    if( $define[cd_column] == 'cd_ex40' ) {
                        $Data[$Cell] = strtoupper($Data[$Cell]);
                        // Status check
                        $codip_status_arry = array('OPP','D-IN','D-WIN','MP','LOST','PENDING');
                        if (!in_array($Data[$Cell], $codip_status_arry)) {
                            // Status가 잘못 됬을 경우
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'OPP','D-IN','D-WIN','MP','LOST','PENDING' 중 하나만 입력해주세요.</li>";
                        }
                        if( empty($NEW) ) {
                            // STATUS 가 변경될때 예외처리
                            if( strcmp($Data[$Cell], $CODiP_info[cd_sub_status]) ) {
                                // STEP
                                $opp_step_arry = array('OPP','D-IN','LOST','PENDING');
                                $d_in_step_arry = array('OPP','D-WIN','LOST','PENDING');
                                $d_win_step_arry = array('D-IN','MP','LOST','PENDING');
                                $mp_step_arry = array('LOST','PENDING');

                                if($CODiP_info[cd_status] == 'OPP')
                                    $array = $opp_step_arry;
                                else if($CODiP_info[cd_status] == 'D-IN')
                                    $array = $d_in_step_arry;
                                else if($CODiP_info[cd_status] == 'D-WIN')
                                    $array = $d_win_step_arry;
                                else if($CODiP_info[cd_status] == 'MP')
                                    $array = $mp_step_arry;

                                $jbstr = implode( ',', $array );
                                if (!in_array($Data[$Cell], $array)) {
                                    // Status가 잘못 됬을 경우
                                    $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                                    $error_count++;
                                    $row_error_count++;
                                    $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 현재 {$CODiP_info[cd_sub_status]}에서는 $jbstr 만 이동이 가능합니다.</li>";
                                }
                            }
                        } else {
                            if($Data[B] != 'OPP') {
                                // Status가 잘못 됬을 경우
                                $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                                $error_count++;
                                $row_error_count++;
                                $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 신규 등록 CODIP은 OPP만 가능합니다.</li>";
                            }
                        }
                    } else if( $define[cd_column] == 'cd_ex4' ) {
                        // Region check
                        $query = "SELECT * FROM CODiP_Region WHERE Region_value = '$Data[$Cell]'"; $select1 = $db->game->query($query);
                        if ($select1->num_rows == false) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Region' 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex9' ) {
                        // Sales Man check
                        $query = "SELECT * FROM tb_user WHERE user_nickname ='$Data[$Cell]'"; $select1 = $db->web->query($query);
                        if ($select1->num_rows == false) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Sales Man' 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex5' ) {
                        // Agency(Distributor(Direct/Distributor)) check
                        if( $Data[B] != 'LOST' ) {
                            $query = "SELECT * FROM CODiP_Agent_List WHERE Agent_name = '$Data[$Cell]' AND cim_type = 'Agency'"; $select1 = $db->game->query($query);
                            if ($select1->num_rows == 0) {
                                $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                                $error_count++;
                                $row_error_count++;
                                $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Agency' 정확하게 입력해 주시기 바랍니다.</li>";
                            }
                        }
                    } else if( $define[cd_column] == 'cd_ex6' ) {
                        // Customer check
                        if( $Data[B] != 'LOST' ) {
                            $query = "SELECT * FROM CODiP_Agent_List WHERE Agent_name = '$Data[$Cell]' AND cim_type = 'Customer'"; $select1 = $db->game->query($query);
                            if ($select1->num_rows == false) {
                                $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                                $error_count++;
                                $row_error_count++;
                                $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Customer' 정확하게 입력해 주시기 바랍니다.</li>";
                            }
                        }
                    } else if( $define[cd_column] == 'cd_ex11' ) {
                        // Application check
                        $query = "SELECT * FROM CODiP_Application WHERE Application_name = '$Data[$Cell]'"; $select1 = $db->game->query($query);
                        if ($select1->num_rows == false) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Application' 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex14' ) {
                        // Device(Sales Type) check
                        $query = "SELECT * FROM CODiP_SalesType WHERE SalesType_name = '$Data[$Cell]'"; $select1 = $db->game->query($query);
                        if ($select1->num_rows == false) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Device' 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex56' ) {
                        // PKG type check
                        $query = " SELECT * 
                                   FROM CODiP_BaseArray BA
                                   LEFT JOIN CODiP_SalesType ST ON BA.BaseArray_name = ST.BaseArray_name 
                                   WHERE ST.SalesType_name = '$Data[J]' ORDER BY ST.st_order ASC ";
                        $select1 = $db->game->query($query);
                        foreach ($select1 AS $key => $val) {
                            $PKG_type = $val;
                        }
                        if (strcmp($Data[$Cell], $PKG_type[PKG_type])) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$Data[$Cell]}</span> = 'PKG type' 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex10' ) {
                        // Sector check
                        $query = "SELECT * FROM CODiP_Sub_Sector WHERE Sector_name = '$Data[$Cell]'"; $select1 = $db->game->query($query);
                        if ($select1->num_rows == false) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Sector' 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex50' ) {
                        // Sub-Sector check
                        $query = "SELECT * FROM CODiP_Sub_Sector WHERE SubSector_name = '$Data[$Cell]'"; $select1 = $db->game->query($query);
                        if ($select1->num_rows == false) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Sub-Sector' 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex13' ) {
                        // Project(Base Array) check
                        $query = "SELECT * FROM CODiP_SalesType WHERE BaseArray_name = '$Data[$Cell]'"; $select1 = $db->game->query($query);
                        if ($select1->num_rows == false) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Project' 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex48' ) {
                        // Marketer check
                        $query = "SELECT * FROM tb_user WHERE user_nickname = '$Data[$Cell]'"; $select1 = $db->web->query($query);
                        if ($select1->num_rows == false) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Marketer' 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex2' ) {
                        // Last Update
                        $row_date[$Cell][0] = $codip_value->value;
                    } else if( $define[cd_column] == 'cd_ex3' ) {
                        // Identification date
                        $query = "SELECT * FROM CODiP_INFO$session_table_name WHERE CODiP_Number = '$codip_number'"; $select1 = $db->game->query($query);
                        if ($select1->num_rows == false) {
                            // 신규 등록으로 date 갱신
                            $row_date[$Cell][0] = DATE('Y-m-d');
                            $codip_status = $Data[B];
                            $codip_sub_status = $Data[B];
                            $row_date[B][1] = 0;
                            $row_date[B][2] = '2000-01-01 00:00:00';

                            $cd_opp_query = DATE('Y-m-d H:i:s');
                            $cd_d_in_query = '2000-01-01 00:00:00';
                            $cd_d_win_query = '2000-01-01 00:00:00';
                            $cd_mp_query = '2000-01-01 00:00:00';
                            $cd_lost_query = '2000-01-01 00:00:00';
                            $cd_pending_query = '2000-01-01 00:00:00';
                        } else {
                            foreach ($select1 AS $key) {
                                // STATUS 변경시 reg_date 변경
                                $cd_opp_query = $key[cd_opp_reg_date];
                                $cd_d_in_query = $key[cd_d_in_reg_date];
                                $cd_d_win_query = $key[cd_d_win_reg_date];
                                $cd_mp_query = $key[cd_mp_reg_date];
                                $cd_lost_query = $key[cd_lost_reg_date];
                                $cd_pending_query = $key[cd_pending_reg_date];

                                // status가 변경이 됬는지 체크해서 변경 됬으면 업로드 날짜로 Identification date 갱신 시켜서 업로드
                                $status_value = json_decode($key[cd_ex40]);
                                if( $Data[B] == $status_value->value ) {
                                    // status 변경X -> date 고정
                                    $identification_value = json_decode($key[cd_ex3]);
                                    $row_date[$Cell][0] = $identification_value->value;
                                    $row_date[$Cell][1] = 0;
                                    $row_date[$Cell][2] = '2000-01-01 00:00:00';

                                    $codip_status = $key[cd_status];
                                    $codip_sub_status = $key[cd_sub_status];
                                } else {
                                    // status 변경0 -> date 갱신
                                    $row_date[$Cell][0] = DATE('Y-m-d');
                                    $row_date[$Cell][1] = 1;
                                    $row_date[$Cell][2] = DATE('Y-m-d H:i:s');

                                    // cd_ex40 -> status 상태값 업데이트
                                    $row_date[B][1] = 1;
                                    $row_date[B][2] = DATE('Y-m-d H:i:s');

                                    if($Data[B] == 'D-IN') $cd_d_in_query = DATE('Y-m-d H:i:s');
                                    else if($Data[B] == 'D-WIN') $cd_d_win_query = DATE('Y-m-d H:i:s');
                                    else if($Data[B] == 'MP') $cd_mp_query = DATE('Y-m-d H:i:s');
                                    else if($Data[B] == 'LOST') $cd_lost_query = DATE('Y-m-d H:i:s');
                                    else if($Data[B] == 'PENDING') $cd_pending_query = DATE('Y-m-d H:i:s');

                                    if($Data[B] == 'LOST' || $Data[B] == 'PENDING') {
                                        $codip_status = $status_value->value;
                                        $codip_sub_status = $Data[B];
                                    } else {
//                                        if($status_value->value == 'LOST' || $status_value->value == 'PENDING') {
//                                            $codip_status = $key[cd_status];
//                                            $codip_sub_status = $key[cd_status];
//                                        } else {
                                            $codip_status = $Data[B];
                                            $codip_sub_status = $Data[B];
//                                        }
                                    }
                                }
                            }
                        }
                    } else if( $define[cd_column] == 'cd_ex1' ) {
                        // Signal
                        $str_uppper = strtoupper($Data[$Cell]);
                        $codip_signal = array('SAFE', 'WARNING', 'DANGER');
                        $jbstr = implode( ',', $codip_signal );
                        if (!in_array($str_uppper, $codip_signal)) {
                            // Signal을 잘못 기입했을 경우
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = $jbstr 중 하나만 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex51' ||  $define[cd_column] == 'cd_ex52' || $define[cd_column] == 'cd_ex55' ) {
                        // Core Project, New Customer Mark, HQ FAE Support -> Y or N 선택 Check
                        $Data[$Cell] = strtoupper($Data[$Cell]);
                        if(empty($Data[$Cell]))
                            $Data[$Cell] = '-';
                        else {
                            if( !($Data[$Cell] == 'Y') && !($Data[$Cell] == 'N') && !($Data[$Cell] == '-') ) {
                                $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                                $error_count++;
                                $row_error_count++;
                                $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 'Y' or 'N'만 입력해 주시기 바랍니다.</li>";
                            }
                        }
                    }
//                    else if( $define[cd_type] == 'date' ) {
//                        // 달력 양식 Check
//                        $calender = $Data[$Cell];
//                        $calender = str_replace("/", "-", $calender);
//                        $calendar_arry = explode('-', $calender);
//                        if ( checkdate($calendar_arry[1], $calendar_arry[2], $calendar_arry[0]) == false && !empty($Data[$Cell])) {
//                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
//                            $error_count++;
//                            $row_error_count++;
//                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 달력양식에 맞춰서 'yyyy-mm-dd' 입력해 주시기 바랍니다.</li>";
//                        }
//                    }
                    else if( $define[cd_column] == 'cd_ex41' ) {
                        if ( strpos($Data[$Cell], '%') !== false ) {
                            $Data[$Cell] = str_replace("%", "", $Data[$Cell]);
                            $row_date[$Cell][0] = $Data[$Cell]."%";
                        } else {
                            $Data[$Cell] = $Data[$Cell] * 100;
                            $row_date[$Cell][0] = $Data[$Cell]."%";
                        }
                        // 퍼센트 값 확인
                        if($Data[$Cell] > 100) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = $Data[$Cell]%를 정확하게 입력해 주시기 바랍니다.</li>";
                        }
                        $Data[$Cell] = $Data[$Cell]."%";
                    } else if($define[cd_type] == 'int') {
                        if(empty($Data[$Cell])) {
                            $row_date[$Cell][0] = 0;
                            $Data[$Cell] = 0;
                        }
                        // 숫자 확인
                        if (is_numeric($Data[$Cell]) == false) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = 숫자만 입력해 주시기 바랍니다.</li>";
                        }
                    } else if( $define[cd_column] == 'cd_ex53' ) {
                        if( $Data[B] != $CODiP_info[cd_sub_status] ) {
                            if($Data[B] == 'LOST' || $Data[B] == 'PENDING') {
                                if( empty($Data[$Cell]) ) {
                                    $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                                    $error_count++;
                                    $row_error_count++;
                                    $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = LOST/PENDING 으로 변경 할 경우 [LOST/PENDING Reason]를 작성해 주시기 바랍니다.</li>";
                                }
                            }
                        }
                    } else if( $define[cd_column] == 'cd_ex47' ) {
                        // status 변경시 -> Reason of project status change 값이 있는지 Check
                        if( empty($NEW) ) {
                            if( $Data[B] != $CODiP_info[cd_sub_status] ) {
                                if( empty($Data[$Cell]) ) {
                                    $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                                    $error_count++;
                                    $row_error_count++;
                                    $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$define[cd_name]}</span> = Status 변경 할 경우 [Reason of project status change]를 작성해 주시기 바랍니다.</li>";
                                }
                            }
                        }
                    }

                    // Q: Last Update, R: Identification date 는 시스템에서 설정
                    if( $Cell != 'Q' && $Cell != 'R' ) {
                        $td_error_html .= "<td style=\"cursor:pointer; padding: 3px; $td_update_style\">
                                                <div>
                                                    {$Data[$Cell]}
                                                </div>
                                            </td>";
                    }

                    $td_update_style = "";
                    $Cell++;
                }

                // CODiP_INFO / CODiP_INFO_History 테이블 명
                $query = " SELECT * FROM CODiP_FCST_INFO WHERE CODiP_Number = '$codip_number'";
                $select = $db->game->query($query);
                if ($select == false)
                    $db->error($select, $db->game, $query);

                foreach ($select AS $key => $val) {
                    $FCST_info = $val;
                }

                // FCST Define
                $query = " SHOW COLUMNS FROM CODiP_FCST_INFO ";
                $select = $db->game->query($query);
                if ($select == false)
                    $db->error($select, $db->game, $query);

                $Cell = 'AW';
                $check_point = false;
                while ($fcst_define = $select->fetch_assoc()) {
                    if ($fcst_define['Field'] === 'jan_qty_fcst_nw')
                        $check_point = true;

                    if ($check_point) {
                        $fcst_value = json_decode($FCST_info[$fcst_define['Field']]);
                        $row_date[$Cell][0] = $Data[$Cell];

                        if( empty($NEW) ) {
                            if (strcmp($Data[$Cell], $fcst_value->value) ) {
                                $td_update_style = " color: blue; font-weight: 700; text-align: right; background:skyblue;";
                                $update_count++;
                                $total_update_count++;
                                $row_date[$Cell][1] = 1;
                                $row_date[$Cell][2] = DATE('Y-m-d H:i:s');
                            } else {
                                $row_date[$Cell][1] = 0;
                                $row_date[$Cell][2] = '2000-01-01 00:00:00';
                            }
                        } else {
                            $total_update_count++;
                            $row_date[$Cell][1] = 0;
                            $row_date[$Cell][2] = '2000-01-01 00:00:00';
                        }

                        if(empty($Data[$Cell])) {
                            $row_date[$Cell][0] = 0;
                            $Data[$Cell] = 0;
                        }

                        // 숫자 확인
                        if (is_numeric($Data[$Cell]) == false && !empty($Data[$Cell])) {
                            $td_update_style = " color: red; font-weight: 700; text-align: right; background-color: yellow;";
                            $error_count++;
                            $row_error_count++;
                            $error_list_html .= "<li><span style='background: springgreen;'>{$row_date['A']}</span> - <span>{$fcst_define['Field']}</span> = 숫자만 입력해주시기 바랍니다.</li>";
                        }


                        if( !empty($Data[$Cell]) && is_numeric($Data[$Cell]) ) {
                            if(strpos($fcst_define['Field'], '_billing_fcst') !== false)
                                $result ="$".number_format($Data[$Cell], 3);
                            elseif(strpos($fcst_define['Field'], 'ASP_') !== false)
                                $result ="$".number_format($Data[$Cell], 3);
                            else
                                $result = $Data[$Cell];
                        } else
                            $result = $Data[$Cell];

                        $td_error_html .= "<td style=\"cursor:pointer; padding: 3px; $td_update_style\">
                                                <div>
                                                    {$result}
                                                </div>
                                            </td>";
                        $Cell++;
                        $td_update_style = "";
                    }
                }

                if($update_count > 0) {
                    // Last Update
                    $row_date[Q][0] = DATE('Y-m-d');
                    $row_date[Q][1] = 1;
                    $row_date[Q][2] = DATE('Y-m-d H:i:s');
                } else {
                    $row_date[Q][1] = 0;
                    $row_date[Q][2] = '2000-01-01 00:00:01';
                }

                if($row_error_count > 0)
                    $td_error_style = " border-style: solid;border-color: yellow;";
                else
                    $td_error_style = "";

//                $td_error_html .= "</tr>";
                $error_html .= "<tr style='$td_error_style' >$td_error_html</tr>";

                $codip_sql .= "('{$row_date['A']}',
                            '$codip_sub_status',
                            '$cd_opp_query',
                            '$cd_d_in_query',
                            '$cd_d_win_query',
                            '$cd_mp_query',
                            '$cd_lost_query',
                            '$cd_pending_query',
                            '{\"update\":\"{$row_date['C'][1]}\",\"define\":\"4\",\"date\":\"{$row_date['C'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['C'][0]}\"}',
                            '{\"update\":\"{$row_date['D'][1]}\",\"define\":\"9\",\"date\":\"{$row_date['D'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['D'][0]}\"}',
                            '{\"update\":\"{$row_date['E'][1]}\",\"define\":\"5\",\"date\":\"{$row_date['E'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['E'][0]}\"}',
                            '{\"update\":\"{$row_date['F'][1]}\",\"define\":\"6\",\"date\":\"{$row_date['F'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['F'][0]}\"}',
                            '{\"update\":\"{$row_date['G'][1]}\",\"define\":\"7\",\"date\":\"{$row_date['G'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['G'][0]}\"}',
                            '{\"update\":\"{$row_date['H'][1]}\",\"define\":\"12\",\"date\":\"{$row_date['H'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['H'][0]}\"}',
                            '{\"update\":\"{$row_date['I'][1]}\",\"define\":\"11\",\"date\":\"{$row_date['I'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['I'][0]}\"}',
                            '{\"update\":\"{$row_date['J'][1]}\",\"define\":\"14\",\"date\":\"{$row_date['J'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['J'][0]}\"}',
                            '{\"update\":\"{$row_date['K'][1]}\",\"define\":\"57\",\"date\":\"{$row_date['K'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['K'][0]}\"}',
                            '{\"update\":\"{$row_date['L'][1]}\",\"define\":\"56\",\"date\":\"{$row_date['L'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['L'][0]}\"}',
                            '{\"update\":\"{$row_date['M'][1]}\",\"define\":\"10\",\"date\":\"{$row_date['M'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['M'][0]}\"}',
                            '{\"update\":\"{$row_date['N'][1]}\",\"define\":\"50\",\"date\":\"{$row_date['N'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['N'][0]}\"}',
                            '{\"update\":\"{$row_date['O'][1]}\",\"define\":\"13\",\"date\":\"{$row_date['O'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['O'][0]}\"}',
                            '{\"update\":\"{$row_date['P'][1]}\",\"define\":\"48\",\"date\":\"{$row_date['P'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['P'][0]}\"}',
                            '$codip_status',
                            '{\"update\":\"{$row_date['Q'][1]}\",\"define\":\"2\",\"date\":\"{$row_date['Q'][2]}\",\"status\":\"COMMON\",\"type\":\"date\",\"value\":\"{$row_date['Q'][0]}\"}',
                            '{\"update\":\"{$row_date['R'][1]}\",\"define\":\"3\",\"date\":\"{$row_date['R'][2]}\",\"status\":\"COMMON\",\"type\":\"date\",\"value\":\"{$row_date['R'][0]}\"}',
                            '{\"update\":\"{$row_date['S'][1]}\",\"define\":\"1\",\"date\":\"{$row_date['S'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['S'][0]}\"}',
                            '{\"update\":\"{$row_date['T'][1]}\",\"define\":\"51\",\"date\":\"{$row_date['T'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['T'][0]}\"}',
                            '{\"update\":\"{$row_date['U'][1]}\",\"define\":\"55\",\"date\":\"{$row_date['U'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['U'][0]}\"}',
                            '{\"update\":\"{$row_date['V'][1]}\",\"define\":\"52\",\"date\":\"{$row_date['V'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['V'][0]}\"}',
                            '{\"update\":\"{$row_date['W'][1]}\",\"define\":\"54\",\"date\":\"{$row_date['W'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['W'][0]}\"}',
                            '{\"update\":\"{$row_date['X'][1]}\",\"define\":\"8\",\"date\":\"{$row_date['X'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['X'][0]}\"}',
                            '{\"update\":\"{$row_date['Y'][1]}\",\"define\":\"49\",\"date\":\"{$row_date['Y'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['Y'][0]}\"}',
                            '{\"update\":\"{$row_date['Z'][1]}\",\"define\":\"15\",\"date\":\"{$row_date['Z'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['Z'][0]}\"}',
                            '{\"update\":\"{$row_date['AA'][1]}\",\"define\":\"16\",\"date\":\"{$row_date['AA'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['AA'][0]}\"}',
                            '{\"update\":\"{$row_date['AB'][1]}\",\"define\":\"17\",\"date\":\"{$row_date['AB'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['AB'][0]}\"}',
                            '{\"update\":\"{$row_date['AC'][1]}\",\"define\":\"18\",\"date\":\"{$row_date['AC'][2]}\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$row_date['AC'][0]}\"}',
                            '{\"update\":\"{$row_date['AD'][1]}\",\"define\":\"19\",\"date\":\"{$row_date['AD'][2]}\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$row_date['AD'][0]}\"}',
                            '{\"update\":\"{$row_date['AE'][1]}\",\"define\":\"20\",\"date\":\"{$row_date['AE'][2]}\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$row_date['AE'][0]}\"}',
                            '{\"update\":\"{$row_date['AF'][1]}\",\"define\":\"21\",\"date\":\"{$row_date['AF'][2]}\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$row_date['AF'][0]}\"}',
                            '{\"update\":\"{$row_date['AG'][1]}\",\"define\":\"22\",\"date\":\"{$row_date['AG'][2]}\",\"status\":\"OPP\",\"type\":\"date\",\"value\":\"{$row_date['AG'][0]}\"}',
                            '{\"update\":\"{$row_date['AH'][1]}\",\"define\":\"23\",\"date\":\"{$row_date['AH'][2]}\",\"status\":\"COMMON\",\"type\":\"date\",\"value\":\"{$row_date['AH'][0]}\"}',
                            '{\"update\":\"{$row_date['AI'][1]}\",\"define\":\"24\",\"date\":\"{$row_date['AI'][2]}\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$row_date['AI'][0]}\"}',
                            '{\"update\":\"{$row_date['AJ'][1]}\",\"define\":\"25\",\"date\":\"{$row_date['AJ'][2]}\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$row_date['AJ'][0]}\"}',
                            '{\"update\":\"{$row_date['AK'][1]}\",\"define\":\"26\",\"date\":\"{$row_date['AK'][2]}\",\"status\":\"D-IND-WINMP\",\"type\":\"date\",\"value\":\"{$row_date['AK'][0]}\"}',
                            '{\"update\":\"{$row_date['AL'][1]}\",\"define\":\"27\",\"date\":\"{$row_date['AL'][2]}\",\"status\":\"COMMON\",\"type\":\"date\",\"value\":\"{$row_date['AL'][0]}\"}',

                            '{\"update\":\"{$row_date['B'][1]}\",\"define\":\"40\",\"date\":\"{$row_date['B'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$codip_sub_status}\"}',
                            '{\"update\":\"{$row_date['AM'][1]}\",\"define\":\"41\",\"date\":\"{$row_date['AM'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['AM'][0]}\"}',
                            '{\"update\":\"{$row_date['AN'][1]}\",\"define\":\"43\",\"date\":\"{$row_date['AN'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['AN'][0]}\"}',
                            '{\"update\":\"{$row_date['AO'][1]}\",\"define\":\"44\",\"date\":\"{$row_date['AO'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['AO'][0]}\"}',
                            '{\"update\":\"{$row_date['AP'][1]}\",\"define\":\"45\",\"date\":\"{$row_date['AP'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['AP'][0]}\"}',
                            '{\"update\":\"{$row_date['AQ'][1]}\",\"define\":\"46\",\"date\":\"{$row_date['AQ'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['AQ'][0]}\"}',
                            '{\"update\":\"{$row_date['AR'][1]}\",\"define\":\"47\",\"date\":\"{$row_date['AR'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['AR'][0]}\"}',
                            '{\"update\":\"{$row_date['AS'][1]}\",\"define\":\"53\",\"date\":\"{$row_date['AS'][2]}\",\"status\":\"COMMON\",\"type\":\"char\",\"value\":\"{$row_date['AS'][0]}\"}',
                            '{\"update\":\"{$row_date['AT'][1]}\",\"define\":\"28\",\"date\":\"{$row_date['AT'][2]}\",\"status\":\"COMMON\",\"type\":\"date\",\"value\":\"{$row_date['AT'][0]}\"}',
                            '{\"update\":\"{$row_date['AU'][1]}\",\"define\":\"29\",\"date\":\"{$row_date['AU'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['AU'][0]}\"}',
                            '{\"update\":\"{$row_date['AV'][1]}\",\"define\":\"30\",\"date\":\"{$row_date['AV'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['AV'][0]}\"}'), ";


                $fcst_sql .=  "('{$row_date['A']}',
                            '{\"update\":\"{$row_date['AW'][1]}\",\"define\":\"1\",\"date\":\"{$row_date['AW'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['AW'][0]}\"}',
                            '{\"update\":\"{$row_date['AX'][1]}\",\"define\":\"2\",\"date\":\"{$row_date['AX'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['AX'][0]}\"}',
                            '{\"update\":\"{$row_date['AY'][1]}\",\"define\":\"3\",\"date\":\"{$row_date['AY'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['AY'][0]}\"}',
                            '{\"update\":\"{$row_date['AZ'][1]}\",\"define\":\"4\",\"date\":\"{$row_date['AZ'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['AZ'][0]}\"}',
                            '{\"update\":\"{$row_date['BA'][1]}\",\"define\":\"5\",\"date\":\"{$row_date['BA'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BA'][0]}\"}',
                            '{\"update\":\"{$row_date['BB'][1]}\",\"define\":\"6\",\"date\":\"{$row_date['BB'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BB'][0]}\"}',
                            '{\"update\":\"{$row_date['BC'][1]}\",\"define\":\"7\",\"date\":\"{$row_date['BC'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BC'][0]}\"}',
                            '{\"update\":\"{$row_date['BD'][1]}\",\"define\":\"8\",\"date\":\"{$row_date['BD'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BD'][0]}\"}',
                            '{\"update\":\"{$row_date['BE'][1]}\",\"define\":\"9\",\"date\":\"{$row_date['BE'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BE'][0]}\"}',
                            '{\"update\":\"{$row_date['BF'][1]}\",\"define\":\"10\",\"date\":\"{$row_date['BF'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BF'][0]}\"}',
                            '{\"update\":\"{$row_date['BG'][1]}\",\"define\":\"11\",\"date\":\"{$row_date['BG'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BG'][0]}\"}',
                            '{\"update\":\"{$row_date['BH'][1]}\",\"define\":\"12\",\"date\":\"{$row_date['BH'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BH'][0]}\"}',

                            '{\"update\":\"{$row_date['BI'][1]}\",\"define\":\"13\",\"date\":\"{$row_date['BI'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BI'][0]}\"}',

                            '{\"update\":\"{$row_date['BJ'][1]}\",\"define\":\"14\",\"date\":\"{$row_date['BJ'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BJ'][0]}\"}',
                            '{\"update\":\"{$row_date['BK'][1]}\",\"define\":\"15\",\"date\":\"{$row_date['BK'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BK'][0]}\"}',
                            '{\"update\":\"{$row_date['BL'][1]}\",\"define\":\"16\",\"date\":\"{$row_date['BL'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BL'][0]}\"}',
                            '{\"update\":\"{$row_date['BM'][1]}\",\"define\":\"17\",\"date\":\"{$row_date['BM'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BM'][0]}\"}',
                            '{\"update\":\"{$row_date['BN'][1]}\",\"define\":\"18\",\"date\":\"{$row_date['BN'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BN'][0]}\"}',
                            '{\"update\":\"{$row_date['BO'][1]}\",\"define\":\"19\",\"date\":\"{$row_date['BO'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BO'][0]}\"}',
                            '{\"update\":\"{$row_date['BP'][1]}\",\"define\":\"20\",\"date\":\"{$row_date['BP'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BP'][0]}\"}',
                            '{\"update\":\"{$row_date['BQ'][1]}\",\"define\":\"21\",\"date\":\"{$row_date['BQ'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BQ'][0]}\"}',
                            '{\"update\":\"{$row_date['BR'][1]}\",\"define\":\"22\",\"date\":\"{$row_date['BR'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BR'][0]}\"}',
                            '{\"update\":\"{$row_date['BS'][1]}\",\"define\":\"23\",\"date\":\"{$row_date['BS'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BS'][0]}\"}',
                            '{\"update\":\"{$row_date['BT'][1]}\",\"define\":\"24\",\"date\":\"{$row_date['BT'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BT'][0]}\"}',
                            '{\"update\":\"{$row_date['BU'][1]}\",\"define\":\"25\",\"date\":\"{$row_date['BU'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BU'][0]}\"}',

                            '{\"update\":\"{$row_date['BV'][1]}\",\"define\":\"26\",\"date\":\"{$row_date['BV'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BV'][0]}\"}',

                            '{\"update\":\"{$row_date['BW'][1]}\",\"define\":\"27\",\"date\":\"{$row_date['BW'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BW'][0]}\"}',
                            '{\"update\":\"{$row_date['BX'][1]}\",\"define\":\"28\",\"date\":\"{$row_date['BX'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BX'][0]}\"}',
                            '{\"update\":\"{$row_date['BY'][1]}\",\"define\":\"29\",\"date\":\"{$row_date['BY'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BY'][0]}\"}',
                            '{\"update\":\"{$row_date['BZ'][1]}\",\"define\":\"30\",\"date\":\"{$row_date['BZ'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['BZ'][0]}\"}',
                            '{\"update\":\"{$row_date['CA'][1]}\",\"define\":\"31\",\"date\":\"{$row_date['CA'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CA'][0]}\"}',
                            '{\"update\":\"{$row_date['CB'][1]}\",\"define\":\"32\",\"date\":\"{$row_date['CB'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CB'][0]}\"}',
                            '{\"update\":\"{$row_date['CC'][1]}\",\"define\":\"33\",\"date\":\"{$row_date['CC'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CC'][0]}\"}',
                            '{\"update\":\"{$row_date['CD'][1]}\",\"define\":\"34\",\"date\":\"{$row_date['CD'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CD'][0]}\"}',
                            '{\"update\":\"{$row_date['CE'][1]}\",\"define\":\"35\",\"date\":\"{$row_date['CE'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CE'][0]}\"}',
                            '{\"update\":\"{$row_date['CF'][1]}\",\"define\":\"36\",\"date\":\"{$row_date['CF'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CF'][0]}\"}',
                            '{\"update\":\"{$row_date['CG'][1]}\",\"define\":\"37\",\"date\":\"{$row_date['CG'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CG'][0]}\"}',
                            '{\"update\":\"{$row_date['CH'][1]}\",\"define\":\"38\",\"date\":\"{$row_date['CH'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CH'][0]}\"}',

                            '{\"update\":\"{$row_date['CI'][1]}\",\"define\":\"39\",\"date\":\"{$row_date['CI'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CI'][0]}\"}',

                            '{\"update\":\"{$row_date['CJ'][1]}\",\"define\":\"40\",\"date\":\"{$row_date['CJ'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CJ'][0]}\"}',
                            '{\"update\":\"{$row_date['CK'][1]}\",\"define\":\"41\",\"date\":\"{$row_date['CK'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CK'][0]}\"}',
                            '{\"update\":\"{$row_date['CL'][1]}\",\"define\":\"42\",\"date\":\"{$row_date['CL'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CL'][0]}\"}',
                            '{\"update\":\"{$row_date['CM'][1]}\",\"define\":\"43\",\"date\":\"{$row_date['CM'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CM'][0]}\"}',
                            '{\"update\":\"{$row_date['CN'][1]}\",\"define\":\"44\",\"date\":\"{$row_date['CN'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CN'][0]}\"}',
                            '{\"update\":\"{$row_date['CO'][1]}\",\"define\":\"45\",\"date\":\"{$row_date['CO'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CO'][0]}\"}',
                            '{\"update\":\"{$row_date['CP'][1]}\",\"define\":\"46\",\"date\":\"{$row_date['CP'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CP'][0]}\"}',
                            '{\"update\":\"{$row_date['CQ'][1]}\",\"define\":\"47\",\"date\":\"{$row_date['CQ'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CQ'][0]}\"}',
                            '{\"update\":\"{$row_date['CR'][1]}\",\"define\":\"48\",\"date\":\"{$row_date['CR'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CR'][0]}\"}',
                            '{\"update\":\"{$row_date['CS'][1]}\",\"define\":\"49\",\"date\":\"{$row_date['CS'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CS'][0]}\"}',
                            '{\"update\":\"{$row_date['CT'][1]}\",\"define\":\"50\",\"date\":\"{$row_date['CT'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CT'][0]}\"}',
                            '{\"update\":\"{$row_date['CU'][1]}\",\"define\":\"51\",\"date\":\"{$row_date['CU'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CU'][0]}\"}',

                            '{\"update\":\"{$row_date['CV'][1]}\",\"define\":\"52\",\"date\":\"{$row_date['CV'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CV'][0]}\"}',

                            '{\"update\":\"{$row_date['CW'][1]}\",\"define\":\"53\",\"date\":\"{$row_date['CW'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CW'][0]}\"}',
                            '{\"update\":\"{$row_date['CX'][1]}\",\"define\":\"54\",\"date\":\"{$row_date['CX'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CX'][0]}\"}',
                            '{\"update\":\"{$row_date['CY'][1]}\",\"define\":\"55\",\"date\":\"{$row_date['CY'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CY'][0]}\"}',
                            '{\"update\":\"{$row_date['CZ'][1]}\",\"define\":\"56\",\"date\":\"{$row_date['CZ'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['CZ'][0]}\"}',
                            '{\"update\":\"{$row_date['DA'][1]}\",\"define\":\"57\",\"date\":\"{$row_date['DA'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DA'][0]}\"}',
                            '{\"update\":\"{$row_date['DB'][1]}\",\"define\":\"58\",\"date\":\"{$row_date['DB'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DB'][0]}\"}',
                            '{\"update\":\"{$row_date['DC'][1]}\",\"define\":\"59\",\"date\":\"{$row_date['DC'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DC'][0]}\"}',
                            '{\"update\":\"{$row_date['DD'][1]}\",\"define\":\"60\",\"date\":\"{$row_date['DD'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DD'][0]}\"}',
                            '{\"update\":\"{$row_date['DE'][1]}\",\"define\":\"61\",\"date\":\"{$row_date['DE'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DE'][0]}\"}',
                            '{\"update\":\"{$row_date['DF'][1]}\",\"define\":\"62\",\"date\":\"{$row_date['DF'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DF'][0]}\"}',
                            '{\"update\":\"{$row_date['DG'][1]}\",\"define\":\"63\",\"date\":\"{$row_date['DG'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DG'][0]}\"}',
                            '{\"update\":\"{$row_date['DH'][1]}\",\"define\":\"64\",\"date\":\"{$row_date['DH'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DH'][0]}\"}',

                            '{\"update\":\"{$row_date['DI'][1]}\",\"define\":\"65\",\"date\":\"{$row_date['DI'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DI'][0]}\"}',

                            '{\"update\":\"{$row_date['DJ'][1]}\",\"define\":\"66\",\"date\":\"{$row_date['DJ'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DJ'][0]}\"}',
                            '{\"update\":\"{$row_date['DK'][1]}\",\"define\":\"67\",\"date\":\"{$row_date['DK'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DK'][0]}\"}',
                            '{\"update\":\"{$row_date['DL'][1]}\",\"define\":\"68\",\"date\":\"{$row_date['DL'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DL'][0]}\"}',
                            '{\"update\":\"{$row_date['DM'][1]}\",\"define\":\"69\",\"date\":\"{$row_date['DM'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DM'][0]}\"}',
                            '{\"update\":\"{$row_date['DN'][1]}\",\"define\":\"70\",\"date\":\"{$row_date['DN'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DN'][0]}\"}',
                            '{\"update\":\"{$row_date['DO'][1]}\",\"define\":\"71\",\"date\":\"{$row_date['DO'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DO'][0]}\"}',
                            '{\"update\":\"{$row_date['DP'][1]}\",\"define\":\"72\",\"date\":\"{$row_date['DP'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DP'][0]}\"}',
                            '{\"update\":\"{$row_date['DQ'][1]}\",\"define\":\"73\",\"date\":\"{$row_date['DQ'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DQ'][0]}\"}',
                            '{\"update\":\"{$row_date['DR'][1]}\",\"define\":\"74\",\"date\":\"{$row_date['DR'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DR'][0]}\"}',
                            '{\"update\":\"{$row_date['DS'][1]}\",\"define\":\"75\",\"date\":\"{$row_date['DS'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DS'][0]}\"}',
                            '{\"update\":\"{$row_date['DT'][1]}\",\"define\":\"76\",\"date\":\"{$row_date['DT'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DT'][0]}\"}',
                            '{\"update\":\"{$row_date['DU'][1]}\",\"define\":\"77\",\"date\":\"{$row_date['DU'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DU'][0]}\"}',

                            '{\"update\":\"{$row_date['DV'][1]}\",\"define\":\"78\",\"date\":\"{$row_date['DV'][2]}\",\"status\":\"COMMON\",\"type\":\"int\",\"value\":\"{$row_date['DV'][0]}\"}' ), ";

            }
        }

        if($total_update_count == 0) {
            $return_value = array(
                'code' => 3,
                'msg' => "업데이트 목록이 없습니다. 다시 확인해주시기 바랍니다."
            );
        } else {
            if($error_count > 0) {
                $error_html .= "<tr style=\"cursor: pointer;padding: 3px;color: red;font-weight: 700;text-align: right;background-color: yellow;\">
                                <td><div style='margin: 5px; font-size: 11px;'>ERROR COUNT = $error_count</div></td></tr>";
                $return_value = array(
                    'code' => 3,
                    'msg' => "codip upload 파일에 오류가 있습니다. 다시 확인해주시기 바랍니다."
                );
            } else {
//                $error_html .= "<tr style=\"cursor: pointer;padding: 3px;color: red;font-weight: 700;text-align: right;background-color: skyblue;\">
//                                <td><div style='margin: 5px; font-size: 11px;'>UPDATE COUNT = $total_update_count</div></td></tr>";
                $upload_codip_column = "";
                // CODiP_INFO_2020_second_half
                //                         CODiP_Number, Status,        cd_d_in_reg_date, cd_lost_reg_date, cd_d_win_reg_date, cd_mp_reg_date, Region, Sales Man,   Distributor, Customer, End Customer, Application (Others), Application, Device, Base Array_wip, PKG type, Sector, Sub-Sector, Project
                $upload_codip_column .= "( CODiP_Number, cd_sub_status, cd_opp_reg_date, cd_d_in_reg_date, cd_lost_reg_date, cd_d_win_reg_date, cd_mp_reg_date, cd_pending_reg_date, cd_ex4, cd_ex9,      cd_ex5,      cd_ex6,   cd_ex7,       cd_ex12,              cd_ex11,     cd_ex14, cd_ex57,       cd_ex56,  cd_ex10, cd_ex50,   cd_ex13,";
                //                        Marketer, Main_Status, Last Update, Identification date, Signal, Core Project, New Customer Mark, HQ FAE Support, Marketing Support, ODM,    Function, Customer Project name, Competitor, Competitor Device Name,
                $upload_codip_column .= " cd_ex48,  cd_status,   cd_ex2,       cd_ex3,             cd_ex1, cd_ex51,      cd_ex55,           cd_ex52,        cd_ex54,           cd_ex8, cd_ex49,  cd_ex15,               cd_ex16,   cd_ex17,";
                //                       Sample Support Date, Hardware Tool Support Date, Software Tool Support Date, Design-In Start Date, Target D-in Date, Target D-win Date, Customer DV(Design Verification), Custome PV(Product Verification),
                $upload_codip_column .= " cd_ex18,            cd_ex19,                    cd_ex20,                    cd_ex21,              cd_ex22,          cd_ex23,           cd_ex24,                          cd_ex25,";
                //                       PP (Pilot Production) Date, Target MP Date, Status,  Confidence, Support Resources, Project Status Issues Actions Comments(Sales), Project Status Issues ActionsComments(FAE), Project Status Issues Actions  Comments(MKT),
                $upload_codip_column .= " cd_ex26,                   cd_ex27,        cd_ex40, cd_ex41,    cd_ex43,           cd_ex44,                                       cd_ex45,                                    cd_ex46, ";
                //                         Reason of project status change, LOST/PENDING Reason, EAU(Kpcs Projection), ASP($), Annual Impact(k$)
                $upload_codip_column .= "  cd_ex47,                         cd_ex53,             cd_ex28,              cd_ex29, cd_ex30)";


                // SET UPDATE 항목 끝에 , 제거
                $end_length = strlen($codip_sql) - 2;
                $codip_sql = substr($codip_sql, 0, $end_length);

                $query = "INSERT INTO CODiP_INFO_2020_second_half $upload_codip_column
                                        VALUES
                                       $codip_sql
                       ON DUPLICATE KEY UPDATE
                       cd_status = VALUES(cd_status), cd_sub_status = VALUES(cd_sub_status), cd_opp_reg_date = VALUES(cd_opp_reg_date), cd_d_in_reg_date = VALUES(cd_d_in_reg_date), cd_d_win_reg_date = VALUES(cd_d_win_reg_date),
                       cd_mp_reg_date = VALUES(cd_mp_reg_date), cd_lost_reg_date = VALUES(cd_lost_reg_date), cd_pending_reg_date = VALUES(cd_pending_reg_date),
                       cd_ex4 = VALUES(cd_ex4), cd_ex9 = VALUES(cd_ex9), cd_ex5 = VALUES(cd_ex5), cd_ex6 = VALUES(cd_ex6),cd_ex7 = VALUES(cd_ex7),cd_ex12 = VALUES(cd_ex12),cd_ex11 = VALUES(cd_ex11),
                       cd_ex14 = VALUES(cd_ex14),cd_ex57 = VALUES(cd_ex57),cd_ex56 = VALUES(cd_ex56),cd_ex10 = VALUES(cd_ex10),cd_ex50 = VALUES(cd_ex50),cd_ex13 = VALUES(cd_ex13),cd_ex48 = VALUES(cd_ex48),cd_ex2 = VALUES(cd_ex2),
                       cd_ex3 = VALUES(cd_ex3),cd_ex1 = VALUES(cd_ex1),cd_ex51 = VALUES(cd_ex51),cd_ex55 = VALUES(cd_ex55),cd_ex52 = VALUES(cd_ex52),cd_ex54 = VALUES(cd_ex54),cd_ex8 = VALUES(cd_ex8),cd_ex49 = VALUES(cd_ex49),
                       cd_ex15 = VALUES(cd_ex15),cd_ex16 = VALUES(cd_ex16),cd_ex17 = VALUES(cd_ex17),cd_ex18 = VALUES(cd_ex18),cd_ex19 = VALUES(cd_ex19),cd_ex20 = VALUES(cd_ex20),cd_ex21 = VALUES(cd_ex21),cd_ex22 = VALUES(cd_ex22),
                       cd_ex23 = VALUES(cd_ex23),cd_ex24 = VALUES(cd_ex24),cd_ex25 = VALUES(cd_ex25),cd_ex26 = VALUES(cd_ex26),cd_ex27 = VALUES(cd_ex27),cd_ex40 = VALUES(cd_ex40),cd_ex41 = VALUES(cd_ex41),cd_ex43 = VALUES(cd_ex43),
                       cd_ex44 = VALUES(cd_ex44),cd_ex45 = VALUES(cd_ex45),cd_ex46 = VALUES(cd_ex46),cd_ex47 = VALUES(cd_ex47),cd_ex53 = VALUES(cd_ex53),cd_ex28 = VALUES(cd_ex28),cd_ex29 = VALUES(cd_ex29),cd_ex30 = VALUES(cd_ex30)";
                $query = preg_replace('/\r\n|\r|\n/','',$query);
                $insert_update = $db->game->query($query);
                if ($insert_update == false) {
                    $db->error($insert_update, $db->game, $query);
                    $return_value = array(
                        'code' => 3,
                        'msg' => "CODIP 업로드 중 오류가 발생했습니다. 관리자에게 문의해주시기 바랍니다."
                    );
                }

                $upload_fcst_column = "";
                // CODiP_FCST_INFO
                // NOW YEAR
                $upload_fcst_column .= "( CODiP_Number, ";
                //                       2023-1 Qty FCST(Kpcs), 2023-2 Qty FCST(Kpcs), 2023-3 Qty FCST(Kpcs), 2023-4 Qty FCST(Kpcs), 2023-5 Qty FCST(Kpcs), 2023-6 Qty FCST(Kpcs), 2023-7 Qty FCST(Kpcs), 2023-8 Qty FCST(Kpcs), 2023-9 Qty FCST(Kpcs), 2023-10 Qty FCST(Kpcs), 2023-11 Qty FCST(Kpcs), 2023-12 Qty FCST(Kpcs), 2023 ASP($),
                $upload_fcst_column .= " jan_qty_fcst_nw, feb_qty_fcst_nw, mar_qty_fcst_nw, apr_qty_fcst_nw, may_qty_fcst_nw, jun_qty_fcst_nw, jul_qty_fcst_nw, aug_qty_fcst_nw, sep_qty_fcst_nw, oct_qty_fcst_nw, nov_qty_fcst_nw, dec_qty_fcst_nw, ASP_fcst_nw, ";
                //                       2023-1 Billing FCST(k$)), 2023-2 Billing FCST(k$)), 2023-3 Billing FCST(k$)), 2023-4 Billing FCST(k$)), 2023-5 Billing FCST(k$)), 2023-6 Billing FCST(k$)), 2023-7 Billing FCST(k$)), 2023-8 Billing FCST(k$)), 2023-9 Billing FCST(k$)), 2023-10 Billing FCST(k$)), 2023-11 Billing FCST(k$)), 2023-12 Billing FCST(k$)) 2023 total_billing_fcst_nw
                $upload_fcst_column .= " jan_billing_fcst_nw, feb_billing_fcst_nw, mar_billing_fcst_nw, apr_billing_fcst_nw, may_billing_fcst_nw, jun_billing_fcst_nw, jul_billing_fcst_nw, aug_billing_fcst_nw, sep_billing_fcst_nw, oct_billing_fcst_nw, nov_billing_fcst_nw, dec_billing_fcst_nw, total_billing_fcst_nw, ";

                // NEXT YEAR
                //                       2024-1 Qty FCST(Kpcs), 2024-2 Qty FCST(Kpcs), 2024-3 Qty FCST(Kpcs), 2024-4 Qty FCST(Kpcs), 2024-5 Qty FCST(Kpcs), 2024-6 Qty FCST(Kpcs), 2024-7 Qty FCST(Kpcs), 2024-8 Qty FCST(Kpcs), 2024-9 Qty FCST(Kpcs), 2024-10 Qty FCST(Kpcs), 2024-11 Qty FCST(Kpcs), 2024-12 Qty FCST(Kpcs), 2024 ASP($),
                $upload_fcst_column .= " jan_qty_fcst_nxt, feb_qty_fcst_nxt, mar_qty_fcst_nxt, apr_qty_fcst_nxt, may_qty_fcst_nxt, jun_qty_fcst_nxt, jul_qty_fcst_nxt, aug_qty_fcst_nxt, sep_qty_fcst_nxt, oct_qty_fcst_nxt, nov_qty_fcst_nxt, dec_qty_fcst_nxt, ASP_fcst_nxt, ";
                //                       2024-1 Billing FCST(k$)), 2024-2 Billing FCST(k$)), 2024-3 Billing FCST(k$)), 2024-4 Billing FCST(k$)), 2024-5 Billing FCST(k$)), 2024-6 Billing FCST(k$)), 2024-7 Billing FCST(k$)), 2024-8 Billing FCST(k$)), 2024-9 Billing FCST(k$)), 2024-10 Billing FCST(k$)), 2024-11 Billing FCST(k$)), 2024-12 Billing FCST(k$)), 2024 total_billing_fcst_nw
                $upload_fcst_column .= " jan_billing_fcst_nxt, feb_billing_fcst_nxt, mar_billing_fcst_nxt, apr_billing_fcst_nxt, may_billing_fcst_nxt, jun_billing_fcst_nxt, jul_billing_fcst_nxt, aug_billing_fcst_nxt, sep_billing_fcst_nxt, oct_billing_fcst_nxt, nov_billing_fcst_nxt, dec_billing_fcst_nxt, total_billing_fcst_nxt, ";

                // IN TWO YEARS
                //                       2025-1 Qty FCST(Kpcs), 2025-2 Qty FCST(Kpcs), 2025-3 Qty FCST(Kpcs), 2025-4 Qty FCST(Kpcs), 2025-5 Qty FCST(Kpcs), 2025-6 Qty FCST(Kpcs), 2025-7 Qty FCST(Kpcs), 2025-8 Qty FCST(Kpcs), 2025-9 Qty FCST(Kpcs), 2025-10 Qty FCST(Kpcs), 2025-11 Qty FCST(Kpcs), 2025-12 Qty FCST(Kpcs), 2025 ASP($),
                $upload_fcst_column .= " jan_qty_fcst_nnxt, feb_qty_fcst_nnxt, mar_qty_fcst_nnxt, apr_qty_fcst_nnxt, may_qty_fcst_nnxt, jun_qty_fcst_nnxt, jul_qty_fcst_nnxt, aug_qty_fcst_nnxt, sep_qty_fcst_nnxt, oct_qty_fcst_nnxt, nov_qty_fcst_nnxt, dec_qty_fcst_nnxt, ASP_fcst_nnxt, ";
                //                       2025-1 Billing FCST(k$)), 2025-2 Billing FCST(k$)), 2025-3 Billing FCST(k$)), 2025-4 Billing FCST(k$)), 2025-5 Billing FCST(k$)), 2025-6 Billing FCST(k$)), 2025-7 Billing FCST(k$)), 2025-8 Billing FCST(k$)), 2025-9 Billing FCST(k$)), 2025-10 Billing FCST(k$)), 2025-11 Billing FCST(k$)), 2025-12 Billing FCST(k$)), 2025 total_billing_fcst_nw
                $upload_fcst_column .= " jan_billing_fcst_nnxt, feb_billing_fcst_nnxt, mar_billing_fcst_nnxt, apr_billing_fcst_nnxt, may_billing_fcst_nnxt, jun_billing_fcst_nnxt, jul_billing_fcst_nnxt, aug_billing_fcst_nnxt, sep_billing_fcst_nnxt, oct_billing_fcst_nnxt, nov_billing_fcst_nnxt, dec_billing_fcst_nnxt, total_billing_fcst_nnxt)";

                // SET UPDATE 항목 끝에 , 제거
                $end_length = strlen($fcst_sql) - 2;
                $fcst_sql = substr($fcst_sql, 0, $end_length);

                $query = "INSERT INTO CODiP_FCST_INFO $upload_fcst_column
                                        VALUES
                                       $fcst_sql
                       ON DUPLICATE KEY UPDATE
                        jan_qty_fcst_nw = VALUES(jan_qty_fcst_nw), feb_qty_fcst_nw = VALUES(feb_qty_fcst_nw), mar_qty_fcst_nw = VALUES(mar_qty_fcst_nw), apr_qty_fcst_nw = VALUES(apr_qty_fcst_nw), may_qty_fcst_nw = VALUES(may_qty_fcst_nw),
                        jun_qty_fcst_nw = VALUES(jun_qty_fcst_nw),jul_qty_fcst_nw = VALUES(jul_qty_fcst_nw),aug_qty_fcst_nw = VALUES(aug_qty_fcst_nw),sep_qty_fcst_nw = VALUES(sep_qty_fcst_nw),oct_qty_fcst_nw = VALUES(oct_qty_fcst_nw),
                        nov_qty_fcst_nw = VALUES(nov_qty_fcst_nw),dec_qty_fcst_nw = VALUES(dec_qty_fcst_nw),
                        ASP_fcst_nw = VALUES(ASP_fcst_nw),
                        jan_billing_fcst_nw = VALUES(jan_billing_fcst_nw),feb_billing_fcst_nw = VALUES(feb_billing_fcst_nw),mar_billing_fcst_nw = VALUES(mar_billing_fcst_nw),apr_billing_fcst_nw = VALUES(apr_billing_fcst_nw),
                        may_billing_fcst_nw = VALUES(may_billing_fcst_nw),jun_billing_fcst_nw = VALUES(jun_billing_fcst_nw),jul_billing_fcst_nw = VALUES(jul_billing_fcst_nw),aug_billing_fcst_nw = VALUES(aug_billing_fcst_nw),
                        sep_billing_fcst_nw = VALUES(sep_billing_fcst_nw),oct_billing_fcst_nw = VALUES(oct_billing_fcst_nw),nov_billing_fcst_nw = VALUES(nov_billing_fcst_nw),dec_billing_fcst_nw = VALUES(dec_billing_fcst_nw),
                        total_billing_fcst_nw = VALUES(total_billing_fcst_nw),

                        jan_qty_fcst_nxt = VALUES(jan_qty_fcst_nxt), feb_qty_fcst_nxt = VALUES(feb_qty_fcst_nxt), mar_qty_fcst_nxt = VALUES(mar_qty_fcst_nxt), apr_qty_fcst_nxt = VALUES(apr_qty_fcst_nxt), may_qty_fcst_nxt = VALUES(may_qty_fcst_nxt),
                        jun_qty_fcst_nxt = VALUES(jun_qty_fcst_nxt),jul_qty_fcst_nxt = VALUES(jul_qty_fcst_nxt),aug_qty_fcst_nxt = VALUES(aug_qty_fcst_nxt),sep_qty_fcst_nxt = VALUES(sep_qty_fcst_nxt),oct_qty_fcst_nxt = VALUES(oct_qty_fcst_nxt),
                        nov_qty_fcst_nxt = VALUES(nov_qty_fcst_nxt),dec_qty_fcst_nxt = VALUES(dec_qty_fcst_nxt),
                        ASP_fcst_nxt = VALUES(ASP_fcst_nxt),
                        jan_billing_fcst_nxt = VALUES(jan_billing_fcst_nxt),feb_billing_fcst_nxt = VALUES(feb_billing_fcst_nxt),mar_billing_fcst_nxt = VALUES(mar_billing_fcst_nxt),apr_billing_fcst_nxt = VALUES(apr_billing_fcst_nxt),
                        may_billing_fcst_nxt = VALUES(may_billing_fcst_nxt),jun_billing_fcst_nxt = VALUES(jun_billing_fcst_nxt),jul_billing_fcst_nxt = VALUES(jul_billing_fcst_nxt),aug_billing_fcst_nxt = VALUES(aug_billing_fcst_nxt),
                        sep_billing_fcst_nxt = VALUES(sep_billing_fcst_nxt),oct_billing_fcst_nxt = VALUES(oct_billing_fcst_nxt),nov_billing_fcst_nxt = VALUES(nov_billing_fcst_nxt),dec_billing_fcst_nxt = VALUES(dec_billing_fcst_nxt),
                        total_billing_fcst_nxt = VALUES(total_billing_fcst_nxt),

                        jan_qty_fcst_nnxt = VALUES(jan_qty_fcst_nnxt), feb_qty_fcst_nnxt = VALUES(feb_qty_fcst_nnxt), mar_qty_fcst_nnxt = VALUES(mar_qty_fcst_nnxt), apr_qty_fcst_nnxt = VALUES(apr_qty_fcst_nnxt), may_qty_fcst_nnxt = VALUES(may_qty_fcst_nnxt),
                        jun_qty_fcst_nnxt = VALUES(jun_qty_fcst_nnxt),jul_qty_fcst_nnxt = VALUES(jul_qty_fcst_nnxt),aug_qty_fcst_nnxt = VALUES(aug_qty_fcst_nnxt),sep_qty_fcst_nnxt = VALUES(sep_qty_fcst_nnxt),oct_qty_fcst_nnxt = VALUES(oct_qty_fcst_nnxt),
                        nov_qty_fcst_nnxt = VALUES(nov_qty_fcst_nnxt),dec_qty_fcst_nnxt = VALUES(dec_qty_fcst_nnxt),
                        ASP_fcst_nnxt = VALUES(ASP_fcst_nnxt),
                        jan_billing_fcst_nnxt = VALUES(jan_billing_fcst_nnxt),feb_billing_fcst_nnxt = VALUES(feb_billing_fcst_nnxt),mar_billing_fcst_nnxt = VALUES(mar_billing_fcst_nnxt),apr_billing_fcst_nnxt = VALUES(apr_billing_fcst_nnxt),
                        may_billing_fcst_nnxt = VALUES(may_billing_fcst_nnxt),jun_billing_fcst_nnxt = VALUES(jun_billing_fcst_nnxt),jul_billing_fcst_nnxt = VALUES(jul_billing_fcst_nnxt),aug_billing_fcst_nnxt = VALUES(aug_billing_fcst_nnxt),
                        sep_billing_fcst_nnxt = VALUES(sep_billing_fcst_nnxt),oct_billing_fcst_nnxt = VALUES(oct_billing_fcst_nnxt),nov_billing_fcst_nnxt = VALUES(nov_billing_fcst_nnxt),dec_billing_fcst_nnxt = VALUES(dec_billing_fcst_nnxt),
                        total_billing_fcst_nnxt = VALUES(total_billing_fcst_nnxt) ";
                $query = preg_replace('/\r\n|\r|\n/','',$query);
                $insert_update = $db->game->query($query);
                if ($insert_update == false) {
                    $db->error($insert_update, $db->game, $query);
                    $return_value = array(
                        'code' => 3,
                        'msg' => "FCST 업로드 중 오류가 발생했습니다. 관리자에게 문의해주시기 바랍니다."
                    );
                } else {
                    $return_value['code'] = 5;
                    $_POST['upload_id'] = ""; // 엑셀 업로드가 성공했으니 첨부파일 정보는 초기화
                }
            }
        }

        $return_value['error_html'] = $error_html;
        $return_value['error_list_html'] = $error_list_html;

        echo json_encode($return_value);
        return true;
    }
}

// 함수 호출 부분
$function_name = '';
if (isset($_POST['function']) && !empty($_POST['function'])) {
    $function_name = $_POST['function'];
}

if (function_exists($function_name)) {
    // 변수값으로 함수 호출
    if ($function_name($Db)) {
        // 트랜잭션 완료
        // $Db->trans_commit();
    } else {
        // 트랜잭션 롤백
        // $Db->trans_rollback();
    }
} else {
    // 함수가 존재 하지 않을 시 반환할 값
    echo json_encode(
        array(
            'function' => $function_name,
            'code' => -2,
            'msg' => '존재하지 않는 함수입니다.'
        )
    );
}
?>
