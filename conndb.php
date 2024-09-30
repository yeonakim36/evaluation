<?php
//SQL환경 설정
$DB_HOST="210.102.6.44";
$DB_USER="root";
$DB_PASSWD="Djqhqmqksehcp7!";
$DB_SNAME="ABOV Evaluation";
$db_link = false;
$db_port = 13306;

// $DB_HOST="210.102.6.88";
// $DB_USER="root";
// $DB_PASSWD="Djqhqmqksehcp7!";
// $DB_SNAME="ABOV Evaluation";
// $db_link = false;
// $db_port = 13336;

//DB 서버 연결
function db_conn(){
    global $DB_HOST,$DB_USER,$DB_PASSWD,$DB_SNAME,$db_link,$db_port;

	$db_link = mysqli_connect($DB_HOST,$DB_USER,$DB_PASSWD,$DB_SNAME,$db_port);

	if (mysqli_connect_errno($db_link)){
		echo "DB connect error";
		exit;
	}

	return $db_link;
}

function Debug( $message, $printInfo = NULL )
{
    $hFile = fopen( "/res/log/debug-log.txt", "a+" );
    if( $hFile == false )
        return false;

    $message = '[ '. date("Y-m-d H:i:s") . ' ] : ' . $message . "\n";
    fwrite( $hFile, $message );

    if( $printInfo != NULL )
    {
        $writeString = "\n" . print_r( $printInfo, true ) . "\n";
        fwrite( $hFile, $writeString );
    }

    fclose( $hFile );

    return true;
}
