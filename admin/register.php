<?php
include "db.php";
$msg = "";

if(isset($_POST['register'])){
    $user = $_POST['username'];
    $email = $_POST['email'];
    $pass = md5($_POST['password']);

    $check = mysqli_query($conn,"SELECT * FROM admin WHERE username='$user'");
    if(mysqli_num_rows($check)>0){
        $msg = "Username already exists!";
    }else{
        mysqli_query($conn,"INSERT INTO admin(username,email,password) 
        VALUES('$user','$email','$pass')");
        $msg = "Registration Successful!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register | Admin Panel</title>
<style>
body{
    height:100vh;display:flex;justify-content:center;align-items:center;
    background:linear-gradient(135deg,#0d6efd,#20c997);font-family:Arial;
}
.box{
    width:380px;background:#fff;padding:30px;border-radius:10px;
}
input,button{
    width:100%;padding:12px;margin:10px 0;
}
button{background:#0d6efd;color:#fff;border:none;}
.msg{text-align:center;color:green;}
.err{text-align:center;color:red;}
</style>
</head>
<body>

<div class="box">
<h2>Admin Register</h2>

<form method="post">
    <input type="text" name="username" placeholder="Username" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button name="register">Register</button>
</form>

<div class="msg"><?= $msg ?></div>
<p align="center"><a href="login.php">Login</a></p>
</div>

</body>
</html>
