<?php
    session_start();
    /*if (!$_SESSION['logged_in'])
    {
        header('Location: http://localhost/login.php');
    }*/
    //including dB conn
    include('../dBConfig.php');
    $userid = $_SESSION['userid'];
    $sql = 'SELECT name FROM members WHERE mid = :userid';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid]);
    $count = $stmt -> rowCount();
    $row = $stmt -> fetch();
    $name = $row['name'];
    //Fetch no of Members
    $sql = 'SELECT * FROM members WHERE adminid = :userid AND status = :status';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid, 'status' => 1]);
    $mcount = $stmt -> rowCount();
    if ($mcount > 100) {
    	$mem = $mcount % 100;
    } else {
    	$mem = $mcount;
    }
    //Fetch no of Books
    $sql = 'SELECT * FROM books WHERE adminid = :userid AND status = :status';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid, 'status' => 1]);
    $bcount = $stmt -> rowCount();
    if ($bcount > 100) {
    	$bkc = $bcount % 100;
    } else {
    	$bkc = $bcount;
    }
    //Fetch no of Issued Books
    $sql = 'SELECT * FROM tranc WHERE adminid = :userid AND status = :status';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid, 'status' => 0]);
    $icount = $stmt -> rowCount();
    if ($icount > 100) {
    	$ic = $icount % 100;
    } else {
    	$ic = $icount;
    }
    //Fetch pending dues
    $sql = 'SELECT * FROM tranc WHERE adminid = :userid AND fine != :fine';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid, 'fine' => 0]);
    $fcount = $stmt -> rowCount();
    if ($fcount > 100) {
    	$fc = $fcount % 100;
    } else {
    	$fc = $fcount;
    }
?>
<!DOCTYPE html>
<html>
<head>
	<?php include('include/head.php'); ?>
