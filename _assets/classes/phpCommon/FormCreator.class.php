<?php
//require_once("addStripSlashes.php");

// The code contained within this file was created by and is owned by NuTerra LLC
// You must obtain permission from NuTerra before using this file, or any of it's contents
// Use without permission may result in procecution.
class FormCreator extends Validator {
    
    var $states = array(
        array('01', 'Ags', 'Aguascalientes'), 
        array('02',  'Baja California', 'BC'), 
        array('03',  'Baja California Sur', 'BCS'), 
        array('04', 'Campeche',    'Camp.'), 
        array('05',  'Coahuila de Zaragoza',    'Coah.'), 
        array('06',  'Colima',  'COL'), 
        array('07',  'Chiapas', 'CHIS'), 
        array('08',  'Chihuahua',   'CHIH'), 
        array('09',  'Distrito Federal',    'DF'), 
        array('10',  'Durango', 'DGO'), 
        array('11',  'Guanajuato',  'GTO'), 
        array('12',  'Guerrero',    'GRO'), 
        array('13',  'Hidalgo', 'HGO'), 
        array('14',  'Jalisco', 'JAL'), 
        array('15',  'México',  'MEX'), 
        array('16',  'Michoacán de Ocampo', 'MICH'), 
        array('17',  'Morelos', 'MOR'), 
        array('18',  'Nayarit', 'NAY'), 
        array('19',  'Nuevo León',  'NL'), 
        array('20',  'Oaxaca',  'OAX'), 
        array('21',  'Puebla',  'PUE'), 
        array('22',  'Querétaro',   'QRO'), 
        array('23',  'Quintana Roo',    'QROO'), 
        array('24',  'San Luis Potosí', 'SLP'), 
        array('25',  'Sinaloa', 'SIN'), 
        array('26',  'Sonora',  'SON'), 
        array('27',  'Tabasco','TAB'), 
        array('28',  'Tamaulipas',  'TAMPS'), 
        array('29',  'Tlaxcala',    'TLAX'), 
        array('30',  'Veracruz de Ignacio de la Llave', 'VER')

    );
   
    var $months = array (
      '01'    =>  'Enero',
      '02'    =>  'Febrero',
      '03'    =>  'Marzo',
      '04'    =>  'Abril',
      '05'    =>  'Mayo',
      '06'    =>  'Junio',
      '07'    =>  'Julio',
      '08'    =>  'Agosto',
      '09'    =>  'Septiembre',
      '10'    =>  'Octubre',
      '11'    =>  'Noviembre',
      '12'    =>  'Diciembre'
    );
    
    function Forms($sql, $mail) {
        $this->sql = $sql;
        $this->mail = $mail;
        die('sssss');
    }
  
    /** 
    * @param action
    * @return null
    */
    public function write_text_field($field_name, $errors = array(), $class = '', $id = '', $data = array(), $postData=false, $placeholder=false, $required='', $tabindex='') {
        
        if (isset($errors[$field_name])) {
          $output .= $errors[$field_name];
        }

        $output .= '<input type="text" class="' . $class . '" '.($id?' id="' . $id . '"':'').' name="' . $field_name . '" '.($placeholder?' placeholder="' . $placeholder . '"':'').'  '.($required?' required="' . $required . '"':'').' value="' . ($data[$field_name] ? $data[$field_name] : $postData[$field_name]) . '" tabindex="' . $tabindex . '">';

        return $output;
    }
  
  /** 
    * @param action
    * @return null
  */
  function write_select_field($select_state=false, $field_name, $errors = array(), $class = '', $id = '', $data = array(), $postData=false, $placeholder=false, $required='', $tabindex='', $defaultLabel='') {
    
    if (isset($errors[$field_name])) {
      $output .= $errors[$field_name];
    }
    
    $output .= '<select name="' . $field_name . '" class="' . $class . '" id="' . $id . '">';
    
    if ($defaultLabel) {
      $output .= '<option value="">' . $defaultLabel . '</option>';
    }
    
    if($select_state){
      if ($defaultLabel) {
      $output .= '<option value="">' . $defaultLabel . '</option>';
    }
      foreach($this->states as $option) {
        $value = $useKeysAsValue?$key:$option;
        $output .= '<option value="' . $option[0] . '" '.(($data[$field_name] == $value)?'selected="selected"':'').'>' . $option[1] . '</option>';
      }

    }else{

      foreach($states as $key => $option) {
        $value = $useKeysAsValue?$key:$option;
        $output .= '<option value="' . $value . '" ' . (($data[$field_name] == $value)?'selected="selected"':'') . '>' . $option[2] . '</option>';
      }

    }
    
    $output .= '</select>';
    return $output;
  }



  function writePasswordField($fieldName, $size = 25, $errors = array(), $extraHtml = '', $data = array()) {
    if (!$data) {
      $data = $_POST;
    }
    if (isset($errors[$fieldName])) {
      print $errors[$fieldName];
    }
    print '<input type="password" name="'.$fieldName.'" value="'.$data[$fieldName].'" size="'.$size.'" '.$extraHtml.' />';
  }
  
