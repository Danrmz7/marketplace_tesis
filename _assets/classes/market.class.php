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
                    <div class="alert alert-success">
                        <strong>Success!</strong> Producto Agregado
                    </div> ';
                    $output .= $this->show_all_rows($alert);
                    return $output;
                }else
                {
                    $alert = '
                    <div class="alert alert-danger">
                        <strong>Error!</strong> Producto NO Agregado
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
                    <div class="alert alert-success">
                        <strong>Success!</strong> Compra Realizada
                    </div> ';
                    $output .= $this->show_all_rows($alert);
                    return $output;
                }
                else
                {
                    $alert = '
                    <div class="alert alert-danger">
                        <strong>Success!</strong> Compra NO Realizada
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
 
         if($this->cart->add($this->postData['id_prd'], $this->postData['qty']))
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
        /* $query = "INSERT INTO ventas (id_comprador,fecha_compra,id_recompensa) VALUES (?,?,?)";
        $params_query = array(
            $this->postData['nombre_comprador'],
            $this->postData['nuevoDescripcion'],
            $this->postData['id_recompensa']
        );

        if($this->sql->insert($query, $params_query))
        
        {
            $query = "SELECT * from ventas where id_venta = ?";
            $params_query = array($this->getData['id_venta']);
            if($rs = $this->sql->select($query, $params_query))
            {
                $ultim_venta = $rs[0];
                $allItems = $this -> cart -> getItems();
                foreach($allItems as $items)
                {
                    foreach($items as $item)
                    {
                        $params_query = array($ultim_venta['id_venta']);
                    }
                }
            }
        }
        else
        {
            return false;
        }      */
        
        $fecha_actual = date("Y-m-d");
        $query = "INSERT INTO ventas (id_comprador,fecha_compra,id_recompensa) VALUES (?,?,0)";
        $params_query = array(
            $this->postData['id_comprador'],
            $fecha_actual,
            // $this->postData['id_recompensa']
        );

        if($this->sql->insert($query, $params_query))
        {
            $query = "SELECT * from ventas where id_comprador = ? order by id_venta ASC";
            $params_query = array($this->postData['id_comprador']);
            
            if ($rs = $this->sql->select($query, $params_query))
            {
                $this->cart->destroy();
                return true;
            }
            else
            {
                return false;
            }
            
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
                            <button type="submit" class="btn btn-success">Confirmar compra</button>
                        </div>
                    </form>
                </div>
            <hr>
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
                    <div class="col">';
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
                            <h1>'.$producto_seleccionado['nombre_producto'].'</h1>
                            <h3>$ '.$producto_seleccionado['precio_producto'].'</h3>
                            <! -- Aquí es donde le das la estructura que quieres a tu pagina de detalles de producto -->
                
                    </div>

                    <div class="col"> 
                        <h3>Detalles del producto</h3>
                        '.$producto_seleccionado['descripcion_producto'].'<br>
                        '.$producto_seleccionado['fecha_pub_producto'].'
                        <hr>
                        
                        <!-- ------ Agregamos la funcionalidad del carrito ------ -->
                        <h3>Agregar al carrito</h3><br>';
                        if (!$this->cart->isItemExists($producto_seleccionado['id_producto']))
                        {
                            $output .= '
                            <form action="./?action=add_product_cart" method="post" class="input-group mb-3">
                                <input type="hidden" value="'.$producto_seleccionado['id_producto'].'" name="id_prd">
                                <input type="number" min="1" value="1" class="form-control" style="max-width:200px;" name="qty">
                                <button class="btn btn-success" type="submit" id="button-addon1"><i class="fa-solid fa-cart-shopping"></i> Agregar</button>
                            </form>
                            ';
                        }
                        else
                        {
                            $output .= '

                            <h4 style="color:darkgreen;"><i class="fa-solid fa-check"></i> | El producto esta agregado</h4>
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
                <h2>Mi Carrito De Compras</h2>';
                if($this->cart->isEmpty())
                {
                    $output .= 'El carrito esta vacio';
                }else
                {
                    $output .= '
                <hr>
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
                                <td>'.$subtotal.'</td>
                                <td>'.$item['quantity'].'</td>
                                <td>
                                <a href = "./?action=delete_product_cart&&id_product='.$item['id'].'" class="btn btn-danger btn-sm">Eliminar producto</a>
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
                        <button type="submit" class="btn btn-danger"> Vaciar Carrito</button>
                    </form>
                </hr>
                    <a href="./?action=confirm_sale" class="btn btn-primary"> Confirmar Compra</a>
                
            </div>
            ';
                }
                

        }
        else
        {
            $output .= '
            <div class="container mt-5">
                <div class="row">
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
                                <h5 style="color:darkblue;">$'.$product['precio_producto'].'</h5>
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