<?php
session_start();
include "db.php";

/* ===== ONLY DOCTOR ===== */
if(!isset($_SESSION['staff_role']) || $_SESSION['staff_role']!="Doctor"){
    header("Location: staff_login.php");
    exit();
}

/* ===== PATIENT ID CHECK ===== */
if(!isset($_GET['id'])){
    die("No patient selected");
}

$id = intval($_GET['id']);
$q  = mysqli_query($conn,"SELECT * FROM patients WHERE id=$id");

if(mysqli_num_rows($q)!=1){
    die("Patient not found");
}

$patient = mysqli_fetch_assoc($q);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Prescription | <?= htmlspecialchars($patient['name']) ?></title>
<style>
/* Copy your existing CSS here from your template */
body{
    margin:0;
    padding:20px;
    background:#ddeee3;
    font-family: Arial, Helvetica, sans-serif;
}

.paper{
    width:210mm;
    height:297mm;
    margin:auto;
    position:relative;
    overflow:hidden;
    border-radius:10px;
    background:
      repeating-linear-gradient(
        45deg,
        rgba(25,135,84,0.035),
        rgba(25,135,84,0.035) 10px,
        transparent 10px,
        transparent 20px
      ),
      #f2fbf6;
    box-shadow:0 0 18px rgba(0,0,0,0.25);
}
.paper::after{
    content:"EYE & ENT CLINIC";
    position:absolute;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%) rotate(-30deg);
    font-size:85px;
    font-weight:700;
    color:rgba(25,135,84,0.06);
    white-space:nowrap;
    pointer-events:none;
}
.header{
    background:linear-gradient(90deg,#198754,#20c997);
    color:#fff;
    padding:20px;
}
.header h1{margin:0;font-size:36px;letter-spacing:1px;}
.header p{margin:6px 0 0;font-size:16px;}
.doctor-bar{
    background:#ffffffcc;
    backdrop-filter:blur(2px);
    display:flex;
    justify-content:space-between;
    padding:14px 20px;
    border-bottom:2px solid #198754;
    font-size:16px;
}
.doctor-bar strong{color:#198754;font-size:18px;}
.patient{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:14px;
    padding:18px 20px;
}
.box{
    background:#ffffff;
    border-left:5px solid #198754;
    padding:9px;
    font-size:16px;
    min-height:32px;
}
.content{
    display:flex;
    padding:0 20px;
    height:calc(100% - 320px);
}
.left{width:45%;padding-right:16px;font-size:16px;line-height:1.8;}
.left div{margin-bottom:10px;}
table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
    background:#fff;
    font-size:15.5px;
}
th{background:#198754;color:#fff;padding:8px;}
td{border:1px solid #198754;padding:8px;text-align:center;height:34px;}
.right{width:55%;padding-left:18px;border-left:2px dotted #198754;font-size:16px;}
.title{font-size:18px;font-weight:700;color:#198754;margin-bottom:10px;}
.write-space{height:170px;border-bottom:1px dashed #aaa;}
.footer{
    position:absolute;
    bottom:0;
    left:0;
    right:0;
    background:#198754;
    color:#fff;
    text-align:center;
    padding:12px;
    font-size:15px;
}

.print-btn{
    position:fixed;
    top:20px;
    right:20px;
    background:#198754;
    color:#fff;
    border:none;
    padding:10px 18px;
    border-radius:6px;
    cursor:pointer;
}
@media print{
    body{background:none;padding:0;}
    .paper{box-shadow:none;border-radius:0;}
    .print-btn{display:none;}
}

@media print{
    body{background:none;padding:0;}
    .paper{box-shadow:none;border-radius:0;}
}
</style>
</head>

<body>


<button class="print-btn" onclick="window.print()">🖨 Print</button>
<div class="paper">

    <div class="header">
        <h1>EYE & ENT CLINIC</h1>
        <p>Complete Eye, Ear, Nose & Throat Care</p>
    </div>

    <div class="doctor-bar">
        <div>
            <strong>Dr. Abhay Kumar</strong><br>
            M.S (Ay) IMS, BHU<br>
            आँख, कान, नाक एवं गला रोग विशेषज्ञ
        </div>
        <div style="text-align:right">
            📞 7897609977<br>
            📞 8083993138
        </div>
    </div>

    <div class="patient">
        <div class="box">Name : <?= htmlspecialchars($patient['name']) ?></div>
        <div class="box">Age / Sex : <?= htmlspecialchars($patient['age_sex']) ?></div>
        <div class="box">Date : <?= date("d-m-Y", strtotime($patient['created_at'])) ?></div>
        <div class="box">Address : <?= htmlspecialchars($patient['address']) ?></div>
        <div class="box">Occupation : <?= htmlspecialchars($patient['occupation']) ?></div>
        <div class="box">Mobile : <?= htmlspecialchars($patient['mobile']) ?></div>
    </div>

    <div class="content">

        <div class="left">
            <div>VA :</div>
            <div>VAG :</div>
            <div>Ocular Edema :</div>
            <div>Anterior Segment :</div>
            <div>Fundus :</div>

            <strong>Refraction</strong>
            <table>
                <tr>
                    <th></th>
                    <th>S</th>
                    <th>C</th>
                    <th>A</th>
                    <th>Add</th>
                </tr>
                <tr>
                    <td>RE</td>
                    <td></td><td></td><td></td><td></td>
                </tr>
                <tr>
                    <td>LE</td>
                    <td></td><td></td><td></td><td></td>
                </tr>
            </table>

            <br>
            <div>EAC :</div>
            <div>TM :</div>
            <div>Nasal Septum :</div>
            <div>Nasal Mucosa :</div>
            <div>Inf. Turbinate :</div>
            <div>Tonsil :</div>
            <div>Buccal Mucosa :</div>
        </div>

        <div class="right">
            <div class="title">Chief Complaints(C/O)</div>
            <div class="write-space"></div>

            <div class="title">Treatment / Advice</div>
            <div class="write-space"></div>
        </div>

    </div>

    <div class="footer">
        ⏰ मिलने का समय : सुबह 8 बजे से 11 बजे | शाम 3 बजे से 8 बजे<br>
        📍 स्थान : Chikni Chowk, Saur Bazar, Saharsa<br>
        ☎️ फोन : 7897609977 , 8083993138
    </div>

</div>

</body>
</html>
