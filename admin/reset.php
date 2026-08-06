<?php
include "db.php";
$email = $_GET['email'];
$msg="";

if(isset($_POST['reset'])){
    $newpass = md5($_POST['password']);
    mysqli_query($conn,"UPDATE admin SET password='$newpass' WHERE email='$email'");
    $msg="Password Updated Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Reset Password</title>
<style>
body{
    height:100vh;display:flex;justify-content:center;align-items:center;
    background:linear-gradient(135deg,#dc3545,#fd7e14);font-family:Arial;
}
.box{width:360px;background:#fff;padding:30px;border-radius:10px;}
input,button{width:100%;padding:12px;margin:10px 0;}
button{background:#dc3545;color:#fff;border:none;}
.msg{text-align:center;color:green;}
</style>
</head>
<body>

<div class="box">
<h2>Reset Password</h2>

<form method="post">
    <input type="password" name="password" placeholder="New Password" required>
    <button name="reset">Reset</button>
</form>

<div class="msg"><?= $msg ?></div>
<p align="center"><a href="login.php">Login</a></p>
</div>

</body>
</html>
