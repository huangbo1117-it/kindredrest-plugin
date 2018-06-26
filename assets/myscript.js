
window.addEventListener("load", function () {

    // store tabs variables
    var tabs = document.querySelectorAll("ul.nav-tabs > li");

    for (i = 0; i < tabs.length; i++) {
        tabs[i].addEventListener("click", switchTab);
    }

    function switchTab(event) {
        event.preventDefault();

        document.querySelector("ul.nav-tabs li.active").classList.remove("active");
        document.querySelector(".tab-pane.active").classList.remove("active");

        var clickedTab = event.currentTarget;
        var anchor = event.target;
        var activePaneID = anchor.getAttribute("href");

        clickedTab.classList.add("active");
        document.querySelector(activePaneID).classList.add("active");

    }

    $(".option_action").click(function (e) {
        var url = '';
        if ($(this).hasClass("option_activate")) {
            $("#mode").val('activate');
            url = plugin_url + "api.php";
        } else if ($(this).hasClass("option_getcode")) {
            $("#mode").val('get_code');
            url = plugin_url + "api.php";
        }else if ($(this).hasClass("option_buycode")) {
            url = 'http://localhost:88/api_sig/ntest5/index.php';
            
            return;
        } else {
            e.preventDefault();
            return;
        }
        var formData = $("#frm_option").serialize();
        console.log(formData);

//        console.log(plugin_url);
//        return;

        $("#wait").css("display", "block");
        $.ajax({
            type: "POST",
            url: url,
            cache: false,
            data: formData,
            success: function (data) {
                console.log(data);
                $("#wait").css("display", "none");
                if (data.response == 200) {
                    alert("success");
                    
//                    $.ajax({
//                        type: "POST",
//                        url: "https://mesmo.co/api_sig/ntest4/general.php?action=generate_f&code_id=" + data.row_code.code_id,
//                        cache: false,
//                        data: {},
//                        success: function (data) {
//                            console.log(data);
//                            
//                        },
//                        error: function (e) {
//                            console.log(e);
//                        }
//                    });

                    location.reload();
                } else {
                    alert("Fail to Activate");
                }


            },
            error: function (e) {
                console.log(e);
                $("#wait").css("display", "none");
            }
        });
    });


    $(".cron_action").click(function (e) {

        if ($(this).hasClass("cron_start")) {
            $("#crond_is_valid").val('1');
        } else if ($(this).hasClass("cron_stop")) {
            $("#crond_is_valid").val('0');
        } else {
            e.preventDefault();
            return;
        }
        var formData = $("#frmCron").serialize();
//        alert(formData);

        console.log(formData);

//        return;

        $("#wait").css("display", "block");
        $.ajax({
            type: "POST",
            url: plugin_url + "api.php",
            cache: false,
            data: formData,
            success: function (data) {
                console.log(data);
                $("#wait").css("display", "none");
                if (data.response == 200) {
                    alert("success");
                    location.reload();
                } else {
                    alert("Fail to Activate");
                }
            },
            error: function (e) {
                console.log(e);
                $("#wait").css("display", "none");
            }
        });
    });


});