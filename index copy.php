<?php
// include "./head.lib.php";
session_start();

if (!$_SESSION['sess_userid']) {
    ?><script>location.replace("login.php");</script><?php
} else {
    ?><script>location.replace("introduction.php");</script><?php
}
print_r($_SESSION);

?>


