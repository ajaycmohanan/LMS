<?php
session_start();
include('dBConfig.php');
        if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash']))
        {
            // Verify data
            $email = $_GET['email']; // Set email variable
            $hash = $_GET['hash']; // Set hash variable
            $userid = $_GET['userid']; // Set userid variable
            $data = [
                'email' => $email,
                'hash' => $hash,
                'verify' => 1
            ];
            $sql = "SELECT * FROM librarian WHERE email = :email AND hash = :hash AND verify = :verify";
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute($data);
            $count = $stmt -> rowCount();
            if ($count > 0) {
                //Show Registration Form
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
                <input type="text" name="phno" id="button" placeholder="Phone Number">&nbsp;&nbsp;
                <input type="text" name="address" id="button" placeholder="Address"><br><br><br>
                <input type="text" name="city" id="button" placeholder="City">&nbsp;&nbsp;
                <input type="text" name="state" id="button" placeholder="State"><br><br><br>
                <input type="text" name="pincode" id="button" placeholder="Pincode">&nbsp;&nbsp;
                <input type="password" name="password" placeholder="Password" id="buttons" onkeyup="check();"><br><br>
                <span id="msg"></span><br>
                <input type="password" name="C_password" placeholder="Confirm Password" id="buttonss" onkeyup="check();">&nbsp;&nbsp;
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
        $phno = $_POST['phno'];
        $address = $_POST['address'];
        $city = $_POST['city'];
        $state = $_POST['state'];
        $pincode = $_POST['pincode'];
        $password = md5($_POST['password']);
        //dBUpdate: admin.verify = 2 and status = 1
        $values = [
            'phno' => $phno,
            'address' => $address,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'verify' => 2,
            'status' => 1,
            'email' => $email,
            'hash' => $hash
        ];
        $sql = "UPDATE librarian SET phno = :phno, address = :address, city = :city, state = :state, pincode = :pincode, verify = :verify, status = :status WHERE email = :email AND hash = :hash";
        $stmt = $pdo -> prepare($sql);
        $stmt -> execute($values);
        $count = $stmt -> rowCount();
        if ($count > 0)
        {
            //dBUpdate: login
            $values = [
                'userid' => $userid,
                'password' => $password,
                'email' => $email,
                'accnttype' => 1,
                'status' => 1
            ];
            $sql = "INSERT INTO login (userid, password, email, accnttype, status) VALUES (:userid, :password, :email, :accnttype, :status)";
            $stmt = $pdo -> prepare($sql);
            $stmt -> execute($values);
            $count = $stmt -> rowCount();
            if ($count > 0)
            {
                //Re-Direct: Login Page
                header("location:http://localhost/login.php");
            }
        }
}
