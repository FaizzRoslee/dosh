<?php
	/************************************************************************************************************************/
	// filename: arrayCommon.php
	// purpose: common function that can be used in all forms
	/************************************************************************************************************************/
	
	/*
	 *
	 */
	$arrLanguage = array(
					array("value" => "may", "label" => _LBL_MALAY),
					array("value" => "eng", "label" => _LBL_ENGLISH)
				);	
	
	/*
	 *
	 */
	$arrStatusAktif = array(
					array("value" => "1", "label" => _LBL_ACTIVE),
					array("value" => "0", "label" => _LBL_INACTIVE)
				);	
	
	/*
	 *
	 */
	$arrLevel = array(
					array("value"=>"6","label"=>_LBL_SUPP_IMPORTER),
					array("value"=>"7","label"=>_LBL_INDUSTRIAL_MANUF),
					array("value"=>"8","label"=>_LBL_SUPP_MANUF),
				);
	
	/*
	 *
	 */
	$arrSource = array(
					array("value"=>"1","label"=>_LBL_LOCALLY_MANUFACTURED,"code"=>"LM"),
					array("value"=>"2","label"=>_LBL_IMPORTED,"code"=>"IM"),
					array("value"=>"3","label"=>_LBL_LOCALLY_MANUFACTURED_IMPORTED,"code"=>"LM/IM"),
					
					
                    // linuxhouse_20220614
                    // start penambahbaikan - 8_ruangan chemical source - v004 1: add 'No Information' label array
                    array("value"=>"4","label"=>_LBL_NO_INFORMATION,"code"=>"NI"),
                    // end penambahbaikan - 8_ruangan chemical source - v004 1: add 'No Information' label array
				);
	
	/*
	 *
	 */
	$arrReport = array(
					array("value"=>"rpt01","label"=>_LBL_REPORT_OVERALL_DETAILED),
					array("value"=>"rpt02","label"=>_LBL_REPORT_OVERALL_SUMMARY)
				);
	
	/*
	 *
	 */
	$arrReportSub 	= array(
						array("value"=>"rpt03","label"=>_LBL_SUBMISSION),
						// array("value"=>"rpt04","label"=>_LBL_RENEW_SUBMISSION)
					);
	
	/*
	 *
	 */
	$arrRptStatusN 	= array(
						array("value"=>"21","label"=>_LBL_CHECKED),
						array("value"=>"31","label"=>_LBL_APPROVED), //32 juga
						array("value"=>"2","label"=>_LBL_REJECTED),
					);
	
	/*
	 *
	 */
	$arrRptStatusR 	= array(
						array("value"=>"22","label"=>_LBL_CHECKED),
						array("value"=>"41","label"=>_LBL_APPROVED), //42 juga
						array("value"=>"4","label"=>_LBL_REJECTED),
					);
	
	/*
	 *
	 */
	$arrRptStatusR 	= array(
						array("value"=>"22","label"=>_LBL_CHECKED),
						array("value"=>"41","label"=>_LBL_APPROVED), //42 juga
						array("value"=>"4","label"=>_LBL_REJECTED),
					);
	
	/*
	 *
	 */
	$arrComposition = array(
						array("value"=>1, "label"=>"&lt;1%", 		"label2"=>"<1%", 		"code"=>"A1"),
						array("value"=>2, "label"=>"1 to &lt;3%", 	"label2"=>"1 to <3%", 	"code"=>"A2"),
						array("value"=>3, "label"=>"3 to &lt;5%", 	"label2"=>"3 to <5%", 	"code"=>"A3"),
						array("value"=>4, "label"=>"5 to &lt;10%", 	"label2"=>"5 to <10%", 	"code"=>"A4"),
						array("value"=>5, "label"=>"10 to &lt;30%", "label2"=>"10 to <30%", "code"=>"A5"),
						array("value"=>6, "label"=>"30 to 60%", "label2"=>"30 to 60%", "code"=>"A6"),
						array("value"=>7, "label"=>"&gt;60%", 		"label2"=>">60%", 		"code"=>"A7")
					);
					
	function multi_in_array($value, $array) {
		foreach ($array AS $item) {
			if (!is_array($item)) {
				if ($item == $value) {
					return true;
				}
				continue;
			}

			if (in_array($value, $item)) {
				return true;
			}
			else if (multi_in_array($value, $item)) {
				return true;
			}
		}
		return false;
	}
	
	/*
	 *
	 */
	$arrFeedback = array(
					array("value"=>"1","label"=>_LBL_ENQUIRIES),
					array("value"=>"2","label"=>_LBL_COMMENTS),
					array("value"=>"3","label"=>_LBL_COMPLAINTS)
				);

?>
