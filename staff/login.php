<?php
session_start();
include "db.php";

$error = "";

// Handle Login
if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    if(!$email || !$password){
        $error = "Please enter both email and password.";
    } else {
        $query = "SELECT * FROM staff WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) == 1){
            $row = mysqli_fetch_assoc($result);
            if(password_verify($password, $row['password'])){
                // Login success
                $_SESSION['staff_id'] = $row['id'];
                $_SESSION['staff_name'] = $row['name'];
                $_SESSION['staff_role'] = $row['role'];

                // Role based redirect
                if($row['role'] == "Doctor"){
                    header("Location: doctor_dashboard.php");
                    exit();
                } elseif($row['role'] == "Compounder"){
                    header("Location: compounder_dashboard.php");
                    exit();
                } else {
                    $error = "Role not recognized.";
                }
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Email not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Login | Eye & ENT Clinic</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

body{
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#198754,#0dcaf0);
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* CARD */
.container{
    width:100%;
    max-width:420px;
    background:#ffffff;
    padding:35px 30px;
    border-radius:14px;
    box-shadow:0 20px 40px rgba(0,0,0,0.25);
    animation:fadeIn 0.6s ease-in-out;
}

/* HEADER */
.logo{
    text-align:center;
    margin-bottom:10px;
    font-size:42px;
    color:#198754;
}

h2{
    text-align:center;
    color:#198754;
    margin-bottom:25px;
    font-size:26px;
}

/* INPUTS */
.form-group{
    position:relative;
    margin-bottom:15px;
}

.form-group i{
    position:absolute;
    top:50%;
    left:14px;
    transform:translateY(-50%);
    color:#198754;
    font-size:16px;
}

form input{
    width:100%;
    padding:13px 15px 13px 42px;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:15px;
    transition:0.3s;
}

form input:focus{
    outline:none;
    border-color:#198754;
    box-shadow:0 0 6px rgba(25,135,84,0.35);
}

/* BUTTON */
form button{
    width:100%;
    padding:14px;
    background:#198754;
    color:#fff;
    border:none;
    border-radius:8px;
    font-size:16px;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

form button:hover{
    background:#157347;
}

/* ERROR */
.error{
    color:#721c24;
    background:#f8d7da;
    padding:10px;
    border-left:5px solid #c82333;
    border-radius:6px;
    margin-bottom:15px;
    text-align:center;
    font-size:14px;
}

/* FOOTER */
.footer-text{
    text-align:center;
    margin-top:15px;
    font-size:13px;
    color:#666;
}

/* ANIMATION */
@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(20px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* MOBILE */
@media(max-width:480px){
    .container{
        margin:15px;
        padding:30px 22px;
    }
}
</style>
</head>
<body>

<div class="container">

    <div class="logo">
        <i class="fa-solid fa-user-nurse"></i>
    </div>

    <h2>Staff Login</h2>

    <?php if($error!=""){ echo "<div class='error'>$error</div>"; } ?>

    <form method="post">

        <div class="form-group">
            <i class="fa-solid fa-envelope"></i>
            <input type="email" name="email" placeholder="Staff Email" required>
        </div>

        <div class="form-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" name="login">
            <i class="fa-solid fa-right-to-bracket"></i> Login
        </button>

    </form>

    <div class="footer-text">
        Eye & ENT Clinic • Staff Portal
    </div>

</div>

</body>
</html>
