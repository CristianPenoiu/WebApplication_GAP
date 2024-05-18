<?php 
session_start();

include("php/config.php");
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

$id = $_SESSION['id'];
$query = mysqli_query($con,"SELECT * FROM users WHERE Id=$id");

while($result = mysqli_fetch_assoc($query)){
    $res_Uname = $result['Username'];
    $res_Email = $result['Email'];
    $res_CNP = $result['CNP'];
    $res_id = $result['Id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Home</title>
    <style>
        .welcome-message {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            text-align: center;
        }
        .welcome-text {
            font-size: 32px; /* text size increased */
            line-height: 1.5;
        }
        .mayor-text {
            font-size: 24px; /* text size increased */
            margin-top: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="admin_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <?php 
                echo "<a href='edit.php?Id=$res_id'>Change Profile</a>";
            ?>
            <a href="departments.php">Departments</a> 
            <a href="employee_assign.php">Employees</a>
            <a href="manage_documents.php">Manage Documents</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <main class="welcome-message">
        <div class="welcome-text">
            Welcome, Mayor <?php echo $res_Uname; ?>!
        </div>
    </main>
</body>
</html>
