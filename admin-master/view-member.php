<?php
    session_start();
    /*if (!$_SESSION['logged_in'])
    {
        header('Location: http://localhost/login.php');
    }*/
    //including dB conn
    include('../dBConfig.php');
    $userid = $_SESSION['userid'];
    $sql = 'SELECT name FROM librarian WHERE adminid = :userid';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid]);
    $count = $stmt -> rowCount();
    $row = $stmt -> fetch();
    $name = $row['name'];


    $userids = $_GET['userid'];
    $sql = 'SELECT * FROM members WHERE mid = :userid';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userids]);
    $row = $stmt -> fetch();
    $names = $row['name'];
    $img = $row['img'];
    $email = $row['email'];
    $phno = $row['phno'];
    $address = $row['hname'];
    $city = $row['city'];
    $state = $row['state'];
    $pincode = $row['pincode'];
    $status = $row['status'];
    require ('vendors/autoload.php');

    $bar = new Picqer\Barcode\BarcodeGeneratorHTML();
    $code = $bar -> getBarcode($userid, $bar::TYPE_CODE_128);
?>
<!DOCTYPE html>
<html>
<head>
	<?php include('include/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="src/plugins/cropperjs/dist/cropper.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/media/css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/media/css/dataTables.bootstrap4.css">
	<link rel="stylesheet" type="text/css" href="src/plugins/datatables/media/css/responsive.dataTables.css">
</head>
<body>
	<?php include('include/header.php'); ?>
	<?php include('include/sidebar.php'); ?>
	<div class="main-container">
		<div class="pd-ltr-20 customscroll customscroll-10-p height-100-p xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Manage Members</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.php">Home</a></li>
									<li class="breadcrumb-item"><a href="index.php">Members</a></li>
									<li class="breadcrumb-item active" aria-current="page">View</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xl-3 col-lg-4 col-md-4 col-sm-12 mb-30">
						<div class="pd-20 bg-white border-radius-4 box-shadow">
							<div class="profile-photo">
								<?php
									if ($img == 0) {
										echo "<img src='uploads/default.jpg' alt='' class='avatar-photo'>";
									} else {
										echo "<img src='uploads/profile".$userid.".jpg' alt='' class='avatar-photo'>";
									}
								?>
							</div>
							<h5 class="text-center"><?php echo $names; ?></h5>
							<div class="profile-info">
								<h5 class="mb-20 weight-500">Contact Information</h5>
								<ul>
									<li>
										<span>Email Address:</span>
										<?php echo $email; ?>
									</li>
									<li>
										<span>Phone Number:</span>
										<?php echo $phno; ?>
									</li>
									<li>
										<span>Country:</span>
										India
									</li>
									<li>
										<span>Address:</span>
										<?php echo $address; echo ",".$city; ?><br>
										<?php echo $state; echo ",".$pincode; ?>
									</li>
								</ul>
							</div>
							<div class="profile-social">
								<h5 class="mb-20 weight-500">Bar Code</h5>
								<?php echo $code; ?>
							</div>
							<!--<div class="profile-skills">
								<h5 class="mb-20 weight-500">Key Skills</h5>
								<h6 class="mb-5">HTML</h6>
								<div class="progress mb-20" style="height: 6px;">
									<div class="progress-bar" role="progressbar" style="width: 90%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
								<h6 class="mb-5">Css</h6>
								<div class="progress mb-20" style="height: 6px;">
									<div class="progress-bar" role="progressbar" style="width: 70%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
								<h6 class="mb-5">jQuery</h6>
								<div class="progress mb-20" style="height: 6px;">
									<div class="progress-bar" role="progressbar" style="width: 60%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
								<h6 class="mb-5">Bootstrap</h6>
								<div class="progress mb-20" style="height: 6px;">
									<div class="progress-bar" role="progressbar" style="width: 80%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
								</div>
							</div>-->
						</div>
					</div>
					<div class="col-xl-9 col-lg-8 col-md-8 col-sm-12 mb-30">
						<div class="bg-white border-radius-4 box-shadow height-100-p">
							<div class="profile-tab height-100-p">
								<div class="tab height-100-p">
									<ul class="nav nav-tabs customtab" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#timeline" role="tab">Account Info</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#transactions" role="tab">Transactions</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#loan" role="tab">On Loan</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#dues" role="tab">Dues</a>
										</li>
									</ul>
									<div class="tab-content">
										<!-- Timeline Tab start -->
										<div class="tab-pane fade show active" id="timeline" role="tabpanel">
											<div class="profile-setting">
												<form method="post">
													<ul class="profile-edit-list row">
														<li class="weight-500 col-md-6">
															<div class="form-group">
																<label>Registration Id</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $userids; ?>" disabled>
															</div>
															<div class="form-group">
																<label>Email</label>
																<input class="form-control form-control-lg" type="email" value="<?php echo $email; ?>" disabled>
															</div>
															<div class="form-group">
																<label>Full Name</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $names; ?>" name="fullname" disabled>
															</div>
															<div class="form-group">
																<label>Phone Number</label>
																<input class="form-control form-control-lg " type="text" value="<?php echo $phno; ?>" name="ph" disabled>
															</div>
															<!--<div class="form-group">
																<label>Gender</label>
																<div class="d-flex">
																<div class="custom-control custom-radio mb-5 mr-20">
																	<input type="radio" id="customRadio4" name="customRadio" class="custom-control-input">
																	<label class="custom-control-label weight-400" for="customRadio4">Male</label>
																</div>
																<div class="custom-control custom-radio mb-5">
																	<input type="radio" id="customRadio5" name="customRadio" class="custom-control-input">
																	<label class="custom-control-label weight-400" for="customRadio5">Female</label>
																</div>
																</div>
															</div>
															<div class="form-group">
																<label>Country</label>
																<select class="selectpicker form-control form-control-lg" data-style="btn-outline-secondary btn-lg" title="Not Chosen">
																	<option>United States</option>
																	<option>India</option>
																	<option>United Kingdom</option>
																</select>
															</div>
															<div class="form-group">
																<label>State/Province/Region</label>
																<input class="form-control form-control-lg" type="text">
															</div>
															<div class="form-group">
																<label>Postal Code</label>
																<input class="form-control form-control-lg" type="text">
															</div>
															<div class="form-group">
																<label>Phone Number</label>
																<input class="form-control form-control-lg" type="text">
															</div>
															<div class="form-group">
																<label>Address</label>
																<textarea class="form-control"></textarea>
															</div>-->
														</li>
														<li class="weight-500 col-md-6">
															<div class="form-group">
																<label>Address</label>
																<input class="form-control form-control-lg " type="text" value="<?php echo $address; ?>" name="add" disabled>
															</div>
															<div class="form-group">
																<label>City</label>
																<input class="form-control form-control-lg " type="text" value="<?php echo $city; ?>" name="citi" disabled>
															</div>
															<div class="form-group">
																<label>State</label>
																<input class="form-control form-control-lg " type="text" value="<?php echo $state; ?>" name="stat" disabled>
															</div>
															<div class="form-group">
																<label>Pincode</label>
																<input class="form-control form-control-lg " type="text" value="<?php echo $pincode; ?>" name="pin" disabled>
															</div>
															<!--<div class="form-group">
																<label>Dribbble URL:</label>
																<input class="form-control form-control-lg" type="text" placeholder="Paste your link here">
															</div>
															<div class="form-group">
																<label>Dropbox URL:</label>
																<input class="form-control form-control-lg" type="text" placeholder="Paste your link here">
															</div>
															<div class="form-group">
																<label>Google-plus URL:</label>
																<input class="form-control form-control-lg" type="text" placeholder="Paste your link here">
															</div>
															<div class="form-group">
																<label>Pinterest URL:</label>
																<input class="form-control form-control-lg" type="text" placeholder="Paste your link here">
															</div>
															<div class="form-group">
																<label>Skype URL:</label>
																<input class="form-control form-control-lg" type="text" placeholder="Paste your link here">
															</div>
															<div class="form-group">
																<label>Vine URL:</label>
																<input class="form-control form-control-lg" type="text" placeholder="Paste your link here">
															</div>
															<div class="form-group mb-0">
																<input type="submit" class="btn btn-primary" value="Save & Update">
															</div>
														</li>-->
													</ul>
												</form>
											</div>
										</div>

										<!-- Transactions Tab Start -->
										<div class="tab-pane fade height-100-p" id="transactions" role="tabpanel">
											<div class="profile-setting">
												<div class="pd-20 bg-white border-radius-4 box-shadow mb-30">
													<div class="row">
														<table class="stripe hover multiple-select-row data-table-export nowrap" id="user_data">
														<thead>
															<tr>
																<th>Sl No.</th>
																<th>Book Name</th>
																<th class="table-plus datatable-nosort">Issue Date</th>
																<th class="table-plus datatable-nosort">Returned Date</th>
															</tr>
														</thead>
														<tbody>
															<?php
																/*$sql = 'SELECT * FROM tranc WHERE userid = :userid';
																$stmt = $pdo -> prepare($sql);
																$stmt -> execute(['userid' => $userids]);
																$i=1;
																while ( $rows = $stmt -> fetch(PDO::FETCH_ASSOC)) {
																	$sql = 'SELECT bookname FROM books WHERE bookid = :bookid';
																	$stmt = $pdo -> prepare($sql);
																	$stmt -> execute(['bookid' => $rows['bookid']]);
																	$result = $stmt -> fetch();
																	echo '
																	    <tr>
																	    	<td>'.$i.'</td>
																	        <td>'.$result['bookname'].'</td>
																	        <td>'.$rows['issuedate'].'</td>
																	        <td>'.$rows['returned'].'</td>
																	    </tr>
																	';
																	$i++;
																};*/
							                        		?>
														</tbody>
													</table>
												</div>
											</div>
											</div>
										</div>

										<div class="tab-pane fade height-100-p" id="loan" role="tabpanel">
											<div class="profile-setting">
												<div class="pd-20 bg-white border-radius-4 box-shadow mb-30">
													<div class="row">
														<table class="stripe hover multiple-select-row data-table-export nowrap">
														<thead>
															<tr>
																<th>Sl No.</th>
																<th>Book Name</th>
																<th class="table-plus datatable-nosort">Issue Date</th>
																<th class="table-plus datatable-nosort">Return Date</th>
															</tr>
														</thead>
														<tbody>
															<?php
																/*$x=1;
																$sql = 'SELECT * FROM tranc WHERE mid = :userid AND status = :status';
																$stmt = $pdo -> prepare($sql);
																$stmt -> execute(['userid' => $userids, 'status' => 0]);
																while ( $rows = $stmt -> fetch(PDO::FETCH_ASSOC)) {
																	$sql = 'SELECT bookname FROM books WHERE bookid = :bookid';
																	$stmt = $pdo -> prepare($sql);
																	$stmt -> execute(['bookid' => $rows['id']]);
																	$result = $stmt -> fetch();
																	echo '
																	    <tr>
																	    	<td>'.$x.'</td>
																	        <td>'.$result['bookname'].'</td>
																	        <td>'.$rows['bdate'].'</td>
																	        <td>'.$rows['rdate'].'</td>
																	    </tr>
																	';
																	$x++;
																};*/
							                        		?>
														</tbody>
													</table>
												</div>
											</div>
											</div>
										</div>

										<div class="tab-pane fade height-100-p" id="dues" role="tabpanel">
											<div class="profile-setting">
												<div class="pd-20 bg-white border-radius-4 box-shadow mb-30">
													<div class="row">
														<table class="stripe hover multiple-select-row data-table-export nowrap">
														<thead>
															<tr>
																<th>Sl No.</th>
																<th>Book Name</th>
																<th class="table-plus datatable-nosort">Issue Date</th>
																<th class="table-plus datatable-nosort">Return Date</th>
																<th class="table-plus datatable-nosort">Fine</th>
															</tr>
														</thead>
														<tbody>
															<?php
																/*$y=1;
																$sql = 'SELECT * FROM tranc WHERE mid = :userid AND fine != :fine';
																$stmt = $pdo -> prepare($sql);
																$stmt -> execute(['userid' => $userids, 'fine' => 0]);
																while ( $rows = $stmt -> fetch(PDO::FETCH_ASSOC)) {
																	$sql = 'SELECT bookname FROM books WHERE bookid = :bookid';
																	$stmt = $pdo -> prepare($sql);
																	$stmt -> execute(['bookid' => $rows['id']]);
																	$result = $stmt -> fetch();
																	echo '
																	    <tr>
																	    	<td>'.$y.'</td>
																	        <td>'.$result['bookname'].'</td>
																	        <td>'.$rows['bdate'].'</td>
																	        <td>'.$rows['rdate'].'</td>
																	        <td>'.$rows['fine'].'</td>
																	    </tr>
																	';
																	$y++;
																};*/
							                        		?>
														</tbody>
													</table>
												</div>
											</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php include('include/script.php'); ?>
	<script src="src/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
	<script src="src/plugins/datatables/media/js/dataTables.bootstrap4.js"></script>
	<script src="src/plugins/datatables/media/js/dataTables.responsive.js"></script>
	<script src="src/plugins/datatables/media/js/responsive.bootstrap4.js"></script>
	<!-- buttons for Export datatable -->
	<script src="src/plugins/datatables/media/js/button/dataTables.buttons.js"></script>
	<script src="src/plugins/datatables/media/js/button/buttons.bootstrap4.js"></script>
	<script src="src/plugins/datatables/media/js/button/buttons.print.js"></script>
	<script src="src/plugins/datatables/media/js/button/buttons.html5.js"></script>
	<script src="src/plugins/datatables/media/js/button/buttons.flash.js"></script>
	<script src="src/plugins/datatables/media/js/button/pdfmake.min.js"></script>
	<script src="src/plugins/datatables/media/js/button/vfs_fonts.js"></script>
	<script>
	<script src="src/plugins/cropperjs/dist/cropper.js"></script>
	<script>
		$('document').ready(function(){
			$('.data-table').DataTable({
				scrollCollapse: true,
				autoWidth: false,
				responsive: true,
				columnDefs: [{
					targets: "datatable-nosort",
					orderable: false,
				}],
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				"language": {
					"info": "_START_-_END_ of _TOTAL_ entries",
					searchPlaceholder: "Search"
				},
			});
			$('.data-table-export').DataTable({
				scrollCollapse: true,
				autoWidth: false,
				responsive: true,
				columnDefs: [{
					targets: "datatable-nosort",
					orderable: false,
				}],
				"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
				"language": {
					"info": "_START_-_END_ of _TOTAL_ entries",
					searchPlaceholder: "Search"
				},
				dom: 'Bfrtip',
				buttons: [
				'copy', 'csv', 'pdf', 'print'
				]
			});
			var table = $('.select-row').DataTable();
			$('.select-row tbody').on('click', 'tr', function () {
				if ($(this).hasClass('selected')) {
					$(this).removeClass('selected');
				}
				else {
					table.$('tr.selected').removeClass('selected');
					$(this).addClass('selected');
				}
			});
			var multipletable = $('.multiple-select-row').DataTable();
			$('.multiple-select-row tbody').on('click', 'tr', function () {
				$(this).toggleClass('selected');
			});
		});
		window.addEventListener('DOMContentLoaded', function () {
			var image = document.getElementById('image');
			var cropBoxData;
			var canvasData;
			var cropper;

			$('#modal').on('shown.bs.modal', function () {
				cropper = new Cropper(image, {
					autoCropArea: 0.5,
					dragMode: 'move',
					aspectRatio: 3 / 3,
					restore: false,
					guides: false,
					center: false,
					highlight: false,
					cropBoxMovable: false,
					cropBoxResizable: false,
					toggleDragModeOnDblclick: false,
					ready: function () {
						cropper.setCropBoxData(cropBoxData).setCanvasData(canvasData);
					}
				});
			}).on('hidden.bs.modal', function () {
				cropBoxData = cropper.getCropBoxData();
				canvasData = cropper.getCanvasData();
				cropper.destroy();
			});
		});
	</script>
</body>
</html>