  function writeTextAreaField($fieldName, $cols = 20, $rows = 3, $errors = array(), $extraHtml = '', $data = array()) {
    if (!$data) {
      $data = $_POST;
    }
    if (isset($errors[$fieldName])) {
      print $errors[$fieldName];
    }
    print '<textarea name="'.$fieldName.'" cols="'.$cols.'" rows="'.$rows.'" '.$extraHtml.'>'.$data[$fieldName].'</textarea>';
  }
  
  function writeSelectField($fieldName, $options, $useKeysAsValue = false, $defaultLabel = '', $errors = array(), $extraHtml = '', $data = array()) {
    if (!$data) {
      $data = $_POST;
    }
    if (isset($errors[$fieldName])) {
      print $errors[$fieldName];
    }
    
    print '<select name="'.$fieldName.'" '.$extraHtml.'>';
    if ($defaultLabel) {
      print '<option value="">'.$defaultLabel.'</option>';
    }
    foreach($options as $key => $option) {
      $value = $useKeysAsValue?$key:$option;
      print '<option value="'.$value.'" '.(($data[$fieldName] == $value)?'selected="selected"':'').'>'.$option.'</option>';
    }
    print '</select>';
  }
  
  function writeRadioField($fieldName, $options, $useKeysAsValue = false, $spacer = '', $errors = array(), $extraHtml = '', $data = array()) {
    if (!$data) {
      $data = $_POST;
    }
    if (isset($errors[$fieldName])) {
      print $errors[$fieldName];
    }
    
    foreach($options as $key => $option) {
      $value = $useKeysAsValue?$key:$option;
      print '<input type="radio" name="'.$fieldName.'" value="'.$value.'" '.(($data[$fieldName] == $value)?'checked="checked"':'').' '.$extraHtml.' /> '.$option.$spacer;
    }
  }
  
  function writeCheckboxField($fieldName, $options, $useKeysAsValue = false, $spacer = '', $errors = array(), $extraHtml = '', $data = array()) {
    if (!$data) {
      $data = $_POST;
    }
    if (isset($errors[$fieldName])) {
      print $errors[$fieldName];
    }
    
    foreach($options as $key => $option) {
      $value = $useKeysAsValue?$key:$option;
      print '<input type="checkbox" name="'.$fieldName.'[]" value="'.$value.'" '.((is_array($data[$fieldName]) && in_array($value, $data[$fieldName]))?'checked="checked"':'').' '.$extraHtml.' /> '.$option.$spacer;
    }
  }
  
