<?php
include "db.php"; // Database connection file
session_start();

// Handle Form Submission
if(isset($_POST['add_patient'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $age_sex = mysqli_real_escape_string($conn, $_POST['age_sex']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $occupation = mysqli_real_escape_string($conn, $_POST['occupation']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);

    if(!$name || !$age_sex || !$address || !$mobile){
        $_SESSION['error'] = "Please fill all required fields (*)";
    } else {
        $sql = "INSERT INTO patients (name, age_sex, address, occupation, mobile) 
                VALUES ('$name','$age_sex','$address','$occupation','$mobile')";
        if(mysqli_query($conn,$sql)){
            $_SESSION['success'] = "Patient added successfully!";
            header("Location: ".$_SERVER['PHP_SELF']); // Redirect to self to show alert once
            exit();
        } else {
            $_SESSION['error'] = "Error adding patient: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Patient | Eye & ENT Clinic</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

/* ===== BACKGROUND ===== */
body{
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background:linear-gradient(135deg,#eaf4ff,#f7fbff);
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
}

/* ===== CARD ===== */
.container{
    width:100%;
    max-width:520px;
    background:#fff;
    padding:35px 30px;
    border-radius:16px;
    box-shadow:0 12px 30px rgba(0,0,0,0.15);
    position:relative;
    overflow:hidden;
}

/* WATERMARK */
.container::after{
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
}

.logo{
    width:60px;
    height:60px;
    border-radius:50%;
    background:#0a5aa5;
    color:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:26px;
    margin:0 auto 10px;
}

.header h2{
    color:#0a5aa5;
    font-size:26px;
    margin-bottom:5px;
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
}

.form-group label{
    font-size:13px;
    font-weight:600;
    color:#333;
    margin-bottom:5px;
    display:block;
}

.form-group i{
    margin-right:6px;
    color:#0a5aa5;
}

form input, form textarea{
    width:100%;
    padding:13px 14px;
    border-radius:10px;
    border:1px solid #ccc;
    font-size:15px;
    transition:0.3s;
}

form input:focus, form textarea:focus{
    outline:none;
    border-color:#0a5aa5;
    box-shadow:0 0 0 3px rgba(10,90,165,0.15);
}

/* ===== BUTTON ===== */
form button{
    width:100%;
    padding:14px;
    background:linear-gradient(135deg,#0a5aa5,#198754);
    color:#fff;
    border:none;
    border-radius:10px;
    font-size:16px;
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
    transition:0.3s;
}

form button:hover{
    transform:translateY(-1px);
    box-shadow:0 6px 15px rgba(0,0,0,0.2);
}

/* ===== ALERTS ===== */
.success{
    background:#d4edda;
    color:#155724;
    padding:12px;
    margin-bottom:15px;
    border-left:5px solid #198754;
    border-radius:8px;
    text-align:center;
}

.error{
    background:#f8d7da;
    color:#721c24;
    padding:12px;
    margin-bottom:15px;
    border-left:5px solid #dc3545;
    border-radius:8px;
    text-align:center;
}

/* ===== MOBILE ===== */
@media(max-width:600px){
    .container{
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

<div class="container">

    <!-- HEADER -->
    <div class="header">
        <div class="logo">
            <i class="fa-solid fa-eye"></i>
        </div>
        <h2>Add New Patient</h2>
        <p>Eye & ENT Clinic Management</p>
    </div>

    <?php
    if(isset($_SESSION['success'])){
        echo "<div class='success'>".$_SESSION['success']."</div>";
        unset($_SESSION['success']);
    }
    if(isset($_SESSION['error'])){
        echo "<div class='error'>".$_SESSION['error']."</div>";
        unset($_SESSION['error']);
    }
    ?>

    <!-- FORM -->
    <form method="post" id="patientForm">

        <div class="form-group">
            <label><i class="fa-solid fa-user"></i> Full Name *</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-venus-mars"></i> Age / Sex *</label>
            <input type="text" name="age_sex" required>
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-location-dot"></i> Address *</label>
            <textarea name="address" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-briefcase"></i> Occupation</label>
            <input type="text" name="occupation">
        </div>

        <div class="form-group">
            <label><i class="fa-solid fa-phone"></i> Mobile *</label>
            <input type="text" name="mobile" required>
        </div>

        <button type="submit" name="add_patient">
            <i class="fa-solid fa-plus"></i> Add Patient
        </button>

    </form>

</div>

</body>
</html>
