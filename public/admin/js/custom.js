$(function () {
    $("div.alert").not(".alert-important").delay(3000).fadeOut(350);

    // Toaster Customization
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: false,
        positionClass: "toast-top-right",
        preventDuplicates: false,
        onclick: null,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "3000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    /* Code to set sidebar li active */
    $(".page-sidebar-menu li")
        .filter(function () {
            return $(this).hasClass("active");
        })
        .parent("ul")
        .parent("li")
        .addClass("active open");

    $(".page-sidebar-menu li")
        .filter(function () {
            return $(this).hasClass("active");
        })
        .parent("ul")
        .siblings("a")
        .children("span.arrow")
        .addClass("open");

    /**
     *  Delete single record from database (using delete action)
     **/

    $(document).on("click", ".action-delete", function (e) {
        e.preventDefault();
        var action = $(this).data("target-href");
        Swal.fire({
            title: "Are you sure?",
            text: "You want to delete this record!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete it!",
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: action,
                    type: "DELETE",
                    dataType: "json",
                    success: function (success) {
                        oTable.ajax.reload();
                    },
                });
                Swal.fire({
                    title: "Deleted!",
                    icon: "success",
                    text: "Record was deleted.",
                    showConfirmButton: false,
                    timer: 1500,
                });
            }
        });
    });

    $(document).on("click", ".action-restore", function (e) {
        e.preventDefault();
        var action = $(this).data("target-href");
        Swal.fire({
            title: "Are you sure?",
            text: "You want to restore this record!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, restore it!",
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    url: action,
                    type: "POST",
                    dataType: "json",
                    success: function (success) {
                        oTable.ajax.reload();
                    },
                });
                Swal.fire({
                    title: "Restored!",
                    icon: "success",
                    text: "Record was restored.",
                    showConfirmButton: false,
                    timer: 1500,
                });
            }
        });
    });

    $(document).on("change", ".toggleSwitch", function (e) {
        var state = $(this).is(":checked");
        var url = $(this).data("url");
        var id = $(this).data("id");
        var customAct =
            typeof $(this).data("getaction") != "undefined"
                ? $(this).data("getaction")
                : "";
        var action = customAct != "" ? customAct : "change_status";
        var is_active;

        is_active = state ? 'y' : 'n';

        $.ajax({
            url: url,
            type: "PUT",
            dataType: "json",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                id: id,
                action: action,
                value: is_active,
            },
            success: function (success) {
                toastr.success("Status Changed!");
                // oTable.DataTable().ajax.reload();
            },
        });
    });

    $(document).on("click", ".all_select", function () {
        if ($(this).hasClass("allChecked")) {
            $('.dataTable tbody input[class="small-chk"]').prop(
                "checked",
                false
            );
        } else {
            $('.dataTable tbody input[class="small-chk"]').prop(
                "checked",
                true
            );
        }
        $(this).toggleClass("allChecked");
    });

    $(document).on(
        "click",
        ".dataTable tbody input[class=small-chk]",
        function () {
            var numberOfChecked = $(
                '.dataTable tbody input[class="small-chk"]:checked'
            ).length;
            var totalCheckboxes = $('.dataTable tbody input[class="small-chk"]')
                .length;

            if (numberOfChecked > 0) {
                if (numberOfChecked == totalCheckboxes) {
                    $(".all_select").prop("indeterminate", false);
                    $(".all_select").prop("checked", true);
                    $(".all_select").addClass("allChecked");
                } else {
                    if ($(".all_select").hasClass("allChecked")) {
                        $(".all_select").removeClass("allChecked");
                    }
                    $(".all_select").prop("indeterminate", true);
                }
            } else {
                $(".all_select").prop("indeterminate", false);
                $(".all_select").prop("checked", false);
            }
        }
    );

    $(document).on("click", ".delete_all_link", function (e) {
        $(".delete_all_link").attr("disabled", "disabled");
        e.preventDefault();
        var url = $(this).attr("href");
        var searchIDs = [];
        $(".dataTable tbody input[class='small-chk']:checked").each(
            function () {
                searchIDs.push($(this).val());
            }
        );
        if (searchIDs.length > 0) {
            var ids = searchIDs.join();
            Swal.fire({
                title: "Are you sure?",
                text: "You want to delete these records!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete them!",
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        dataType: "json",
                        data: {
                            action: "delete_all",
                            ids: ids,
                            _token: $('meta[name="csrf_token"]').attr(
                                "content"
                            ),
                        },
                        success: function (success) {
                            $(".all_select").prop("indeterminate", false);
                            $(".all_select").prop("checked", false);
                            if ($(".all_select").hasClass("allChecked")) {
                                $(".all_select").removeClass("allChecked");
                            }
                            $(".all_select").prop("indeterminate", false);
                            $(".delete_all_link").removeAttr("disabled");
                            oTable.ajax.reload();
                        },
                    });

                    Swal.fire({
                        title: "Deleted!",
                        icon: "success",
                        text: "Records were deleted.",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                }
            });
        } else {
            $(".all_select").prop("indeterminate", false);
            $(".delete_all_link").removeAttr("disabled");
        }
    });

    $(document).on('click','.approve_all_link',function(e){
        $('.approve_all_link').attr('disabled','disabled');
        e.preventDefault();
        var url = $(this).attr('href');
        var searchIDs = [];
        $('.dataTable tbody input[class="small-chk"]:checked').each(
            function () {
                searchIDs.push($(this).val());
            }
        );
        if (searchIDs.length > 0) {
            var ids = searchIDs.join();
            Swal.fire({
                title: "Are you sure?",
                text: "You want to approve these records!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, approve them!",
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url:url,
                        type:'post',
                        dataType:'json',
                        data: {
                            _token:$('meta[name="csrf_token"]').attr('content'),
                            ids:ids,
                        },
                        success: function (success) {
                            $(".all_select").prop('indeterminate',false);
                            $(".all_select").prop('checked',false);
                            if($('.all_select').hasClass('allChecked')){
                                $('.all_select').removeClass('allChecked');
                            }
                            $('.all_select').prop('indeterminate',false);
                            oTable.ajax.reload();
                        },
                        complete: function(){
                            $('.approve_all_link').removeAttr("disabled");
                        }
                    });

                    Swal.fire({
                        title: "Approved!",
                        icon: "success",
                        text: "Records were approved.",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                }
            });
        } else {
            $('.all_select').prop('indeterminate',false);
            $('.approve_all_link').removeAttr("disabled");
        }
    });

    $(document).on("click", ".restore_all_link", function (e) {
        $(".restore_all_link").attr("disabled", "disabled");
        e.preventDefault();
        var url = $(this).attr("href");
        var searchIDs = [];
        $(".dataTable tbody input[class='small-chk']:checked").each(
            function () {
                searchIDs.push($(this).val());
            }
        );
        if (searchIDs.length > 0) {
            var ids = searchIDs.join();
            Swal.fire({
                title: "Are you sure?",
                text: "You want to restore these records!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, restore them!",
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        url: url,
                        type: "POST",
                        dataType: "json",
                        data: {
                            action: "restore_all",
                            ids: ids,
                            _token: $('meta[name="csrf_token"]').attr(
                                "content"
                            ),
                        },
                        success: function (success) {
                            $(".all_select").prop("indeterminate", false);
                            $(".all_select").prop("checked", false);
                            if ($(".all_select").hasClass("allChecked")) {
                                $(".all_select").removeClass("allChecked");
                            }
                            $(".all_select").prop("indeterminate", false);
                            $(".restore_all_link").removeAttr("disabled");
                            oTable.ajax.reload();
                        },
                    });

                    Swal.fire({
                        title: "Restored!",
                        icon: "success",
                        text: "Records were restored.",
                        showConfirmButton: false,
                        timer: 1500,
                    });
                }
            });
        } else {
            $(".all_select").prop("indeterminate", false);
            $(".restore_all_link").removeAttr("disabled");
        }
    });

    $(document).on("change", ".table_gender", function () {
        var gender = $(this).val();
        var url = $(this).data("url");
        var id = $(this).data("id");

        $.ajax({
            url: url,
            type: "post",
            dataType: "json",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                id: id,
                gender: gender,
            },
            cache: false,
            success: function (success) {
                console.log(success['message']);
                toastr.success("Gender Changed!");
                // oTable.DataTable().ajax.reload();
            },
        });
    });

    $(document).on("click", "#update_gender", function (e) {

        e.preventDefault();
        
        var searchIDs       = [];
        var searchAutoIDs   = [];

        $(".dataTable tbody input[class='small-chk']:checked").each(
            function () {
                searchIDs.push($(this).val());
                searchAutoIDs.push($(this).data('id'));
            }
        );

        if (searchIDs.length == 0) {

            Swal.fire({                
                text: "Please select at least one checkbox.",
                icon: "warning",
                showConfirmButton: true,
            });

        } else {

            $("#myModal #multi_user_id").val(searchIDs);
            $("#myModal #multi_auto_user_id").val(searchAutoIDs);
            $("#myModal").modal('show');
        }
    });

    $(document).on("click", "#SingleUpdateGenderStatus", function (e) {

        e.preventDefault();
        
        var searchIDs       = [];
        var searchAutoIDs   = [];

        var id = $(this).attr('data-id');
        searchIDs.push(id);
        searchAutoIDs.push(id);

        if (searchIDs.length == 0) {

            Swal.fire({                
                text: "Please select at least one checkbox.",
                icon: "warning",
                showConfirmButton: true,
            });

        } else {

            $("#myModalPhotoVerification #multi_user_id").val(searchIDs);
            $("#myModalPhotoVerification").modal('show');
        }
    });


    $(document).on("click", ".save_frm_gender", function (e) {

        e.preventDefault();

        $(".processing").show();
        $(this).attr("disabled", true);        
        var data               = $('#frm_gender').serializeArray();
        var url                = $('#frm_gender').attr('action');
        var multi_auto_user_id = $("#myModal #multi_auto_user_id").val();
        var target_gender      = $("select[name=target_gender] :selected").val();

        $.ajax({
            url: url,
            type: "post",
            dataType: "json",
            data: data,
            cache: false,
            success: function (success) {
                console.log(success['message']);
                toastr.success("Gender Changed!");
                $("#myModal").modal('hide');
                $(".save_frm_gender").attr("disabled", false);
                $(".processing").hide();

                var arr_id = multi_auto_user_id.split(",");
                for(var index = 0; index < arr_id.length; index++) {                    
                    $(".dynamic_gender_"+arr_id[index]).val(target_gender);
                }

                $(".dataTable tbody input[class='small-chk']:checked").each(function () {
                    $(this).prop('checked', false);                    
                    $(this).parent().parent().trigger("click");
                });

            },
        });
    });
    
    $(document).on("click", "#photo_verification", function (e) {

        e.preventDefault();
        
        var searchIDs       = [];

        $(".dataTable tbody input[class='small-chk']:checked").each(
            function () {
                searchIDs.push($(this).val());
            }
        );

        if (searchIDs.length == 0) {

            Swal.fire({                
                text: "Please select at least one checkbox.",
                icon: "warning",
                showConfirmButton: true,
            });

        } else {

            $("#myModalPhotoVerification #multi_user_id").val(searchIDs);
            $("#myModalPhotoVerification").modal('show');
        }
    });
    
    $(document).on("click", "#email_verification", function (e) {

        e.preventDefault();
        
        var searchIDs       = [];

        $(".dataTable tbody input[class='small-chk']:checked").each(
            function () {
                searchIDs.push($(this).val());
            }
        );

        if (searchIDs.length == 0) {

            Swal.fire({                
                text: "Please select at least one checkbox.",
                icon: "warning",
                showConfirmButton: true,
            });

        } else {

            $("#myModalEmailVerification #multi_user_email_id").val(searchIDs);
            $("#myModalEmailVerification").modal('show');
        }
    });

    $(document).on("click", ".save_frm_photo_verification", function (e) {

        e.preventDefault();
        $(".processing").show();
        $(this).attr("disabled", true);
        var data    = $('#frm_photo_verification').serializeArray();
        var url     = $('#frm_photo_verification').attr('action');

        $.ajax({
            url: url,
            type: "post",
            dataType: "json",
            data: data,
            cache: false,
            success: function (success) {                
                
                toastr.success("Photo verification done!");

                $("#myModalPhotoVerification").modal('hide');
                $(".save_frm_photo_verification").attr("disabled", false);
                $(".processing").hide();

                var table = $('#users_table').DataTable();
                table.ajax.reload(null, false);
            },
        });
    });

    $(document).on("click", ".save_frm_email_verification", function (e) {

        e.preventDefault();
        $(".processing").show();
        $(this).attr("disabled", true);
        var data    = $('#frm_email_verification').serializeArray();
        var url     = $('#frm_email_verification').attr('action');

        $.ajax({
            url: url,
            type: "post",
            dataType: "json",
            data: data,
            cache: false,
            success: function (success) {                
                
                toastr.success("Email verification done!");

                $("#myModalEmailVerification").modal('hide');
                $(".save_frm_email_verification").attr("disabled", false);
                $(".processing").hide();

                var table = $('#users_table').DataTable();
                table.ajax.reload(null, false);
            },
        });
    });
});


