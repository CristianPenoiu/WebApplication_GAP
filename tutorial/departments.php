<?php 
session_start();

include("php/config.php");
if(!isset($_SESSION['valid'])){
    header("Location: index.php");
}

// Verificăm dacă s-a trimis un formular pentru adăugarea unui nou departament
if(isset($_POST['submit'])){
    // Procesăm datele trimise din formular
    $department_name = mysqli_real_escape_string($con, $_POST['department_name']);
    $description = mysqli_real_escape_string($con, $_POST['description']); // Adăugăm detalii

    // Verificăm dacă numele departamentului nu este gol
    if(!empty($department_name)) {
        // Inserăm noul departament în baza de date
        $insert_query = "INSERT INTO departments (department_name, description) VALUES ('$department_name', '$description')"; // Adăugăm detalii
        if(mysqli_query($con, $insert_query)) {
            echo "<div class='message'>
                      <p>Department added successfully!</p>
                  </div> <br>";
        } else {
            echo "<div class='message'>
                      <p>Error: " . mysqli_error($con) . "</p>
                  </div> <br>";
        }
    } else {
        echo "<div class='message'>
                  <p>Department name cannot be empty!</p>
              </div> <br>";
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
    <title>Add Department</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="admin_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <a href="departments.php">Add Department</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
            <a href="employee_assign.php">Employee</a>
        </div>
    </div>
    <div class="container">
        <div class="box form-box">
            <header>Add Department</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="department_name">Department Name</label>
                    <input type="text" name="department_name" id="department_name" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="description">Description</label>
                    <input name="description" id="description"  autocomplete="off" required></input>
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Add Department" required>
                </div>
            </form>
        </div>

        <!-- Afisarea departamentelor existente -->
        <div class="box">
            <h2>Existing Departments</h2>
            <?php 
            // Interogare SQL pentru a selecta toate departamentele
            $query = "SELECT * FROM departments";
            $result = mysqli_query($con, $query);

            // Verificăm dacă există rezultate
            if (mysqli_num_rows($result) > 0) {
                // Afisăm departamentele sub formă de listă
                echo "<ul>";
                while ($row = mysqli_fetch_assoc($result)) {
                    // Adăugăm un link către pagina de detalii a departamentului
                    echo "<li><a href='department_details.php?Id=" . $row['department_id'] . "'>" . $row['department_name'] . "</a> <a href='edit_department.php?Id=" . $row['department_id'] . "'>(Edit)</a></li>";

                }
                echo "</ul>";
            } else {
                echo "No departments added yet.";
            }
            ?>
        </div>
    </div>
</body>
</html>
