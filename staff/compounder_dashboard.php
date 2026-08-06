<?php
session_start();
include "db.php";

/* ===== ONLY Compounder ===== */
if(!isset($_SESSION['staff_role']) || $_SESSION['staff_role']!="Compounder"){
    header("Location: login.php");
    exit();
}

$page = $_GET['page'] ?? 'dashboard';

/* ================= ADD PATIENT ================= */
if(isset($_POST['add_patient'])){
    $name       = trim($_POST['name']);
    $age_sex    = trim($_POST['age_sex']);
    $address    = trim($_POST['address']);
    $occupation = trim($_POST['occupation'] ?? '');
    $mobile     = trim($_POST['mobile']);

    if($name && $age_sex && $address && $mobile){
        $stmt = mysqli_prepare($conn,
            "INSERT INTO patients(name,age_sex,address,occupation,mobile,created_at) 
             VALUES(?,?,?,?,?,NOW())"
        );
        mysqli_stmt_bind_param($stmt, "sssss", $name,$age_sex,$address,$occupation,$mobile);
        if(mysqli_stmt_execute($stmt)){
            // Success message stored in session
            $_SESSION['success'] = "Patient added successfully";
            header("Location: ?page=add_patient");
            exit;
        } else {
            $_SESSION['error'] = "Something went wrong";
            header("Location: ?page=add_patient");
            exit;
        }
    }else{
        $_SESSION['error'] = "All fields are required";
        header("Location: ?page=add_patient");
        exit;
    }
}

// Fetch messages from session and then clear
$success = $_SESSION['success'] ?? "";
$error   = $_SESSION['error'] ?? "";
unset($_SESSION['success'], $_SESSION['error']);

/* ================= COUNTS ================= */
$total_patients = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) t FROM patients"))['t'];
$today = date('Y-m-d');
$today_count = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT COUNT(*) t FROM patients WHERE DATE(created_at)='$today'")
)['t'];

