<?php
    session_start();
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    //Load Composer's autoloader
    require 'vendor/autoload.php';
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
    function mailVerify($email,$userid,$hash) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'info4prjmanager@gmail.com';                 // SMTP username
        $mail->Password = 'Q9qqelin';                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                    // TCP port to connect to

        $mail->setFrom('info4prjmanager@gmail.com');
        $mail->addAddress($email);     // Add a recipient
        $mail->Subject = 'Signup | Verification';
        $mail->Body    = '

        Thanks for joining us! We have so much to share with you. We aim to provide you with the best possible service.
        Your account has been created with the Registration Id '.$userid.'. Please activate your account by pressing the url below.

        Please click this link to activate your account:
        http://pusthakangal.com/verify.php?email='.$email.'&hash='.$hash.'&userid='.$userid.'

        '; // Our message above including the link
        if(!$mail->send()) {
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } else {
           return true;
        }
    }
    //Check whether the submit button is clicked
    if (isset($_POST["register"]) && !empty($_POST)) {
        //Get all the values from the form
        $name = $_POST['name'];
        $email = $_POST['email'];
        //Generate Admin ID
        $adminid = random_num(8);
        //Generate a hash
        $hash = getUniqueHash();
        //Get current timestamp
        date_default_timezone_set('Asia/Kolkata');
        $dom = date('Y-m-d H:i:s', time());
        //Create a password
        //$password = getUniqueHash(8);
        //creating DATA Array
        $data = [
            'adminid' => $adminid,
            'name' => $name,
            'email' => $email,
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
                    header("refresh:3;url=http://localhost/");
                } else {
                    //Email: Verification Link
                    if(mailVerify($email,$adminid,$hash)) {
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
                            /*$data = [
                                'userid' => $userid,
                                'password' => md5($password),
                                'email' => $email
                            ];
                            $sql = 'INSERT INTO login (userid, password, email) VALUES (:userid, :password, :email);';
                            $stmt = $pdo -> prepare($sql);
                            $stmt -> execute($data);
                            $count = $stmt -> rowCount();
                            if ($count > 0) {*/
                                $_SESSION['succmsg'] = 'Please Verify your Email.';
                                //Re-Direct: Home Page
                                header("refresh:3;url=http://localhost/");
                            } else {
                                //DataBase Insertion Failed
                                $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                            }
                        /*} else {
                            //DataBase Updation Failed
                            $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                        }*/
                    } else {
                        //Failed to sent Email
                        $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                    }
                }
            }
        } else {
            //Insert data into the admin table
            $sql = 'INSERT INTO librarian (adminid, name, email, hash, dom) VALUES (:adminid, :name, :email, :hash, :dom);';
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute($data);
            $count = $stmt -> rowCount();
            if($count > 0)  {
                //Data Successfully Inserted. Now send the E-mail
                if(mailVerify($email,$adminid,$hash))  {
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
                        /*$data = [
                            'userid' => $adminid,
                            'password' => md5($password),
                            'email' => $email
                        ];
                        $sql = 'INSERT INTO login (userid, password, email) VALUES (:userid, :password, :email);';
                        $stmt = $pdo -> prepare($sql);
                        $stmt -> execute($data);
                        $count = $stmt -> rowCount();
                        if ($count > 0) {*/
                            $_SESSION['succmsg'] = 'Please Verify your Email.';
                            //Re-Direct: Home Page
                            header("refresh:3;url=http://localhost/");
                        } else {
                            //DataBase Insertion Failed
                            $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                        }
                   /* } else  {
                            //DataBase Updation Failed
                            $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
                        }*/
            } else  {
                $_SESSION['errmsg'] = "Connection Problem Occured! Please try again Later";
            }
        }
    }
}
?>
<html>
<head>
    <title>Register Form</title>
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
            height: 420px;
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
        <h1>Register Here</h1>
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
            <p>Name</p>
            <input type="text" name="name">
            <p>Email</p>
            <input type="text" name="email">
            <input type="submit" name="register" value="Register">
            <a href="login.php">Already have an account?</a>
        </form>
    </div>
</body>
</head>
</html>
</html>
