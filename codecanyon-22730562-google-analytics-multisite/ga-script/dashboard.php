<?php
require_once 'connection.php';
require_once 'admin_security.php';
$db = new DB();
$ga = new GA();
if(isset($_GET['site']))
{	
		$site = $db->getAccessToken(['name'=>$_GET['site']]);
		if($site['isSuccess'])
		{
			$site = $site['objSite'];
		}
		else
		{
			$error = $site['message'];
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
        <?php require_once 'head.php'; ?>
        <link rel="stylesheet" href="assets/vendor/jvectormap-next/jquery-jvectormap.css">
        <!-- ======================= PAGE LEVEL VENDOR STYLES ========================-->
        <link rel="stylesheet" href="assets/vendor/bootstrap-datepicker/bootstrap-datepicker.min.css">
        <link rel="stylesheet" href="assets/vendor/bootstrap-daterangepicker/daterangepicker.css">
    </head>

    <body class="layout-horizontal">
        <!-- START APP WRAPPER -->
        <input type="hidden" value="<?php echo $site->name; ?>" id="site" />
        <div id="app">
            <?php require_once 'header.php'; ?>
            <?php  
            if(isset($site))
            {
            ?>
            <div class="content-wrapper">
                <div class="content container-fluid">
                    <!--START PAGE HEADER -->
                    <header class="page-header">
                        <div class="d-flex align-items-center">
                            <div class="mr-auto">
                                <h1 class="separator"> Analytics Dashboard -
                                    <?php echo $site->name; ?>
                                </h1>
                            </div>
                        </div>
                    </header>
                    <section class="page-content">
                        <div class="row">
                            <div class="col">
                                <div class="card" id="card_user_type">
                                    <h5 class="card-header p-t-25 p-b-20">
                                        Visitors
                                    </h5>
                                    <div class="card-toolbar top-right">
                                        <ul class="nav nav-pills nav-pills-primary justify-content-end chart_duration" id="pills-demo-1" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link active" id="pills-1-tab" data-toggle="pill" href="#pills-1" role="tab" aria-controls="pills-1" aria-selected="true">Week</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-2-tab" data-toggle="pill" href="#pills-2" role="tab" aria-controls="pills-2" aria-selected="false">Month</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" id="pills-3-tab" data-toggle="pill" href="#pills-3" role="tab" aria-controls="pills-3" aria-selected="false">Year</a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="row m-0 col-border-xl">
                                            <div class="col-md-2 p-20">
                                                <div class="card-body p-0">
                                                    <h6>Total New Visitors</h6>
                                                    <span class="h3 text-primary total_visitor">0</span>
                                                    <h6 class="m-t-20">Total Returning Visitor</h6>
                                                    <span class="h3 text-danger total_returning_visitor">0</span>

                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="card-body">
                                                    <canvas id="chart_user_type"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="card-deck m-b-30">
                                    <div class="card">
                                        <h5 class="card-header border-none">Users</h5>
                                        <div class="card-body p-0">
                                            <h4 class="card-title text-info p-t-10 p-l-15 total_user">0</h4>
                                            <div class="h-200">
                                                <canvas id="usersChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <h5 class="card-header border-none">Bounce Rate</h5>
                                        <div class="card-body p-0">
                                            <h4 class="card-title text-warning p-t-10 p-l-15 total_bounce_rate">0%</h4>
                                            <div class="h-200">
                                                <canvas id="bounceRateChart"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <h5 class="card-header border-none">Session Duration</h5>
                                        <div class="card-body p-0">
                                            <h4 class="card-title text-primary p-t-10 p-l-15 total_session_duration">0</h4>
                                            <div class="h-200">
                                                <canvas id="sessionDuration"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-30">
                            <div class="col">
                                <div class="card-deck">
                                    <div class="card">
                                        <h5 class="card-header">New Users by Location</h5>
                                        <div class="card-body">
                                            <div id="world-map" style="height: 300px"> </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <h5 class="card-header">Live Active Users</h5>
                                        <div class="card-body">
                                            <div class="icon-rounded icon-rounded-primary float-left m-r-20">
                                                <i class="icon dripicons-graph-bar"></i>
                                            </div>
                                            <h5 class="card-title m-b-5 counter" data-count="0" id="live_users">0</h5>
                                            <h6 class="text-muted m-t-10">
                                                Active Users
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row m-b-30">
                            <div class="col">
                                <div class="card-deck">

                                    <div class="card">
                                        <h5 class="card-header">Top Active Pages</h5>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col"></th>
                                                            <th scope="col">Active Page</th>
                                                            <th scope="col">Active Users</th>
                                                            <th scope="col">% New Sessions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="active_pages"></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card">
                                        <h5 class="card-header">Sessions by device</h5>
                                        <div class="card-body">
                                            <div class="card-body p-10">
                                                <div id="session_by_device" style="height:400px"></div>
                                            </div>
                                            <div class="card-footer">
                                                <ul class="list-reset list-inline-block text-center">
                                                    <li class="text-muted text-info m-r-10">
                                                        <i class="badge badge-info m-r-5  badge-circle w-10 h-10"></i>Desktop
                                                    </li>
                                                    <li class="text-muted text-accent m-r-10">
                                                        <i class="badge badge-accent m-r-5  badge-circle w-10 h-10 "></i>Mobile
                                                    </li>
                                                    <li class="text-muted text-primary m-r-10 ">
                                                        <i class="badge badge-primary m-r-5  badge-circle w-10 h-10 "></i>Tablet
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="card" id="card_custom">
                                    <h5 class="card-header p-t-25 p-b-20">
                                        Custom Chart
                                    </h5>
                                    <div class="card-toolbar top-right">
                                        <?php 
                                        $arrMatrics = [];
                                        $arrMatrics['ga:pageviews'] = [
                                                                            'lable'=>"Pageviews",
                                                                            'dimension'=>[
                                                                                'ga:userType'=>'User Type',
                                                                                'ga:medium'=>'Medium',
                                                                                'ga:source'=>'Source',
                                                                                'ga:keyword'=>'Keyword',
                                                                                'ga:socialNetwork'=>'Social Network',
                                                                                'ga:browser'=>'Browser',
                                                                                'ga:operatingSystem'=>'Operating System',
                                                                                'ga:deviceCategory'=>'Device Category',
                                                                                'ga:language'=>'Language',
                                                                                'ga:screenResolution'=>'Screen Resolution'
                                                                            ]
                                                                      ];
                                        $arrMatrics['ga:newUsers'] = [
                                                                            'lable'=>"New Users",
                                                                            'dimension'=>[
                                                                                'ga:userType'=>'User Type',
                                                                                'ga:medium'=>'Medium',
                                                                                'ga:source'=>'Source',
                                                                                'ga:keyword'=>'Keyword',
                                                                                'ga:socialNetwork'=>'Social Network',
                                                                                'ga:browser'=>'Browser',
                                                                                'ga:operatingSystem'=>'Operating System',
                                                                                'ga:deviceCategory'=>'Device Category',
                                                                                'ga:language'=>'Language',
                                                                                'ga:screenResolution'=>'Screen Resolution'
                                                                            ]
                                                                      ];
                                        $arrMatrics['ga:sessions'] = [
                                                                            'lable'=>"Sessions",
                                                                            'dimension'=>[
                                                                                'ga:userType'=>'User Type',
                                                                                'ga:medium'=>'Medium',
                                                                                'ga:source'=>'Source',
                                                                                'ga:keyword'=>'Keyword',
                                                                                'ga:socialNetwork'=>'Social Network',
                                                                                'ga:browser'=>'Browser',
                                                                                'ga:operatingSystem'=>'Operating System',
                                                                                'ga:deviceCategory'=>'Device Category',
                                                                                'ga:language'=>'Language',
                                                                                'ga:screenResolution'=>'Screen Resolution'
                                                                            ]
                                                                      ];
                                        $arrMatrics['ga:hits'] = [
                                                                            'lable'=>"Hits",
                                                                            'dimension'=>[
                                                                                'ga:userType'=>'User Type',
                                                                                'ga:medium'=>'Medium',
                                                                                'ga:source'=>'Source',
                                                                                'ga:keyword'=>'Keyword',
                                                                                'ga:socialNetwork'=>'Social Network',
                                                                                'ga:browser'=>'Browser',
                                                                                'ga:operatingSystem'=>'Operating System',
                                                                                'ga:deviceCategory'=>'Device Category',
                                                                                'ga:language'=>'Language',
                                                                                'ga:screenResolution'=>'Screen Resolution'
                                                                            ]
                                                                      ];
                                        ?>
                                        <form class="form-inline text-right" method="post" id="custom_form">
                                            <label class="sr-only" for="inlineFormInputName2">Metrics</label>
                                            <select name="metrics" class="form-control mb-2 mr-sm-2 metrics">
                                                <option value="">Metrics</option>
                                                <?php foreach($arrMatrics as $key => $matrics){ ?>
                                                <option data-dimension='<?php echo json_encode($matrics['dimension']); ?>' value="<?php echo $key; ?>"><?php echo $matrics['lable']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <label class="sr-only" for="inlineFormInputName2">Dimension</label>
                                            <select name="dimension" class="form-control mb-2 mr-sm-2 dimension">
                                                <option value="">Dimension</option>
                                            </select>
                                            <label class="sr-only" for="inlineFormInputName2">Duration</label>
                                            <input type="text" name="dates" class="form-control mb-2 mr-sm-2 duration" />
                                            <button type="button" class="btn btn-primary mb-2">Refresh</button>
                                        </form>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="row m-0 col-border-xl">
                                            <div class="col-md-12">
                                                <div class="card-body" id="container_custom_chart">
                                                    <div class="text-center" id="chart_loader" style="display:none;"><img src='assets/img/loader.gif' /></div>
                                                    <canvas id="chart_custom"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
            <?php
            }
            else
            {
                ?>
                <div class="content-wrapper">
                    <div class="content container-fluid">
                        <!--START PAGE HEADER -->
                        <header class="page-header">
                            <div class="d-flex align-items-center">
                                <div class="mr-auto">
                                    <h1 class="separator"> Analytics Dashboard</h1>
                                </div>
                                
                            </div>
                        </header>
                        <section class="page-content">
                            <div class="col-12">
                                <div class="">
                                    <div class="row m-0 col-border-xl">
                                        <?php 
                                        $arrSite = $db->select('site');
                                        if($arrSite['total_record'])
										{
											while($row = $arrSite['rs']->fetch_object())
											{
                                        ?>
                                        <div class="col-md-12 col-lg-6 col-xl-3 m-2 card">
                                            <div class="card-body">
                                                <div class="icon-rounded icon-rounded-primary float-left m-r-20">
                                                    <i class="icon dripicons-graph-bar"></i>
                                                </div>
                                                <h5 class="card-title m-b-5"><?php echo $row->name; ?></h5>
                                                <h6 class="text-muted m-t-10">
                                                    <?php echo $row->project_id; ?>
                                                </h6>
                                                <small class="text-muted float-right m-t-5 mb-3">
                                                <a class="btn" href="dashboard.php?site=<?php echo $row->name; ?>">View</a>
                                                </small>
                                            </div>
                                        </div>
                                        <?php
                                            }
                                        }
                                            ?>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <!-- END CONTENT WRAPPER -->
        <script>
		<?php $dirr=dirname($_SERVER['REQUEST_URI']);

		?>
            var url = "<?php if($dirr==''){echo ''; }else{$dirr.'/';} ?>getChart.php";
           
        </script>
        <?php require_once 'footer.php' ?>
        <!-- ================== PAGE LEVEL VENDOR SCRIPTS ==================-->
        <script src="assets/vendor/countup.js/dist/countUp.min.js"></script>
        <script src="assets/vendor/flot/jquery.flot.js"></script>
        <script src="assets/vendor/jquery.flot.tooltip/js/jquery.flot.tooltip.min.js"></script>
        <script src="assets/vendor/flot.curvedlines/curvedLines.js"></script>
        <script src="assets/vendor/d3/dist/d3.min.js"></script>
        <script src="assets/vendor/c3/c3.min.js"></script>
        <!-- ================== MAP SCRIPTS ==================-->
        <script src="assets/vendor/jvectormap-next/jquery-jvectormap.min.js"></script>
        <script src="assets/vendor/jvectormap-next/jquery-jvectormap-world-mill.js"></script>
        <!-- ================== PAGE LEVEL SCRIPTS ==================-->
        <script src="assets/vendor/chart.js/dist/Chart.bundle.min.js"></script>
        <script src="assets/js/charts/chartjs-init.js"></script>
        <!-- ================== DATE SCRIPTS ==================-->
        <script src="assets/vendor/moment/min/moment.min.js"></script>
        <script src="assets/vendor/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
        <script src="assets/vendor/bootstrap-daterangepicker/daterangepicker.js"></script>

        <script>
            $(document).ready(function() {
                $('input[name="dates"]').daterangepicker({
                    opens: 'left',
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment(),
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });
            });
            $(document).on("change", ".metrics", function() {
                var dimension = ($(this).find(':selected').data('dimension'));
                $(".dimension").html('');
                var html = '<option value="">Dimension</option>';
                $.each(dimension, function(k, v) {
                    html += '<option value="' + k + '">' + v + '</option>';
                });
                $(".dimension").html(html);
            });

        </script>

    </body>

   </html>
