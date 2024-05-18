<?php
session_start();
include("php/config.php");

if(!isset($_SESSION['valid'])){
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['id']; // Presupunem că id-ul utilizatorului este stocat în sesiune

// Interogăm baza de date pentru a obține consultațiile utilizatorului curent
$query = "SELECT * FROM consultations WHERE user_id = '$user_id' ORDER BY preferred_time DESC";
$result = mysqli_query($con, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <link rel="stylesheet" href="style/table_style.css">
    <title>My Consultations</title>
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
        <div class="box">
            <h2>My Consultations</h2>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Consultation Date</th>
                            <th>Time</th>
                            <th>Consultant Preferences</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo date('d-m-Y', strtotime($row['preferred_time'])); ?></td>
                                <td><?php echo date('H:i', strtotime($row['preferred_time'])); ?></td>
                                <td><?php echo htmlspecialchars($row['consultant_preferences']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no scheduled consultations.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
