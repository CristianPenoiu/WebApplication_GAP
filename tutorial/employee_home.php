<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
   }

   $id = $_SESSION['id'];
   $query = mysqli_query($con, "SELECT * FROM users WHERE Id=$id");
   $user = mysqli_fetch_assoc($query);

   $res_Uname = $user['Username'];
   $res_Email = $user['Email'];
   $res_CNP = $user['CNP'];
   $res_id = $user['Id'];

   // Obținem numele departamentului pentru employee
   $department_query = "SELECT d.department_name FROM employee_departments ed
                        JOIN departments d ON ed.department_id = d.department_id
                        WHERE ed.employee_id = '$id'";
   $department_result = mysqli_query($con, $department_query);
   $department = mysqli_fetch_assoc($department_result);
   $department_name = $department['department_name'];
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
        .department-text {
            font-size: 24px; /* text size increased */
            margin-top: 10px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="employee_home.php">Logo</a> </p>
        </div>
        <div class="right-links">
            <?php 
                echo "<a href='edit_employee.php?Id=$res_id'>Change Profile</a>";
            ?>
            <a href="manage_documents_employee.php">Manage Documents</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <main class="welcome-message">
        <div class="welcome-text">
            Welcome, <?php echo $res_Uname; ?>!
            <div class="department-text">
            Part of the <?php echo $department_name; ?> department.
            </div>
        </div>
    </main>
</body>
</html>
