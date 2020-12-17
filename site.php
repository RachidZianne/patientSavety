<?php
require_once 'connection.php';
require_once 'admin_security.php';
$db = new DB();

if(isset($_GET['delete']))
{
    $result = $db->delete('site',['id'=>$_GET['delete']]);
    header('location:site.php');
}

$ga = new GA();
$authUrl = $ga->getAuthUrl();
if(isset($_POST['add_site']))
{
	$arrData = [];
	$arrData['name'] = $_POST['name'];
	$arrData['code'] = $_POST['code'];
	$arrData['project_id'] = $_POST['project_id'];
	$arrResult = $db->getAccessToken($arrData,true);
	if($arrResult['isSuccess'])
	{
		$success = "Site added.";
	}
	else
	{
		$error = $arrResult['message'];
	}
}
$arrSite = $db->select('site');
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Google Analytics - Multisite | Deadlock</title>
	<?php require_once 'head.php'; ?>
</head>
<body class="layout-horizontal">
	<!-- START APP WRAPPER -->
	<div id="app">
		<?php require_once 'header.php'; ?>
		<div class="content-wrapper">
			<div class="content container-fluid">
			<header class="page-header">
					<div class="d-flex align-items-center">
						<div class="mr-auto">
							<h1 class="separator">Sites</h1>
							<nav class="breadcrumb-wrapper" aria-label="breadcrumb">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="dashboard.php"><i class="icon dripicons-home"></i></a></li>
									<li class="breadcrumb-item active" aria-current="page">Sites</li>
								</ol>
							</nav>
						</div>
					</div>
				</header>
				<section class="page-content">
						<div class="row">
							<div class="col-md-6">
								<div class="card">
									
									<h5 class="card-header">Add Site</h5>
									<div class="card-body">
									<?php 
									if(isset($success))
									{
									?>
									<div class="alert alert-success"><?php echo $success; ?></div>
									<?php
									}
									?>
									<?php 
									if(isset($error))
									{
									?>
									<div class="alert alert-error"><?php echo $error; ?></div>
									<?php
									}
									?>
											<form method="post">
												<div class="form-group">
													<label for="demoTextInput1">Site Name</label>
													<input type="text" class="form-control" id="demoTextInput1" required name="name" placeholder="Site name">
												</div>
												<div class="form-group">
													<label for="demoTextInput1">View ID</label>
													<input type="text" class="form-control" id="demoTextInput1" required name="project_id" placeholder="View ID">
													<a href="https://keyword-hero.com/documentation/finding-your-view-id-in-google-analytics" style="font-size: 13px;font-weight: 500;" target="_blank">*Follow this for get your View ID</a>
												</div>
												<div class="form-group">
													<label for="staticEmail">Google Authentication Code</label>
													<div class="input-group mb-3">
														<input type="text" class="form-control" required name="code">
														<div class="input-group-append">
															<span class="input-group-text" id="basic-addon2"><a target="_new" href="<?php echo $authUrl; ?>">Get Code</a></span>
														</div>
													</div>
												</div>
												<div>
												 <button name="add_site" class="btn btn-primary btn-rounded btn-floating" type="submit">Add Site</button>
												</div>
											</form>
									</div>
								</div>
							</div>
							<div class="col-md-6">
							<div class="card">
									<h5 class="card-header">All Sites</h5>
									<div class="card-body">
											<div class="table-responsive">
												<table class="table">
													<thead>
														<tr>
															<th>Site Name</th>
															<th>View ID</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
													<?php 
													if($arrSite['total_record'])
													{
														while($row = $arrSite['rs']->fetch_object())
														{
													?>
														<tr>
															<td><?php echo $row->name; ?></td>
															<td><?php echo $row->project_id; ?></td>
															<td><a onclick="return confirm('Are you sure?');" class="btn btn-danger" href="site.php?delete=<?php echo $row->id; ?>">Delete</a></td>
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
				</section>			
			</div>
		</div>	
		<!-- END CONTENT WRAPPER -->
		
		<?php require_once 'footer.php' ?>
		
		
	</body>
	
</html>
