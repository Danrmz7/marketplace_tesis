<?php
/**
 * File Name: Market
 * Creator URI: 
 * Description: Main configuration file
 * Author: 
 * Version: 1.1
 * Author URI: 
 */
class Market {
 
    var $sql;
    var $mail;
    var $current_user;
    var $output;
    var $alert;
    var $recompensas;
  
    /*---------------------------------*/
    public function __construct($sql, $getCurrentUser, $cart){
      
      $this->sql        = $sql;
      $this->_user      = $getCurrentUser;
      $this->cart       = $cart;  
    }
  
    /** 
    * @param action
    * @return null
    */
    public function setData(){
      
      $this->postData       = BlockXSS::sanitizes($_POST);
      $this->getData        = BlockXSS::sanitizes($_GET);
      $this->requestData    = BlockXSS::sanitizes($_REQUEST);
      $this->action         = $this->getData['action'];  
  
    }

    public function process()
    {
        switch($this->action){
            case 'save_product':
                if($this->insert_products())
                {
                    $alert = '
                    <div class="alert alert-success">
                        <strong>Success!</strong> Producto Agregado
                    </div> ';
                    $output .= $this->show_all_rows($alert);
                    return $output;
                }
            break;

            case 'add_product_cart':
                if($this->add_product_cart())
                {
                    $alert = '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Producto Agregado
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div> ';
                    $output .= $this->show_all_rows($alert);
                    return $output;
                }else
                {
                    $alert = '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> Producto NO Agregado
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div> ';
                    $output .= $this->show_all_rows($alert);
                    return $output;
                }
            break;

            case 'eliminate_product_cart':
                if($this->eliminate_product_cart())
                {
                    $alert = '
                    <div class="alert alert-success">
                        <strong>Success!</strong> Producto eliminado
                    </div> ';
                    $output .= $this->show_all_rows($alert);
                    return $output;
                }else
                {
                    $alert = '
                    <div class="alert alert-danger">
                        <strong>Error!</strong> Producto NO eliminado
                    </div> ';
                    $output .= $this->show_all_rows($alert);
                    return $output;
                }
            break;

            case 'destroy_cart':
                if($this->cart->destroy())
                {
                    $alert = '
                    <div class="alert alert-success">
                        <strong>Success!</strong> Carrito Vaciado
                    </div> ';
                }
                $output .= $this->show_all_rows($alert);
                return $output;
            break;

            case 'confirm_form':
                if($this->insert_confirm_form())
                {
                    $alert = '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Compra Realizada
                        <hr>
                        Recompensas Ganadas: <strong>$'.$this->recompensas.'</strong>
                        <a href="./"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                    </div> ';
                    $output .= $this->show_all_rows($alert);
                    return $output;
                }
                else
                {
                    $alert = '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Success!</strong> Compra NO Realizada
                        <a href="./"><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></a>
                    </div> ';
                    $output .= $this->show_all_rows($alert);
                    return $output;
                }
            break;


            default:
                $output.= $this->show_all_rows();
                return $output;
            break;
        }
    }

    /***
     * Funciones SQL
     */

     public function get_products()
     {
        $query = "SELECT * from productos";
        $params_query = array();

        if($rs = $this->sql->select($query, $params_query))
        {
            return $rs;
        }
        else
        {
            return false;
        }
     }

     public function get_product_added($id_prod)
     {
        $query = "SELECT * from productos where id_producto = ?";
        $params_query = array($id_prod);

        if($rs = $this->sql->select($query, $params_query))
        {
            return $rs[0];
        }
        else
        {
            return false;
        }
     }

     public function get_product_details($id_prod)
     {
         $query = "SELECT * from productos where id_producto = ?";
         $params_query = array($id_prod);
 
         if($rs = $this->sql->select($query, $params_query))
         {
             return $rs[0];
         }
         else
         {
             return false;
         }
     }

     public function get_products_info()
     {
         $query = "SELECT * from productos Where id_producto = ?";
         $params_query = array($this->getData['id_product']);
 
         if($rs = $this->sql->select($query, $params_query))
         {
             return $rs;
         }
         else
         {
             return false;
         }
     }


