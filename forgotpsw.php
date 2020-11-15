<?php
    session_start();
    //function to mail the verification link to user
    function resetPsw($email,$password) {
        /*require 'PHPMailerAutoload.php';
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
        //$mail->addCC('cc@example.com');
        //$mail->addBCC('bcc@example.com');
        //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
        //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Reset Password';
        $mail->Body    = '

        Please click this link to reset your account password:
        http://localhost/resetpsw.php?email='.$email.'

        '; // Our message above including the link
        if(!$mail->send()) {
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
           return true;
        }*/
        return true;
    }
    //including dB conn
    include('dBConfig.php');
    //Check whether the submit button is clicked
    if (isset($_POST["submit"]) && !empty($_POST)) {
        //Get all the values from the form
        $email = $_POST['email'];
        //Checks whether the email exists in the login table with status 1
        $sql = 'SELECT password FROM login WHERE email = :email AND status = :status;';
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute(['email' => $email, 'status' => 1]);
        $count = $stmt -> rowCount();
        $row = $stmt -> fetch();
        $password = $row['password'];
        if ($count > 0) {
            //Active User Account exists. Email the user
            if(resetPsw($email,$password))
            {
                //MSG: Check mail
                $_SESSION['succmsg'] = 'We have sent an Password Reset Link.';

            } else {
                //MSG: Oops!
                $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
            }
        } else {
            //invalid email id
            $_SESSION['errmsg'] = 'Please enter your Registred Email id';
        }
    }
?>
<html>
<head>
    <title>Forgot Password</title>
    <style type="text/css">
        body{
            margin: 0;
            padding: 0;
            background: url(pic1.jpg);
            background-size: cover;
            background-position: center;
            font-family: sans-serif;
        }

        .loginbox{
            width: 320px;
            height: 380px;
            background: #000;
            color: #fff;
            top: 50%;
            left: 50%;
            position: absolute;
            transform: translate(-50%,-50%);
            box-sizing: border-box;
            padding: 70px 30px;
        }

        .avatar{
            width: 100px;
            height: 100px;
            border-radius: 50%;
            position: absolute;
            top: -50px;
            left: calc(50% - 50px);
        }

        h1{
            margin: 0;
            padding: 0 0 20px;
            text-align: center;
            font-size: 22px;
        }

        .loginbox p{
            margin: 0;
            padding: 0;
            font-weight: bold;
        }

        .loginbox input{
            width: 100%;
            margin-bottom: 20px;
        }

        .loginbox input[type="text"], input[type="password"]
        {
            border: none;
            border-bottom: 1px solid #fff;
            background: transparent;
            outline: none;
            height: 40px;
            color: #fff;
            font-size: 16px;
        }
        .loginbox input[type="submit"]
        {
            border: none;
            outline: none;
            height: 40px;
            background: #fb2525;
            color: #fff;
            font-size: 18px;
            border-radius: 20px;
        }
        .loginbox input[type="submit"]:hover
        {
            cursor: pointer;
            background: #ffc107;
            color: #000;
        }
        .loginbox a{
            text-decoration: none;
            font-size: 12px;
            line-height: 20px;
            color: darkgrey;
        }

        .loginbox a:hover
        {
            color: #ffc107;
        }
    </style>
<body>
    <div class="loginbox">
    <img src="avatar.png" class="avatar">
        <h1>Forgot Password</h1>
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
        ?><br>
        <form method="post">
            <p>Email</p>
            <input type="text" name="email">
            <input type="submit" name="submit" value="Reset">
        </form>
    </div>
</body>
</head>
</html>
</html>
