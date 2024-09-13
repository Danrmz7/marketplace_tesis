<nav class="navbar navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
    <a class="navbar-brand" href="./"><b>Marke</b>tPlace</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <nav class="navbar bg-body-tertiary">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="./">Home</a>
                    </li>
                    <li class="nav-item">

                    <a class="nav-link" href="./?action=view_purchases">Mis Compras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Recompensas</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="./?action=view_cart">Carrito</a>
                    </li> -->
                    <li class="nav-item">
                        <!-- <a class="nav-link disabled" aria-disabled="true">Disabled</a> -->
                        <a class="nav-link" href="./?logout=1">Cerrar Sesión De <?php echo $get_user['nombre_comprador']; ?></a>
                    </li>
                    
                </ul>
            
            
        </div>

        <div class="navbar bg-body-tertiary">
                <a class="nav-link" href="#"><img src="_assets/img/coin.png" width="20"> <?php print $get_user['dino_coins']; ?></a>
                <a class="nav-link" href="./?action=view_cart">&nbsp; <i class="fa-solid fa-cart-shopping"></i> (<?php print $cart->getTotalItem(); ?>)</a>
        </div>
 
    </div>
</nav>