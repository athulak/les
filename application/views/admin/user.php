<style>
    #example_wrapper .dt-buttons .buttons-csv{
        background-color: #1fbba6;
        padding: 5px 15px 5px 15px;

    }
</style>
<div class="main-content">
    <div class="wrap-content container" id="container">
        <!-- start: PAGE TITLE -->
        <section id="page-title">
            <div class="row">
                <div class="col-sm-8">
                    <h1 class="mainTitle">List Of User Data</h1>
                </div>
            </div>
        </section>
        <!-- end: PAGE TITLE -->
        <!-- start: DYNAMIC TABLE -->
        <div class="container-fluid container-fullw">
            <div class="row">
                <div class="col-md-5 col-md-offset-1">
                    <div class="panel panel-primary" id="panel5">
                        <div class="panel-heading">
                            <h4 class="panel-title text-white">Import CSV for User</h4>
                        </div>
                        <div class="panel-body bg-white" style="border: 1px solid #b2b7bb;">
                            <form class="form-login" id="frm_import_full_conference_with_roundtables" name="frm_import_full_conference_with_roundtables" enctype="multipart/form-data" method="post" action="<?= base_url() ?>admin/user/import_user">
                                <div class="form-body">
                                    <div class="form-group">
                                        <a href="<?= base_url() ?>uploads/user_import_sample.csv" download>Download Sample CSV</a>
                                    </div>
                                    <div class="form-group">
                                        <label class="text-large">Select Choose File :</label>
                                        <label id="projectinput8" class="file center-block">
                                            <input type="file" name="import_file" accept=".csv" id="import_file">
                                            <span class="file-custom"></span>
                                        </label><br>
                                        <span id="errorimport_file" style="color:red;"></span>
                                    </div>
                                </div>
                                <div class="form-actions center">
                                    <button type="submit" id="btn_import" class="btn btn-info">
                                        <i class="la la-check-square-o"></i> Import
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-5">
                    <div class="panel panel-primary" id="panel5">
                        <div class="panel-heading">
                            <h4 class="panel-title text-white">Add New Member </h4>
                        </div>
                        <div class="panel-body bg-white" style="border: 1px solid #b2b7bb;">
                            <form name="frm_credit" id="frm_credit" method="POST" action="">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="text-large">Select Member Type :</label>
                                            <select class="form-control" id="member_type_manualy" name="member_type">
                                                <option value="">Select Member Type</option>
                                                <option value="LES Member">LES Member</option>
                                                <option value="New Member and Meeting Combo">New Member and Meeting Combo</option>
                                                <option value="Non-Member">Non-Member</option>
                                                <option value="Corporate & Non-profit - Group Deal">Corporate & Non-profit - Group Deal</option>
                                                <option value="Speaker">Speaker</option>
                                            </select>
                                            <span id="errormember_type_manualy" style="color:red;"></span>
                                        </div>
                                        <div class="form-group">
                                            <label class="text-large">First name:</label>
                                            <input type="text" name="first_name" id="first_name" placeholder="First name" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label class="text-large">Last name:</label>
                                            <input type="text" name="last_name" id="last_name" placeholder="Last name" class="form-control">
                                        </div>
                                        <div class="form-group" id="email_section">
                                            <label class="text-large">Email:</label>
                                            <input type="email" name="email" id="email" placeholder="Email" class="form-control">
                                        </div>
                                        <div class="form-group" id="username_section">
                                            <label class="text-large">Username:</label>
                                            <input type="text" name="username" id="username" placeholder="Username" class="form-control">
                                            <input type="hidden" name="cust_id" id="cust_id" value="0">
                                            <input type="hidden" name="cr_type" id="cr_type" value="save">
                                        </div>
                                        <div class="form-group" id="password_section">
                                            <label class="text-large">Password:</label>
                                            <input type="text" name="password" id="password" placeholder="Password" class="form-control">
                                        </div>
                                        <h5 class="over-title margin-bottom-15">
                                            <button type="button" id="save_btn" name="save_btn" class="btn btn-green add-row">
                                                Submit
                                            </button>
                                        </h5>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (isset($import_user_session) && !empty($import_user_session)) { ?>
                <div class="row">
                    <div class="panel panel-primary" id="panel5">
                        <div class="panel-heading">
                            <h4 class="panel-title text-red">Import Fail User</h4>
                        </div>
                        <div class="panel-body bg-white" style="border: 1px solid #b2b7bb!important;">

                            <div class="row">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-bordered table-striped text-center" id="user_tbl2">
                                        <thead class="th_center">
                                            <tr>
                                                <th>ID</th>
                                                <th>Full Name</th>
                                                <th>Email</th>
                                                <th>Username</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($import_user_session) && !empty($import_user_session)) {
                                                foreach ($import_user_session as $key => $val) {
                                                    $key++;
                                                    ?>
                                                    <tr>
                                                        <td><?= $key ?></td>
                                                        <td><?= $val['first_name'] . " " . $val['last_name'] ?></td>
                                                        <td><?= $val['username'] ?></td>
                                                        <td><?= $val['email'] ?></td>
                                                        <td><b style="color: red"><?= $val['status'] ?></b></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="row">
                <div class="panel panel-primary" id="panel5">
                    <div class="panel-heading">
                        <h4 class="panel-title text-white">User Data</h4>
                    </div>
                    <div class="panel-body bg-white" style="border: 1px solid #b2b7bb!important;">
                        <form id="frm_member" name="frm_member" action="#" method="post">
                            <div class="row" style="margin-top: 10px; margin-bottom: 10px;">
                                <div class="col-md-12">
                                    <button type="button" id="btndeleteall" class="btn btn-sm btn-danger"><i class="ti-trash"></i> Delete</button>
                                    <span style="color: #000; font-size: 20px; font-weight: 600; margin-top: 5px; margin-left: 20px;">Current Users:  <?= sizeof($user) ?></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-bordered table-striped text-center" id="user">
                                        <thead class="th_center">
                                            <tr>
                                                <td style="width: 60px; text-align: center;"><input type="checkbox" id="select_all" name="select_all"></td>
                                                <th>Date</th>
                                                <th>Register ID</th>
                                                <th>Profile</th>
                                                <th>Full Name</th>
                                                <th>Phone No.</th>
                                                <th>Email</th>
                                                <th>Username</th>
                                                <th>Password</th>
                                                <th>City</th>
                                                <th>State</th>
                                                <th>Country</th>
                                                <th>Members</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if (isset($user) && !empty($user)) {
                                                foreach ($user as $val) {
                                                    ?>
                                                    <tr>
                                                        <td align="center">
                                                            <div>
                                                                <input type="checkbox" class="grid_checkbox" id="members[]" name="members[]" value="<?= $val->cust_id ?>"/>                                                
                                                            </div>
                                                        </td>
                                                        <td><?= date("Y-m-d", strtotime($val->register_date)) ?></td>
                                                        <td><?= $val->register_id ?></td>
                                                        <td>
                                                            <?php if ($val->profile != "") { ?>
                                                                <img src="<?= base_url() ?>uploads/customer_profile/<?= $val->profile ?>" style="height: 40px; width: 40px;">
                                                            <?php } else { ?>
                                                                <img src="<?= base_url() ?>assets/images/Avatar.png" style="height: 40px; width: 40px;">
                                                            <?php } ?>
                                                        </td>
                                                        <td><?= $val->last_name . ' ' . $val->first_name ?></td>
                                                        <td><?= $val->phone ?></td>
                                                        <td><?= $val->email ?></td>
                                                        <td><?= $val->username ?></td>
                                                        <td><?= base64_decode($val->password) ?></td>
                                                        <td><?= $val->city ?></td>
                                                        <td><?= $val->state ?></td>
                                                        <td><?= $val->country ?></td>
                                                        <td><?= $val->member_status ?></td> 
                                                        <td>
                                                            <a class="btn btn-danger btn-sm delete_presenter" href="<?= base_url() . 'admin/user/deleteuser/' . $val->cust_id ?>" style="margin-bottom: 2px;">
                                                                <i class="fa fa-trash-o"></i> Delete
                                                            </a>
                                                            <a class="btn btn-primary btn-sm" href="<?= base_url() . 'admin/user/user_activity/' . $val->cust_id ?>" style="margin-bottom: 2px;">
                                                                Activity
                                                            </a>
                                                            <?php if ($val->v_card != "") { ?>
                                                                <a download class="btn btn-info btn-sm" href="<?= base_url() . 'uploads/upload_vcard/' . $val->v_card ?>" style="margin-bottom: 2px;">
                                                                    vCard
                                                                </a>
                                                            <?php } else { ?>
                                                                <a class="btn btn-info btn-sm" href="<?= base_url() . 'admin/exportvcard/' . $val->cust_id ?>" style="margin-bottom: 2px;">
                                                                    vCard
                                                                </a>
                                                            <?php } ?>
                                                            <a class="btn btn-primary btn-sm" href="<?= base_url() . 'admin/user/editUser/' . $val->cust_id ?>" style="margin-bottom: 2px;">
                                                                <i class="fa fa-pencil"></i> Edit
                                                            </a>
        <!--                                                            <a class="btn btn-primary btn-sm edit_user" data-id="<?= $val->cust_id ?>" href="#" style="margin-bottom: 2px;">
                                                                <i class="fa fa-pencil"></i> Edit
                                                            </a>-->
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php
$msg = $this->input->get('msg');
switch ($msg) {
    case "A":
        $m = "Already exists Email or Username exist with new User";
        $t = "success";
        break;
    case "D":
        $m = "User Delete Successfully...!!!";
        $t = "success";
        break;
    case "U":
        $m = "User Update Successfully...!!!";
        $t = "success";
        break;
    case "E":
        $m = "Something went wrong, Please try again!!!";
        $t = "error";
        break;
    default:
        $m = 0;
        break;
}
?>
<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
<?php if ($msg): ?>
            alertify.<?= $t ?>("<?= $m ?>");
<?php endif; ?>
        $("#user").dataTable({
            "ordering": true,
        });
        $("#btn_import").on("click", function () {
            if ($('#import_file').val() == '') {
                alertify.error('Select File');
                return false;
            } else {
                return true; //submit form
            }
            return false; //Prevent form to submitting
        });

        $('#select_all').click(function () {
            if (this.checked)
            {
                $('.grid_checkbox').each(function () {
                    this.checked = true;
                });
            } else
            {
                $('.grid_checkbox').each(function () {
                    this.checked = false;
                });
            }
        });

        $('#btndeleteall').on("click", function () {
            var checkValues = $('.grid_checkbox:checked').map(function ()
            {
                return $(this).val();
            }).get();
            if (checkValues.length != 0)
            {
                alertify.confirm("Are you sure to delete " + checkValues.length + " record?", function (e) {
                    if (e)
                    {
                        $('#frm_member').attr('action', '<?= base_url() ?>admin/user/alldelete/' + checkValues);
                        $('#frm_member').submit();
                        return true;
                    }
                });
            } else
            {
                alertify.error('Select any record for delete!');
                return false;
            }
            return false;
        });

        $('#save_btn').click(function () {
            if ($('#cr_type').val() == "save") {
                if ($('#member_type_manualy').val() == '') {
                    alertify.error('Select Import Member Type');
                    return false;
                } else if ($('#first_name').val() == '') {
                    alertify.error('Please Enter First Name');
                    return false;
                } else if ($('#last_name').val() == '') {
                    alertify.error('Please Enter Last Name');
                    return false;
                } else if ($('#email').val() == '') {
                    alertify.error('Please Enter Email');
                    return false;
                } else if ($('#username').val() == '') {
                    alertify.error('Please Enter Username');
                    return false;
                } else if ($('#password').val() == '') {
                    alertify.error('Please Enter Password');
                    return false;
                } else {
                    $('#frm_credit').attr('action', '<?= base_url() ?>admin/user/add_user_with_type');
                    $('#frm_credit').submit();
                    return true;
                }
            } else if ($('#cr_type').val() == "update") {
                if ($('#member_type_manualy').val() == '') {
                    alertify.error('Select Import Member Type');
                    return false;
                } else {
                    $('#frm_credit').attr('action', '<?= base_url() ?>admin/user/add_user_with_type');
                    $('#frm_credit').submit();
                    return true;
                }
            }
        });


        $(document).on("click", ".edit_user", function () {
            var cr_id = $(this).attr('data-id');
            if (cr_id != '') {
                $.ajax({
                    url: "<?= base_url() ?>admin/user/getUserById/" + cr_id,
                    type: "post",
                    success: function (response) {
                        cr_data = JSON.parse(response);
                        if (cr_data.msg == "success")
                        {
                            $("#email_section").hide();
                            $("#username_section").hide();
                            $("#password_section").hide();
                            $('#member_type_manualy').val(cr_data.data.customer_type);
                            $('#cust_id').val(cr_data.data.cust_id);
                            $('#cr_type').val('update');
                        } else {
                            alertify.error('Something went wrong, Please try again!');
                            window.setTimeout('location.reload()', 3000);
                        }
                    }
                });
            } else {
                alertify.error('Something went wrong, Please try again!');
                window.setTimeout('location.reload()', 3000);
            }
        });


    });
</script>
