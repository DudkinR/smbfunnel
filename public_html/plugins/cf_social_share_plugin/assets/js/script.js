$(document).ready(function() {
    $("#cf_submit").hide();
    $("#add").click(function(e) {
        e.preventDefault();
        $("#cf_submit").show();
    });

    var i = 1;
    $("#add").click(function() {
        i++;
        var option = "";
        $("#dynamic_field").append(
            '<tr id="row' +
            i +
            '"><td class="col-md-8"><select name="network_name[]"  id="cf_social' +
            i +
            '" class="cf_social form-control"></select></td><td class="col-md-4"><button type="button" name="remove" id="' +
            i +
            '" class="btn btn-danger btn_remove">X</button></td></tr>'
        );
        var $select = $("#cf_social" + i + "");
        var url = $("#cf_social_url").val();
        $.getJSON(url, function(data) {
            array1 = document.querySelector("#cf_social_check").value;

            $select.html("");

            for (let j = 0; j < data.length; j++) {
                if (!array1.includes(data[j]["name"])) {
                    option = btoa(
                        '{"icon":"' +
                        data[j]["icon"] +
                        '","name":"' +
                        data[j]["name"] +
                        '","url":"' +
                        data[j]["url"] +
                        '"}'
                    );

                    $select.append(
                        '<Option value ="' + option + '">' + data[j]["name"] + "</Option>"
                    );
                }
            }
        });
    });

    $(document).on("click", ".btn_remove", function() {
        var button_id = $(this).attr("id");
        $("#row" + button_id + "").remove();
    });

    $("#cf_social_form").on("submit", function(eve) {
        eve.preventDefault();
        var array = $("#cf_social_form").serializeArray();
        let arr = [];

        for (let k = 0; k < array.length; k++) {
            let store = array[k].value;
            arr.push(store);
        }

        let result = [];

        for (let i = 0; i < arr.length; i++) {
            if (result.indexOf(arr[i]) === -1 && arr.indexOf(arr[i], i + 1) !== -1) {
                result.push(arr[i]);
            }
        }

        if (result.length > 0) {
            // alert("You cannot add the same value 2 times");
            Swal.fire({
                icon: 'error',
                text: "You can't add duplicate value"
              })
        } else {
            $.ajax({
                url: $("#cf_social_ajax").val(),
                method: "POST",
                data: "action=cf_social_add_action&" + $("#cf_social_form").serialize(),
                success: function(data) {
                    if (data == "201") {
                        Swal.fire({
                            icon: 'error',
                            text: 'This icon is already used'
                          })
                    } else {
                        location.reload();
                    }
                },
            });
        }
    });

    $(".edit_btn").click(function(e) {
        e.preventDefault();
        var form_id = $(this).attr("id");
        console.log(form_id);
        $.ajax({
            type: "POST",
            url: $("#cf_social_ajax").val(),
            data: {
                checking_answer_btn: true,
                f_id: form_id,
                action: "cf_social_preview",
            },
            success: function(response) {
                $.each(response, function(key, value) {
                    $("#edit_id").val(value["social_id"]);
                    $("#edit_answer").val(value["network_update"]);

                });
                $("#edit_form_Modal").modal("show");
                var $select = $('#cf_modal_social');
                var url = $('#cf_social_url').val();

                $.getJSON(url, function(data) {
                    console.log(data);
                    array1 = document.querySelector("#cf_social_check").value;

                    $select.html('');

                    for (let j = 0; j < data.length; j++) {

                        if (!(array1.includes(data[j]['name']))) {
                            option = btoa('{"icon":"' + data[j]["icon"] + '","name":"' + data[j]["name"] + '","url":"' + data[j]["url"] + '"}');

                            $select.append('<Option value ="' + option + '">' + data[j]['name'] + '</Option>');

                        }

                    }
                });
            },
        });
    })


    $("#update_social_form").on("submit", function(event) {
        event.preventDefault();
        $.ajax({
            url: $("#cf_social_ajax").val(),
            method: "POST",
            data: "action=cf_social_update&" + $("#update_social_form").serialize(),
            success: function(data) {
                $("#update_social_form")[0].reset();
                $("#edit_form_Modal").modal("hide");
                location.reload();
            },
        });
    });

    $(".delete_btn").click(function(e) {
        e.preventDefault();
        var delete_id = $(this).attr("id");
        $("#deleteModal").modal("show");
        $("#delete_recordes").click(function(e) {
            $.ajax({
                type: "POST",
                url: $("#cf_social_ajax").val(),
                data: {
                    del_id: delete_id,
                    action: "cf_social_delete",
                },
                success: function(data) {
                    $("#delete-message").html(data);
                    $("#deleteModal").modal("hide");
                    location.reload();
                },
            });
        });
    });

    $("#cf_addsocialsetting").on("submit", function(eve) {
        eve.preventDefault();

        $.ajax({
            url: $("#cf_social_setting_ajax").val(),
            method: "POST",
            data: "action=cf_social_setting&" + $("#cf_addsocialsetting").serialize(),
            success: function(data) {
                swal.fire("Done!", "It was succesfully Saved!", "success");
         },
        });
    });
});