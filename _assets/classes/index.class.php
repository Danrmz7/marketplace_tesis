<?php
/**
 * File Name: Market
 * Creator URI: 
 * Description: Main configuration file
 * Author: 
 * Version: 1.1
 * Author URI: 
 */
class index {
 
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
        switch($this->action)
        {
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



     /***
     * FIN de Funciones SQL
     */
  
     public function show_all_rows($alert='')
    {  $output .= 'Hola mundo';

        return $output;
    }
}

?>