  function writeMultiSelectField($fieldName, $options, $useKeysAsValue = false, $errors = array(), $extraHtml = '', $data = array()) {
    if (!$data) {
      $data = $_POST;
    }
    if (isset($errors[$fieldName])) {
      print $errors[$fieldName];
    }
    
    print '<select name="'.$fieldName.'[]" multiple="multiple">';
    foreach($options as $key => $option) {
      $value = $useKeysAsValue?$key:$option;
      print '<option value="'.$value.'" '.((is_array($data[$fieldName]) && in_array($value, $data[$fieldName]))?'selected="selected"':'').' '.$extraHtml.' /> '.$option.'</option>';
    }
    print '</select>';
  } 
    /***********************************************
    FUNCTION AVAILABLE
        submitToDB($table, $fields, $source = $_REQUEST, $updateWhere = '', $returnOne = true)
            Automatically inserts / updates the provided table with the provided information.
            Returns either a single or an array of a entries from the table that were (or are indistinguishable from) the modified entries
            $table - The name of the table to insert / update to
            $field - An array of all possible fields
            $source - A keyed array to get the data from where the keys match $field
            $updateWhere - If provided it checks to see if there are any entries in $table that
                are returned when a select is done with $updateWhere after WHERE.
                If there is then it updates those entries using $updateWhere
            $returnOne - If true only returns the first qualifying entry, otherwise it returns an array
        sendEmail($fields, $subject, $toAddr, $fromLabel, $fromAddr, $source = null, $header = '', $footer = '')
            Generates and sends an e-mail using information from source.
            If either the label from $fields or the value from $source are empty it will be skipped.
            If the value is either 't' or 'f', then 'Yes' and 'No' respectivly are used.
            $fields - A keyed array of field => label where field matches the keys in source.
                If the label is empty, the field will be skipped
            $source - A keyed array of field => value
            $subject - The subject of the e-mail
            $toAddr - The e-mail(s) to send to, seperate multiple with commas
            $fromLabel - The label for the from address
            $fromAddr - The address the e-mail should be from
            $header - Text to appear above the values from $source
            $footer - Text to appear below the values from $source
        sqlDateToTimeStamp($sqlDate) - $sqlDate is a date or date/time stamp from a sql database, function returns
        	a timestamp for the entered date or datetime.
              
    ***********************************************/
    function submitToDB($table, $fields, $source = null, $updateWhere = '', $returnOne = true, $nowDateFields = array(), $onlyUpdate = false) {
        if ($source == null) {
            $source = $_REQUEST;
        }
        if ($updateWhere == '') {
            $updateWhere = '1=0';
        }
        if (!is_array($nowDateFields)) {
            $nowDateFields = array($nowDateFields);
        }
        
        $fieldSql = array();
        $valueSql = array();
        $setSql = array();
        foreach($fields as $field) {
            $fieldSql[] = $field;
            if ($source[$field] == '*NULL') {
              $valueSql[] = 'NULL';
              $setSql[] = $field.' = NULL';
            } else { 
              /*$valueSql[] = '\''.addslashes($source[$field]).'\'';
              $setSql[] = $field.' = \''.addslashes($source[$field]).'\'';*/			  
			  $valueSql[] = '\''.stripsDecodeInjection($source[$field]).'\'';
              $setSql[] = $field.' = \''.stripsDecodeInjection($source[$field]).'\'';
            }
        }
        foreach($nowDateFields as $field) {
            if( $source[$field] ) {
                $fieldSql[] = $field;
                $valueSql[] = 'now()';
                $setSql[] = $field.' = now()';
            }
        }
        $fieldSql = implode(', ', $fieldSql);
        $valueSql = implode(', ', $valueSql);
        $setSql = implode(', ', $setSql);
        
		$this->sql->Query('BEGIN;');
        if (!$onlyUpdate) {
            //$sql = "INSERT INTO $table ($fieldSql) SELECT $valueSql WHERE NOT EXISTS(SELECT * FROM $table WHERE $updateWhere) LIMIT 1;";
		   $this->sql->Query("INSERT INTO $table ($fieldSql) SELECT $valueSql FROM dual WHERE NOT EXISTS(SELECT * FROM $table WHERE $updateWhere) LIMIT 1;");

		   // //Added: 05/27/2010
			// if(!$this->sql->Select("SELECT * FROM $table WHERE $updateWhere ;")){
				// $this->sql->Insert("INSERT INTO $table ($fieldSql) VALUES ($valueSql);");
			// }
        }
        $sql = "UPDATE $table SET $setSql WHERE $updateWhere;";
		//exit($sql);
		//exit("Product.show: ".$source['product.show']);
		
		$this->sql->Query($sql);
		$this->sql->Query('COMMIT;');
        //print '<p>'.$sql.'</p>';
        
        $whereSql = array();
        foreach($fields as $field) {
            if ($source[$field] == '*NULL') {
              $whereSql[] = $field.' IS NULL';
            } else {
              $whereSql[] = $field.' = \''.addslashes($source[$field]).'\'';
            }
        }
        $whereSql = implode(' AND ', $whereSql);
/*------------------------------------------------------------*/
// $msg= "SELECT * FROM $table WHERE $updateWhere;";
// $to = 'pthao@nuterrallc.com'; 
// $subject = "FormsNew.class.php";	//Daedalus
// $header = 'From: pthao@nuterrallc.com';
// mail($to, $subject , $msg, $header);
/*------------------------------------------------------------*/        
        if (!($updateWhere && $return = $this->sql->Select("SELECT * FROM $table WHERE $updateWhere;"))) {
            $return = $this->sql->Select("SELECT * FROM $table WHERE $whereSql;");
        }
        if ($returnOne) {
            return $return[0];
        } else {
            return $return;
        }
    }
    
    function sendEmail($fields, $subject, $toAddr, $fromLabel, $fromAddr, $source = null, $header = '', $footer = '', $actualSend = true) {
        if ($source == null) {
            $source = $_REQUEST;
        }
        $body = "<html><head></head><body><p>$header</p>\n<p>";
        foreach(array_keys($fields) as $field) {
            if($fields[$field] && strlen($source[$field])) {
                if ($source[$field] == 't') {
                    $source[$field] = 'Yes';
                }
                if ($source[$field] == 'f') {
                    $source[$field] = 'No';
                }
                if (is_array($source[$field])) {
                    $source[$field] = implode(', ', $source[$field]);
                }
                $body .= "<b>$fields[$field]</b> $source[$field]<br>\n";
            }
        }
        $body .= "</p><p>$footer</p>\n</body></html>";
        
        if ($actualSend) {			
            $this->mail->write($subject, stripslashes($body), $fromLabel,$fromAddr, $toAddr, 3, 0, 0, 0);
            $this->mail->send();
        }
        
        return stripslashes($body);
    }
	
	
	/////////////
	function sendEmailQuote($subject, $body, $fromLabel, $toAddr, $fromAddr,  $actualSend = true){

	if ($actualSend) {			
            $this->mail->write($subject, stripslashes($body), $fromLabel, $fromAddr, $toAddr, 3, 0, 0, 0);
            $this->mail->send();
        }
	}
	////////////

	function sqlDateToTimeStamp($sqlDate){
		$date=preg_split('/-/',$sqlDate);
		$dayTime=preg_split('/ /',$date[2]);
		$date[2]=$dayTime[0];
		if ($dayTime[1]){
			$time=preg_split('/:/',$dayTime[1]);
		}
		else{
			$time[0]='00';
			$time[1]='00';
			$time[2]='00';
		}
		$timestamp=mktime($time[0],$time[1],$time[2],$date[1],$date[2],$date[0]);
		return $timestamp;
	}
  


}
?>
