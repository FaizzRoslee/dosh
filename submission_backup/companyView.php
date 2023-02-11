<?php
/* * ****** */
$regID = "";
$regNo = "";
$companyNo = "";
$companyName = "";
$address = "";
$city = "";
$postcode = "";
$state = "";
$p_address = "";
$p_city = "";
$p_postcode = "";
$p_state = "";
$contactCode = "";
$phoneNo = "";
$faxNo = "";
$email = "";
/* * ****** */
$c_name = "";
$c_designation = "";
$c_mobile = "";
$c_email = "";
/* * ****** */
$userid = "";
/* * ****** */
$active = 1;
//$securityCode	= "";
/* * ****** */
if ($clientID != 0) {
    $dataColumns = "*";
    $dataTable = "sys_User u";
    $dataJoin = array("sys_User_Client uc" => "u.Usr_ID = uc.Usr_ID");
    $dataWhere = array("AND u.Usr_ID" => "= " . quote_smart($clientID));
    $arrayData = _get_arrayData($dataColumns, $dataTable, $dataJoin, $dataWhere);
    /*     * ****** */
    foreach ($arrayData as $detail) {
        $regID = $detail["Reg_ID"];
        $regNo = $detail["Reg_No"];
        $companyNo = $detail["Company_No"];
        $companyName = $detail["Company_Name"];
        $address = $detail["Address_Reg"];
        $city = $detail["City_Reg"];
        $postcode = $detail["Postcode_Reg"];
        $state = $detail["State_Reg"];
        $p_address = $detail["Address_Postal"];
        $p_city = $detail["City_Postal"];
        $p_postcode = $detail["Postcode_Postal"];
        $p_state = $detail["State_Postal"];
        $contactCode = "";
        $phoneNo = $detail["Phone_No"];
        $faxNo = $detail["Fax_No"];
        $email = $detail["Email"];
        /*         * ****** */
        $c_name = $detail["Contact_Name"];
        $c_designation = $detail["Contact_Designation"];
        $c_mobile = $detail["Contact_Mobile_No"];
        $c_email = $detail["Contact_Email"];
        /*         * ****** */
        $active = $detail["Active"];
    }
}
// var_dump($clientID);
//==========================================================
?>
<script language="javascript">
</script>
<?php
$lvlName = '';
foreach ($arrLevel as $detail) {
    if ($detail['value'] == $levelID) {
        $lvlName = $detail['label'];
        break;
    }
}
?>

<table border="0" class="contents" cellspacing="1" cellpadding="3" width="100%" align="center">
    <tr class="contents" height="28">
        <td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_COMPANY_INFO ?></td>
    </tr>
    <tr class="contents">
        <td width="20%" class="label"><?= _LBL_LEVEL_TYPE ?></td>
        <td colspan="3"><label><?= ($lvlName != '') ? $lvlName : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td width="20%" class="label"><?= _LBL_REGISTRATION_ID ?></td>
        <td colspan="3"><label><?= ($regID != '') ? $regID : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td width="20%" class="label"><?= _LBL_REGISTRATION_NO ?></td>
        <td colspan="3"><label><?= ($regNo != '') ? $regNo : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td width="20%" class="label"><?= _LBL_COMPANY_NO ?></td>
        <td colspan="3"><label><?= ($companyNo != '') ? $companyNo : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_COMPANY_NAME ?></td>
        <td colspan="3"><label><?= ($companyName != '') ? $companyName : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label" rowspan="3"><?= _LBL_REG_ADDRESS ?></td>
        <td width="30%" rowspan="3"><label><?= ($address != '') ? nl2br($address) : '-'; ?></label></td>
        <td class="label" width="20%"><?= _LBL_CITY ?></td>
        <td width="30%"><label><?= ($city != '') ? $city : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_POSTCODE ?></td>
        <td><label><?= ($postcode != '') ? $postcode : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_STATE ?></td>
        <td><label><?= ($state != '') ? _get_StrFromCondition('sys_State', 'State_Name', 'State_ID', $state) : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label" rowspan="3"><?= _LBL_POSTAL_ADDRESS ?></td>
        <td width="30%" rowspan="3"><label><?= ($p_address != '') ? nl2br($p_address) : '-'; ?></label></td>
        <td class="label"><?= _LBL_CITY ?></td>
        <td><label><?= ($p_city != '') ? $p_city : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_POSTCODE ?></td>
        <td><label><?= ($p_postcode != '') ? $p_postcode : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_STATE ?></td>
        <td><label><?= ($p_state != '') ? _get_StrFromCondition('sys_State', 'State_Name', 'State_ID', $p_state) : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_PHONE_NO ?></td>
        <td colspan="3"><label><?= ($phoneNo != '') ? $phoneNo : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_FAX_NO ?></td>
        <td colspan="3"><label><?= ($faxNo != '') ? $faxNo : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_EMAIL ?></td>
        <td colspan="3"><label><?= ($email != '') ? $email : '-'; ?></label></td>
    </tr>
    <tr class="contents" height="28">
        <td colspan="4" class="label title" style="vertical-align:middle"><?= _LBL_CONTACT_PERSON ?></td>
    </tr>
    <tr class="contents">
        <td width="20%" class="label"><?= _LBL_NAME ?></td>
        <td colspan="3"><label><?= ($c_name != '') ? $c_name : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_DESIGNATION ?></td>
        <td colspan="3"><label><?= ($c_designation != '') ? $c_designation : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_MOBILE_NO ?></td>
        <td colspan="3"><label><?= ($c_mobile != '') ? $c_mobile : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_EMAIL ?></td>
        <td colspan="3"><label><?= ($c_email != '') ? $c_email : '-'; ?></label></td>
    </tr>
    <tr class="contents">
        <td class="label"><?= _LBL_STATUS ?></td>
        <td colspan="3"><label><?= _getLabel($active, $arrStatusAktif) ?></label></td>
    </tr>
</table>