<?php
session_start();
include "db.php"; // Database connection

if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}

// --- Summary counts ---
$total_patients = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM patients"))['total'];
$total_staff = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as total FROM staff"))['total'];

// --- Page routing ---
$page = $_GET['page'] ?? 'home';

// --- Patients ---
$search = "";
if($page=='patients'){
    if(isset($_GET['search'])){
        $search = mysqli_real_escape_string($conn,$_GET['search']);
        $sql = "SELECT * FROM patients WHERE name LIKE '%$search%' OR DATE(created_at)='$search' ORDER BY created_at DESC";
    } else {
        $sql = "SELECT * FROM patients ORDER BY created_at DESC";
    }
    $result = mysqli_query($conn,$sql);
}

// --- Add Patient ---
if($page=='add_patient' && isset($_POST['add_patient'])){
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $age_sex = mysqli_real_escape_string($conn,$_POST['age_sex']);
    $address = mysqli_real_escape_string($conn,$_POST['address']);
    $occupation = mysqli_real_escape_string($conn,$_POST['occupation']);
    $mobile = mysqli_real_escape_string($conn,$_POST['mobile']);

    if(!$name || !$age_sex || !$address || !$mobile){
        $_SESSION['add_error']="Please fill all required fields";
    } else {
        mysqli_query($conn,"INSERT INTO patients (name,age_sex,address,occupation,mobile) VALUES ('$name','$age_sex','$address','$occupation','$mobile')");
        $_SESSION['add_success']="Patient added successfully!";
        header("Location: dashboard.php?page=patients"); exit();
    }
}

// --- Delete Patient ---
if($page=='patients' && isset($_GET['delete_id'])){
    $id=intval($_GET['delete_id']);
    mysqli_query($conn,"DELETE FROM patients WHERE id=$id");
    $_SESSION['add_success']="Patient deleted successfully!";
    header("Location: dashboard.php?page=patients"); exit();
}

// --- Medicines ---
if($page=='view_medicines'){
    $medicines_result=mysqli_query($conn,"SELECT * FROM medicines ORDER BY created_at DESC");
}

// --- Add Medicine ---
if($page=='add_medicine' && isset($_POST['add_medicine'])){
    $name=mysqli_real_escape_string($conn,$_POST['name']);
    $dosage=mysqli_real_escape_string($conn,$_POST['dosage']);
    $quantity=intval($_POST['quantity']);
    $mrp=floatval($_POST['mrp']);
    $expiry_date=mysqli_real_escape_string($conn,$_POST['expiry_date']);
    $description=mysqli_real_escape_string($conn,$_POST['description']);

    if(!$name || $quantity<0 || $mrp<=0 || !$expiry_date){
        $_SESSION['med_error']="Please fill all fields correctly!";
    } else {
        mysqli_query($conn,"INSERT INTO medicines (name,dosage,quantity,mrp,expiry_date,description) VALUES ('$name','$dosage','$quantity','$mrp','$expiry_date','$description')");
        $_SESSION['med_success']="Medicine added successfully!";
        header("Location: dashboard.php?page=add_medicine"); exit();
    }
}

// --- Delete Medicine ---
if(isset($_GET['delete_med'])){
    $id=intval($_GET['delete_med']);
    mysqli_query($conn,"DELETE FROM medicines WHERE id=$id");
    $_SESSION['med_success']="Medicine deleted successfully!";
    header("Location: dashboard.php?page=view_medicines"); exit();
}

// --- Flash messages ---
$flash_success = $_SESSION['add_success'] ?? $_SESSION['med_success'] ?? "";
$flash_error = $_SESSION['add_error'] ?? $_SESSION['med_error'] ?? "";
unset($_SESSION['add_success'],$_SESSION['add_error'],$_SESSION['med_success'],$_SESSION['med_error']);


