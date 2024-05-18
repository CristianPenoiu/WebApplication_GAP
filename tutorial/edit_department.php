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
        $description = $department['description']; // Adăugăm descrierea departamentului
    } else {
        // Dacă nu există niciun departament cu ID-ul specificat, putem afișa un mesaj de eroare sau redirecționa către o altă pagină
        echo "Department not found.";
        exit(); // Opriți executarea scriptului pentru a evita afișarea conținutului restului paginii
    }
} else {
    // Dacă nu s-a transmis niciun ID de departament în URL, putem afișa un mesaj de eroare sau redirecționa către o altă pagină
    echo "Department ID not provided.";
    exit(); // Opriți executarea scriptului pentru a evita afișarea conținutului restului paginii
}

// Verificăm dacă s-a trimis un formular pentru actualizarea departamentului
if(isset($_POST['submit'])){
    // Procesăm datele trimise din formular
    $new_department_name = mysqli_real_escape_string($con, $_POST['department_name']);
    $new_description = mysqli_real_escape_string($con, $_POST['description']);

    // Actualizăm detaliile departamentului în baza de date
    $update_query = "UPDATE departments SET department_name = '$new_department_name', description = '$new_description' WHERE department_id = $department_id";
    if(mysqli_query($con, $update_query)) {
        echo "<div class='message'>
                  <p>Department updated successfully!</p>
              </div> <br>";
    } else {
        echo "<div class='message'>
                  <p>Error: " . mysqli_error($con) . "</p>
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
    <title>Edit Department</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="admin_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <a href="add_department.php">Add Department</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box form-box">
            <header>Edit Department</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="department_name">Department Name</label>
                    <input type="text" name="department_name" id="department_name" value="<?php echo $department_name; ?>" autocomplete="off" required>
                </div>
                <div class="field input">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="4" cols="50" required><?php echo $description; ?></textarea>
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Update Department" required>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
