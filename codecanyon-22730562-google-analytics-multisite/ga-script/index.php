<?php
require_once 'connection.php';
if(!file_exists("./config/database.php") || !file_exists("./config/client_secret_native.json"))
{
    header('location:install.php');
}
if(isset($_SESSION['user']))
{
	header('location:dashboard.php');
}
if(isset($_POST['login']))
{
	$db = new DB();
	$login = [];
	$login['username'] = $_POST['username'];
	$login['password'] = $_POST['password'];	
	$loginData = $db->select('admin',$login);
	if($loginData['total_record']==1)
	{
		$_SESSION['user'] = $_POST['username'];
		header("location:dashboard.php");
	}
}
?>
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
        <div class="container-fluid">
            <form class="sign-in-form" method="POST" action="">
                <div class="card">
                    <div class="card-body">
                        <a href="" class="brand text-center d-block m-b-20">
						<img src="assets/img/deadlock-logo.png" alt="Deadlock Logo" style="width: 200px;"/>
					</a>
                        <h5 class="sign-in-heading text-center m-b-20">Sign in to your account</h5>
                        <div class="form-group">
                            <label for="inputEmail" class="sr-only">Email address</label>
                            <input type="email" name="username" id="inputEmail" class="form-control" placeholder="Email address" required="">
                        </div>

                        <div class="form-group">
                            <label for="inputPassword" class="sr-only">Password</label>
                            <input type="password" name="password" id="inputPassword" class="form-control" placeholder="Password" required="">
                        </div>
                        <button name="login" class="btn btn-primary btn-rounded btn-floating btn-lg btn-block" type="submit">Sign In</button>
                        
                    </div>

                </div>
            </form>
        </div>
        <?php require_once 'footer.php'; ?>
    </body>

    </html>
