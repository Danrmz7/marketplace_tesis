<?php
error_reporting(0);
//error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 1);

if (isset($_GET['sess']) ){
    session_id($_GET['sess']);
}
session_start();
header("Cache-control: private");

    $database_name = "tesisg";

    // secure class data
    $table_users   = "compradores";
    $idField       = 'id_comprador';
    $unField       = 'correo_comprador';
    $psField       = 'contrasena_comprador';


    require('phpCommon/BlockXSS.class.php');
    require('phpCommon/MySqlPdoHandler.class.php');
    require('phpCommon/addStripSlashes.php');
    require('phpCommon/SecureV2.class.php');
    require('class.Cart.php');
    require('market.class.php');
    // require('products.class.php');

    $MySqlHandler =  MySqlPdoHandler::getInstance(); 
    $MySqlHandler->connect($database_name);
    $MySqlHandler->Query("SET NAMES utf8");

    $secure      = new Secure($MySqlHandler, $database_name, $table_users, $unField, $psField, $idField, 'remember_me' , '', 'pos_secure', 'login.php', true);
   
    $cart        = new Cart([
        // Can add unlimited number of item to cart
        'cartMaxItem'      => 0,
        
        // Set maximum quantity allowed per item to 99
        'itemMaxQuantity'  => 99,
        
        // Do not use cookie, cart data will lost when browser is closed
        'useCookie'        => false,
      ]);
    // $Productos      = new Products($MySqlHandler/*, $secure->getCurrentUser()*/);
    $Market      = new Market($MySqlHandler, $secure->getCurrentUser(), $cart);


    if($get_user = $secure->getCurrentUser()){

        if($get_user['active']==1){
    
        }else{
            header('Location: ./?logout=1');
            die();
        }
    }

    

?>