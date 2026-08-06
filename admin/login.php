<?php
session_start();
include "db.php";

$error = "";

if(isset($_POST['login'])){
    $user_input = $_POST['username']; // username OR email
    $password   = md5($_POST['password']); // MD5 as per your database

    $sql = "SELECT * FROM admin WHERE (username='$user_input' OR email='$user_input') AND password='$password'";
    $result = mysqli_query($conn,$sql);

    if(mysqli_num_rows($result)==1){
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin'] = $row['username'];
        $_SESSION['admin_email'] = $row['email'];
        header("Location: dashboard.php");
        exit();
    }else{
        $error = "Invalid Username / Email or Password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login | Eye & ENT Clinic</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

/* ===== BACKGROUND ===== */
body{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:
        linear-gradient(135deg,#0a5aa5,#198754);
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* ===== LOGIN CARD ===== */
.login-box{
    width:100%;
    max-width:420px;
    background:#ffffff;
    padding:35px 30px;
    border-radius:16px;
    box-shadow:0 18px 40px rgba(0,0,0,0.25);
    position:relative;
    overflow:hidden;
}

/* WATERMARK */
.login-box::after{
    content:"Eye & ENT Clinic";
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%) rotate(-25deg);
    font-size:60px;
    color:rgba(10,90,165,0.05);
    white-space:nowrap;
    pointer-events:none;
}

/* ===== HEADER ===== */
.header{
    text-align:center;
    margin-bottom:25px;
    position:relative;
    z-index:1;
}

.logo{
    width:70px;
    height:70px;
    border-radius:50%;
    background:#0a5aa5;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:30px;
    margin:0 auto 10px;
}

.header h2{
    color:#0a5aa5;
    font-size:26px;
}

.header p{
    font-size:14px;
    color:#555;
}

/* ===== FORM ===== */
form{
    position:relative;
    z-index:1;
}

.form-group{
    margin-bottom:15px;
    position:relative;
}

.form-group i{
    position:absolute;
    top:50%;
    left:14px;
    transform:translateY(-50%);
    color:#0a5aa5;
}

.form-group input{
    width:100%;
    padding:13px 14px 13px 42px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:15px;
    transition:0.3s;
}

.form-group input:focus{
    outline:none;
    border-color:#0a5aa5;
    box-shadow:0 0 0 3px rgba(10,90,165,0.15);
}

/* ===== BUTTON ===== */
button{
    width:100%;
    padding:14px;
    background:linear-gradient(135deg,#0a5aa5,#198754);
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:translateY(-1px);
    box-shadow:0 6px 15px rgba(0,0,0,0.25);
}

/* ===== ERROR ===== */
.error{
    background:#f8d7da;
    color:#721c24;
    padding:12px;
    margin-top:15px;
    border-left:5px solid #dc3545;
    border-radius:8px;
    text-align:center;
    position:relative;
    z-index:1;
}

/* ===== LINKS ===== */
.links{
    display:flex;
    justify-content:space-between;
    margin-top:18px;
    font-size:14px;
    position:relative;
    z-index:1;
}

.links a{
    text-decoration:none;
    color:#0a5aa5;
    font-weight:600;
}

.links a:hover{
    text-decoration:underline;
}

/* ===== NOTE ===== */
.note{
    margin-top:12px;
    font-size:13px;
    text-align:center;
    color:#555;
    position:relative;
    z-index:1;
}

/* ===== MOBILE ===== */
@media(max-width:500px){
    .login-box{
        margin:15px;
        padding:30px 22px;
    }
    .header h2{
        font-size:22px;
    }
}
</style>
</head>

<body>

<div class="login-box">

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            <i class="fa-solid fa-user-shield"></i>
        </div>
        <h2>Admin Login</h2>
        <p>Eye & ENT Clinic Management</p>
    </div>

    <!-- FORM -->
    <form method="post">
        <div class="form-group">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="username" placeholder="Username or Email" required>
        </div>

        <div class="form-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" name="login">
            <i class="fa-solid fa-right-to-bracket"></i> Login
        </button>
    </form>

    <?php if(isset($error) && $error!=""){ ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <!-- LINKS -->
    <div class="links">
        <a href="register.php"><i class="fa-solid fa-user-plus"></i> Register</a>
        <a href="forgot.php">Forgot Password?</a>
    </div>

    <div class="note">
        Login using <b>Username</b> or <b>Email</b>
    </div>

</div>

</body>
</html>