$(document).on("click", ".my_profile_image", function (e) {
// Get the modal

var user_id = $(this).attr('data-id');

var modal = document.getElementById("myimageModal");

// Get the image and insert it inside the modal - use its "alt" text as a caption

var modalImg1 = document.getElementById("img01");
var modalImg2 = document.getElementById("img02");


modal.style.display = "block";
modalImg1.src = $('#1photo_'+user_id).attr('src');
modalImg2.src = $('#2photo_'+user_id).attr('src');

        
});


$(document).on("click", ".close", function (e) {
    var modal = document.getElementById("myimageModal");
      modal.style.display = "none";
});


function getStatusText(code) {
    sText = "";
    if (code !== undefined) {
        switch (code) {
            case 200: {
                sText = "Success";
                break;
            }
            case 404: {
                sText = "Error";
                break;
            }
            case 403: {
                sText = "Error";
                break;
            }
            case 500: {
                sText = "Error";
                break;
            }
            case "success": {
                sText = "Success";
                break;
            }
            case "danger": {
                sText = "Error";
                break;
            }
            case "warning": {
                sText = "Error";
                break;
            }
            default: {
                sText = "Error";
            }
        }
    }
    return sText;
}

   

function showMessage(sType, sText) {
    sType = getStatusText(sType);
    toastr[sType.toLowerCase()](sText);
}

