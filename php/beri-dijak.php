<?php
// Check existence of id parameter before processing further
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
    // Include config file
    require_once "config.php";

    if(isset($_GET['id'])){
        $id = $_GET['id'];
        $vhod = "admin";
    }else {
        if(session_id() == '' || !isset($_SESSION) || session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $id = $_SESSION["id"];
        $vhod = "dijak";
    }
    // Prepare a select statement
    $sql = "SELECT * FROM dijaki WHERE id_dijaki = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "i", $param_id);
        
        // Set parameters
        $param_id = trim($_GET["id"]);
        
        // Attempt to execute the prepared statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
    
            if(mysqli_num_rows($result) == 1){
                /* Fetch result row as an associative array. Since the result set
                contains only one row, we don't need to use while loop */
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                
                // Retrieve individual field value
                $name = $row["ime"];
                $address = $row["Priimek"];
                $salary = $row["mail"];
            } else{
                // URL doesn't contain valid id parameter. Redirect to error page
                header("location: error-dijaki.php");
                exit();
            }
            
        } else{
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
     
    // Close statement
    mysqli_stmt_close($stmt);
    
    // Close connection
    mysqli_close($link);
} else{
    // URL doesn't contain id parameter. Redirect to error page
    header("location: error-dijaki.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $vhod = trim($_POST['vhod']);
    if(!empty($_POST['getId'])){
        $id = $_POST['getId'];
    }

    if(!empty($_POST['lang'])) {    
        foreach($_POST['lang'] as $value){


            $sql = "INSERT INTO dijakPredmet (id_dijaki, id_predmet) VALUES (?, ?)";
     
            if($stmt = mysqli_prepare($link, $sql)){
                mysqli_stmt_bind_param($stmt, "ii", $par_dijak, $par_predmet);
                
                $par_dijak = $id;
                $par_predmet = $value;
                if(mysqli_stmt_execute($stmt)){
                    
                } else{
                    echo "Oops! Something went wrong. Please try again later.";
                }
            }
            mysqli_stmt_close($stmt);
        }
        if($vhod == "dijak"){
            header("location: mainPage.php");
        } else{
            header("location: dijaki.php");
        }
        exit();

}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
        .btn{
            background: #fa941d !important;
            border: none;
        }
    </style>
</head>
<body>
<?php include('header.php') ?>

    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="mt-5 mb-3">Poglej dijaka</h1>
                    <div class="form-group">
                        <label>Ime</label>
                        <p><b><?php echo $row["ime"]; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Priimek</label>
                        <p><b><?php echo $row["Priimek"]; ?></b></p>
                    </div>
                    <div class="form-group">
                        <label>Mail</label>
                        <p><b><?php echo $row["mail"]; ?></b></p>
                    </div>
                    <p><a href="dijaki.php" class="btn btn-primary">Nazaj</a></p>
                </div>
            </div>        
        </div>
    </div>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="POST">
    <?php
    require_once "config.php";
    if(isset($_GET['id'])){
        $id = $_GET['id'];
    }else {
        $id = $_SESSION["id"];
    }
    
    $sql = "SELECT DISTINCT p.*
            FROM dijakPredmet d
            RIGHT JOIN predmeti p
            ON d.id_predmet = p.id_predmet AND id_dijaki = $id
            WHERE d.id_predmet IS NULL;";
    if($result = mysqli_query($link, $sql)){
        if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_array($result)){
                        echo '<div class="predmetForm">';
                        echo $row['ime_predmeta'];
                        echo '<input type="checkbox" class="checkMark" name="lang[]" value="'.$row['id_predmet'].'"></div>';
                }
            mysqli_free_result($result);
        } else{
            echo '<div class="alert alert-danger"><em>??estitam izbral si vse predmete.</em></div>';
        }
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }
    mysqli_close($link);
    
    echo '<input type="submit" class="predmetUredi" value="Dodaj">';

    if(isset($_GET['id'])){
        echo '<a href="dijaki.php" class="predmetUredi">Nazaj</a>
                <input type="text" name="vhod" hidden value="admin">
                <input type="number" name="getId" hidden value="'.$_GET['id'].'">';
    } else{
        echo '<a href="mainPage.php" class="predmetUredi">Nazaj</a>
                <input type="text" name="vhod" hidden value="dijak">';
    }
    ?>
    
    </form>
</body>
</html>