<?php 
   session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Login</title>
</head>
<body>
<div class="container">
    <div class="box form-box">
        <?php 
        include("php/config.php");

        // Afișăm mesajul de deconectare dacă există
        if(isset($_SESSION['logout_message'])) {
            echo "<div class='message'>" . $_SESSION['logout_message'] . "</div>";
            unset($_SESSION['logout_message']); // Eliminăm mesajul după afișare
        }

        if(isset($_POST['submit'])){
            $username = mysqli_real_escape_string($con,$_POST['username']);
            $password = mysqli_real_escape_string($con,$_POST['password']);

            $result = mysqli_query($con,"SELECT * FROM users WHERE Username='$username' AND Password='$password' ") or die("Select Error");
            $row = mysqli_fetch_assoc($result);

            if(is_array($row) && !empty($row)){
                $_SESSION['valid'] = $row['Email'];
                $_SESSION['username'] = $row['Username'];
                $_SESSION['cnp'] = $row['CNP'];
                $_SESSION['id'] = $row['Id'];
                $_SESSION['role_id'] = $row['role_id'];
                
                // Verificăm rolul utilizatorului și redirecționăm către pagina corectă
                if($row['role_id'] == 1) { // Employee
                    header("Location: admin_home.php");
                } elseif ($row['role_id'] == 2) { // Admin
                    header("Location: employee_home.php");
                } else { // User
                    header("Location: user_home.php");
                }
                exit();
            } else {
                echo "<div class='message'>
                          <p>Wrong Username or Password</p>
                      </div> <br>";
                echo "<a href='index.php'><button class='btn'>Go Back</button></a>";
            }
        }
        ?>
        <header>Login</header>
        <form action="" method="post">
            <div class="field input">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" autocomplete="off" required>
            </div>

            <div class="field input">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" autocomplete="off" required>
            </div>

            <div class="field">
                <input type="submit" class="btn" name="submit" value="Login" required>
            </div>
            <div class="links">
                Don't have an account? <a href="register.php">Sign Up Now</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
