<?php
include_once "./conndb.php";

$db_link = db_conn();

$update_sql_tmp = "UPDATE eval_user SET user_use = '0' WHERE user_type = '1';";
$result_tmp = mysqli_query($db_link, $update_sql_tmp);

$adServer = "ldap://210.102.6.151";
$ldap = ldap_connect($adServer);
$username = 'pmsadmin';
$password = 'woehdir!2';
// $username = 'yeona.kim';
// $password = 'Kitty0000!!';
$ldaprdn = 'abov' . "\\" . $username;
ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

if ($ldap) {
    echo "1";
    $bind = @ldap_bind($ldap, $ldaprdn, $password);
    if ($bind) {
        echo "2";
        $filter="(objectClass=user)";
        $result = ldap_search($ldap,"OU=Users,OU=ABOV,dc=abov,dc=co,dc=kr",$filter);
        $info = ldap_get_entries($ldap, $result);

        for ($i = 0; $i < $info['count']; $i++) {
            $user_id = $info[$i]["samaccountname"][0];
            $user_name = $info[$i]["displayname"][0];
            $user_group = $info[$i]["description"][0];
            $user_team = $info[$i]["department"][0];

            $SQL = " SELECT * FROM eval_user WHERE user_id = '$user_id' AND user_name = '$user_name';";
            $result1 = mysqli_query($db_link, $SQL);

            if ($result1->num_rows == 0) { //새로운 사용자
                echo "new";
                $insert_sql = "INSERT IGNORE INTO eval_user (user_id, user_name, user_group, user_team, last_login, user_use, user_type)
                                VALUES ('$user_id', '$user_name', '$user_group', '$user_team', now(), '1', '1');";
                $result2 = mysqli_query($db_link, $insert_sql);
            } else { //기존사용자
                $update_sql = "UPDATE eval_user
                            SET user_group = '$user_group', user_team  = '$user_team', user_type = '1', user_use = '1'
                            WHERE user_id = '$user_id' AND user_name = '$user_name' AND user_type <> 5 ;";
                $result3 = mysqli_query($db_link, $update_sql);

                echo $update_sql;
            }
        }
        @ldap_close($ldap);

        $msg = "입력이 완료되었습니다.";
        echo $msg;

    } else {
        echo "LDAP 바인드 실패";
    }
}
?>

