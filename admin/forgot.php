<?php
include "db.php";
$msg="";

if(isset($_POST['check'])){
    $email = $_POST['email'];

    $q = mysqli_query($conn,"SELECT * FROM admin WHERE email='$email'");
    if(mysqli_num_rows($q)==1){
        header("Location: reset.php?email=$email");
        exit();
    }else{
        $msg="Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Forgot Password</title>
<style>
body{
    height:100vh;display:flex;justify-content:center;align-items:center;
    background:linear-gradient(135deg,#198754,#20c997);font-family:Arial;
}
.box{width:360px;background:#fff;padding:30px;border-radius:10px;}
input,button{width:100%;padding:12px;margin:10px 0;}
button{background:#198754;color:#fff;border:none;}
.error{color:red;text-align:center;}
</style>
</head>
<body>

<div class="box">
<h2>Forgot Password</h2>

<form method="post">
    <input type="email" name="email" placeholder="Registered Email" required>
    <button name="check">Continue</button>
</form>

<div class="error"><?= $msg ?></div>
<p align="center"><a href="login.php">Back to Login</a></p>
</div>

</body>
</html>
