<?php
    session_start();
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
    //including dB conn
    include('dBConfig.php');
   if (isset($_POST["login"]) && !empty($_POST)) {
        //Get all the values from the form
        $userid = $_POST['username'];
        $password = md5($_POST['password']);
        //Checks whether the user with giver id, password and status =1 exists
        $data = [
            'userid' => $userid,
            'password' => $password,
            'status' => 1
        ];
        $sql = 'SELECT accnttype FROM login WHERE userid = :userid AND password = :password AND status = :status';
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute($data);
        $count = $stmt -> rowCount();
        $row = $stmt -> fetch();
        $accnttype = $row['accnttype'];
        if ($count > 0) {
            //Set SESSION Variables
            $_SESSION['userid'] = $userid;
            $_SESSION['accnttype'] = $accnttype;
            $_SESSION['logged_in'] = true;
            //dBInsert : register
            $token = random_num(8);
            $_SESSION['token'] = $token;
            date_default_timezone_set('Asia/Kolkata');
            $timein = date('Y-m-d H:i:s', time());
            $values = [
                'token' => $token,
                'userid' => $userid,
                'timein' => $timein
            ];
            $sql = 'INSERT INTO register (token, userid, timein) VALUES (:token, :userid, :timein);';
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute($values);
            $count = $stmt -> rowCount();
            if ($count > 0) {
                if ($accnttype == 1) {
                    //Re-Direct: Adminpanel
                    header("location: http://localhost/admin-master/");
                } else {
                    //Re-Direct: member panel
                    header("location: http://localhost/user-master/");
                }
            }
        } else {
            $_SESSION['errmsg'] = 'Invalid Credentials';
        }
    }
?>
<html>
<head>
    <title>Login Form</title>
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
        <h1>Login Here</h1>
        <form method="post">
            <p>Username</p>
            <input type="text" name="username">
            <p>Password</p>
            <input type="password" name="password">
            <input type="submit" name="login" value="Login">
            <a href="forgotpsw.php">Lost your password?</a><br>
            <a href="register.php">Don't have an account?</a>
        </form>
    </div>
</body>
</head>
</html>