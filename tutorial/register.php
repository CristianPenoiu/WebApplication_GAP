<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Register</title>
</head>
<body>
<div class="container">
    <div class="box form-box">
    <?php
    include("php/config.php");
    if(isset($_POST['submit'])){
        $username = $_POST['username'];
        $email = $_POST['email'];
        $CNP = $_POST['CNP'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role_id = $_POST['role_id'];

        // Verificare dacă parola are cel puțin 8 caractere
        if(strlen($password) < 8) {
            echo "<div class='message'>
                      <p>Password must be at least 8 characters long!</p>
                  </div> <br>";
            echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
        } else {
            // Verificare parolă confirmată
            if($password !== $confirm_password) {
                echo "<div class='message'>
                          <p>Passwords do not match!</p>
                      </div> <br>";
                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
            } else {
                // Verificare email unic
                $verify_query = mysqli_query($con, "SELECT Email FROM users WHERE Email='$email'");
                if(mysqli_num_rows($verify_query) != 0 ){
                    echo "<div class='message'>
                              <p>This email is already in use. Please try another one!</p>
                          </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                } else {
                    // Verificare CNP unic
                    $verify_cnp_query = mysqli_query($con, "SELECT CNP FROM users WHERE CNP='$CNP'");
                    if(mysqli_num_rows($verify_cnp_query) != 0 ){
                        echo "<div class='message'>
                                  <p>This CNP is already associated with a username. Please try another one!</p>
                              </div> <br>";
                        echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                    } else {
                        // Verificare username unic
                        $verify_username_query = mysqli_query($con, "SELECT Username FROM users WHERE Username='$username'");
                        if(mysqli_num_rows($verify_username_query) != 0 ){
                            echo "<div class='message'>
                                      <p>This username is already in use. Please try another one!</p>
                                  </div> <br>";
                            echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                        } else {
                            // Verificare rol admin existent
                            if ($role_id == 1) {
                                $admin_query = mysqli_query($con, "SELECT * FROM users WHERE role_id = 1");
                                if (mysqli_num_rows($admin_query) > 0) {
                                    echo "<div class='message'>
                                              <p>An admin already exists. You cannot assign this role.</p>
                                          </div> <br>";
                                    echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                                    exit();
                                }
                            }

                            // Verificare parolă unică 
                            $verify_password_query = mysqli_query($con, "SELECT * FROM users WHERE Password='$password'");
                            if (mysqli_num_rows($verify_password_query) != 0) {
                                echo "<div class='message'>
                                          <p>This password has already been used. Please choose a different one!</p>
                                      </div> <br>";
                                echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button></a>";
                            } else {
                                // Inserare utilizator nou
                                $insert_query = "INSERT INTO users(Username,Email,CNP,Password,role_id) VALUES('$username','$email','$CNP','$password','$role_id')";
                                if(mysqli_query($con, $insert_query)) {
                                    echo "<div class='message'>
                                              <p>Registration successful!</p>
                                          </div> <br>";
                                    echo "<a href='index.php'><button class='btn'>Login Now</button></a>";
                                } else {
                                    echo "<div class='message'>
                                              <p>Error: " . mysqli_error($con) . "</p>
                                          </div> <br>";
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
    ?>

            <header>Sign Up</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="CNP">CNP</label>
                    <input type="text" name="CNP" id="age" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="role_id">Role</label>
                    <select name="role_id" id="role_id" required>
                        <option value="" disabled selected>Select Role</option>
                        <?php
                        $role_query = mysqli_query($con,"SELECT * FROM roles");
                        while($row = mysqli_fetch_assoc($role_query)) {
                            echo "<option value='".$row['id']."'>".$row['role_type']."</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
                <div class="links">
                    Already a member? <a href="index.php">Sign In</a>
                </div>
            </form>
        </div>

    <?php } ?>

</div>
</body>
</html>