$today_patients = mysqli_query(
    $conn,
    "SELECT * FROM patients WHERE DATE(created_at)='$today' ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Compounder Dashboard | Eye & ENT Clinic</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f1f4f9;}

/* SIDEBAR */
.sidebar{
    width:240px;height:100vh;
    background:linear-gradient(180deg,#198754,#146c43);
    position:fixed;color:#fff;
}
.sidebar h2{
    text-align:center;padding:20px;font-size:22px;
    border-bottom:1px solid rgba(255,255,255,.2);
}
.sidebar a{
    display:flex;align-items:center;gap:12px;
    padding:14px 22px;color:#fff;text-decoration:none;font-size:15px;
    transition:.3s;
}
.sidebar a:hover,.sidebar a.active{
    background:rgba(255,255,255,.15);
}

/* MAIN */
.main{margin-left:240px;padding:25px;}
.header h2{color:#198754;margin-bottom:20px;}

/* DASH CARDS */
.cards{
    display:flex;gap:20px;flex-wrap:wrap;margin-bottom:25px;
}
.card{
    background:#fff;padding:25px;
    border-radius:14px;
    box-shadow:0 8px 25px rgba(0,0,0,.12);
    flex:1;min-width:220px;text-align:center;
}
.card h1{color:#198754;font-size:36px;}
.card p{color:#555;margin-top:8px;font-size:16px;font-weight:500;}

/* ALERTS */
.success,.error{
    padding:12px;border-radius:8px;margin-bottom:15px;font-weight:500;
}
.success{background:#d4edda;color:#155724;border-left:5px solid #198754;}
.error{background:#f8d7da;color:#721c24;border-left:5px solid #dc3545;}

/* FORM */
.form-box{
    background:#fff;padding:25px;
    border-radius:14px;
    box-shadow:0 8px 25px rgba(0,0,0,.12);
    max-width:520px;margin-bottom:25px;
}
.form-box h3{color:#198754;margin-bottom:15px;}
input,textarea{
    width:100%;padding:12px;margin:8px 0;border:1px solid #ccc;border-radius:8px;
    font-size:14px;
}
input:focus,textarea:focus{outline:none;border-color:#198754;box-shadow:0 0 6px rgba(25,135,84,.3);}
button{
    background:#198754;color:#fff;padding:12px 18px;border:none;border-radius:8px;cursor:pointer;font-size:15px;
}
button:hover{background:#146c43;}

/* TABLE */
.table-box{
    background:#fff;padding:20px;border-radius:14px;
    box-shadow:0 8px 25px rgba(0,0,0,.12);overflow-x:auto;
}
table{width:100%;border-collapse:collapse;}
th,td{padding:10px;border:1px solid #ddd;text-align:center;font-size:14px;}
th{background:#198754;color:#fff;}
tr:nth-child(even){background:#f9f9f9;}

/* MOBILE */
@media(max-width:768px){
    .sidebar{width:100%;height:auto;position:relative;}
    .main{margin-left:0;}
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2><i class="fa-solid fa-eye"></i> Compounder</h2>

    <a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>">
        <i class="fa-solid fa-gauge"></i> Dashboard
    </a>

    <a href="?page=add_patient" class="<?= $page=='add_patient'?'active':'' ?>">
        <i class="fa-solid fa-user-plus"></i> Add Patient
    </a>

    <a href="?page=today_patients" class="<?= $page=='today_patients'?'active':'' ?>">
        <i class="fa-solid fa-calendar-day"></i> Today Patients
    </a>

    <a href="logout.php">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</div>

<!-- MAIN -->
<div class="main">
<div class="header">
    <h2>Welcome <?= htmlspecialchars($_SESSION['staff_name']) ?></h2>
</div>

<?php if($success){?><div class="success"><?= $success ?></div><?php } ?>
<?php if($error){?><div class="error"><?= $error ?></div><?php } ?>

<!-- DASHBOARD -->
<?php if($page=='dashboard'){ ?>
<div class="cards">
    <div class="card">
        <h1><?= $today_count ?></h1>
        <p>Today Patients</p>
    </div>
    <div class="card">
        <h1><?= $total_patients ?></h1>
        <p>Total Patients</p>
    </div>
</div>
<?php } ?>

<!-- ADD PATIENT -->
<?php if($page=='add_patient'){ ?>
<div class="form-box">
    <h3><i class="fa-solid fa-user-plus"></i> Add Patient</h3>
    <form method="post">
        <input name="name" placeholder="Patient Name *" required>
        <input name="age_sex" placeholder="Age / Sex *" required>
        <textarea name="address" placeholder="Address *" required></textarea>
        <input name="occupation" placeholder="Occupation (Optional)">
        <input name="mobile" placeholder="Mobile *" required>
        <button name="add_patient"><i class="fa-solid fa-plus"></i> Save Patient</button>
    </form>
</div>
<?php } ?>

<!-- TODAY PATIENTS -->
<?php if($page=='today_patients'){ ?>
<div class="table-box">
    <h3><i class="fa-solid fa-calendar-day"></i> Today Patients</h3>
    <table>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Age/Sex</th>
            <th>Occupation</th>
            <th>Mobile</th>
            <th>Time</th>
        </tr>
        <?php $i=1; if(mysqli_num_rows($today_patients)>0){
            while($p=mysqli_fetch_assoc($today_patients)){ ?>
        <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($p['name']) ?></td>
            <td><?= $p['age_sex'] ?></td>
            <td><?= htmlspecialchars($p['occupation']) ?></td>
            <td><?= $p['mobile'] ?></td>
            <td><?= date("h:i A",strtotime($p['created_at'])) ?></td>
        </tr>
        <?php }} else { ?>
        <tr><td colspan="6">No patients registered today</td></tr>
        <?php } ?>
    </table>
</div>
<?php } ?>

</div>
</body>
</html>
