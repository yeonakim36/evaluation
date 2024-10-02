<?php
include "./conndb.php";

$db_link=db_conn();
mysqli_select_db($db_link,$DB_SNAME);

$adServer = "ldap://210.102.6.151";
$ldap = ldap_connect($adServer);
$username = 'yeona.kim';
$password = 'Kitty0034**';
$ldaprdn = 'abov' . "\\" . $username;

ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);

if ($ldap) {
    $bind = @ldap_bind($ldap, $ldaprdn, $password);
    if ($bind) { 
        $filter="(objectClass=user)";
        $result = ldap_search($ldap,"OU=Users,OU=ABOV,dc=abov,dc=co,dc=kr",$filter);
        $info = ldap_get_entries($ldap, $result);

        for ($i = 0; $i < $info['count']; $i++) {
            $user_id = $info[$i]["samaccountname"][0];
            $user_name = $info[$i]["displayname"][0];
            $user_group = $info[$i]["description"][0];
            $user_team = $info[$i]["department"][0];

            $SQL = " SELECT * FROM eval_user WHERE user_id = '$_SESSION[sess_username]'";
            $result = mysqli_query($db_link, $SQL);

            if($result->num_rows == 0) {
                $SQL = " insert into eval_user (user_id, user_name, user_group, user_team, last_login, user_use)
                                        values ('$user_id', '$user_name', '$user_group', '$user_team', now(), '1')";
                $result = mysqli_query($db_link, $SQL);
            } else {
                $SQL = "UPDATE eval_user
                        SET user_group = '$user_group',
                            user_team  = '$user_team'";
                $result = mysqli_query($db_link, $SQL);
            }
            // echo "id: " . $user_id . "<br>";
            // echo "이름: " . $user_name . "<br>";
            // echo "그룹: " . $user_team . "<br>";
            // echo "<hr>";
        }
        // print_r($info);
        // 이메일 - $info[$i]['mail'][0], 이름 - $info[$i]['cn'][0]

        @ldap_close($ldap);
    } else {
        echo "LDAP 바인드 실패";
    }
}
?>

