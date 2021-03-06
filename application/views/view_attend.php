<style>
    .progress-bar {
        height: 100%;
        padding: 3px;
        background: rgb(200, 201, 202);
        box-shadow: none; 
    }
    .progress_bar_new {
        height: 100%;
        padding: 3px;
        background: #99d9ea;
        box-shadow: none;
        text-align: center;
        color: #fff;
        padding-top: 0px;
    }

    .option_section_css{
        background-color: #f1f1f1;
        padding-top: 4px;
        padding-left: 6px;
        border-radius: 9px;
        margin-bottom: 10px;
    }
    .option_section_css_selected{
        background-color: #e1f6ff;
        padding-top: 4px;
        padding-left: 6px;
        border-radius: 9px;
        margin-bottom: 10px;
    }
    .progress {
        height: 26px;
        margin-bottom: 10px;
        overflow: hidden;
        background-color: #e6edf3;
        border-radius: 5px;
        -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
        box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
    }
    section{
        padding: 25px 0px;
    }
</style>

<section class="parallax" data-roundtable="<?=$roundtable->roundtable?>" style="background-image: url(<?= base_url() ?>front_assets/images/bubble_bg_1920.jpg); top: 0; padding-top: 0px;" data-session-type="<?=$sessions->sessions_type_status == "Private"?>">
    <div class="container container-fullscreen" style="min-height: 900px;"> 
        <div class="">
            <div class="row">
                <div class="col-md-12">
                    <!-- CONTENT -->
                    <?php
                    if ((isset($sessions) && !empty($sessions))) {
                        if ($sessions->sessions_type_status == "Private") {
                            $time = $sessions->time_slot;
                            $datetime = $sessions->sessions_date . ' ' . $time;
                            $datetime = date("Y-m-d H:i", strtotime($datetime));
                            $datetime = new DateTime($datetime);
                            $datetime1 = new DateTime();
                            $diff = $datetime->getTimestamp() - $datetime1->getTimestamp();
                            if ($diff >= 60) {
                                $diff = $diff - 60;
                            } else {
                                $diff = 0;
                            }
                        } else {
                            $time = $sessions->time_slot;
                            $datetime = $sessions->sessions_date . ' ' . $time;
                            $datetime = date("Y-m-d H:i", strtotime($datetime));
                            $datetime = new DateTime($datetime);
                            $datetime1 = new DateTime();
                            $diff = $datetime->getTimestamp() - $datetime1->getTimestamp();
                            if ($diff >= 60) {
                                $diff = $diff - 60;
                            } else {
                                $diff = 0;
                            }
                        }
                    }
                    ?>
                    <input type="hidden" id="time_second" value="<?= $diff ?>">
                    <input type="hidden" id="sessions_type_status" value="<?= $sessions->sessions_type_status ?>">
					  <input type="hidden" id="sessions_id" value="<?= $sessions->sessions_id ?>">
                    <section class="content">
                        <div class="container" style=" background: rgb(223 223 223 / 60%);"> 
                            <div class="row p-b-40">
                                <div class="col-md-12" style="background-color: #B2B7BB; margin-bottom: 10px;">
                                    <h3 style="margin-bottom: 5px; color: #fff; font-weight: 700; text-transform: uppercase;"><?= isset($sessions) ? $sessions->session_title : "" ?></h3>
                                </div>    
                                <div class="col-md-9 m-t-20" style="border-right: 1px solid;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <?php
                                            if ($sessions->sessions_photo != "") {
                                                ?>
                                                <img alt="" src="<?= base_url() ?>uploads/sessions/<?= (isset($sessions) && !empty($sessions)) ? $sessions->sessions_photo : "" ?>" width="100%">
                                                <?php
                                            } else {
                                                ?>
                                                <img alt="" src="<?= base_url() ?>front_assets/images/session_avtar.jpg" width="100%">
                                            <?php } ?>   
                                        </div>  
                                        <div class="col-md-8">
                                            <h2 style="margin-bottom: 0px;"><?= (isset($sessions) && !empty($sessions)) ? $sessions->session_title : "" ?></h2>
                                            <small><i class="fa fa-calendar" aria-hidden="true"></i> <?= date("M-d-Y", strtotime($sessions->sessions_date)) . ' ' . date("H:i", strtotime($sessions->time_slot)) . ' - ' . date("H:i", strtotime($sessions->end_time)) ?></small>
                                            <p class="m-t-20"><?= (isset($sessions) && !empty($sessions)) ? $sessions->sessions_description : "" ?></p>
                                            <?php if ($sessions->sessions_type_status == "Private") { ?>
                                                <p class="m-t-20" style="border: 1px solid #000; padding: 5px; color: #000;"><b>Q&A During Sessions:</b> We will be monitoring the Q&A window during the session. Please submit questions as they arise, and we will do our best to answer all of those questions during the Q&A session that follows</p>
                                            <?php } else { ?>
                                                <p class="m-t-20" style="border: 1px solid #000; padding: 5px; color: #000;"><b>Q&A During Sessions:</b> We will be monitoring the Q&A window during the session. Please submit questions as they arise, and we will do our best to answer all of those questions during the Q&A session that follows</p>
                                            <?php } ?>
                                        </div>    
                                    </div>
                                </div>
                                <div class="col-md-3" style="text-align: center">
                                    <?php
                                    $size = 0;
                                    if (isset($sessions->presenter) && !empty($sessions->presenter)) {
                                        $size = sizeof($sessions->presenter);
                                    }
                                    ?>
                                    <?php if ($size <= 2) { ?>
                                        <br>
                                        <br>
                                    <?php } ?>
                                    <?php
                                    if (isset($sessions->presenter) && !empty($sessions->presenter)) {
                                        foreach ($sessions->presenter as $value) {
                                            ?>
                                            <h3 style="margin-bottom: 0px;  cursor: pointer;" data-presenter_photo="<?= $value->presenter_photo ?>" data-presenter_name="<?= $value->presenter_name ?>" data-designation="<?= $value->designation ?>" data-email="<?= $value->email ?>" data-company_name="<?= $value->company_name ?>" class="presenter_open_modul" ><u style="color: #337ab7;"><?= $value->presenter_name ?></u><?= ($value->title != "") ? "," : "" ?> <?= $value->title ?></h3>
                                            <h3 style="margin-bottom: 0px;  cursor: pointer;"> <?= $value->company_name ?></h3>
                                            <!--<p class="m-t-20"><?= (isset($sessions) && !empty($sessions)) ? $sessions->bio : "" ?></p>-->
                                            <!--<img alt="" src="<?= base_url() ?>uploads/presenter_photo/<?= (isset($sessions) && !empty($sessions)) ? $sessions->presenter_photo : "" ?>" class="img-circle" height="100" width="100">-->
                                            <?php
                                        }
                                    }
                                    ?>
