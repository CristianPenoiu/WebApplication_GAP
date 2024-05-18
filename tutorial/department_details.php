<?php 
session_start();

include("php/config.php");
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
}

// Verificăm dacă s-a transmis un ID de departament în URL
if(isset($_GET['Id'])){
    // Obținem ID-ul departamentului din parametrul URL
    $department_id = $_GET['Id'];

    // Interogăm baza de date pentru a obține informațiile despre departamentul specific
    $query = "SELECT * FROM departments WHERE department_id = $department_id";
    $result = mysqli_query($con, $query);

    // Verificăm dacă există un rezultat
    if(mysqli_num_rows($result) > 0){
        $department = mysqli_fetch_assoc($result);
        $department_name = $department['department_name'];
        $department_details = $department['description']; // Adăugăm descrierea departamentului
    } else {
        echo "Department not found.";
        exit();
    }

    // Interogăm baza de date pentru a obține angajații asignați la departamentul respectiv
    $employee_query = "
        SELECT users.Username 
        FROM employee_departments 
        JOIN users ON employee_departments.employee_id = users.id 
        WHERE employee_departments.department_id = $department_id";
        
    $employee_result = mysqli_query($con, $employee_query);
    $employees = [];
    if(mysqli_num_rows($employee_result) > 0){
        while($employee = mysqli_fetch_assoc($employee_result)){
            $employees[] = $employee;
        }
    }
} else {
    echo "Department ID not provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Department Details</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="admin_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <a href="departments.php">Add Department</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box">
            <h2>Department Details</h2>
            <p><strong>Department Name:</strong> <?php echo $department_name; ?></p>
            <p><strong>Details:</strong> <?php echo $department_details; ?></p> <!-- Afișăm descrierea departamentului -->

            <!-- Afișăm angajații asignați la departament -->
            <h3>Assigned Employees</h3>
            <?php if(!empty($employees)): ?>
                <ul>
                    <?php foreach($employees as $employee): ?>
                        <li><?php echo $employee['Username']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No employees assigned to this department.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