jQuery.validator.addMethod(
    "not_empty",
    function (value, element) {
        return this.optional(element) || /\S/.test(value);
    },
    "Only space is not allowed."
);

jQuery.validator.addMethod(
    "not_equal",
    function (value, element, compare_with) {
        return value != $(compare_with).val();
    },
    "Same values are not allowed."
);

jQuery.validator.addMethod(
    "no_space",
    function (value, element) {
        return value.indexOf(" ") < 0 && value != "";
    },
    "Space is not allowed."
);

jQuery.validator.addMethod(
    "alpha_numeric",
    function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9\s.]+$/.test(value);
    },
    "This field may only contain letters, numbers and space."
);

function addOverlay(){
    $('<div class="page-loader page-loader-base" id="overlayDocument"><div class="page-loader page-loader-non-block" ><div class="blockui"><span>Please   wait...</span><span><div class="spinner spinner-primary"></div></span></div></div></div>').prependTo(document.body);
    $("#overlayDocument").show().children().show();
}

function removeOverlay(){$('#overlayDocument').remove();}

function user_match_data(user_id){

    var type = $("#type").val();
    var from_date = $('#search_fromdate').val();
    var to_date = $('#search_todate').val();
    var url = $(".usermatchmodel").attr('data-url');
    if (url != '' && user_id != '') {
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                user_id: user_id,
                filter_types: type,
                from_date: from_date,
                to_date: to_date,
            },
            cache: false,
            success: function (responce) {
                // console.log(responce.length);
                var str =''; 
                if (responce != '' && responce.length > 0) {
                    $.each(responce, function(key,value ) {
                        str +='<tr><td>'+value.full_name+'</td><td>'+value.gender+'</td><td>'+value.created_at+'</td></tr>';
                    });
                    $("#user_match_table_body").html(str);
                }
                else
                {
                    var nostr = '<tr>No data found..</tr>';
                    $("#user_match_table_body").html(nostr);
                }
            },
        });
    }
}