</head>
<body>
	<?php include('include/header.php'); ?>
	<?php include('include/sidebar.php'); ?>
	<div class="main-container">

		<div class="pd-ltr-20 customscroll customscroll-10-p height-100-p xs-pd-20-10">
			<div class="row clearfix progress-box">
				<div class="col-lg-3 col-md-6 col-sm-12 mb-30">
					<div class="bg-white pd-20 box-shadow border-radius-5 height-100-p">
						<div class="project-info clearfix">
							<div class="project-info-left">
								<div class="icon box-shadow bg-blue text-white">
									<i class="fa fa-briefcase"></i>
								</div>
							</div>
							<div class="project-info-right">
								<span class="no text-blue weight-500 font-24"><?php echo $mcount; ?></span>
								<p class="weight-400 font-18">Members</p>
							</div>
						</div>
						<div class="project-info-progress">
							<div class="row clearfix">
								<div class="col-sm-6 text-muted weight-500">Members</div>
								<div class="col-sm-6 text-right weight-500 font-14 text-muted"><?php echo $mcount; ?></div>
							</div>
							<div class="progress" style="height: 10px;">
								<div class="progress-bar bg-blue progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $mem; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-12 mb-30">
					<div class="bg-white pd-20 box-shadow border-radius-5 height-100-p">
						<div class="project-info clearfix">
							<div class="project-info-left">
								<div class="icon box-shadow bg-light-green text-white">
									<i class="fa fa-handshake-o"></i>
								</div>
							</div>
							<div class="project-info-right">
								<span class="no text-light-green weight-500 font-24"><?php echo $bcount; ?></span>
								<p class="weight-400 font-18">Books</p>
							</div>
						</div>
						<div class="project-info-progress">
							<div class="row clearfix">
								<div class="col-sm-6 text-muted weight-500">Books</div>
								<div class="col-sm-6 text-right weight-500 font-14 text-muted"><?php echo $bcount; ?></div>
							</div>
							<div class="progress" style="height: 10px;">
								<div class="progress-bar bg-light-green progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $bkc; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-12 mb-30">
					<div class="bg-white pd-20 box-shadow border-radius-5 height-100-p">
						<div class="project-info clearfix">
							<div class="project-info-left">
								<div class="icon box-shadow bg-light-orange text-white">
									<i class="fa fa-list-alt"></i>
								</div>
							</div>
							<div class="project-info-right">
								<span class="no text-light-orange weight-500 font-24"><?php echo $icount; ?></span>
								<p class="weight-400 font-18">Issued</p>
							</div>
						</div>
						<div class="project-info-progress">
							<div class="row clearfix">
								<div class="col-sm-6 text-muted weight-500">Issued</div>
								<div class="col-sm-6 text-right weight-500 font-14 text-muted"><?php echo $icount; ?></div>
							</div>
							<div class="progress" style="height: 10px;">
								<div class="progress-bar bg-light-orange progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $ic; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-12 mb-30">
					<div class="bg-white pd-20 box-shadow border-radius-5 margin-5 height-100-p">
						<div class="project-info clearfix">
							<div class="project-info-left">
								<div class="icon box-shadow bg-light-purple text-white">
									<i class="fa fa-podcast"></i>
								</div>
							</div>
							<div class="project-info-right">
								<span class="no text-light-purple weight-500 font-24"><?php echo $fcount; ?></span>
								<p class="weight-400 font-18">Dues</p>
							</div>
						</div>
						<div class="project-info-progress">
							<div class="row clearfix">
								<div class="col-sm-6 text-muted weight-500">Dued</div>
								<div class="col-sm-6 text-right weight-500 font-14 text-muted"><?php echo $fcount; ?></div>
							</div>
							<div class="progress" style="height: 10px;">
								<div class="progress-bar bg-light-purple progress-bar-striped progress-bar-animated" role="progressbar" style="width: <?php echo $fc; ?>%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
			</div>

			<?php //include('include/footer.php'); ?>
		</div>-->
	</div>
	<?php include('include/script.php'); ?>
	<script src="src/plugins/highcharts-6.0.7/code/highcharts.js"></script>
	<script src="src/plugins/highcharts-6.0.7/code/highcharts-more.js"></script>
	<script type="text/javascript">
		Highcharts.chart('areaspline-chart', {
			chart: {
				type: 'areaspline'
			},
			title: {
				text: ''
			},
			legend: {
				layout: 'vertical',
				align: 'left',
				verticalAlign: 'top',
				x: 70,
				y: 20,
				floating: true,
				borderWidth: 1,
				backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
			},
			xAxis: {
				categories: [
					'Monday',
					'Tuesday',
					'Wednesday',
					'Thursday',
					'Friday',
					'Saturday',
					'Sunday'
				],
				plotBands: [{
					from: 4.5,
					to: 6.5,
				}],
				gridLineDashStyle: 'longdash',
                gridLineWidth: 1,
                crosshair: true
			},
			yAxis: {
				title: {
					text: ''
				},
				gridLineDashStyle: 'longdash',
			},
			tooltip: {
				shared: true,
				valueSuffix: ' units'
			},
			credits: {
				enabled: false
			},
			plotOptions: {
				areaspline: {
					fillOpacity: 0.6
				}
			},
			series: [{
				name: 'John',
				data: [0, 5, 8, 6, 3, 10, 8],
				color: '#f5956c'
			}, {
				name: 'Jane',
				data: [0, 3, 6, 3, 7, 5, 9],
				color: '#f56767'
			}, {
				name: 'Johnny',
				data: [0, 2, 5, 3, 2, 4, 0],
				color: '#a683eb'
			}, {
				name: 'Daniel',
				data: [0, 4, 7, 3, 0, 7, 4],
				color: '#41ccba'
			}]
		});


		// Device Usage chart
		Highcharts.chart('device-usage', {
			chart: {
				type: 'pie'
			},
			title: {
				text: ''
			},
			subtitle: {
				text: ''
			},
			credits: {
				enabled: false
			},
			plotOptions: {
				series: {
					dataLabels: {
						enabled: false,
						format: '{point.name}: {point.y:.1f}%'
					}
				},
				pie: {
					innerSize: 127,
					depth: 45
				}
			},

			tooltip: {
				headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
				pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'
			},
			series: [{
				name: 'Brands',
				colorByPoint: true,
				data: [{
					name: 'IE',
					y: 10,
					color: '#ecc72f'
				}, {
					name: 'Chrome',
					y: 40,
					color: '#46be8a'
				}, {
					name: 'Firefox',
					y: 30,
					color: '#f2a654'
				}, {
					name: 'Safari',
					y: 10,
					color: '#62a8ea'
				}, {
					name: 'Opera',
					y: 10,
					color: '#e14e50'
				}]
			}]
		});

		// gauge chart
		Highcharts.chart('ram', {

			chart: {
				type: 'gauge',
				plotBackgroundColor: null,
				plotBackgroundImage: null,
				plotBorderWidth: 0,
				plotShadow: false
			},
			title: {
				text: ''
			},
			credits: {
				enabled: false
			},
			pane: {
				startAngle: -150,
				endAngle: 150,
				background: [{
					borderWidth: 0,
					outerRadius: '109%'
				}, {
					borderWidth: 0,
					outerRadius: '107%'
				}, {
				}, {
					backgroundColor: '#fff',
					borderWidth: 0,
					outerRadius: '105%',
					innerRadius: '103%'
				}]
			},

			yAxis: {
				min: 0,
				max: 200,

				minorTickInterval: 'auto',
				minorTickWidth: 1,
				minorTickLength: 10,
				minorTickPosition: 'inside',
				minorTickColor: '#666',

				tickPixelInterval: 30,
				tickWidth: 2,
				tickPosition: 'inside',
				tickLength: 10,
				tickColor: '#666',
				labels: {
					step: 2,
					rotation: 'auto'
				},
				title: {
					text: 'RAM'
				},
				plotBands: [{
					from: 0,
					to: 120,
					color: '#55BF3B'
				}, {
					from: 120,
					to: 160,
					color: '#DDDF0D'
				}, {
					from: 160,
					to: 200,
					color: '#DF5353'
				}]
			},

			series: [{
				name: 'Speed',
				data: [80],
				tooltip: {
					valueSuffix: '%'
				}
			}]
		});
		Highcharts.chart('cpu', {

			chart: {
				type: 'gauge',
				plotBackgroundColor: null,
				plotBackgroundImage: null,
				plotBorderWidth: 0,
				plotShadow: false
			},
			title: {
				text: ''
			},
			credits: {
				enabled: false
			},
			pane: {
				startAngle: -150,
				endAngle: 150,
				background: [{
					borderWidth: 0,
					outerRadius: '109%'
				}, {
					borderWidth: 0,
					outerRadius: '107%'
				}, {
				}, {
					backgroundColor: '#fff',
					borderWidth: 0,
					outerRadius: '105%',
					innerRadius: '103%'
				}]
			},

			yAxis: {
				min: 0,
				max: 200,

				minorTickInterval: 'auto',
				minorTickWidth: 1,
				minorTickLength: 10,
				minorTickPosition: 'inside',
				minorTickColor: '#666',

				tickPixelInterval: 30,
				tickWidth: 2,
				tickPosition: 'inside',
				tickLength: 10,
				tickColor: '#666',
				labels: {
					step: 2,
					rotation: 'auto'
				},
				title: {
					text: 'CPU'
				},
				plotBands: [{
					from: 0,
					to: 120,
					color: '#55BF3B'
				}, {
					from: 120,
					to: 160,
					color: '#DDDF0D'
				}, {
					from: 160,
					to: 200,
					color: '#DF5353'
				}]
			},

			series: [{
				name: 'Speed',
				data: [120],
				tooltip: {
					valueSuffix: ' %'
				}
			}]
		});
	</script>
</body>
</html>