<!--<p class="m-t-20"><?= (isset($sessions) && !empty($sessions)) ? $sessions->bio : "" ?></p>-->
<!--<img alt="" src="<?= base_url() ?>uploads/presenter_photo/<?= (isset($sessions) && !empty($sessions)) ? $sessions->presenter_photo : "" ?>" class="img-circle" height="100" width="100">-->

                                </div>
                                <div class="col-md-12 m-t-40">
                                    <div class="col-md-3" style="text-align: center;">
                                        <?php
                                        if (isset($sessions) && !empty($sessions)) {
                                            if ($sessions->sponsor_log != "") {
                                                if (file_exists('./uploads/sponsor_log/' . $sessions->sponsor_log)) {
                                                    ?>
                                                    <img src="<?= base_url() ?>uploads/sponsor_log/<?= (isset($sessions) && !empty($sessions)) ? $sessions->sponsor_log : "" ?>" style="width: 100%;">
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </div>
                                    <div class="col-md-3" style="text-align: center; text-align: center; padding: 10px; border: 1px solid; margin-right: 10px;">
                                        <p><i class="fa fa-volume-up" aria-hidden="true" style="font-size: 20px;"></i></p>
                                        <p style="color: #000; margin-bottom: 0px;">SESSION AUDIO</p>
                                        <p style="color: #000; text-align: left;">You may need to turn the audio on the session recording.  Hover your mouse over the bottom left hand corner of the  video in the next page and adjust the audio.</p>
                                    </div>
                                    <div class="col-md-4" style="text-align: center; text-align: center; padding: 10px; background-color: #fff; border: 1px solid;">
                                        <p><i class="fa fa-info-circle" aria-hidden="true" style="font-size: 20px;"></i></p>
                                        <?php if ($sessions->sessions_type_status == "Private") { ?>
                                            <p>You will automatically enter the session 1 minute before it is due to begin.</p>
                                            <p>Entry will be enabled in <span id="id_day_time" ></span></p>
                                        <?php } else { ?>
                                            <p>You will automatically enter the session 1 minute before it is due to begin.</p>
                                            <p>Entry will be enabled in <span id="id_day_time" ></span></p>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <?php if ($sessions->sessions_type_status == "Private") { ?>
                                                                                                                                                                                                                    <!--<a class="button black-light button-3d rounded right" style="margin: 0px 0;" href="<?= base_url() ?>private_sessions/view/<?= (isset($sessions) && !empty($sessions)) ? $sessions->sessions_id : "" ?>"><span>Take me there</span></a>-->
                                    <?php } else { ?>
                                                                                                                                                                                                                    <!--<a class="button black-light button-3d rounded right" style="margin: 0px 0;" href="<?= base_url() ?>sessions/view/<?= (isset($sessions) && !empty($sessions)) ? $sessions->sessions_id : "" ?>"><span>Take me there</span></a>-->
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </section><br><br>
                    <!-- END: SECTION --> 
                </div>
            </div> 
        </div>
    </div>
</section>
<div class="modal fade" id="modal" tabindex="-1" role="modal" aria-labelledby="modal-label" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding: 0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row" style="padding-top: 10px; padding-bottom: 20px;">
                    <div class="col-sm-12">
                        <div class="col-sm-4">
                            <img src="" id="presenter_profile" class="img-circle" style="height: 170px; width: 170px;">
                        </div>
                        <div class="col-sm-8" style="padding-top: 15px;">
                            <h3 id="presenter_title" style="font-weight: 700"></h3>
                            <p style="border-bottom: 1px dotted; margin-bottom: 10px; padding-bottom: 10px;"><b style="color: #000;">Email </b> <span id="email" style="padding-left: 10px;"></span></p>
                            <p style="border-bottom: 1px dotted; margin-bottom: 10px; padding-bottom: 10px;"><b style="color: #000;" id="company_lbl">Company </b> <span id="company" style="padding-left: 10px;"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="push_notification" tabindex="-1" role="modal" aria-labelledby="modal-label" aria-hidden="true" style="display: none; text-align: left; right: unset;">
    <input type="hidden" id="push_notification_id" value="">
    <div class="modal-dialog">
        <div class="modal-content" style="border: 1px solid #ef9d45;">
            <div class="modal-body">
                <div class="row" style="padding-top: 10px; padding-bottom: 20px;">
                    <div class="col-sm-12">
                        <div style="color:#ef9d45; font-size: 16px; font-weight: 800; " id="push_notification_message"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="close push_notification_close" style="padding: 10px; color: #fff; background-color: #ef9d45; opacity: 1;" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.3.0/socket.io.js" integrity="sha512-v8ng/uGxkge3d1IJuEo6dJP8JViyvms0cly9pnbfRxT6/31c3dRWxIiwGnMSWwZjHKOuY3EVmijs7k1jz/9bLA==" crossorigin="anonymous"></script>

<div class="modal fade" id="image_popup" tabindex="-1" role="modal" aria-labelledby="modal-label" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding: 0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row" style="padding-top: 10px; padding-bottom: 20px;">
                    <div class="col-sm-12">
                        <img id="my_image" style="width:100%;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="video_popup" tabindex="-1" role="modal" aria-labelledby="modal-label" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding: 0px;">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <div class="row" style="padding-top: 10px; padding-bottom: 20px;">
                    <div class="col-sm-12" style="text-align:center">
<!--                        <iframe src="https://player.vimeo.com/video/469724540?autoplay=1&title=0&byline=0&portrait=0" width="640" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        var sessions_id = $("#sessions_id").val();
        if (sessions_id == '144') {
            $('#image_popup').modal('show');
            $('#my_image').attr('src', '<?= base_url() ?>front_assets/images/New_Frontier_Ad.jpg');
            var delayInMilliseconds = 10000; //1 second
            setTimeout(function () {
                $('#image_popup').modal('hide');
            }, delayInMilliseconds);
        } else if (sessions_id == '137') {
            $('#video_popup').modal('show');
            var delayInMilliseconds = 10000; //1 second
            setTimeout(function () {
                $('#video_popup').modal('hide');
            }, delayInMilliseconds);
        } else if (sessions_id == '182') {
            $('#image_popup').modal('show');
            $('#my_image').attr('src', '<?= base_url() ?>front_assets/images/182.jpg');
            var delayInMilliseconds = 10000; //1 second
            setTimeout(function () {
                $('#image_popup').modal('hide');
            }, delayInMilliseconds);
        } else if (sessions_id == '183') {
            $('#image_popup').modal('show');
            $('#my_image').attr('src', '<?= base_url() ?>front_assets/images/183.jpg');
            var delayInMilliseconds = 10000; //1 second
            setTimeout(function () {
                $('#image_popup').modal('hide');
            }, delayInMilliseconds);
        } else if (sessions_id == '185') {
            $('#image_popup').modal('show');
            $('#my_image').attr('src', '<?= base_url() ?>front_assets/images/185.jpg');
            var delayInMilliseconds = 10000; //1 second
            setTimeout(function () {
                $('#image_popup').modal('hide');
            }, delayInMilliseconds);
        }
    });
</script>    

<script type="text/javascript">
    $(document).ready(function () {
        let socket = io("<?=getSocketUrl()?>");
        var sessionType=$(".parallax").data("session-type")
        var roundTable=parseInt($(".parallax").data("roundtable"));


        if(sessionType=="1"){
            socket.emit("connectHallViewUsers","<?=getAppName($sessions->sessions_id) ?>",function (users) {
                if(users>roundTable) {
                   window.location.href="<?=base_url()?>sessions";
                    alertify.error('Roundtable is full!');

                }
            })
        }

        if ($("#time_second").val() <= 0) {
            timer();
        } else {
            setInterval('timer()', 1000);
        }

        $(".presenter_open_modul").click(function () {

            var presenter_photo = $(this).attr("data-presenter_photo");
            var presenter_name = $(this).attr("data-presenter_name");
            var designation = $(this).attr("data-designation");
            var company_name = $(this).attr("data-company_name");
            var email = $(this).attr("data-email");

            if (presenter_photo != "" && presenter_photo != null) {
                $.ajax({
                    url: '<?= base_url() ?>uploads/presenter_photo/' + presenter_photo,
                    type: 'HEAD',
                    error: function ()
                    {
                        $('#presenter_profile').attr('src', "<?= base_url() ?>uploads/presenter_photo/presenter_avtar.png");
                    },
                    success: function ()
                    {
                        $('#presenter_profile').attr('src', "<?= base_url() ?>uploads/presenter_photo/" + presenter_photo);
                    }
                });
            } else {
                $('#presenter_profile').attr('src', "<?= base_url() ?>uploads/presenter_photo/presenter_avtar.png");
            }
            if (designation != "" && designation != null) {
                $('#presenter_title').text(presenter_name + ", " + designation);
            } else {
                $('#presenter_title').text(presenter_name);
            }
            $('#email').text(email);
            if (company_name != "" && company_name != null) {
                $('#company').text(company_name);
                $('#company_lbl').text("Company");
            } else {
                $('#company').text("");
                $('#company_lbl').text("");
            }
            $('#modal').modal('show');
        });
    });
    // console.log($("#time_second").val())
    var upgradeTime = $("#time_second").val();
    var seconds = upgradeTime;
    function timer() {
        var days = Math.floor(seconds / 24 / 60 / 60);
        var hoursLeft = Math.floor((seconds) - (days * 86400));
        var hours = Math.floor(hoursLeft / 3600);
        var minutesLeft = Math.floor((hoursLeft) - (hours * 3600));
        var minutes = Math.floor(minutesLeft / 60);
        var remainingSeconds = seconds % 60;
        function pad(n) {
            return (n < 10 ? "0" + n : n);
        }
        if (pad(days) > 1) {
            var days_lable = "days"
        } else {
            var days_lable = "day"
        }

        if (pad(hours) > 1) {
            var hours_lable = "hours"
        } else {
            var hours_lable = "hour"
        }

        if (pad(minutes) > 1) {
            var minutes_lable = "minutes"
        } else {
            var minutes_lable = "minute"
        }
        if (pad(remainingSeconds) > 1) {
            var remainingSeconds_lable = "seconds"
        } else {
            var remainingSeconds_lable = "second"
        }
        document.getElementById('id_day_time').innerHTML = pad(days) + " " + days_lable + ", " + pad(hours) + " " + hours_lable + ", " + pad(minutes) + " " + minutes_lable + ", " + pad(remainingSeconds) + " " + remainingSeconds_lable;

        if (seconds <= 1800 ||seconds <= 60 || seconds <= 0) {
            if ($("#sessions_type_status").val() == "Private")
            {
                window.location = "<?= site_url() ?>private_sessions/view/<?= (isset($sessions) && !empty($sessions)) ? $sessions->sessions_id : "" ?>";
            } else {
                if (seconds <= 60)
                    window.location = "<?= site_url() ?>sessions/view/<?= (isset($sessions) && !empty($sessions)) ? $sessions->sessions_id : "" ?>";
                seconds--;
            }
        } else {
            seconds--;
        }
    }
</script>
<script type="text/javascript">
    $(document).ready(function () {
        var page_link = $(location).attr('href');
        var user_id = <?= $this->session->userdata("cid") ?>;
        var page_name = "Attend View";
        $.ajax({
            url: "<?= base_url() ?>home/add_user_activity",
            type: "post",
            data: {'user_id': user_id, 'page_name': page_name, 'page_link': page_link},
            dataType: "json",
            success: function (data) {
            }
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        push_notification_admin();
        setInterval(push_notification_admin, 4000);

        $('.push_notification_close').on('click', function () {
            var push_notification_id = $("#push_notification_id").val();
            $.ajax({
                url: "<?= base_url() ?>push_notification/push_notification_close",
                type: "post",
                data: {'push_notification_id': push_notification_id},
                dataType: "json",
                success: function (data) {
                }
            });
        });

        function push_notification_admin()
        {
            var push_notification_id = $("#push_notification_id").val();

            $.ajax({
                url: "<?= base_url() ?>push_notification/get_push_notification_admin",
                type: "post",
                dataType: "json",
                success: function (data) {
                    if (data.status == "success") {
                        if (push_notification_id == "0") {
                            $("#push_notification_id").val(data.result.push_notification_id);
                        }
                        if (push_notification_id != data.result.push_notification_id) {
                            $.ajax({
                                url: "<?= base_url() ?>push_notification/get_push_notification_admin_check_status",
                                type: "post",
                                data: {'push_notification_id': data.result.push_notification_id},
                                dataType: "json",
                                success: function (dt) {
                                    if (dt.status == "success") {
                                        $("#push_notification_id").val(data.result.push_notification_id);
                                        $('#push_notification').modal('show');
                                        $("#push_notification_message").text(data.result.message);
                                    }
                                }
                            });
                        }
                    } else {
                        $('#push_notification').modal('hide');
                    }
                }
            });
        }
    });
</script>
