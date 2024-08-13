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
            $output .= '';
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
                    <a class="col-sm" href="./?action=product_details" style="text-decoration:none;">
                        <div class="card">
                            <div class="card-body">
                                <h3>'.$product['nombre_producto'].'</h3>
                                '.$product['descripcion_producto'].'<br>
                                $'.$product['precio_producto'].'
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