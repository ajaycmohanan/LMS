<?php
    session_start();
    /*if (!$_SESSION['logged_in'])
    {
        header('Location: http://localhost/login.php');
    }*/
    //including dB conn
    include('../dBConfig.php');
    //function to Generate Random String like XX00000000
    function random_num($size) {
        $alpha_key = '';
        $keys = range('A', 'Z');

        for ($i = 0; $i < 2; $i++) {
            $alpha_key .= $keys[array_rand($keys)];
        }

        $length = $size - 2;

        $key = '';
        $keys = range(0, 9);

        for ($i = 0; $i < $length; $i++) {
            $key .= $keys[array_rand($keys)];
        }

        return $alpha_key . $key;
    }
    //function to Generate hash String
    function getUniqueHash($length = 32) {
        $token = "";
        $combinationString = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for ($i=0; $i < $length; $i++) {
            $token .= $combinationString[rand(0, strlen($combinationString)-1)];
        }
        return $token;
    }
    //function to mail the verification link to user
    function mailVerify($email,$userid,$hash,$password) {
        /*require '../PHPMailerAutoload.php';
        $mail = new PHPMailer;
        //$mail->SMTPDebug = 4;                               // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'info4prjmanager@gmail.com';                 // SMTP username
        $mail->Password = 'Q9qqelin';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->setFrom('info4prjmanager@gmail.com');
        $mail->addAddress($email);     // Add a recipient
        //$mail->addAddress('ellen@example.com');               // Name is optional
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Membership | Verification';
        $mail->Body    = '

        Thanks for joining us! We have so much to share with you. We aim to provide you with the best possible service.
        Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
         <br>
        ------------------------<br>
        Username: '.$userid.'<br>
        Password: '.$password.'<br>
        ------------------------<br>

        Please click this link to activate your account:
        http://localhost/verifymember.php?email='.$email.'&hash='.$hash.'&userid='.$userid.'

        '; // Our message above including the link
        if(!$mail->send()) {
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
           return true;
        }*/
        return true;
    }
    $userid = $_SESSION['userid'];
    $sql = 'SELECT name FROM librarian WHERE adminid = :userid';
    $stmt = $pdo -> prepare($sql);
    $stmt -> execute(['userid' => $userid]);
    $count = $stmt -> rowCount();
    $row = $stmt -> fetch();
    $name = $row['name'];
    if (isset($_POST["membership"]) && !empty($_POST)) {
        //Get all the values from the form

    	$mid = random_num(8);
    	$adminid = $userid;
    	$name = $_POST['name'];
    	$email = $_POST['email'];
    	$phno = $_POST['phonenumber'];
    	$aadhar = $_POST['aadhar'];
    	$hname = $_POST['housename'];
    	$place = $_POST['place'];
    	$city = $_POST['city'];
    	$state = $_POST['state'];
    	$pincode = $_POST['pincode'];
    	$hash = getUniqueHash();
    	$password = getUniqueHash(8);
    	date_default_timezone_set('Asia/Kolkata');
        $dom = date('Y-m-d H:i:s', time());
        $data = [
        	'mid' => $mid,
            'adminid' => $adminid,
            'name' => $name,
            'email' => $email,
            'phno' => $phno,
            'aadhar' => $aadhar,
            'hname' => $hname,
            'place' => $place,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'hash' => $hash,
            'password' => $password,
            'dom' => $dom
        ];
        //Checks whether the email already exists in the dB
        $sql = 'SELECT email, verify FROM members WHERE email = :email;';
        $q = $pdo -> prepare($sql);
        $q -> execute(['email' => $email]);
        $count = $q -> rowCount();
        if($count > 0) {
        	//Account Alredy Exists
        	while($row = $q -> fetch()) {
                $verify = $row['verify'];
                if ($verify == 2) {
                    //MSG: Account already exits.
                    $_SESSION['errmsg'] = "Account already exists!";
                } elseif ($verify == 1) {
                    //MSG: Account already exists. Please verify your email
                    $_SESSION['errmsg'] = "Account Already Exists! Email not Verified.";
                } else {
                    //Email: Verification Link
                    if(mailVerify($email,$mid,$hash,$password)) {
                        //UPDATE: admin.verify = 1
                        $values = [
                            'value' => 1,
                            'email' => $email
                        ];
                        $sql = "UPDATE members SET verify = :value WHERE email = :email";
                        $stmt = $pdo -> prepare($sql);
                        $stmt -> execute($values);
                        $count = $stmt -> rowCount();

                    } else {
                        //Failed to sent Email
                        $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                    }
                }
            }
        } else {
        	//Insert data into the members table
        	$sql = 'INSERT INTO members (mid, adminid, name, email, phno, aadhar, hname, place, city, state, pincode, hash, password, dom) VALUES (:mid, :adminid, :name, :email, :phno, :aadhar, :hname, :place, :city, :state, :pincode, :hash, :password, :dom);';
        	$stmt = $pdo -> prepare($sql);
        	$stmt -> execute($data);
        	$count = $stmt -> rowCount();
        	if($count > 0)  {
        	    //Data Successfully Inserted. Now send the E-mail
        	    if(mailVerify($email,$mid,$hash,$password))  {
        	        //UPDATE: admin.verify = 1
        	        $values = [
        	            'value' => 1,
        	            'email' => $email
        	        ];
        	        $sql = "UPDATE members SET verify = :value WHERE email = :email";
        	        $stmt = $pdo -> prepare($sql);
        	        $stmt-> execute($values);
        	        $count = $stmt -> rowCount();
        	        if ($count > 0) {
        	            $_SESSION['succmsg'] = 'New Account has been created.';
        	        } else {
        	            //DataBase Insertion Failed
        	            $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
        	        }
        	    } else  {
                	$_SESSION['errmsg'] = "Connection Problem Occured! Please try again Later";
            	}
        	} else  {
                $_SESSION['errmsg'] = "Connection Problem Occured! Please try again Later";
            }
        }
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
			<div class="min-height-200px">
				<div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.php">Home</a></li>
									<li class="breadcrumb-item"><a href="index.php">Books</a></li>
									<li class="breadcrumb-item active" aria-current="page">New</li>
								</ol>
							</nav>
						</div>
					</div>
				</div>
				<!-- Default Basic Forms Start -->
				<div class="pd-20 bg-white border-radius-4 box-shadow mb-30">
					<div class="clearfix">
						<div class="pull-left">
							<h4 class="text-blue">New Book</h4><br>
						</div>
						<div>
							<?php
							    if (!empty($_SESSION['errmsg'])) {
							        echo '<div class="alert alert-danger alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
							        echo $_SESSION['errmsg'];
							        echo '</div>';
							        unset($_SESSION['errmsg']);
							    }
							    if (!empty($_SESSION['succmsg'])) {
							        echo '<div class="alert alert-success alert-dismissible"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
							        echo $_SESSION['succmsg'];
							        echo '</div>';
							        unset($_SESSION['succmsg']);
							    }
							?>
						</div>
					</div>
					<form method="post">
						<div class="form-group row">
							<label class="col-sm-12 col-md-2 col-form-label">Book Name</label>
							<div class="col-sm-12 col-md-10">
								<input class="form-control" type="text" name="name" required="">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-12 col-md-2 col-form-label">Author(s)</label>
							<div class="col-sm-12 col-md-10">
								<input class="form-control" type="text" name="authors" required="">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-12 col-md-2 col-form-label">Publisher</label>
							<div class="col-sm-12 col-md-10">
								<input class="form-control" type="text" name="publisher" required="">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-12 col-md-2 col-form-label">Aadhar</label>
							<div class="col-sm-12 col-md-10">
								<input class="form-control" type="text" name="aadhar" required="">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-12 col-md-2 col-form-label">House Name/No</label>
							<div class="col-sm-12 col-md-10">
								<input class="form-control" type="text" name="housename" required="">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-12 col-md-2 col-form-label">Place</label>
							<div class="col-sm-12 col-md-10">
								<input class="form-control" type="text" name="place" required="">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-12 col-md-2 col-form-label">City</label>
							<div class="col-sm-12 col-md-10">
								<input class="form-control" type="text" name="city" required="">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-12 col-md-2 col-form-label">State</label>
							<div class="col-sm-12 col-md-10">
								<input class="form-control" type="text" name="state" required="">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-12 col-md-2 col-form-label">Pincode</label>
							<div class="col-sm-12 col-md-10">
								<input class="form-control" type="text" name="pincode" required="">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12 col-md-10">
								<button class="btn btn-info btn-lg" type="submit" name="membership">Add Member</button>
							</div>
						</div>
					</form>
			<?php //include('include/footer.php'); ?>
		</div>
	</div>
	<?php include('include/script.php'); ?>
</body>
</html>
