<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/header.css">
</head>
<body>
    <header>
        <h1 ><a href="mainPage.php" class="zribs"> ADASTRA</a></h1>
        <div style="width: 100%"></div>
        <?php
        if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $status = $_SESSION['status'];
        if($status === 'a'){
            echo '<a href="admin.php" class="logout">ADMIN</a>';
        }
        if($status === 'd'){
            echo '<a href="profil.php" class="logout">Moj profil</a>';
            
        }
        ?>
        <a href="logout.php" class="logout">Odjavi se</a>
    </header>
</body>
</html>