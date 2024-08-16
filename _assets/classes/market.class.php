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
    public function __construct($sql/*, $getCurrentUser*/){
      
      $this->sql        = $sql;
      //$this->_user      = $getCurrentUser;
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

     /*public function get_product_category($id_cat)
     {
         $query = "SELECT * from categorias where id_categoria = ?";
         $params_query = array($id_cat);
 
         if($rs = $this->sql->select($query, $params_query))
         {
             return $rs[0];
         }
         else
         {
             return false;
         }
     }
     
     public function get_product_user($id_us)
    {
        $query = "SELECT * from usuarios where id_usuario = ?";
        $params_query = array($id_us);

        if($rs = $this->sql->select($query, $params_query))
        {
            return $rs[0];
        }
        else
        {
            return false;
        }
    }*/



    /***
     * FIN de Funciones SQL
     */

    public function show_all_rows($alert='')
    {   
        if ($this->action=="product_details")
        {
            $producto_seleccionado = $this->get_product_details($this->getData['prod_id']); // Creas una variable nueva para acceder a los objetos regresados por tu funcion
            
            $output .= '<div class="container mt-5"> 
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
                            <h2>'.$producto_seleccionado['precio_producto'].'</h2>
                            <! -- Aquí es donde le das la estructura que quieres a tu pagina de detalles de producto, gei -->
                
                    </div>
                </div>    
            </div>
            ';
        }
        else
        {
            $output .= '
            <div class="container mt-5">
                <div class="row">';
               
                foreach ($this->get_products() as $product)
                { 
                    /*$categoria  = $this->get_product_category($product['id_categoria']);
                    $usuario = $this->get_product_user($product['id_usuario']);*/
                    $output .= '
                    <a class="col-sm" href="./?action=product_details&prod_id='.$product['id_producto'].'" style="text-decoration:none;">
                        <div class="card">
                            <div class="card-body">
                            <center>';
                            

                            if ($product['foto_producto'])
                            {
                                # code...
                            $output .= '<img src="_assets/img/productos/'.$product['foto_producto'].'" height="200">';
                            }else 
                            {
                            # code...
                            $output .= '<img src="_assets/img/productos/default.jpg" width="100">';
                            }
                            $output .= ' 
                            
                                <h3>'.$product['nombre_producto'].'</h3>
                                '.$product['descripcion_producto'].'<br>
                                $'.$product['precio_producto'].'
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