// --- Staff ---
if($page=='view_staff'){
    $staff_result = mysqli_query($conn,"SELECT * FROM staff ORDER BY created_at DESC");
}
// --- Add Staff ---
if($page=='add_staff' && isset($_POST['add_staff'])){
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $role = mysqli_real_escape_string($conn,$_POST['role']);
    $mobile = mysqli_real_escape_string($conn,$_POST['mobile']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $password = mysqli_real_escape_string($conn,$_POST['password']);

    if(!$name || !$mobile || !$email || !$password){
        $_SESSION['staff_error'] = "Please fill all required fields!";
    } else {
        // Hash password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into staff table
        $insert = mysqli_query($conn,"INSERT INTO staff (name, role, mobile, email, password) 
                                      VALUES ('$name','$role','$mobile','$email','$hashed_password')");
        if($insert){
            $_SESSION['staff_success'] = "Staff added successfully!";
        } else {
            $_SESSION['staff_error'] = "Error: ".mysqli_error($conn);
        }
        header("Location: dashboard.php?page=view_staff"); exit();
    }
}

// --- Delete Staff ---
if(isset($_GET['delete_staff'])){
    $id = intval($_GET['delete_staff']);
    mysqli_query($conn,"DELETE FROM staff WHERE id=$id");
    $_SESSION['staff_success'] = "Staff deleted successfully!";
    header("Location: dashboard.php?page=view_staff"); exit();
}

// Flash messages
$flash_staff_success = $_SESSION['staff_success'] ?? "";
$flash_staff_error = $_SESSION['staff_error'] ?? "";
unset($_SESSION['staff_success'],$_SESSION['staff_error']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | Eye & ENT Clinic</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Segoe UI', Tahoma, Geneva, Verdana,sans-serif; background:#f1f5f9; color:#333;}

/* ===== SIDEBAR ===== */
.sidebar{
    position:fixed; top:0; left:0; width:240px; height:100vh;
    background:linear-gradient(180deg,#0a5aa5,#198754); color:#fff;
    padding-top:20px; transition:transform 0.3s ease; z-index:1000;
}
.sidebar h2{ text-align:center; margin-bottom:25px; font-size:22px;}
.sidebar a{
    display:flex; align-items:center; gap:10px; color:#fff;
    padding:12px 22px; margin:4px 10px; border-radius:8px;
    font-weight:500; text-decoration:none; transition:0.3s;
}
.sidebar a:hover, .sidebar a.active{ background:rgba(255,255,255,0.18);}
.sidebar .close-btn{display:none; font-size:28px; position:absolute; top:10px; right:15px; cursor:pointer; color:#fff;}

/* ===== MAIN ===== */
.main{margin-left:240px; padding:25px; transition: margin-left 0.3s;}

/* ===== HEADER ===== */
.header{display:flex; justify-content:space-between; align-items:center; margin-bottom:25px;}
.header-title{font-size:26px; font-weight:700; color:#0a5aa5;}
.logout{background:#0a5aa5; color:#fff; padding:10px 16px; border-radius:8px; text-decoration:none; font-weight:600; transition:0.3s;}
.logout:hover{background:#198754;}

/* ===== CARDS ===== */
.cards{display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:20px; margin-bottom:30px;}
.card{background:#fff; padding:22px; border-radius:14px; box-shadow:0 6px 18px rgba(0,0,0,0.08); display:flex; align-items:center; justify-content:space-between;}
.card .info h3{font-size:30px; color:#0a5aa5;}
.card .info p{margin-top:4px; color:#666; font-size:15px;}
.card .icon{font-size:40px; color:#198754; opacity:0.8;}

/* ===== FORMS ===== */
form{background:#fff; padding:22px; border-radius:14px; box-shadow:0 6px 18px rgba(0,0,0,0.08); margin-bottom:25px;}
form input, form textarea{width:100%; padding:12px 14px; margin:8px 0; border:1px solid #ccc; border-radius:8px; font-size:15px;}
form input:focus, form textarea:focus{outline:none; border-color:#0a5aa5; box-shadow:0 0 0 3px rgba(10,90,165,0.15);}
form button{margin-top:10px; padding:12px 18px; background:linear-gradient(135deg,#0a5aa5,#198754); color:#fff; border:none; border-radius:8px; font-size:16px; font-weight:600; cursor:pointer;}
form button:hover{opacity:0.9;}

/* ===== TABLE ===== */
table{width:100%; border-collapse:collapse;}
th{background:#0a5aa5; color:#fff; padding:12px; font-size:14px;}
td{padding:12px; text-align:center; font-size:14px; border-bottom:1px solid #eee;}
tr:nth-child(even){background:#f9fbfd;}
table a{text-decoration:none; font-weight:600;}
.success{background:#d4edda; color:#155724; padding:12px; border-left:5px solid #198754; border-radius:8px; margin-bottom:15px;}
.error{background:#f8d7da; color:#721c24; padding:12px; border-left:5px solid #dc3545; border-radius:8px; margin-bottom:15px;}

/* ===== MOBILE ===== */
.toggle-btn{display:none; font-size:26px; cursor:pointer; color:#0a5aa5; position:fixed; top:15px; left:15px; z-index:1100;}
@media(max-width:768px){
    .toggle-btn{display:block;}
    .sidebar{width:220px; transform:translateX(-100%);}
    .sidebar.mobile-open{transform:translateX(0);}
    .sidebar .close-btn{display:block;}
    .main{margin-left:0;}
}
</style>

<script>
<?php if($flash_success){ ?>alert("<?= $flash_success ?>");<?php } ?>
<?php if($flash_error){ ?>alert("<?= $flash_error ?>");<?php } ?>

function openSidebar(){ document.querySelector('.sidebar').classList.add('mobile-open'); }
function closeSidebar(){ document.querySelector('.sidebar').classList.remove('mobile-open'); }
document.addEventListener('click', function(e){
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.toggle-btn');
    if(window.innerWidth <= 768 && sidebar.classList.contains('mobile-open')){
        if(!sidebar.contains(e.target) && !toggleBtn.contains(e.target)){ closeSidebar(); }
    }
});
</script>
</head>

<body>
<span class="toggle-btn" onclick="openSidebar()"><i class="fa-solid fa-bars"></i></span>

<div class="sidebar">
    <span class="close-btn" onclick="closeSidebar()">&times;</span>
    <h2>Eye & ENT</h2>
    <a href="dashboard.php" class="<?= $page=='home'?'active':'' ?>"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a href="dashboard.php?page=patients" class="<?= $page=='patients'?'active':'' ?>"><i class="fa-solid fa-hospital-user"></i> Patients</a>
    <a href="dashboard.php?page=add_patient" class="<?= $page=='add_patient'?'active':'' ?>"><i class="fa-solid fa-user-plus"></i> Add Patient</a>
    <a href="dashboard.php?page=add_staff" class="<?= $page=='add_staff'?'active':'' ?>"><i class="fa-solid fa-user-doctor"></i> Add Staff</a>
    <a href="dashboard.php?page=view_staff" class="<?= $page=='view_staff'?'active':'' ?>"><i class="fa-solid fa-users"></i> View Staff</a>
    <a href="dashboard.php?page=add_medicine" class="<?= $page=='add_medicine'?'active':'' ?>"><i class="fa-solid fa-pills"></i> Add Medicine</a>
    <a href="dashboard.php?page=view_medicines" class="<?= $page=='view_medicines'?'active':'' ?>"><i class="fa-solid fa-capsules"></i> Medicines</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- ===== MAIN ===== -->
<div class="main">

<div class="header">
    <div class="header-title">
        <?php
        if($page=='patients') echo "Registered Patients";
        elseif($page=='add_patient') echo "Add New Patient";
        elseif($page=='add_staff') echo "Add Staff";
        elseif($page=='view_staff') echo "Staff Members";
        elseif($page=='add_medicine') echo "Add Medicine";
        elseif($page=='view_medicines') echo "Medicines List";
        else echo "Dashboard Overview";
        ?>
    </div>
    <a href="logout.php" class="logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<?php if($page=='home'){ ?>
<div class="cards">
    <div class="card">
        <div class="info">
            <h3><?= $total_patients ?></h3>
            <p>Total Patients</p>
        </div>
        <div class="icon"><i class="fa-solid fa-hospital-user"></i></div>
    </div>
    <div class="card">
        <div class="info">
            <h3><?= $total_staff ?></h3>
            <p>Total Staff</p>
        </div>
        <div class="icon"><i class="fa-solid fa-user-doctor"></i></div>
    </div>
</div>
<?php } ?>

<!-- Patients List -->
<?php if($page=='patients'){ ?>
<form method="get" style="margin-bottom:15px;">
<input type="hidden" name="page" value="patients">
<input type="text" name="search" placeholder="Search by name or date YYYY-MM-DD" value="<?= htmlspecialchars($search) ?>">
<button type="submit">Search</button>
</form>

<table>
<tr><th>#</th><th>Name</th><th>Age/Sex</th><th>Address</th><th>Occupation</th><th>Mobile</th><th>Registered Date</th><th>Action</th></tr>
<?php
$count=1;
if(mysqli_num_rows($result)>0){
while($row=mysqli_fetch_assoc($result)){
echo "<tr>";
echo "<td>".$count++."</td>";
echo "<td>".$row['name']."</td>";
echo "<td>".$row['age_sex']."</td>";
echo "<td>".$row['address']."</td>";
echo "<td>".$row['occupation']."</td>";
echo "<td>".$row['mobile']."</td>";
echo "<td>".$row['created_at']."</td>";
echo "<td>
<a href='view_patient.php?id=".$row['id']."' style='color:green;'>View</a> | 
<a href='dashboard.php?page=patients&delete_id=".$row['id']."' onclick=\"return confirm('Are you sure?');\" style='color:red;'>Delete</a>
</td>";
echo "</tr>";
}
}else{ echo "<tr><td colspan='8'>No patients found</td></tr>"; }
?>
</table>
<?php } ?>

<!-- Add Patient -->
<?php if($page=='add_patient'){ ?>
<form method="post">
<input type="text" name="name" placeholder="Patient Name *" required>
<input type="text" name="age_sex" placeholder="Age / Sex *" required>
<textarea name="address" placeholder="Address *" required></textarea>
<input type="text" name="occupation" placeholder="Occupation">
<input type="text" name="mobile" placeholder="Mobile *" required>
<button type="submit" name="add_patient">Add Patient</button>
</form>
<?php } ?>

<!-- Add Staff -->
<?php if($page=='add_staff'){ ?>
<h2>Add Staff</h2>
<?php if($flash_staff_error){ echo "<div class='error'>$flash_staff_error</div>"; } ?>
<form method="post">
    <input type="text" name="name" placeholder="Staff Name *" required>
    <input type="text" name="role" placeholder="Role / Designation">
    <input type="text" name="mobile" placeholder="Mobile *" required>
    <input type="email" name="email" placeholder="Email *" required>
    <input type="password" name="password" placeholder="Password *" required>
    <button type="submit" name="add_staff">Add Staff</button>
</form>
<?php } ?>

<!-- View Staff -->
 <?php if($page=='view_staff'){ ?>
<h2>Staff Members</h2>
<?php if($flash_staff_success){ echo "<div class='success'>$flash_staff_success</div>"; } ?>

<table>
<tr>
    <th>#</th>
    <th>Name</th>
    <th>Role</th>
    <th>Mobile</th>
    <th>Email</th>
    <th>Joined On</th>
    <th>Action</th>
</tr>

<?php
$count=1;
if(mysqli_num_rows($staff_result) > 0){
    while($row=mysqli_fetch_assoc($staff_result)){
        echo "<tr>";
        echo "<td>".$count++."</td>";
        echo "<td>".htmlspecialchars($row['name'])."</td>";
        echo "<td>".htmlspecialchars($row['role'])."</td>";
        echo "<td>".htmlspecialchars($row['mobile'])."</td>";
        echo "<td>".htmlspecialchars($row['email'])."</td>";
        echo "<td>".$row['created_at']."</td>";
        echo "<td>
            <a href='dashboard.php?page=view_staff&delete_staff=".$row['id']."' onclick=\"return confirm('Are you sure?');\" style='color:red;'>Delete</a>
        </td>";
        echo "</tr>";
    }
}else{
    echo "<tr><td colspan='7'>No staff found.</td></tr>";
}
?>
</table>
<?php } ?>



<!-- Add Medicine -->
<?php if($page=='add_medicine'){ ?>
<form method="post">
<input type="text" name="name" placeholder="Medicine Name *" required>
<input type="text" name="dosage" placeholder="Dosage">
<input type="number" name="quantity" placeholder="Quantity *" min="0" required>
<input type="number" step="0.01" name="mrp" placeholder="MRP (₹) *" required>
<input type="date" name="expiry_date" placeholder="Expiry Date *" required>
<textarea name="description" placeholder="Description"></textarea>
<button type="submit" name="add_medicine">Add Medicine</button>
</form>
<?php } ?>

<!-- View Medicines -->
<?php if($page=='view_medicines'){ ?>
<table>
<tr><th>#</th><th>Name</th><th>Dosage</th><th>Quantity</th><th>MRP (₹)</th><th>Expiry Date</th><th>Description</th><th>Action</th></tr>
<?php
$count=1;
if(mysqli_num_rows($medicines_result)>0){
while($row=mysqli_fetch_assoc($medicines_result)){
echo "<tr>";
echo "<td>".$count++."</td>";
echo "<td>".$row['name']."</td>";
echo "<td>".$row['dosage']."</td>";
echo "<td>".$row['quantity']."</td>";
echo "<td>".$row['mrp']."</td>";
echo "<td>".$row['expiry_date']."</td>";
echo "<td>".$row['description']."</td>";
echo "<td><a href='dashboard.php?page=view_medicines&delete_med=".$row['id']."' onclick=\"return confirm('Are you sure?');\" style='color:red;'>Delete</a></td>";
echo "</tr>";
}
}else{ echo "<tr><td colspan='8'>No medicines added yet.</td></tr>"; }
?>
</table>
<?php } ?>
</div>
</body>
<script>
function openSidebar(){
    document.querySelector('.sidebar').classList.remove('mobile-closed');
    document.querySelector('.sidebar').classList.add('mobile-open');
}
function closeSidebar(){
    document.querySelector('.sidebar').classList.remove('mobile-open');
    document.querySelector('.sidebar').classList.add('mobile-closed');
}

// Optional: close sidebar if user clicks outside it (mobile)
document.addEventListener('click', function(e){
    const sidebar = document.querySelector('.sidebar');
    const toggleBtn = document.querySelector('.toggle-btn');
    if(window.innerWidth <= 768 && sidebar.classList.contains('mobile-open')){
        if(!sidebar.contains(e.target) && !toggleBtn.contains(e.target)){
            closeSidebar();
        }
    }
});
</script>

</html>
