<?php
session_start();
include("php/config.php");

if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

if(isset($_POST['submit'])){
    $user_id = $_SESSION['id']; // Presupunem că id-ul utilizatorului este stocat în sesiune
    $preferred_time = mysqli_real_escape_string($con, $_POST['preferred_time']);
    $consultant_preferences = mysqli_real_escape_string($con, $_POST['consultant_preferences']);

    // Validarea datelor de intrare
    if(!empty($preferred_time)){
        // Calculăm intervalul orar de 1 oră
        $start_time = date('Y-m-d H:i:s', strtotime($preferred_time) - 3600); // 1 oră înainte de ora preferată
        $end_time = date('Y-m-d H:i:s', strtotime($preferred_time) + 3600); // 1 oră după ora preferată

        // Verificăm dacă există deja consultații în intervalul de 1 oră
        $conflict_query = "SELECT * FROM consultations 
                           WHERE preferred_time BETWEEN '$start_time' AND '$end_time'";
        $conflict_result = mysqli_query($con, $conflict_query);

        if(mysqli_num_rows($conflict_result) == 0){
            // Nu există conflicte, inserăm cererea în baza de date cu statusul "approved"
            $query = "INSERT INTO consultations (user_id, preferred_time, consultant_preferences, status) 
                      VALUES ('$user_id', '$preferred_time', '$consultant_preferences', 'approved')";
            if(mysqli_query($con, $query)){
                echo "<div class='message'>
                          <p>Consultation requested successfully!</p>
                      </div> <br>";
            } else {
                echo "<div class='message'>
                          <p>Error: " . mysqli_error($con) . "</p>
                      </div> <br>";
            }
        } else {
            // Există un conflict de programare
            echo "<div class='message'>
                      <p>There is already a consultation scheduled within this time</p>
                  </div> <br>";
        }
    } else {
        echo "<div class='message'>
                  <p>Preferred time cannot be empty!</p>
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
    <title>Request Consultation</title>
</head>
<body>
    <div class="nav">
        <div class="logo">
            <p><a href="user_home.php">Logo</a></p>
        </div>
        <div class="right-links">
            <a href="php/logout.php"> <button class="btn">Log Out</button> </a>
        </div>
    </div>
    <div class="container">
        <div class="box form-box">
            <header>Request Consultation</header>
            <form action="request_consultation.php" method="post">
                <div class="field input">
                    <label for="preferred_time">Preferred Time</label>
                    <input type="datetime-local" name="preferred_time" id="preferred_time" required>
                </div>
                <div class="field input">
                    <label for="consultant_preferences">Consultant Preferences</label>
                    <input type="text" name="consultant_preferences" id="consultant_preferences">
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Request Consultation">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
