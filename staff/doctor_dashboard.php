<?php
session_start();
include "db.php";

/* ===== ONLY DOCTOR ===== */
if(!isset($_SESSION['staff_role']) || $_SESSION['staff_role']!="Doctor"){
    header("Location: login.php");
    exit();
}

/* ===== SUMMARY ===== */
$total_patients = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) total FROM patients")
)['total'];

/* ===== PAGE ROUTE ===== */
$page = $_GET['page'] ?? 'home';

/* ===== ADD PATIENT ===== */
if($page=="add_patient" && isset($_POST['add_patient'])){
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $age_sex = mysqli_real_escape_string($conn,$_POST['age_sex']);
    $address = mysqli_real_escape_string($conn,$_POST['address']);
    $occupation = mysqli_real_escape_string($conn,$_POST['occupation']);
    $mobile = mysqli_real_escape_string($conn,$_POST['mobile']);

    if($name && $age_sex && $address && $mobile){
        mysqli_query($conn,"
            INSERT INTO patients (name,age_sex,address,occupation,mobile)
            VALUES ('$name','$age_sex','$address','$occupation','$mobile')
        ");
        $_SESSION['success']="Patient added successfully!";
        header("Location: doctor_dashboard.php?page=patients");
        exit();
    }else{
        $_SESSION['error']="All * fields required!";
    }
}

/* ===== DELETE PATIENT ===== */
if($page=="patients" && isset($_GET['delete_id'])){
    $id=intval($_GET['delete_id']);
    mysqli_query($conn,"DELETE FROM patients WHERE id=$id");
    $_SESSION['success']="Patient deleted!";
    header("Location: doctor_dashboard.php?page=patients");
    exit();
}

/* ===== PATIENT LIST ===== */
$search="";
if($page=="patients"){
    if(!empty($_GET['search'])){
        $search=mysqli_real_escape_string($conn,$_GET['search']);
        $patients=mysqli_query($conn,"
            SELECT * FROM patients 
            WHERE name LIKE '%$search%' 
            OR DATE(created_at)='$search'
            ORDER BY created_at DESC
        ");
    }else{
        $patients=mysqli_query($conn,"
            SELECT * FROM patients ORDER BY created_at DESC
        ");
    }
}

/* ===== FLASH ===== */
$success=$_SESSION['success']??"";
$error=$_SESSION['error']??"";
unset($_SESSION['success'],$_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Dashboard | Eye & ENT Clinic</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body{
    background:#f1f4f9;
}

/* SIDEBAR */
.sidebar{
    width:240px;
    height:100vh;
    background:linear-gradient(180deg,#198754,#157347);
    position:fixed;
    color:#fff;
}

.sidebar h2{
    text-align:center;
    padding:18px;
    font-size:22px;
    border-bottom:1px solid rgba(255,255,255,0.2);
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:10px;
    padding:14px 22px;
    color:#fff;
    text-decoration:none;
    font-size:15px;
    transition:0.3s;
}

.sidebar a:hover,
.sidebar .active{
    background:rgba(255,255,255,0.2);
}

/* MAIN */
.main{
    margin-left:240px;
    padding:25px;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.header h2{
    color:#198754;
}

/* CARDS */
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:30px;
}

.card{
    background:#fff;
    padding:25px;
    border-radius:14px;
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
    text-align:center;
}

.card i{
    font-size:36px;
    color:#198754;
    margin-bottom:10px;
}

.card h1{
    font-size:36px;
    margin:10px 0;
}

.card p{
    color:#666;
    font-size:15px;
}

/* FORMS */
input, textarea{
    width:100%;
    padding:12px;
    margin:8px 0;
    border:1px solid #ccc;
    border-radius:8px;
    font-size:14px;
}

input:focus, textarea:focus{
    outline:none;
    border-color:#198754;
    box-shadow:0 0 5px rgba(25,135,84,.3);
}

button{
    background:#198754;
    color:#fff;
    padding:12px 18px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-size:15px;
}

button:hover{
    background:#157347;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 6px 18px rgba(0,0,0,0.08);
}

th{
    background:#198754;
    color:#fff;
}

th,td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #eee;
}

tr:hover{
    background:#f8f9fa;
}

/* ALERTS */
.success{
    background:#d4edda;
    color:#155724;
    padding:12px;
    border-left:5px solid #198754;
    border-radius:8px;
    margin-bottom:15px;
}

.error{
    background:#f8d7da;
    color:#721c24;
    padding:12px;
    border-left:5px solid #c82333;
    border-radius:8px;
    margin-bottom:15px;
}

/* MOBILE */
@media(max-width:768px){
    .sidebar{
        width:100%;
        height:auto;
        position:relative;
    }
    .main{
        margin-left:0;
    }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2><i class="fa-solid fa-user-doctor"></i> Doctor Panel</h2>

    <a href="doctor_dashboard.php" class="<?= $page=='home'?'active':'' ?>">
        <i class="fa-solid fa-house"></i> Dashboard
    </a>

    <a href="?page=patients" class="<?= $page=='patients'?'active':'' ?>">
        <i class="fa-solid fa-users"></i> Patients
    </a>

    <a href="?page=add_patient" class="<?= $page=='add_patient'?'active':'' ?>">
        <i class="fa-solid fa-user-plus"></i> Add Patient
    </a>

    <a href="logout.php">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</div>

<!-- MAIN -->
<div class="main">

<div class="header">
    <h2>Welcome Dr. <?= htmlspecialchars($_SESSION['staff_name']) ?></h2>
</div>

<?php if($success){ ?><div class="success"><?= $success ?></div><?php } ?>
<?php if($error){ ?><div class="error"><?= $error ?></div><?php } ?>

<!-- DASHBOARD -->
<?php if($page=="home"){ ?>
<div class="cards">
    <div class="card">
        <i class="fa-solid fa-users"></i>
        <h1><?= $total_patients ?></h1>
        <p>Total Patients</p>
    </div>
</div>
<?php } ?>

<!-- ADD PATIENT -->
<?php if($page=="add_patient"){ ?>
<h3>Add Patient</h3>
<form method="post">
    <input name="name" placeholder="Patient Name *" required>
    <input name="age_sex" placeholder="Age / Sex *" required>
    <textarea name="address" placeholder="Address *" required></textarea>
    <input name="occupation" placeholder="Occupation">
    <input name="mobile" placeholder="Mobile *" required>
    <button name="add_patient"><i class="fa-solid fa-plus"></i> Add Patient</button>
</form>
<?php } ?>

<!-- PATIENT LIST -->
<?php if($page=="patients"){ ?>
<form style="display:flex;gap:10px;margin-bottom:15px;">
    <input type="hidden" name="page" value="patients">
    <input name="search" placeholder="Search name or date">
    <button><i class="fa-solid fa-magnifying-glass"></i></button>
</form>

<table>
<tr>
    <th>#</th>
    <th>Name</th>
    <th>Age/Sex</th>
    <th>Mobile</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php $i=1; if(mysqli_num_rows($patients)>0){
while($p=mysqli_fetch_assoc($patients)){ ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= $p['age_sex'] ?></td>
    <td><?= $p['mobile'] ?></td>
    <td><?= date("d-m-Y",strtotime($p['created_at'])) ?></td>
    <td>
        <a href="doctor_prescription.php?id=<?= $p['id'] ?>" style="color:#198754;">View</a> |
        <a href="?page=patients&delete_id=<?= $p['id'] ?>"
           onclick="return confirm('Delete patient?')" style="color:red;">Delete</a>
    </td>
</tr>
<?php }} else { ?>
<tr><td colspan="6">No patients found</td></tr>
<?php } ?>
</table>
<?php } ?>

</div>
</body>
</html>
