<?php
    session_start();
    /*if (!$_SESSION['logged_in'])
    {
        header('Location: http://localhost/login.php');
    }*/
    //including dB conn
    include('../dBConfig.php');
    $userid = $_SESSION['userid'];
    $sql = 'SELECT * FROM members WHERE mid = :userid';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid]);
    $count = $stmt -> rowCount();
    $row = $stmt -> fetch();
    $name = $row['name'];
    $img = $row['img'];
    $email = $row['email'];
    $phno = $row['phno'];
    $aadhaar = $row['aadhar'];
    $hname = $row['hname'];
    $place = $row['place'];
    $city = $row['city'];
    $state = $row['state'];
    $pincode = $row['pincode'];
    if (isset($_POST["updateinfo"]) && !empty($_POST)) {
        //Get all the values from the form
        $fullname = $_POST['fullname'];
        $ph = $_POST['ph'];
        $aadhaar = $_POST['aadhaar'];
        $citi = $_POST['citi'];
        $place = $_POST['place'];
        $hname = $_POST['hname'];
        $stat = $_POST['stat'];
        $pin = $_POST['pin'];
        $file = $_FILES['file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];
        $fileType = $file['type'];

        $fileExt = explode('.', $fileName);
        $fileActualExt = strtolower(end($fileExt));

        $allowed = array('jpg', 'jpeg', 'png');

        if (in_array($fileActualExt, $allowed) ) {
        	if ($fileError === 0) {
        		if ($fileSize < 1000000) {
        			$fileNameNew = "profile".$userid.".".$fileActualExt;
        			$fileDestination = 'uploads/'.$fileNameNew;
        			if(move_uploaded_file($fileTmpName, $fileDestination)) {
        				$sql = 'UPDATE members SET img = :img WHERE mid = :mid';
        				$stmt = $pdo -> prepare($sql);
        				$stmt -> execute(['img' => 1, 'mid' => $userid]);
        			}
        		} else {
        			echo "Your file is too big!";
        		}
        	} else {
        		echo "There was an error in uploading your file!";
        	}
        } else {
        	echo "You Cannot upload files of this type!";
        }
        $data = [
            'name' => $fullname,
            'phno' => $ph,
            'aadhaar' => $aadhaar,
            'hname' => $hname,
            'place' => $place,
            'city' => $citi,
            'state' => $stat,
            'pincode' => $pin,
            'mid' => $userid
        ];
        //Insert data into the admin table
        $sql = 'UPDATE members SET name = :name, phno = :phno, aadhar = :aadhaar, hname = :hname, place = :place, city = :city, state = :state, pincode = :pincode WHERE mid = :mid';
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute($data);
        header("location: http://localhost/user-master/profile.php");
    }
    if (isset($_POST["btnPsw"]) && !empty($_POST)) {
    	$psw = md5($_POST["password"]);
    	$sql = "UPDATE login SET password = :password WHERE userid = :userid";
		$stmt = $pdo -> prepare($sql);
		$stmt -> execute(['password' => $psw, 'userid' => $userid]);
    }

    require ('vendors/autoload.php');
    $bar = new Picqer\Barcode\BarcodeGeneratorHTML();
    $code = $bar -> getBarcode($userid, $bar::TYPE_CODE_128);
?>
<!DOCTYPE html>
<html>
<head>
	<?php include('include/head.php'); ?>
	<link rel="stylesheet" type="text/css" href="src/plugins/cropperjs/dist/cropper.css">
	<script type="text/javascript">
		var check = function() {
			if (document.getElementById('password').value == document.getElementById('C_password').value) {
				document.getElementById('msg').style.color = 'green';
				document.getElementById('msg').innerHTML = "";
				document.getElementById('btnPsw').disabled = false;
			} else {
				document.getElementById('msg').style.color = 'red';
				document.getElementById('msg').innerHTML = "Passwords not matching";
				document.getElementById('btnPsw').disabled = true;
			}
		}
	</script>
</head>
<body>
	<?php include('include/header.php'); ?>
	<?php include('include/sidebar.php'); ?>
	<div class="main-container">
		<div class="pd-ltr-20 customscroll customscroll-10-p height-100-p xs-pd-20-10">
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="title">
								<h4>Profile</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.php">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">Profile</li>
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
							<h5 class="text-center"><?php echo $name; ?></h5>
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
										<?php echo $hname; echo ",".$city; ?><br>
										<?php echo $state; echo ",".$pincode; ?>
									</li>
								</ul>
							</div>
							<div class="profile-social">
								<h5 class="mb-20 weight-500">Bar Code</h5>
								<?php echo $code; ?>
							</div>
						</div>
					</div>
					<div class="col-xl-9 col-lg-8 col-md-8 col-sm-12 mb-30">
						<div class="bg-white border-radius-4 box-shadow height-100-p">
							<div class="profile-tab height-100-p">
								<div class="tab height-100-p">
									<ul class="nav nav-tabs customtab" role="tablist">
										<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#timeline" role="tab">Settings</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#setting" role="tab">Change Password</a>
										</li>
									</ul>
									<div class="tab-content">
										<!-- Timeline Tab start -->
										<div class="tab-pane fade show active" id="timeline" role="tabpanel">
											<div class="profile-setting">
												<form method="post" enctype="multipart/form-data">
													<ul class="profile-edit-list row">
														<li class="weight-500 col-md-6">
															<h4 class="text-blue mb-20">Edit Your Personal Setting</h4>
															<div class="form-group">
																<label>Registration Id</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $userid; ?>" disabled>
															</div>
															<div class="form-group">
																<label>Email</label>
																<input class="form-control form-control-lg" type="email" value="<?php echo $email; ?>" disabled>
															</div>
															<div class="form-group">
																<label>Full Name</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $name; ?>" name="fullname" >
															</div>
															<div class="form-group">
																<label>Phone Number</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $phno; ?>" name="ph">
															</div>
															<div class="form-group">
																<label>Aadhaar</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $aadhaar; ?>" name="aadhaar" >
															</div>
															<div class="form-group">
																<label>House Name/No.</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $hname; ?>" name="hname">
															</div>
														</li>
														<li class="weight-500 col-md-6">
															<br><br>
															<div class="form-group">
																<label>Place</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $place; ?>" name="place">
															</div>
															<div class="form-group">
																<label>City</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $city; ?>" name="citi">
															</div>
															<div class="form-group">
																<label>State</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $state; ?>" name="stat">
															</div>
															<div class="form-group">
																<label>Pincode</label>
																<input class="form-control form-control-lg" type="text" value="<?php echo $pincode; ?>" name="pin">
															</div>
															<div class="form-group">
																<label>Profile Photo</label>
																<input type="file" name="file" class="form-control form-control-lg">
															</div><br>
															<div class="form-group mb-0">
																<button class="btn btn-info btn-lg btn-block" type="submit" name="updateinfo">Update Information</button>
															</div>
													</ul>
												</form>
											</div>
										</div>
										<div class="tab-pane fade height-100-p" id="setting" role="tabpanel">
											<div class="profile-setting">
												<form method="post">
													<ul class="profile-edit-list row">
														<li class="weight-500 col-md-6">
															<h4 class="text-blue mb-20">Change Password</h4>
															<div class="form-group">
																<label>New Password</label>
																<input class="form-control form-control-lg" type="password" name="password" id="password" onkeyup="check();">
															</div>
															<div class="form-group">
																<label>Re-enter Password</label>
																<input class="form-control form-control-lg" type="password" name="C_password" id="C_password" onkeyup="check();">
															</div>
															<div><span id="msg"></span></div>
											<div class="form-group mb-0">
												<button class="btn btn-info btn-lg btn-block" type="submit" name="btnPsw" id="btnPsw">Update Password</button>
											</div>
										</li>
									</ul>
								</form>
										</div>
										<!-- Setting Tab End -->
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
	<script src="src/plugins/cropperjs/dist/cropper.js"></script>
	<script>
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
