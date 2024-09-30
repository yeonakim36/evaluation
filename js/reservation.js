$(document).ready(function () {
    // game_select();
});

function game_select() {
    var game_index = $(".game_index").val();

    if(game_index == 0) {
        var html = "<option value='0'>-전체-</option>";

        $(".mode_index").html(html);
        return;
    }

    var send_data = new Object();
    send_data.game_index = game_index;
    send_data.mode_index = mode_index;

    $.ajax({
        url:'module.php',
        data: {"p": "event_manage", "data": send_data, "function": "game_select_all"},
        type: 'POST',
        dataType: 'Json',
        success: function (rst) {
            if (rst.code == 0) {
                $(".mode_index").html(rst.mode);
            } else {
                alert(rst.msg);
                return;
            }
        },
        error: function (rst) {
            alert("Error! Please contact your administrator if it still occurs.");
            return;
        }
    });
}
