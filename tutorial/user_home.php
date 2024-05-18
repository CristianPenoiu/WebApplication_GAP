<?php 
   session_start();

   include("php/config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
   }

   $id = $_SESSION['id'];
   $query = mysqli_query($con,"SELECT * FROM users WHERE Id=$id");
   $user = mysqli_fetch_assoc($query);

   $res_Uname = $user['Username'];
   $res_Email = $user['Email'];
   $res_CNP = $user['CNP'];
   $res_id = $user['Id'];
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
            font-size: 24px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="user_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <?php 
                echo "<a href='edit_user.php?Id=$res_id'>Change Profile</a>";
            ?>
            <a href="request_consultation.php">Consultation Schedule</a>
            <a href="my_consultation.php">My Consultations</a>
            <a href="chat.php">Chat</a>
            <a href="generate_report.php">Report Generator</a>
            <a href="upload_documents.php">Upload Document</a>
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <main class="welcome-message">
        <div class="welcome-text">
            Welcome, <?php echo $res_Uname; ?>!
        </div>
    </main>
</body>
</html>
