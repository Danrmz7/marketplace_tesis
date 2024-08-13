<?php
include ('_assets/classes/header.inc.php');
$Market->setData();
$process = $Market->process();
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
</head>
<body>
    <?php include ("_assets/includes/navbar.inc.php") ?>
    <div class="container">
        <?php print $process; ?>
    </div>

    <script src="_assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>