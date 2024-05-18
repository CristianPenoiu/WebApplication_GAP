<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: index.php");
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <title>Change Profile</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="admin_home.php">Logo</a></p>
        </div>

        <div class="right-links">
            <a href="#">Change Profile</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
    <div class="box form-box">
        <?php 
           if(isset($_POST['submit'])){
            $username = $_POST['username'];
            $email = $_POST['email'];
            $CNP = $_POST['cnp'];
            $new_password = $_POST['new_password'];
            $confirm_new_password = $_POST['confirm_new_password'];

            // Verificare dacă parolele noi coincid și au cel puțin 8 caractere, doar dacă acestea au fost completate
            if(!empty($new_password) && ($new_password !== $confirm_new_password || strlen($new_password) < 8)) {
                echo "<div class='message'>
                          <p>New passwords do not match or do not meet the minimum length requirement!</p>
                      </div> <br>";
                      echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
            } else {
                $id = $_SESSION['id'];

                // Actualizare baza de date cu noile date de profil și parolă, doar dacă acestea au fost completate
                $update_query = "UPDATE users SET Username='$username', Email='$email', CNP='$CNP'";
                if(!empty($new_password)) {
                    $update_query .= ", Password='$new_password'";
                }
                $update_query .= " WHERE Id=$id";

                $edit_query = mysqli_query($con, $update_query) or die("Error occurred");

                if($edit_query){
                    echo "<div class='message'>
                          <p>Profile Updated!</p>
                      </div> <br>";
                    echo "<a href='admin_home.php'><button class='btn'>Go Home</button>";
                }
            }
           }else{
            $id = $_SESSION['id'];
            $query = mysqli_query($con,"SELECT * FROM users WHERE Id=$id ");

            while($result = mysqli_fetch_assoc($query)){
                $res_Uname = $result['Username'];
                $res_Email = $result['Email'];
                $res_CNP = $result['CNP'];
            }

        ?>
        <header>Change Profile</header>
        <form action="" method="post">
            <div class="field input">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="<?php echo $res_Uname; ?>" autocomplete="off" required>
            </div>

            <div class="field input">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" value="<?php echo $res_Email; ?>" autocomplete="off" required>
            </div>

            <div class="field input">
                <label for="cnp">CNP</label>
                <input type="text" name="cnp" id="cnp" value="<?php echo $res_CNP; ?>" autocomplete="off" required>
            </div>

            <div class="field input">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" autocomplete="off">
            </div>

            <div class="field input">
                <label for="confirm_new_password">Confirm New Password</label>
                <input type="password" name="confirm_new_password" id="confirm_new_password" autocomplete="off">
            </div>
            
            <div class="field">
                <input type="submit" class="btn" name="submit" value="Update">
            </div>
            
        </form>
    </div>
    <?php } ?>
  </div>
