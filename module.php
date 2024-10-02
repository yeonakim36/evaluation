<?php
$PAGE_INDEX = $_POST['p'];

switch ($PAGE_INDEX){
    case "excel" :
        include_once "module/excel.php";
        break;
    case "excel_" :
        include_once "module/excel.php";
        break;
}