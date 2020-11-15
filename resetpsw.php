<?php 
    session_start();
    include('dBConfig.php');
    if(isset($_GET['email']) && !empty($_GET['email'])) {
        // Verify data
        $email = $_GET['email']; // Set email variable
        $sql = 'SELECT email, password FROM login WHERE email = :email;';
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute(['email' => $email]);
        $count = $stmt -> rowCount();
        if ($count > 0) {
            //Valid Account Exists
            ?>
<html>
<head>
    <title>Forgot Password</title>
    <script type="text/javascript">
        var check = function() {
            if (document.getElementById('password').value == document.getElementById('C_password').value) {
                document.getElementById('msg').style.color = 'green';
                document.getElementById('msg').innerHTML = "";
            } else {
                document.getElementById('msg').style.color = 'red';
                document.getElementById('msg').innerHTML = "Passwords not matching";
            }
        }
    </script>
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
            <p>New Password</p>
            <input type="password" name="password" id="password" onkeyup="check();">
            <p>Confirm Password</p>
            <input type="password" name="C_password" id="C_password" onkeyup="check();">
            <div class="form-group">
                <div>
                    <span id="msg"></span>
                </div>
            </div><br>
            <input type="submit" name="submit" value="Reset">
        </form>
    </div>
</body>
</head>
</html>
</html>
        <?php
        } else { 
            $_SESSION['errmsg'] = 'Invalid Approach!';
            header("refresh:0;url=http://localhost/login.php");
        }
    }
    if (isset($_POST["submit"]) && !empty($_POST)) {
        $password = md5($_POST['password']);
        $sql = "UPDATE login SET password = :password WHERE email = :email";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute(['password' => $password, 'email' => $email]);
        $res = $stmt -> rowCount();
        if ($res > 0) {
            header("refresh:0;url=http://localhost/login.php");
        } else
        {
            $_SESSION['errmsg'] = 'Oops..Something Happened! Please try again Later!';
        }
    }
?>