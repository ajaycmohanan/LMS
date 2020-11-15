<?php
    session_start();
    include('dBConfig.php');
    if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])) {
        // Verify data
        $email = $_GET['email']; // Set email variable
        $hash = $_GET['hash']; // Set hash variable
        $userid = $_GET['userid']; // Set userid variable

        $data = [
            'email' => $email,
            'hash' => $hash,
            'verify' => 1
        ];
        $sql = "SELECT * FROM members WHERE email = :email AND hash = :hash AND verify = :verify";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute($data);
        $count = $stmt -> rowCount();
        if ($count > 0) {
?>
<!DOCTYPE html>
    <html>
    <head>
        <title>Registration</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        	<script type="text/javascript">
          var check = function() {
        			if (document.getElementById('buttons').value == document.getElementById('buttonss').value) {
        				document.getElementById('msg').style.color = 'green';
        				document.getElementById('msg').innerHTML = "";
        				document.getElementById('butt').disabled = false;
        			} else {
        				document.getElementById('msg').style.color = 'red';
        				document.getElementById('msg').innerHTML = "Passwords not matching";
        				document.getElementById('butt').disabled = true;
        			}
        		}
          </script>
    </head>
    <body>
        <div class="simple-form">
            <form id="registration" method="post">
                <h1>Contact Information</h1><br>
                <input type="password" name="password" placeholder="Password" id="buttons" onkeyup="check();"><br><br>
                <span id="msg"></span><br>
                <input type="password" name="C_password" placeholder="Confirm Password" id="buttonss" onkeyup="check();"><br><br>
                <input type="submit" name="register" value="Register" id="butt">
            </form>
        </div>
    </body>
    </html>
    <?php
        } else{
        // Invalid approach
        echo '<div class="statusmsg">Invalid approach!</div>';
    }
    }else{
        // Invalid approach
        echo '<div class="statusmsg">Invalid approach, please use the link that has been send to your email.</div>';
    }
    ?>
    <?php
    if (isset($_POST["register"]) && !empty($_POST))
    {
        $userid = $_GET['userid']; // Set userid variable
        $email = $_GET['email']; // Set email variable
        $hash = $_GET['hash']; // Set hash variable
        $password = md5($_POST['password']);
        //dBUpdate: admin.verify = 2 and status = 1
            $values = [
                'verify' => 2,
                'status' => 1,
                'email' => $email,
                'hash' => $hash
            ];
            $sql = "UPDATE members SET verify = :verify, status = :status WHERE email = :email AND hash = :hash";
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute($values);
            $data = [
                'userid' => $userid,
                'password' => md5($password),
                'email' => $email,
                'accnttype' => 0,
                'status' => 1
            ];
            $sql = "INSERT INTO login (userid, password, email, accnttype, status) VALUES (:userid, :password, :email, :accnttype, :status)";
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute($data);
            $count = $stmt -> rowCount();
            if ($count > 0) {
                //Re-Direct: Login Page
                header("refresh:3;url=http://www.pusthakangal.com/");
                }
              }
?>
