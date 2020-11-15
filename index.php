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
        $userid = $_POST['userid'];
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
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="bootstrap/css/custom.css">
</head>
<body class="animsition">
    <div class="container" style="margin-top: 100px;">
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <h2><b>Login</b></h2><br>
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
                                <input  name="userid" class="form-control" placeholder="Registration Id" type="text" required="">
                            </div>
                            <div class="form-group">
                                <input class="form-control" type="password" placeholder="Password" name="password" required="" >
                            </div>
                            <div class="form-group">
                                <button class="btn btn-info btn-lg btn-block" type="submit" name="login">Log in</button>
                                <a class="btn btn-link" href="register.php" >Not yet Registered!</a>
                                <a class="btn btn-link" href="forgotpsw.php">Forgot Password</a>
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