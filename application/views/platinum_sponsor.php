<!-- SECTION -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
<style>
    .icon-home {
        color: #ef9d45;
        font-size: 1.5em;
        font-weight: 700;
        vertical-align: middle;
    }

    .box-home {
        background-color: #fff;
        /*border-radius: 30px*/;
        /*background: rgba(250, 250, 250, 0.8);*/
        width: 240px;
        height: 190px;
        cursor: pointer;
    }

    #alertify{
        padding: 30px 0;
    }

</style>
<a href="<?= base_url() ?>sponsor/other_sponsor" style="text-align: center; color: #ef9d45; font-weight: 900;position: absolute;margin-left:37.2%;margin-top: 30%;z-index: 10; font-size: 31px;">Check out the exhibits!</a>
<a href="javascript:void(0)" id="btn_expo_hrs" style="text-align: center; color: #fffef0; position: absolute;margin-top: 33%;z-index: 10; font-size: 19px;left: 0;right: 0;"><u>Live Expo Hours</u></a>
<div style="background-image: url(<?= base_url() ?>front_assets/images/expo_background.jpg); background-attachment: fixed; background-size: cover !important; background-position: center center !important; height: 4000px">
    <section class="parallax" style="position: fixed !important;">
        <div class="container container-fullscreen">
            <div class="text-middle" style="vertical-align: top !important;">
                <div class="row">
                    <!--                <div class="col-md-12">
                                    <div class="text-center m-t-0">
                                        <h1 style="color: orange; font-family: 'Architects Daughter', cursive; margin-bottom: 0px; font-weight: 700; font-size: 40px;">Welcome, <?= $this->session->userdata('cname') ?></h1>
                                    </div>
                                </div>-->
<!--                    <div class="row">-->
<!--                        --><?php
//                        if (isset($all_sponsor) && !empty($all_sponsor)) {
//                            $i = 1;
//
//                            foreach ($all_sponsor as $val) {
//                                if ($val->sponsors_id == 18 || $val->sponsors_id == 22){
////                                    $backgroundRemover = 'style="background: none !important;"';
//                                    $backgroundRemover = '';
//                                }else{
//                                    $backgroundRemover = '';
//                                }
//                                if ($i % 2 == 1){
//
//                                ?>
<!--                                <div class="col-md-6 p-l-35">-->
<!--                                    <span class="icon-home">-->
<!--                                        <div class="col-lg box-home text-center" --><?//=$backgroundRemover?><!-- onclick="window.location='--><?//= base_url() ?><!--sponsor/view/<?//= $val->sponsors_id ?>';">
                                           <!-- <img src="<?//= base_url() ?> uploads/sponsors/<?//= $val->sponsors_logo ?>" alt="welcome" style="max-width: 160px">-->
<!--                                        </div>-->
<!--                                    </span>-->
<!--                                </div>-->
<!--                                --><?php
//                                $i++;
//                                }else{
//                                    ?>
<!---->
<!--                                    <div class="col-md-6 p-r-35" style="text-align: -webkit-right; text-align: -moz-right; text-align: -o-right; text-align: -ms-right;">-->
<!--                                        <span class="icon-home">-->
<!--                                            <div class="col-lg box-home text-center" --><?//=$backgroundRemover?><!--  onclick="window.location='--><?//= base_url() ?><!--sponsor/view/<?//= $val->sponsors_id ?>';">
                                                <!-- <img src="<?//= base_url() ?>uploads/sponsors/<?//= $val->sponsors_logo ?>" alt="welcome" style="max-width: 160px">-->
<!--                                            </div>-->
<!--                                        </span>-->
<!--                                    </div>-->
<!---->
<!--                                    --><?php
//                                    $i++;
//                                }
//                            }
//                        }
//                        ?>
<!--                    </div>-->
                </div>
            </div>
        </div>
    </section>
</div>
<!--<div class="modal fade" id="push_notification" tabindex="-1" role="modal" aria-labelledby="modal-label" aria-hidden="true" style="display: none; text-align: left; right: unset;">
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
                <button type="button" class="close" style="padding: 10px; color: #fff; background-color: #ef9d45; opacity: 1;" data-dismiss="modal" aria-hidden="true">Close</button>
            </div>
        </div>
    </div>
</div>-->
<script type="text/javascript">
    $(document).ready(function () {
        var page_link = $(location).attr('href');
        var user_id = <?= $this->session->userdata("cid") ?>;
        var page_name = "Sponsor";
        $.ajax({
            url: "<?= base_url() ?>home/add_user_activity",
            type: "post",
            data: {'user_id': user_id, 'page_name': page_name, 'page_link': page_link},
            dataType: "json",
            success: function (data) {
            }
        });

        $('#btn_expo_hrs').on('click', function () {
            var ttl = '<h3>Live Expo Hours</h3>';
            var expo_hrs = '<p>Sunday, August 16th  6:30pm - 9:00pm</br>Monday, August 17th  12:00pm - 2:00pm</br>Tuesday, August 18th  12:00pm - 2:00pm</br>Wednesday, August 19th  12:00pm - 2:00pm</p>';
            alertify.alert(ttl+expo_hrs);
        });
		
    });
</script>

