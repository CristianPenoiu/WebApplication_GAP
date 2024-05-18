<?php 
session_start();

include("php/config.php");
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

// Verificăm dacă s-a trimis un formular pentru asignarea unui angajat la un departament
if(isset($_POST['submit'])){
    // Procesăm datele trimise din formular
    $employee_id = isset($_POST['employee_id']) ? mysqli_real_escape_string($con, $_POST['employee_id']) : '';
    $department_id = isset($_POST['department_id']) ? mysqli_real_escape_string($con, $_POST['department_id']) : '';

    // Verificăm dacă există un utilizator cu acest ID în tabela users și role_id=2
    $check_user_query = "SELECT id, Username FROM users WHERE id = '$employee_id' AND role_id = 2";
    $user_result = mysqli_query($con, $check_user_query);
    
    if(mysqli_num_rows($user_result) > 0) {

        // Verificăm dacă angajatul este deja asignat unui departament
        $check_assignment_query = "SELECT * FROM employee_departments WHERE employee_id = '$employee_id'";
        $assignment_result = mysqli_query($con, $check_assignment_query);

        if (mysqli_num_rows($assignment_result) > 0) {
            // Angajatul este deja asignat unui departament
            echo "<div class='message'><p>Error: Employee is already assigned to a department!</p></div><br>";
        } else {
            // Angajatul nu este asignat niciunui departament, putem să inserăm relația angajat-departament în baza de date
            $insert_query = "INSERT INTO employee_departments (employee_id, department_id, start_date, end_date) VALUES ('$employee_id', '$department_id', NOW(), NULL)";
            if(mysqli_query($con, $insert_query)) {
                echo "<div class='message'><p>Employee assigned to department successfully!</p></div><br>";
            } else {
                echo "<div class='message'><p>Error: " . mysqli_error($con) . "</p></div><br>";
            }
        }
    } else {
       
        // Utilizatorul nu există, afișăm un mesaj de eroare
        echo "<div class='message'><p>Error: User not found!</p></div><br>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Assign Employee to Department</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="admin_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <a href="add_department.php">Add Department</a>
            <a href="php/logout.php"><button class="btn">Log Out</button></a>
        </div>
    </div>
    <div class="container">
        <div class="box form-box">
            <header>Assign Employee to Department</header>
            <form action="" method="post">
                <div class="field">
                    <label for="employee_id">Employee</label>
                    <select name="employee_id" id="employee_id" required>
                        <?php 
                        // Interogare SQL pentru a selecta toți angajații cu role_id=2
                        $query = "SELECT id, Username FROM users WHERE role_id = 2";
                        $result = mysqli_query($con, $query);

                        // Verificăm dacă există rezultate
                        if (mysqli_num_rows($result) > 0) {
                            // Afisăm angajații sub formă de opțiuni într-o listă derulantă
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['Username']) . "</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No employees found</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="field">
                    <label for="department_id">Department</label>
                    <select name="department_id" id="department_id" required>
                        <?php 
                        // Interogare SQL pentru a selecta toate departamentele
                        $query = "SELECT department_id, department_name FROM departments";
                        $result = mysqli_query($con, $query);

                        // Verificăm dacă există rezultate
                        if (mysqli_num_rows($result) > 0) {
                            // Afisăm departamentele sub formă de opțiuni într-o listă derulantă
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='" . $row['department_id'] . "'>" . htmlspecialchars($row['department_name']) . "</option>";
                            }
                        } else {
                            echo "<option value='' disabled>No departments added yet</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Assign Employee">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
