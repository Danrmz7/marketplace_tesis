<?php
include ('_assets/classes/header.inc.php');
$Market->setData();
$process = $Market->process();
/*$index ->setData();
$process = $index ->process();*/
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- favicon/relicon -->
    <link rel="shortcut icon" href="_assets/img/favicon.png">

    <!-- css -->
    <link rel="stylesheet" href="_assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <?php include ("_assets/includes/navbar.inc.php") ?>
    <!-- <div style=" background: url(https://www.code-brew.com/wp-content/uploads/2016/12/marketplace.png) no-repeat fixed center;-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;height:140px;width:100%;"></div> -->
    <div class="container">
        <?php print $process; ?>


        <hr>
        <footer>
            <div class="row" style="padding:20px;">
                <div class="col">
                    Irving Daniel Ramirez<br>
                    <strong>Catal</strong>Place Tesis 2024
                </div>
                <div class="col">
                    <strong>Universidad Autónoma de Ciudad Juárez</strong><br>
                    Seminario De Titulación II
                </div>
            </div>
        </footer>

    </div>

    <script src="_assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>



<?php
include ('_assets/classes/header.inc.php');
$index->setData();
$process = $index->process();
/*$index ->setData();
$process = $index ->process();*/
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <!-- favicon/relicon -->
    <link rel="shortcut icon" href="_assets/img/favicon.png">

    <!-- css -->
    <link rel="stylesheet" href="_assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body>
    <?php include ("_assets/includes/navbar.inc.php") ?>
    <!-- <div style=" background: url(https://www.code-brew.com/wp-content/uploads/2016/12/marketplace.png) no-repeat fixed center;-webkit-background-size: cover;-moz-background-size: cover;-o-background-size: cover;background-size: cover;height:140px;width:100%;"></div> -->
    <div class="container">
        <?php print $process; ?>


        <hr>
        <footer>
            <div class="row" style="padding:20px;">
                <div class="col">
                    Irving Daniel Ramirez<br>
                    <strong>Market</strong>Place Tesis 2024
                </div>
                <div class="col">
                    <strong>Universidad Autónoma de Ciudad Juárez</strong><br>
                    Seminario De Titulación II
                </div>
            </div>
        </footer>

    </div>

    <script src="_assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>