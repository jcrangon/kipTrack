<?php
function set_jcr_page_transition($num=4,$rand="random"){
	//******************************
		$number_of_transitions=6;
	//******************************
	if($rand==="strict"){
		return "jcr_transition_set".strval($num);
	}
	else{
		return "jcr_transition_set".strval(mt_rand(1,$number_of_transitions));
	}
}


?>