     public function add_product_cart()
     {
 
         if($this->cart->add($this->postData['id_prd'], $this->postData['qty'], [
              'price'  => $this->postData['price_prd'],
            ]))
         {
             return true;
         }
         else
         {
             return false;
         }
     }

     public function eliminate_product_cart()
     {
        if($this->cart->remove($this->postData['id_product']))
        {
            return true;
        }
        else
        {
            return false;
        }
     }

     public function get_reward()
     {
        $query = "SELECT * from recompensas";
        $params_query = array();

        if($rs = $this->sql->select($query, $params_query))
        {
            return $rs;
        }
        else
        {
            return false;
        }
     }



    

     public function insert_confirm_form()
     {        
        $fecha_actual = date("Y-m-d");
        $query = "INSERT INTO ventas (id_comprador,fecha_compra,id_recompensa) VALUES (?,?,0)";
        $params_query = array(
            $this->postData['id_comprador'],
            $fecha_actual,
            // $this->postData['id_recompensa']
        );

        if($this->sql->insert($query, $params_query))
        {
            $query = "SELECT id_venta from ventas where id_comprador = ? order by id_venta DESC Limit 1;";
            $params_query = array($this->_user['id_comprador']);
            
            if ($rs = $this->sql->select($query, $params_query))
            {
                //$this->cart->destroy();
                $allItems = $this->cart->getItems();
                $ultima_venta = $rs[0];
                foreach ($allItems as $items)
                {
                    foreach ($items as $item)
                    {
                        
                        $query = "INSERT INTO carrito_productos (id_carrito, id_producto) VALUES (?,?)";
                        $params_query = array($ultima_venta['id_venta'], $item['id']);
                        if ($this->sql->insert($query, $params_query))
                        {
                            $query = "SELECT * from productos where id_producto = ?";
                            $params_query = array($item['id']);

                            if ($rs = $this->sql->select($query, $params_query))
                            {
                                $productos_comprados = $rs[0];
                                $this->recompensas = $productos_comprados['dino_producto'] * $item['quantity'];
                                // $this->recompensas = $sub_rec + $sub_rec;
                            }

                        }                            
                        
                    }
                }
                $dinero_comprador = $this->_user['dino_coins'];
                $total_compra = $this->cart->getAttributeTotal('price');
                $total_dinero_comprador = $dinero_comprador - $total_compra;
               
                
            }
            else
            {
                return false;
            }


            if ($this->update_money($total_dinero_comprador)){
                    
            }

            if ($this->add_rewards($this->recompensas)){
                
            }

            $this->cart->destroy();
            return true;            
            
        }
        else
        {
            return false;
        }

     }


/** 
    * @param action
    * @return null
    */ 
  public function update_money($total_dinero_comprador){
     $query        = "UPDATE `compradores` SET dino_coins = ? WHERE id_comprador = ?; ";

    $params_query = array( $total_dinero_comprador, $this->_user['id_comprador'] );    

      if($article = $this->sql->update($query, $params_query) ) {
        return true;
        }else{
        return false; 
      }

  }

    public function add_rewards($recompensas)
    {
        $query = "SELECT * FROM compradores where id_comprador = ?";
        $params_query = array( $this->_user['id_comprador'] );   
        
        if ($rs = $this->sql->select($query, $params_query))
        {
            $dinero_usuario = $rs[0];
            $dinero_acumulado = $dinero_usuario['dino_coins'] + $recompensas;

            $query = "UPDATE `compradores` SET dino_coins = ? WHERE id_comprador = ?; ";
            $params_query = array( $dinero_acumulado, $this->_user['id_comprador'] );   
            
            if($article = $this->sql->update($query, $params_query) ) {
                return true;
                }else{
                return false; 
            }
        }
    }

    public function get_purchases()
    {
        $query = "select * from ventas where id_comprador = ? order by id_venta desc";
        $params_query = array($this->_user['id_comprador']);

        if ($rs = $this->sql->select($query, $params_query))
        {
            return $rs;
        }
        else
        {
            return false;
        }
        
    }

