<div class="container-fluid">
			<!-- START TOP TOOLBAR WRAPPER -->
			<div class="top-toolbar-wrapper">
				<nav class="top-toolbar navbar flex-nowrap">
					<ul class="navbar-nav nav-right">
                                            <li class="nav-item dropdown dropdown-menu-lg">
							<a class="nav-link nav-pill user-avatar" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                                                            <img src="assets/img/deadlock-favi.png" class="w-35 rounded-circle" alt="Deadlock">
							</a>
							<div class="dropdown-menu dropdown-menu-right dropdown-menu-accout">
								<div class="dropdown-header pb-3">
									<div class="media d-user">
                                                                            <img class="align-self-center mr-3 w-40 rounded-circle" src="assets/img/deadlock-favi.png" alt="Deadlock">
										<div class="media-body">
											<h5 class="mt-0 mb-0">Admin</h5>
											<span><?php echo $_SESSION['user']; ?></span>
										</div>
									</div>
								</div>
								<a class="dropdown-item" href="logout.php"><i class="icon dripicons-lock-open"></i> Sign Out</a>
							</div>
						</li>
						
					</ul>
				</nav>
			</div>
			<!-- END TOP TOOLBAR WRAPPER -->
		</div>
		<!-- START TOP HEADER WRAPPER -->
		<div class="header-wrapper">
			<div class="header-top">
				<!-- START MOBILE MENU TRIGGER -->
				<ul class="mobile-only navbar-nav nav-left">
					<li class="nav-item">
						<a href="javascript:void(0)" data-toggle-state="aside-left-open">
							<i class="icon dripicons-align-left"></i>
						</a>
					</li>
				</ul>
				<!-- END MOBILE MENU TRIGGER -->
				<div class="container-fluid">
					<div class="row">
						<div class="col-sm-12 col-lg-6">
							<ul class="site-logo">
								<li>
									<!-- START LOGO -->
									<a href="dashboard.php">
										
										<h1 class="brand-text">Deadlock</h1>
									</a>
									<!-- END LOGO -->
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- START MOBILE TOOLBAR TRIGGER -->
				<ul class="mobile-only navbar-nav nav-right">
					<li class="nav-item">
						<a href="javascript:void(0)" data-toggle-state="mobile-topbar-toggle">
							<i class="icon dripicons-dots-3 rotate-90"></i>
						</a>
					</li>
				</ul>
				<!-- END MOBILE TOOLBAR TRIGGER -->
			</div>
			<!-- START HEADER BOTTOM -->
			<div class="header-bottom">
				<div class="container-fluid">
					<!-- START MAIN MENU -->
					<nav class="main-menu">
						<ul class="nav metismenu">
							<li class="sidebar-header mobile-only"><span>NAVIGATION</span></li>
							<li>
								<a class="has-arrow" href="dashboard.php" aria-expanded="false"><i class="icon dripicons-meter"></i><span class="hide-menu">Dashboard</span></a>
								<?php
								$sites = $db->select('site');
									if($sites['total_record'])
									{
								?>
								<ul aria-expanded="false" class="collapse">
									<?php
									
										while($row = $sites['rs']->fetch_object())
										{
										?>
										<li><a href="dashboard.php?site=<?php echo $row->name; ?>"><?php echo $row->name; ?></a></li>
										<?php
										}
									?>
								</ul>
								<?php
									}
								?>
							</li>
							<li>
								<a class="has-arrow" href="site.php" aria-expanded="false"><i class="icon dripicons-browser"></i><span class="hide-menu">Site</span></a>
							</li>
						</ul>
					</nav>
					<!-- END MAIN MENU -->
				</div>
			</div>
			<!-- END HEADER BOTTOM -->
		</div>
		<!-- END TOP HEADER WRAPPER -->