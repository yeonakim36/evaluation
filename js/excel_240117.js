/**
 * Created by park029 on 2017-05-28.
 */

// var btn_status = false;
// var upload_id = 0;
// var upload_status = true;
// $().ready(function () {

//     file_upload_init();

//     $("#file_upload").click(function () {
//         $("#upload_file").click();
//     });
// });
// function cancelFileSelection() {
//     $("#upload_file").replaceWith($("#upload_file").clone(true));
//     document.getElementById('upload_file').value = ''; // 파일 입력 요소의 값을 지웁니다.
//     $("#file_name").html('No file Selected');

//     document.getElementById('codip_list').style.display = 'none';
//     document.getElementById('error_list').style.display = 'none';
// }

// function file_upload_init() {
//     $('#upload_file').on('change', function (e) {
//         var files = this.files;
//         file_check(files, this);
//     });
// }

// function file_check(files, elm) {

//     if (!upload_status) {
//         alert("파일 업로드 중입니다.");
//         cancelFileSelection()
//         // empty_file(elm);
//         return;
//     }

//     upload_status = false;

//     var form_data = new FormData();
//     form_data.append("id", elm.id);
//     form_data.append("category", "unpaid_upload");

//     // 첨부된 파일이 있을 경우
//     if (files.length > 0) {
//         for (var i = 0; i < files.length; i++) {
//             form_data.append("upload_file_" + i, files[i]);
//         }

//         upload_file(form_data, elm, function (rst) {
//             var file_info = rst.result.file_info;
//             var file_key = Object.keys(file_info);

//             for (var i = 0; i < file_key.length; i++) {
//                 var key = file_key[i];
//                 $("#file_name").html(file_info[key][0]);
//                 upload_id = file_info[key][2];
//             }
//             upload_status = true;
//         });
//     }
// }

// function upload_file(form_data, elm, callback) {
//     if (!btn_status) {
//         return;
//     }
//     if (form_data == null || form_data == undefined) {
//         return;
//     }
//     else if (typeof callback != 'function') {
//         return;
//     }

//     // if (elm.id === "upload_file") {
//     //     empty_file(elm);
//     // }

//     // 서버와 통신
//     $.ajax({
//         url: '/upload/upload.php',
//         processData: false,
//         contentType: false,
//         data: form_data,
//         type: 'POST',
//         success: function (rst) {
//             if (rst.code == 0) {
//                 callback(rst);
//             }
//             else {
//                 // empty_file(elm);
//                 upload_status = true;
//                 alert(rst.msg);
//                 return;
//             }
//         },
//         error: function (rst) {
//             // empty_file(elm);
//             upload_status = true;
//             alert(rst.msg);
//             return;
//         }
//     });
// }


function form_submit() {
    console.log('ddd');

    // if (!btn_status) {
    //     return;
    // }
    // if (!upload_status) {
    //     alert("파일 업로드 중입니다.");
    //     return;
    // }

    if (confirm("데이터를 수정 하시겠습니까?")) {
        btn_status = false;
        $.ajax({
            url: './module/excel.php',
            data: {"upload_id": upload_id, "function": "excel_update", "p": "codip_manage"},
            type: 'POST',
            dataType: 'Json',
            success: function (rst) {
                alert(rst.msg);
                if (rst.code == 0) {
                    post_to_url('',{'p':'eval_upload'});
                } else {
                    btn_status = true;
                    upload_status = true;
                    if(rst.error_html != null) {
                        document.getElementById('codip_list').style.display = 'block';
                        $('#codip_tbody').html(rst.error_html);

                        document.getElementById('error_list').style.display = 'block';
                        $('#error_list_li').html(rst.error_list_html);
                    }
                    return;
                }
            },
            error: function (rst) {
                alert("Error! Please contact your administrator if it still occurs.");
                btn_status = true;
                upload_status = true;
                return;
            }
        });
    }
}