    public function get_products_purchased($id_venta)
    {
        $query = "select * from carrito_productos where id_carrito = ?";
        $params_query = array($id_venta);

        if ($rs = $this->sql->select($query, $params_query))
        {
            return $rs;
        }
        else
        {
            return false;
        }
        
    }
    public function get_product_info($id_producto)
    {
        $query = "select * from productos where id_producto = ?";
        $params_query = array($id_producto);

        if ($rs = $this->sql->select($query, $params_query))
        {
            return $rs[0];
        }
        else
        {
            return false;
        }
        
    }



    /***
     * FIN de Funciones SQL
     */

    public function show_all_rows($alert='')
    {   
        if($this->action=="confirm_sale")
        {
            $allItems = $this->cart->getItems();
            $output .= '
            <h1> Confirmar compra </h1>
            <hr>
            <form action="./?action=confirm_form" method="POST">
                <input value="'.$this->_user['id_comprador'].'" type="hidden" name="id_comprador" >

            
            <h3>Resumen de compra</h3>
            <div class="row">
                <div class="col-md">
                    <ul class="list-group col-md">';
                        foreach ($allItems as $items)
                        {
                            foreach ($items as $item)
                            {
                                $detalles_de_producto_agregado = $this->get_product_added($item['id']);
                                $subtotal = ($detalles_de_producto_agregado['precio_producto'] * $item['quantity']);
                                $output .= '
                                <tr>
                                    <!-- <td>'.$item['id'].'</td> -->
                                    <li class="list-group-item">
                                        <a class="col-sm mb-5" href="./?action=product_details&prod_id='.$item['id'].'">
                                        
                                            <b>'.$detalles_de_producto_agregado['nombre_producto'].'</b></a> 
                                            - $ '.$subtotal.'
                                            <input value="'.$item['id'].'" type="hidden" name="id_product" >
                                    </li>
                                </tr>
                                ';
                                $total_compra = $subtotal + $total_compra;
                            }
                        }
                        $output .= ' 
                    </ul>
                </div>

                <div class="col-md card text-bg-light">
                    <div class="card-body">
                        <h3>Mis DinoCoins</h3>
                        <i class="fa-solid fa-money-bill"></i> - $'.$this->_user['dino_coins'].'<br>
                        <i class="fa-solid fa-coins"></i> - Total compra: $'.$total_compra.'
                    ';
                        $output .= '
                        <hr>
                        <div class="mt-1">
                            <h5>Recompensas disponibles</h5>
                            
                            <select class="form-control" name="id_recompensa">';
                       
                            foreach ($this-> get_reward() as $rwrd)
                            { 
                                $output .= '
                                    <option value="'.$rwrd['id_recompensa'].'">'.$rwrd['titulo_recompensa'].'</option>';
                            }
                            $output .= ' 
                                </select>
                            </div>
                        </div>'; 
                        
                 $output .= '
                        
                        </div>
                        <div class="container">
                        <hr>';
                        if ($this->_user['dino_coins'] >= $total_compra)
                        {
                            $output .= '<button type="submit" class="btn btn-success">Confirmar compra</button>';
                        }
                        else
                        {
                            $output .= '<button disabled type="submit" class="btn btn-success">Confirmar compra</button>
                            <p class="text-danger">Fondos insuficientes</p>';
                        }
                        $output .= '
                        </div>
                    </form>
                </div>
            ';

            
        }
        else if ($this->action=="delete_product_cart")
        { 
            $producto_seleccionado = $this->get_product_details($this->getData['id_product']);
            
            $output .= '
            <form action="./?action=eliminate_product_cart" method="POST">
               <h1>¿Estás Seguro Que Desea Eliminar este producto?</h1>
                   <input value="'.$this->getData['id_product'].'" type="hidden" name="id_product" >
                   <div class="form-group">
                       <div class="input-group">
                           <button type="submit" class="btn btn-warning">Si</button>
                           <a href="javascript:history.back()" class="btn btn-primary">No, Volver</a>
                       </div>
                   </div>   
           </form>';
        }
        else if ($this->action=="product_details")
        {
            $producto_seleccionado = $this->get_product_details($this->getData['prod_id']); // Creas una variable nueva para acceder a los objetos regresados por tu funcion
            
            $output .= '
            <div class="container mt-5"> 
                <div class="row">
                    <div class="col"><center>';
                            if ($producto_seleccionado['foto_producto'])
                            {
                                # code...
                            $output .= '<img src="_assets/img/productos/'.$producto_seleccionado['foto_producto'].'" width="200">';
                            }else 
                            {
                            # code...
                            $output .= '<img src="_assets/img/productos/default.jpg" width="100">';
                            }
                        
                        $output .= '
                            <! -- Aquí es donde le das la estructura que quieres a tu pagina de detalles de producto -->
                
                    </center></div>

                    <div class="col"> 
                        <h1>'.ucfirst($producto_seleccionado['nombre_producto']).'</h1>
                        <h3>Costo de $ '.$producto_seleccionado['costo_original_producto'].' Con descuento de $ '.$producto_seleccionado['precio_producto'].'</h3>
                        <hr>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <strong>Descripción:</strong><br>
                                '.$producto_seleccionado['descripcion_producto'].'</li>
                            <li class="list-group-item">
                                <strong>Por cada '.$producto_seleccionado['nombre_producto'].' que compres, te da:</strong><br>
                                $'.$producto_seleccionado['dino_producto'].'</li>
                            </li>
                            <li class="list-group-item">
                                <strong>Tienda:</strong><br>
                                '.$producto_seleccionado['nombre_usuario'].'</li>
                            </li>
                            <li class="list-group-item">
                                <strong>Direccion de tienda:</strong><br>
                                '.$producto_seleccionado['direccion_usuario'].'</li>
                            </li>
                            
                        </ul>
                        <hr>
                        
                        <!-- ------ Agregamos la funcionalidad del carrito ------ -->
                        <h3>Agregar al carrito</h3><br>';
                        if ($this->cart->isItemExists($producto_seleccionado['id_producto']))
                        {
                            $output .= '

                            <h4 style="color:darkgreen;"><i class="fa-solid fa-check"></i> | El producto esta agregado</h4>
                            ';
                        }
                        else
                        {
                            $output .= '
                            <form action="./?action=add_product_cart" method="post" class="input-group mb-3">
                                <input type="hidden" value="'.$producto_seleccionado['id_producto'].'" name="id_prd">
                                <input type="hidden" value="'.$producto_seleccionado['precio_producto'].'" name="price_prd">
                                <input type="number" min="1" value="1" class="form-control" style="max-width:200px;" name="qty">
                                <button class="btn btn-success" type="submit" id="button-addon1"><i class="fa-solid fa-cart-shopping"></i> Agregar</button>
                            </form>

                            <form action="./?action=" method="post" class="input-group mb-3">
                                <input type="hidden" value="'.$producto_seleccionado['id_producto'].'" name="id_prd">
                                <input type="hidden" value="'.$producto_seleccionado['precio_producto'].'" name="price_prd">
                                <input type="hidden" min="1" value="1" class="form-control" style="max-width:200px;" name="qty">
                                <button class="btn btn-primary" type="submit" id="button-addon1"><i class="fa-solid fa-store"></i> Compra directa</button>
                            </form>
                            ';
                        }
                        $output .= '
                    </div>
                </div>    
            </div>
            ';
        }
        else if ($this->action=="view_cart") //Creamos interfaz para ver los productos del carrito
        {
            $allItems = $this->cart->getItems(); // Creamos variable para poder recorrer todos los objetos/productos que hay en el carrito. Ya que todo los productos se guardan en un array. En realidad el carrito es un array

            $output .= '
                <div class="container mt-4">
                <h2>Mi Carrito De Compras</h2>
                <hr>';
                if($this->cart->isEmpty())
                {
                    $output .= '
                    <div class="alert alert-primary mt-3">
                        El carrito esta vacio
                    </div>
                    ';
                }else
                {
                    $output .= '
                        <table class = "table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <!-- <th>ID Producto</th> -->
                                    <th>Nombre De Product</th>
                                    <th>Precio Unitario</th>
                                    <th>Subtotal</th>
                                    <th>Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>';

                            foreach ($allItems as $items)
                            {
                                foreach ($items as $item)
                                {
                                    $detalles_de_producto_agregado = $this->get_product_added($item['id']);
                                    $subtotal = ($detalles_de_producto_agregado['precio_producto'] * $item['quantity']);
                                    $output .= '
                                    <tr>
                                        <!-- <td>'.$item['id'].'</td> -->
                                        <td>'.$detalles_de_producto_agregado['nombre_producto'].'</td>
                                        <td>$'.$detalles_de_producto_agregado['precio_producto'].'</td>
                                        <td>$'.$subtotal.'</td>
                                        <td>'.$item['quantity'].'</td>
                                        <td>
                                        <a href = "./?action=delete_product_cart&&id_product='.$item['id'].'" class="btn btn-danger btn-sm"><i class="fa-solid fa-times"></i></a>
                                        </td>
                                    </tr>
                                    ';
                                }
                            }

                            $output .= '
                            </tbody>
                            </table>
                            <hr>
                                <form action="./?action=destroy_cart" method="POST">
                                    <a href="./?action=confirm_sale" class="btn btn-primary"> Confirmar Compra</a>
                                    <button type="submit" class="btn btn-danger"> Vaciar Carrito</button>
                                </form>
                            </hr>
                            
                        </div>
                        ';
                }
                

        }
        else if($this->action=="view_purchases")
        {
            $output .= '
            <div class="container mt-2">
                <h1> Mis Compras </h1>
                <hr>';
                foreach ($this->get_purchases() as $prch)
                {
                    $output .= '
                    <div class="accordion accordion-flush" id="accordionFlushExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$prch['id_venta'].'" aria-expanded="false" aria-controls="collapse'.$pr['id_venta'].'">
                                Compra realizada #'.$prch['id_venta'].'  - '.$prch['fecha_compra'].'
                            </button>
                            </h2>
                            <div id="collapse'.$prch['id_venta'].'" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <ul class="list-group">';
                                    foreach ($this->get_products_purchased($prch['id_venta']) as $prod)
                                    {
                                        $product_details = $this->get_product_info($prod['id_producto']);
                                        $output .= '<li class="list-group-item">
                                                        <div class="fw-bold">'.$product_details['nombre_producto'].'</div>
                                                        # de producto: '.$prod['id_producto'].'
                                                    </li>';
                                    }
                                    $output .= '
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <hr>
                    </div>
                    ';
                }
            $output .= '
            </div>
            <hr class="mt-5">
            ';
        }
        else
        {
            $output .= '
            <div class="container mt-5">
                <div class="row">
                <!-- Button trigger modal -->
                <button type="button" class= "btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                    Tienes <strong>'.$this->_user['dino_coins'].' Dinocoins Gastalos!!!</strong>
                </button>  
                '.$alert;
               
                foreach ($this->get_products() as $product)
                { 
                    /*$categoria  = $this->get_product_category($product['id_categoria']);
                    $usuario = $this->get_product_user($product['id_usuario']);*/
                    $output .= '
                    <a class="col-sm mb-5" href="./?action=product_details&prod_id='.$product['id_producto'].'" style="text-decoration:none;">
                        <div class="card">
                            <div class="card-body">
                            <center>';
                            

                            if ($product['foto_producto'])
                            {
                                # code...
                            $output .= '<img src="_assets/img/productos/'.$product['foto_producto'].'" width="200" height="200">';
                            }else 
                            {
                            # code...
                            $output .= '<img src="_assets/img/productos/default.jpg" width="100" height="100">';
                            }
                            $output .= ' 
                                <hr>
                                <h3>'.$product['nombre_producto'].'</h3>
                                <h6> Precio Original $'.$product['costo_original_producto'].'</h6>
                                <h5 style="color:darkblue;">Descuento de $'.$product['precio_producto'].' Dinocoins</h5>
                            <center>
                            </div>
                        </div>
                    </a>'; 
                }
                $output .= '
                </div>
            </div>
    
            ';
        }
       
        
        
        return $output;
    }
    
}

?>