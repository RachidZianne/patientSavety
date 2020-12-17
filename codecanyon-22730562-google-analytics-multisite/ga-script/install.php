<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Google Analytics - Multisite | Deadlock</title>
    <?php require_once 'head.php' ?>
</head>

<body class="layout-horizontal">
    <!-- START APP WRAPPER -->
    <div id="app" class="custom-wizard">
        <div class="content-wrapper">
            <div class="container-fluid">

                <section class="content container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <a href="" class="brand text-center d-block m-b-20">
                                        <img src="assets/img/deadlock-logo.png" alt="Deadlock Logo" style="width: 200px;"/>
                                    </a>
                                    <h5 class="sign-in-heading text-center m-b-20">Installation</h5>
                                    <form id="horizontal-wizard" method="post" enctype="multipart/form-data" action="#">
                                        <h3>Account</h3>
                                        <section>
                                            <h5 class="card-title">Account</h5>
                                            <div class="form-group">
                                                <label for="userName">Email Address *</label>
                                                <input type="email" class="form-control required email" name="userName" id="userName">
                                            </div>
                                            <div class="form-group">
                                                <label for="password">Password *</label>
                                                <input id="password" name="password" type="password" class="form-control required">
                                            </div>
                                            <div class="form-group">
                                                <label for="confirm">Confirm Password *</label>
                                                <input id="confirm" name="confirm" type="password" class="form-control required">
                                            </div>
                                        </section>
                                        <h3>Database</h3>
                                        <section>
                                            <h5 class="card-title">Database</h5>

                                            <div class="form-group">
                                                <label for="dbhost">Host *</label>
                                                <input type="text" class="form-control required" name="dbhost" id="dbhost" placeholder="">
                                            </div>
                                            <div class="form-group">
                                                <label for="dbuser">User Name *</label>
                                                <input type="text" class="form-control required" name="dbuser" id="dbuser" placeholder="">
                                            </div>
                                            <div class="form-group">
                                                <label for="dbpassword">Password *</label>
                                                <input type="text" class="form-control required" name="dbpassword" id="dbpassword" placeholder="">
                                            </div>
                                            <div class="form-group">
                                                <label for="dbname">Database Name *</label>
                                                <input type="text" class="form-control required" name="dbname" id="dbname" placeholder="">
                                            </div>
                                        </section>
                                        <h3>Google API</h3>
                                        <section>
                                            <h5 class="card-title">Google API</h5>
                                            <div class="form-group">
                                                <label for="client_secret">JSON File *</label>
                                                <input type="file" class="form-control required" name="client_secret" id="client_secret" placeholder="">
												<a href="http://web.deadlockinfotech.com/wp-content/uploads/2018/10/GA-Create-Client-JSON.pdf" style="font-size: 13px;font-weight: 500;" target="_blank">*Follow this for create your client json file</a>
											</div>
                                            <h5 class="card-title">Confirm</h5>
                                            <div class="custom-control custom-checkbox checkbox-primary form-check">
                                                <input type="checkbox" class="custom-control-input required" id="acceptTerms" name="acceptTerms">
                                                <label class="custom-control-label" for="acceptTerms">I agree with the Terms and Conditions.</label>
                                            </div>
                                        </section>
                                    </form>
                                    <div id="targetLayer">
                                    </div>
                                </div>
                            </div>
                        </div>
                </section>
                </div>

            </div>

        </div>
        <!-- END CONTENT WRAPPER -->

        <?php require_once 'footer.php' ?>
        <script src="assets/js/components/horizontal-wizard-init.js"></script>
</body>

</html>
