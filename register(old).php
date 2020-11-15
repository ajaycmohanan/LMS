<?php
    session_start();
    //including dB conn
    include('dBConfig.php');
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
        require 'PHPMailerAutoload.php';
        $mail = new PHPMailer;
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'info4prjmanager@gmail.com';                 // SMTP username
        $mail->Password = 'Q9qqelin';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->setFrom('info4prjmanager@gmail.com');
        $mail->addAddress($email);     // Add a recipient
        $mail->addReplyTo('info@example.com', 'Information');
        $mail->Subject = 'Signup | Verification';
        $mail->Body    = '
         
        Thanks for joining us! We have so much to share with you. We aim to provide you with the best possible service.
        Your account has been created, you can login with the following credentials after you have activated your account by pressing the url below.
         <br>
        ------------------------<br>
        Username: '.$userid.'<br>
        Password: '.$password.'<br>
        ------------------------<br>
         
        Please click this link to activate your account:
        http://localhost/verify.php?email='.$email.'&hash='.$hash.'&userid='.$userid.'
         
        '; // Our message above including the link
        if(!$mail->send()) {
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
           return true;
        }
    }
    //Check whether the submit button is clicked
    if (isset($_POST["submit"]) && !empty($_POST)) {
        //Get all the values from the form
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phno = $_POST['phno'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $pincode = $_POST['pincode'];
        //Generate Admin ID
        $adminid = random_num(8);
        //Generate a hash
        $hash = getUniqueHash();
        //Get current timestamp
        date_default_timezone_set('Asia/Kolkata');
        $dom = date('Y-m-d H:i:s', time());
        //Create a password
        $password = getUniqueHash(8);
        //creating DATA Array
        $data = [
            'adminid' => $adminid,
            'name' => $name,
            'email' => $email,
            'phno' => $phno,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'hash' => $hash,
            'dom' => $dom
        ];
        //Checks whether the email already exists in the dB
        $sql = 'SELECT email, verify FROM librarian WHERE email = :email;';
        $q = $pdo -> prepare($sql);
        $q -> execute(['email' => $email]);
        $count = $q -> rowCount();
        if($count > 0) {
            //Email already exists in the admin Table
            while($row = $q -> fetch()) {
                $verify = $row['verify'];
                if ($verify == 2) {
                    //MSG: Account already exits.
                    $_SESSION['errmsg'] = "Account already exists!";
                    //Re-Direct: Login Page
                    header("refresh:3;url=http://localhost/login.php");
                } elseif ($verify == 1) {
                    //MSG: Account already exists. Please verify your email
                    $_SESSION['errmsg'] = "Please Verify your Email";
                    //Re-Direct: Home Page
                    header("refresh:3;url=http://localhost/login.php");
                } else {
                    //Email: Verification Link
                    if(mailVerify($email,$adminid,$hash,$password)) {
                        //UPDATE: admin.verify = 1
                        $values = [
                            'value' => 1,
                            'email' => $email
                        ];
                        $sql = "UPDATE librarian SET verify = :value WHERE email = :email";
                        $stmt = $pdo -> prepare($sql);
                        $stmt -> execute($values);
                        $count = $stmt -> rowCount();
                        if($count > 0) {
                            //Data Successfully Updated. Now insert into login Table
                            $data = [
                                'userid' => $userid,
                                'password' => md5($password),
                                'email' => $email
                            ];
                            $sql = 'INSERT INTO login (userid, password, email) VALUES (:userid, :password, :email);';
                            $stmt = $pdo -> prepare($sql);
                            $stmt -> execute($data);
                            $count = $stmt -> rowCount();
                            if ($count > 0) {
                                $_SESSION['succmsg'] = 'Please Verify your Email.';   
                            } else {
                                //DataBase Insertion Failed
                                $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                            }
                        } else {
                            //DataBase Updation Failed
                            $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                        }
                    } else {
                        //Failed to sent Email
                        $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                    }
                }
            }
        } else {
            //Insert data into the admin table
            $sql = 'INSERT INTO librarian (adminid, name, email, phno, address, city, state, pincode, hash, dom) VALUES (:adminid, :name, :email, :phno, :address, :city, :state, :pincode, :hash, :dom);';
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute($data);
            $count = $stmt -> rowCount();
            if($count > 0)  {
                //Data Successfully Inserted. Now send the E-mail
                if(mailVerify($email,$adminid,$hash,$password))  {
                    //UPDATE: admin.verify = 1
                    $values = [
                        'value' => 1,
                        'email' => $email
                    ];
                    $sql = "UPDATE librarian SET verify = :value WHERE email = :email";
                    $stmt = $pdo -> prepare($sql); 
                    $stmt-> execute($values);
                    $res = $stmt -> rowCount();
                    if($count > 0) {
                        //Data Successfully Updated.
                        $data = [
                            'userid' => $adminid,
                            'password' => md5($password),
                            'email' => $email
                        ];
                        $sql = 'INSERT INTO login (userid, password, email) VALUES (:userid, :password, :email);';
                        $stmt = $pdo -> prepare($sql);
                        $stmt -> execute($data);
                        $count = $stmt -> rowCount();
                        if ($count > 0) {
                            $_SESSION['succmsg'] = 'Please Verify your Email.';
                        } else {
                            //DataBase Insertion Failed
                            $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                        }
                    } else  {
                            //DataBase Updation Failed
                            $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                        }
            } else  {
                $_SESSION['errmsg'] = "Connection Problem Occured! Please try again Later";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="bootstrap/css/custom.css">
</head>
<body class="animsition">
        <div class="container" style="margin-top: 100px;">
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <h2><b>Register</b></h2><br>
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
                    <div class="panel-body">
                        <form method="post">
                            <div class="form-group">
                                <input  name="name" class="form-control" placeholder="Name" type="text" required="">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="E-Mail" name="email" required="">
                            </div>

                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="Phone Number" name="phno" required="">
                            </div>

                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="Address" name="address" required="">
                            </div>

                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="City" name="city" required="">
                            </div>

                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="State" name="state" required="">
                            </div>

                            <div class="form-group">
                                <input class="form-control" type="text" placeholder="Pincode" name="pincode" required="">

                            </div>
                            <div class="form-group">
                                <button class="btn btn-success btn-lg btn-block" type="submit" name="submit">Register</button>
                                <a class="btn btn-link" href="login.php">Already Registered!</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>
</body>

</html>