function user_apilog_data(user_id){

    var url = $(".usermatchmodel").attr('data-url');
    if (url != '' && user_id != '') {
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                user_id: user_id,
            },
            cache: false,
            success: function (responce) {
                var str =''; 
                if (responce != '') {
                    var req = JSON.parse(responce.request);
                    str +='<tr><td>'+responce.account_id+'</td><td>'+responce.full_name+'</td><td>'+responce.created_at+'</td></tr>';
                    str +='<tr><td>'+req.gender+'</td><td></td><td>'+req.birth_date+'</td></tr>';
                    str +='<tr><td>'+req.gender+'</td><td></td><td>'+req.birth_date+'</td></tr>';
                    str +='<tr><td>'+req.gender+'</td><td></td><td>'+req.birth_date+'</td></tr>';
                    str +='<tr><td>'+req.gender+'</td><td></td><td>'+req.birth_date+'</td></tr>';
                    $("#user_api_log").html(str);
                }
                else
                {
                    var nostr = '<tr>No data found..</tr>';
                    $("#user_api_log").html(nostr);
                }
            },
        });
    }
}

function user_report_data(user_id){

    var url = $(".userreportmodel").attr('data-url');
    if (url != '' && user_id != '') {
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json",
            data: {
                _token: $("meta[name='csrf-token']").attr("content"),
                user_id: user_id,
            },
            cache: false,
            success: function (responce) {
                // console.log(responce.length);
                var str ='';
                if (responce.profile_reports != '' && responce.profile_reports.length > 0) {
                    $.each(responce.profile_reports, function(key,value ) {
                        str +='<tr><td>'+value.full_name+'</td><td>'+value.gender+'</td><td>'+value.message+'</td><td>'+value.created_at+'</td></tr>';
                    });
                    $("#user_profile_report_table_body").html(str);
                }
                else
                {
                    var nostr = '<tr><td colspan="4" class="text-center">No data found..</td></tr>';
                    $("#user_profile_report_table_body").html(nostr);
                }
                str ='';
                if (responce.profile_blocks != '' && responce.profile_blocks.length > 0) {
                    $.each(responce.profile_blocks, function(key,value ) {
                        str +='<tr><td>'+value.full_name+'</td><td>'+value.gender+'</td><td>'+value.created_at+'</td></tr>';
                    });
                    $("#user_profile_block_table_body").html(str);
                }
                else
                {
                    var nostr = '<tr><td colspan="3" class="text-center">No data found..</td></tr>';
                    $("#user_profile_block_table_body").html(nostr);
                }
            },
        });
    }
}