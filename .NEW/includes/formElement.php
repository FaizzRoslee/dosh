<?php
	/****************************************************/
	// filename: formElement.php
	// 
	/****************************************************/
	//=========================================================================================================
	function form_input($formelement,$value='',$others=array()) {
		$form_input = '<input name="'.$formelement.'" id="'.$formelement.'" value="'.$value.'"';
		if(is_array($others)){
			foreach($others as $attribute => $detail)
				$form_input .= ' '.$attribute.'="'.$detail.'"';
		}
		$form_input .= ' />';
		return $form_input;        
	}
	//=========================================================================================================
	function form_button($formelement,$value='',$others=array()) {
		$form_button = '<span><button class="btn-new-style" name="'.$formelement.'" id="'.$formelement.'"';
		// $form_button = '<span><button name="'.$formelement.'" id="'.$formelement.'"';
		if(is_array($others)){
			foreach($others as $attribute => $detail)
				$form_button .= ' '.$attribute.'="'.$detail.'"';
		}
		$form_button .= '>'.$value.'</button></span>';
		return $form_button;
	}
	//=========================================================================================================
	function form_textarea($formelement,$value='',$others=array()) {
		$curr_val = $value;

		$form_field = ' <textarea name="'.$formelement.'" id="'.$formelement.'"';
		if(is_array($others)){
			foreach($others as $attribute => $detail)
				$form_field .= ' '.$attribute.'="'.$detail.'"';
		}
		$form_field .= ' >'.$curr_val.'</textarea>';
		
		return $form_field;        
	}
	//=========================================================================================================
	function form_select($formelement,$group_array=array(),$option_array=array(),$current='',$others=array()) {
		$curr_val = $current;
		
		//if($curr_val!='' && $curr_val==0) $curr_val = '';
		
		$select = '<select name="'.$formelement.'" id="'.$formelement.'"';
		if(is_array($others)){
			foreach($others as $attribute => $value)
				$select .= ' '.$attribute.'="'.$value.'"';
		}
		$select .= '>';
		$select .= '
			<option value="" '.(($curr_val=='')?'selected':'').'>'. _LBL_SELECT .'</option>';
		if(is_array($group_array)){
			foreach($group_array as $gkey => $gval){
				$select .= '
				<optgroup label="'.$gval.'">\n';
				if(is_array($option_array)){
					foreach($option_array as $opt_val){
						if($opt_val['parent'] == $gkey){
							$select .= '
							<option value="'.$opt_val['value'].'" ';
							$select .= (trim($opt_val['value'])==trim($curr_val))?' selected ':'';
							$select .= '>';
							$select .= $opt_val['label'].'</option>';
						}
					}
					$select .= '</optgroup>\n';
				}
			}
		}else{
			if(is_array($option_array)){
				foreach($option_array as $opt_val){
					$select .= '
					<option value="'.$opt_val['value'].'" ';
					$select .= (trim($opt_val['value'])==trim($curr_val))?' selected ':'';
					$select .= '>';
					$select .= $opt_val['label'].'</option>';
				}
			}
		}
		$select .= '</select>';
		return $select;
	}
	//=========================================================================================================
	function form_select2($formelement,$group_array=array(),$option_array=array(),$current='',$others=array()) {
		$curr_val = $current;
		
		//if($curr_val!='' && $curr_val==0) $curr_val = '';
		
		$select = '<select name="'.$formelement.'" id="'.$formelement.'"';
		if(is_array($others)){
			foreach($others as $attribute => $value)
				$select .= ' '.$attribute.'="'.$value.'"';
		}
		$select .= '>';
		if(is_array($group_array)){
			foreach($group_array as $gkey => $gval){
				$select .= '
				<optgroup label="'.$gval.'" title="'.$gval.'">';
				if(is_array($option_array)){
					foreach($option_array as $opt_val){
						if($opt_val['parent'] == $gkey){
							$select .= '
							<option value="'.$opt_val['value'].'" title="'. (isset($opt_val['title'])?$opt_val['title']:$opt_val['label']) .'"';
							$select .= ($opt_val['value']==$curr_val)?' selected ':'';
							$select .= '>';
							$select .= $opt_val['label'].'</option>';
						}
					}
					$select .= '</optgroup>';
				}
			}
		}else{
			if(is_array($option_array)){
				foreach($option_array as $opt_val){
					$select .= '
					<option value="'.$opt_val['value'].'" title="'. (isset($opt_val['title'])?$opt_val['title']:$opt_val['label']) .'"';
					$select .= ($opt_val['value']==$curr_val)?' selected ':'';
					$select .= '>';
					$select .= $opt_val['label'].'</option>\n';
				}
			}
		}
		$select .= '</select>';
		return $select;
	}
	//=========================================================================================================
	function form_multiselect($formelement,$group_array=array(),$option_array=array(),$current=array(),$others=array()) {	//for 1st & 2nd level only
		$curr_val = $current;
		//print_r($curr_val);
		$select = '<select multiple name="'.$formelement.'[]" id="'.$formelement.'"';
		if(is_array($others)){
			foreach($others as $attribute => $value)
				$select .= ' '.$attribute.'="'.$value.'"';
		}
		$select .= '>';
		if(is_array($group_array)){
			foreach($group_array as $gkey => $gval){
				$select .= '
				<optgroup label="'.$gval.'" title="'.$gval.'">';
				if(is_array($option_array)){
					foreach($option_array as $opt_val){
						if($opt_val['parent'] == $gkey){
							$select .= '
							<option value="'.$opt_val['value'].'" title="'. (isset($opt_val['title'])?$opt_val['title']:$opt_val['label']) .'"';
							//$select .= '<option value="'.$opt_val['value'].'" title="'. $opt_val['label'] .'"';
							$select .= in_array($opt_val['value'],$curr_val)?' selected ':'';
							$select .= '>';
							$select .= $opt_val['label'].'</option>';
						}
					}
					$select .= '</optgroup>';
				}
			}
		}else{
			if(is_array($option_array)){
				//extractArray($option_array);
				foreach($option_array as $opt_val){
					$select .= '
					<option value="'.$opt_val['value'].'" title="'. (isset($opt_val['title'])?$opt_val['title']:$opt_val['label']) .'"';
					//$select .= '<option value="'.$opt_val['value'].'" title="'. $opt_val['label'] .'"';
					$select .= in_array($opt_val['value'],$curr_val)?' selected ':'';
					$select .= '>';
					$select .= $opt_val['label'].'</option>';
				}
			}
		}
		$select .= '</select>';
		return $select;
	}
	//=========================================================================================================
	function form_radio($formelement,$option_array=array(),$current='',$labelSeparator='',$others=array()){
		$curr_val = $current;
				
		$form_rc = '';
		if(is_array($option_array)){
			foreach($option_array as $opt_val){
				$form_rc .= '<label><input type="radio" name="'.$formelement.'" id="'.$formelement.'" ';
				$form_rc .= ($opt_val['value']==$curr_val)?'checked':'';
				$form_rc .= ' value="'.$opt_val['value'].'"';
				if(is_array($others)){
					foreach($others as $attribute => $detail)
						$form_rc .= ' '.$attribute.'="'.$detail.'"';
				}
				$form_rc .= ' />'.$opt_val['label'].'</label>';
				$form_rc .= $labelSeparator;
			}
		}
		return $form_rc;
	}
	//=========================================================================================================
	function form_checkbox($formelement,$option_array=array(),$current='',$labelSeparator='',$others=array()){
		$curr_val = $current;
		
		$form_rc = '';
		if(is_array($option_array)){
			foreach($option_array as $opt_val){
				$form_rc .= '<label><input type="checkbox" name="'.$formelement.'[]" id="'.$formelement.'" ';
				$form_rc .= in_array($opt_val['value'],$curr_val)?'checked':'';
				$form_rc .= ' value="'.$opt_val['value'].'"';
				if(is_array($others)){
					foreach($others as $attribute => $detail)
						$form_rc .= ' '.$attribute.'="'.$detail.'"';
				}
				$form_rc .= ' />'.$opt_val['label'].'</label>';
				$form_rc .= $labelSeparator;
			}
		}
		return $form_rc;
	}
	//=========================================================================================================
?>