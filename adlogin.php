<?php
include "./conndb.php";
$db_link=db_conn();
mysqli_select_db($db_link,$DB_SNAME);
if(isset($_POST['userid']) && isset($_POST['userpassword'])) {
    session_start();
    $adServer = "ldap://210.102.6.140";
    $ldap = ldap_connect($adServer);
    $username = $_POST['userid'];
    $password = $_POST['userpassword'];
    $ldaprdn = 'abov' . "\\" . $username;
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

    $bind = @ldap_bind($ldap, $ldaprdn, $password);
    if ($bind) { // id없으면 insert 안되도록
        $filter="(sAMAccountName=$username)";
        $result = ldap_search($ldap,"dc=abov,dc=co,dc=kr",$filter);

        ldap_sort($ldap,$result,"sn");
        $info = ldap_get_entries($ldap, $result);

        session_start();
        //세션값 생성
        $_SESSION['sess_userid'] = $info[0]["displayname"][0];
        $_SESSION['sess_username'] = $info[0]["samaccountname"][0];

        $SQL = " SELECT * FROM eval_user WHERE user_id = '$_SESSION[sess_username]'";
        $result = mysqli_query($db_link, $SQL);
        $row = mysqli_fetch_array($result);
        
        $_SESSION['sess_grade'] = $row['user_grade'];
        $_SESSION['sess_level'] = $row['user_level'];
        $_SESSION['sess_manage'] = $row['user_manage'];

        if($result->num_rows == 0) {
            $SQL = " insert into eval_user (user_id, user_name, user_group, user_team, last_login, user_use)
                            values       ('$_SESSION[sess_username]', '{$info[0][displayname][0]}', '{$info[0][description][0]}', '{$info[0][department][0]}', now(), '1')";
            $result = mysqli_query($db_link, $SQL);
        } else {
            // 기본 정보 업그레이드
            $SQL = "UPDATE eval_user
                    SET user_group = '{$info[0][description][0]}',
                        user_team  = '{$info[0][department][0]}',
                        last_login = now()
                    WHERE user_id = '$_SESSION[sess_username]' ";
            $result = mysqli_query($db_link, $SQL);
        }

        @ldap_close($ldap);
    } else {
        // 관리자 계정인지 확인
        $SQL = " SELECT * FROM eval_user WHERE user_id = '$_POST[userid]' AND user_pw = '$_POST[userpassword]'";
        $result = mysqli_query($db_link, $SQL);

        if($result->num_rows == 0) {
            $msg = "Invalid email address / password";
            echo "<script>
                    alert(\"아이디 또는 비밀번호가 다릅니다.\");
              </script>";
        } else {
        }
    }
}
// print_r($_SESSION);
?>
<script>
    location.replace("index.php");
</script>
