<?php
include "./head.lib.php";

if($_SESSION['sess_userid']) { ?>
    <script>
        location.replace("introduction.php");
    </script> <?
    exit;
}
?>
<!---------------------------------------------------------------- // 헤더영역 시작 ----------------------------------------------------------->
<script src="js/jquery.bpopup2.min.js" type="text/javascript"></script>
<script language=javascript>
    $(function () {
        // 아이디 검사
        $("input[name='userid']").blur(function() {
            if ($(this).val() == "") {
                $("input[name='userid']").focus();
                $(this).next().text('아이디을 입력해주세요.');
                $(this).next().show();
            }
        });
    });

    function goLogin()
    {
        var frm = document.frm;
        if(!$.trim(frm.userid.value))
        {
            alert("아이디를 입력해주십시오");
            frm.userid.focus();
            return false;
        }
        if(!$.trim(frm.userpassword.value))
        {
            alert("패스워드를 입력해주십시오");
            frm.userpassword.focus();
            return false;
        }
        frm.submit();
    }
</script>
<div>
    <div class="login_title" >
        <!-- <div id="wrap">
            <a href="index.php">
                <img src="./images/main2_2.png" class="pc_img" alt="main" />
            </a>
        </div> -->
        <div class="mainForm">
            <form name=frm method="post" action="adlogin.php">
                <input type="hidden" name="action" value="form_submit" />
                <div class="clause_login" >
                    <div style="border-bottom: 1px solid #d3d3d3;padding-bottom: 10px;">
                        <img src="/evaluation/images/head_logo_pc.png" class="pc_img" alt="main" />
                        <h3 style="font-weight: 600; float: right;">APEC <font size=2;>(TEST서버)</font></h3>
                    </div>
                    <div class="login_info" >
                        <div class="login_input">
                            <table class="login_tb">
                                <colgroup>
                                    <col style="width:30%">
                                    <col style="width:70%">
                                </colgroup>
                                <tbody>
                                <tr>
                                    <th scope="row" class="info_sign"><label for="userid">아이디</label></th>
                                    <td >
                                        <input type="text" id="userid" name="userid" value="" maxlength="50" placeholder="아이디를 입력해주세요."  >
                                        <p class="caution_txt" style="display:none;"></p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="info_sign"><label for="passwd">비밀번호</label></th>
                                    <td >
                                        <input type="password" id="userpassword" name="userpassword" value="" maxlength="16" placeholder="비밀번호를 입력해주세요."><p class="caution_txt" style="display:none;"></p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="member_info">
                        <div class="login_input2">
                            <div class="btn_login">
                                <input type="submit" onclick="javascript:goLogin();" style="font-size:17px" value="로그인"></input>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="login_footer" >
            <div>COPYRIGHT 2023 ABOV Semiconductor Co., Ltd. ALL RIGHTS RESERVED.</div>
        </div>
    </div>
</div>
<!---------------------------------------------------------------- // 푸터영역 시작 ----------------------------------------------------------->
