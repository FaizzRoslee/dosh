<?php
	//****************************************************************/
	// filename: empList.php
	// description: list of the employee
	//****************************************************************/
	include_once '../includes/sys_config.php';
	//==========================================================
	include_once $sys_config['includes_path'].'db_config.php';
	include_once $sys_config['includes_path'].'func_master.php';
	include_once $sys_config['includes_path'].'func_header.php';
	include_once $sys_config['includes_path'].'func_window.php';
	include_once $sys_config['includes_path'].'func_rand_str.php';
	include_once $sys_config['includes_path'].'func_valid_login.php';
	include_once $sys_config['includes_path'].'func_get_user.php';
	//==========================================================
	if(isset($_POST['language'])) $language = $_POST['language'];
	elseif(isset($_GET['language'])) $language = $_GET['language'];
	else{
		if(isset($_SESSION['lang'])) $language = $_SESSION['lang'];
		else $language = 'eng';
	}
	if(empty($language)) $language = 'eng';
	$fileLang = $language.'.php';
	include_once $sys_config['languages_path'].$fileLang;
	//==========================================================
	include_once $sys_config['includes_path'].'formElement.php'; //need file language
	include_once $sys_config['includes_path'].'arrayCommon.php'; //need file language
	//==========================================================
	if(isset($_POST['language'])){
		$_SESSION['lang'] = $language;
	}
	
	if (isset($_SESSION['user'])){	// user already logged in, check session expired
		//if (!func_is_session_expired())  // if session not expired, redirect user to home page
		//{
			header("Location: " . $sys_config['authentication_path'] . "home.php");
			exit();
		//}
	}

	//======================================================================================================================
	// form pass back process (begin)-----------------------------------------------------------------------
	if (isset($_POST['login'])){
		// print_r($_POST);die;
	
		$error = 0;
		if (!isset($_SESSION['password_field'])){ // wrong session redirect to new session
			header("Location: " . $sys_config['system_url']);
			exit();
		}
		
		$passField	=	$_SESSION['password_field']; // the magic password field name from session
		if (!isset($_POST[$passField]))
		{	// cannot find the magic password field from the submitted form, 
			// this because user submit the form at wrong session
			// redirect user to new session
			header("Location: " . $sys_config['system_url']);
			exit();
		}
		if($_POST['rbType']==1){ $userType = 'sys_User_Staff'; }
		else{ $userType = 'sys_User_Client'; }
		
		//--login via systems--//
		if (func_is_valid_login($_POST['userName'],$_POST[$passField], $_SESSION['magic_string'],$userType))
		{	// valid user login
			$_SESSION['user']			= func_get_user($_POST['userName'],$userType);	// store user id into session
			$_SESSION['last_login']		= time(); // get current timestamp
			$_SESSION['lang'] 			= $language;
			
			unset($_SESSION['password_field']);		// clear session variables used during login process
			unset($_SESSION['magic_string']);		// clear session variables used during login process
			echo '
				<script language="JavaScript">
				location.href = "'.$sys_config['authentication_path'].'home.php";
				</script>
				';
			exit();

		}
		//--end login via systems --//
		else	// invalid user login
		{
			if ( (func_id_extists($_POST['userName'],$userType)==TRUE) && (func_is_usr_active($_POST['userName'],$userType)==FALSE) ){
				$str_error_msg = _LBL_MSG_NOT_ACTIVE;
			}elseif ( func_id_extists($_POST['userName'],$userType)==FALSE ){
				$str_error_msg = _LBL_MSG_NOT_REG;
			}else{
				$str_error_msg = _LBL_MSG_NOT_VALID;
			}	

			echo '
				<script language="Javascript">
				alert("'. $str_error_msg .'");
				location.href = "login.php";
				</script>
				';
		}
	}
	// form pass back process (end)-----------------------------------------------------------------------
	//======================================================================================================================
	// random generate password field name and magic string
	$_SESSION['password_field']	=	'txt_' . func_rand_str(32);		// magic password field name
	$_SESSION['magic_string']	=	func_rand_str(64);				// magic string for md5 purpose
	
	header("Cache-Control: no-store, no-cache, must-revalidate");	// force client not to cache
	func_header("",
				//$sys_config['includes_path']."css/style.compactLabels.css,". // css
				"",
				$sys_config['includes_path']."javascript/js_common.js,". // javascript
				$sys_config['includes_path']."javascript/js_validate.js,". // javascript
				//$sys_config['includes_path']."jquery/Qtip/jquery.qtip.min.js,". // javascript
				$sys_config['includes_path']."javascript/js_md5.js,". // javascript
				$sys_config['includes_path']."javascript/js_trim.js,". // javascript
				$sys_config['includes_path']."jquery/jquery.slides/js/jquery.slides.min.js,". // javascript
				"",
				false, // menu
				false, // portlet
				true, // header
				false, // frame
				'', // str url frame top
				'', // str url frame left
				'', // str url frame content
				false // int top for [enter]
				);
				
	if(isset($_POST['rbType'])) $rbType = $_POST['rbType'];
	else $rbType = 2;
?>
	<style>
    #slides {
      display: none
    }

    #slides .slidesjs-navigation {
      margin-top:5px;
    }

    a.slidesjs-next,
    a.slidesjs-previous,
    a.slidesjs-play,
    a.slidesjs-stop {
      background-image: url('../includes/jquery/jquery.slides/img/btns-next-prev.png');
      background-repeat: no-repeat;
      display:block;
      width:12px;
      height:18px;
      overflow: hidden;
      text-indent: -9999px;
      float: left;
      margin-right:5px;
    }

    a.slidesjs-next {
      margin-right:10px;
      background-position: -12px 0;
    }

    a:hover.slidesjs-next {
      background-position: -12px -18px;
    }

    a.slidesjs-previous {
      background-position: 0 0;
    }

    a:hover.slidesjs-previous {
      background-position: 0 -18px;
    }

    a.slidesjs-play {
      width:15px;
      background-position: -25px 0;
    }

    a:hover.slidesjs-play {
      background-position: -25px -18px;
    }

    a.slidesjs-stop {
      width:18px;
      background-position: -41px 0;
    }

    a:hover.slidesjs-stop {
      background-position: -41px -18px;
    }

    .slidesjs-pagination {
      margin: 7px 0 0;
      float: right;
      list-style: none;
    }

    .slidesjs-pagination li {
      float: left;
      margin: 0 1px;
    }

    .slidesjs-pagination li a {
      display: block;
      width: 13px;
      height: 0;
      padding-top: 13px;
      background-image: url('../includes/jquery/jquery.slides/img/pagination.png');
      background-position: 0 0;
      float: left;
      overflow: hidden;
    }

    .slidesjs-pagination li a.active,
    .slidesjs-pagination li a:hover.active {
      background-position: 0 -13px
    }

    .slidesjs-pagination li a:hover {
      background-position: 0 -26px
    }

    #slides a:link,
    #slides a:visited {
      color: #333
    }

    #slides a:hover,
    #slides a:active {
      color: #9e2020
    }
	</style>  
	<style>
    #slides {
      display: none
    }

    .slides-container {
      margin: 0 auto
    }

    /* For tablets & smart phones */
    @media (max-width: 767px) {
      body {
        padding-left: 20px;
        padding-right: 20px;
      }
      .slides-container {
        width: auto
      }
    }

    /* For smartphones */
    @media (max-width: 480px) {
      .slides-container {
        width: auto
      }
    }

    /* For smaller displays like laptops */
    @media (min-width: 768px) and (max-width: 979px) {
      .slides-container {
        width: auto
      }
    }

    /* For larger displays */
    @media (min-width: 1200px) {
      .slides-container {
        width: auto
      }
    }
	</style>
	<script language="JavaScript">
	$(document).ready(function() {	
	    if($.browser.msie && $.browser.version <= 9){
			func_placeholder();
		}
		$(".cls-supplier").show();
		$("input[name=rbType]").on('click',function(){
			if( $(this).val()==1 ){
				$('#userName').attr('placeholder', '<?php echo _LBL_STAFF_ID ?>');
				$(".cls-supplier").hide();
			}
			else if( $(this).val()==2 ){
				$('#userName').attr('placeholder', '<?php echo _LBL_USERID ?>');
				$(".cls-supplier").show();
			}
			
			if($.browser.msie && $.browser.version <= 9){
				func_placeholder();
			}
		});
		/* 
		if($("button").hasClass("btn-new-style"))
			$("button").removeClass("btn-new-style").addClass('btn btn-primary');		
		 */
		$('#slides').slidesjs({
			//width: 940,
			height: 300,
			play: {
			  active: true,
			  auto: true,
			  interval: 4000,
			  swap: true
			}
		});
	});
	
	(function($) {            
			$("[placeholder]").focus(function () {
                 if ($(this).val() == $(this).attr("placeholder")) $(this).val("");
             }).blur(function () {
                 if ($(this).val() == "") $(this).val($(this).attr("placeholder"));
             }).blur();


		func_placeholder = function(){
			$('input[placeholder]').each(function () {
				var obj = $(this);

				if (obj.attr('placeholder') != '') {
					obj.addClass('IePlaceHolder');
					if (($.trim(obj.val()) == '' || $.trim(obj.val()) != obj.attr('placeholder')) && obj.attr('type') != 'password') {
						obj.val(obj.attr('placeholder'));
					}
				}
			});

			$('.IePlaceHolder').on('focus', function () {
				var obj = $(this);
				if (obj.val() == obj.attr('placeholder')) {
					obj.val('');
				}
			});

			$('.IePlaceHolder').on('blur', function () {
				var obj = $(this);
				if ($.trim(obj.val()) == '') {
					obj.val(obj.attr('placeholder'));
				}
			});
			
			$('[placeholder]').closest('form').submit(function() {
				$(this).find('[placeholder]').each(function() {
					var input = $(this);
					if (input.val() == input.attr('placeholder')) {
						input.val('');
					}
				})
			});
		}
		
		func_checkForm = function(){
			
			var rbType = $("input[name=rbType]").filter(":checked").val();
			
			var userErrorMsg;
			if(rbType==1) userErrorMsg = "<?= _LBL_MSG_STAFFID_EMPTY ?>";
			else userErrorMsg = "<?= _LBL_MSG_USERNAME_EMPTY ?>";
			
			$("#userName").val( Trim( $("#userName").val() ) );
			$("#<?= $_SESSION['password_field']; ?>").val( Trim( $("#<?= $_SESSION['password_field']; ?>").val() ) );
		
			var str_username 	=	$("#userName").val();
			var str_password 	=	$("#<?= $_SESSION['password_field']; ?>").val();
			var str_magic_str	=	$("#txt_magic_str").val();
			
			if ((str_username.length > 0) && (str_password.length > 0)){
				str_password		=	hex_md5(str_password);
				str_password		=	str_password + str_magic_str;
				str_password		=	hex_md5(str_password);
				
				$("#<?= $_SESSION['password_field']; ?>").val( str_password.toUpperCase() );
				return true;
			}
		
			if (str_username.length <= 0){
				alert(userErrorMsg);
				$("#userName").focus();
			}else if (str_password.length <= 0){
				alert("<?= _LBL_MSG_PASSWORD_EMPTY; ?>");
				$("#<?= $_SESSION['password_field']; ?>").focus();
			}
		
			return false;
		}
		
	})(jQuery);
	</script>
	
    
	<form name="myForm" id="myForm" action="" method="post">
		<div class="container well">
			<div class="col-xs-5 col-sm-4 col-md-4 col-lg-4">
					<div class="panel panel-default">
						<div class="panel-heading"><b><?= _LBL_LANGUAGE ?></b></div>
						<div class="panel-body"><?php
							$param1 = 'language';
							$param2 = '';
							$param3 = $arrLanguage;
							$param4 = isset($_POST[$param1])?$_POST[$param1]:$language;
							$param5 = array('onChange'=>'document.myForm.submit();','class'=>'form-control');
							
							echo form_select($param1,$param2,$param3,$param4,$param5);
						?></div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading"><b><?= _LBL_LOGIN ?></b></div>
						<div class="panel-body">
							<div>
								<input type="radio" name="rbType" id="rbType1" value="1" <?= ($rbType==1)?'checked':'' ?> /> <label for="rbType1"><?= _LBL_LOGIN_STAFF ?></label><br /><input type="radio" name="rbType" id="rbType2" value="2" <?= ($rbType==2)?'checked':'' ?>  /> <label for="rbType2"><?= _LBL_LOGIN_USER ?></label>
							</div>
							<div>&nbsp;</div>
							<div><?php
								$param1 = "userName";
								$param2 = "";
								$param3 = array("type"=>"text" ,"class" => "form-control", 'placeholder' => _LBL_USERID);
								//echo form_input($param1,$param2,$param3);
								// echo '<label for="'.$param1.'" class="cls-staff">'. _LBL_STAFF_ID .'</label><label for="'.$param1.'" class="cls-supplier">'. _LBL_USERID .'</label>';
								echo '<div class="input-group"><div class="input-group-addon"><span class="glyphicon glyphicon-user"></span></div>';
								echo form_input($param1,$param2,$param3);
								echo '</div>';
							?></div>
							<br />
							<div><?php
								$param1 = $_SESSION['password_field'];
								$param2 = '';
								$param3 = array("type"=>"password","maxlength"=>"32","class" => "form-control", 'placeholder' => _LBL_PASSWORD);
								// echo form_input($param1,$param2,$param3);
								//echo '<label for="'. $param1 .'">'. _LBL_PASSWORD .'</label>';
								echo '<div class="input-group"><div class="input-group-addon"><span class="glyphicon glyphicon-lock"></span></div>';
								echo form_input($param1,$param2,$param3);
								echo '</div>';
								
								$param1 = 'txt_magic_str';
								$param2 = $_SESSION['magic_string'];
								$param3 = array('type'=>'hidden','style'=>'width:10px','maxlength'=>'64');
								echo form_input($param1,$param2,$param3);
							?></div>
							<br />
							<div><?php
								$param1 = 'login';
								$param2 = 'Login';
								$param3 = array('type'=>'submit','onClick'=>'return func_checkForm();','class'=>'btn btn-primary');
								echo form_button($param1,$param2,$param3);
							?></div>
							<br />
								<?php
							$failname = $sys_config['dl_path'].'CIMS User Manual for Supplier - Registration.pdf';
							if(is_file(trim($failname)))
								$a_url = 'href="'.$failname.'" target=\"_blank\"';
							else
								$a_url = 'href="#"';
						?>
							<div class="cls-supplier">
								<a href="<?= $sys_config['authentication_path'] ?>registration.php" title="<?= _LBL_NEW_USER ?>" target="_top"><?= _LBL_REGISTER ?></a>&nbsp;<a <?= $a_url ?> ><span class="glyphicon glyphicon-floppy-save" title="<?php echo _LBL_DL_MANUAL ?>" style="font-size: 140%;"></span></a><br />
								<a href="<?= $sys_config['authentication_path'] ?>forgotPassword.php" title="<?= _LBL_FORGOT_PASSWORD ?>" target="_top"><?= _LBL_FORGOT_PASSWORD ?></a>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-heading"><b><?= _LBL_CHEMICAL_INFO_SEARCH ?></b></div>
						<div class="panel-body text-center"><?php
							$imgParam1 = $sys_config['images_path'].'search-.png';
							$imgParam2 = _LBL_SEARCH_REG_CHEM;
							$imgParam3 = "location.href='".$sys_config['authentication_path']."complete_search.php'";
							$imgParam4 = array('width'=>'140','height'=>'110');
							
							echo _get_imagebutton2($imgParam1,$imgParam2,$imgParam3,$imgParam4);
						?></div>
					</div>
					<?php
					$linkInfo	= _get_StrFromCondition("sys_Info","Info_Desc","Info_ID","20");
					if(!empty($linkInfo)){
					?>
					<div class="panel panel-default">
						<div class="panel-heading"><b><?= _LBL_RELATED_LINKS ?></b></div>
						<div class="panel-body"><?php echo $linkInfo ?></div>
					</div>
					<?php } ?>
					<div class="panel panel-default">
						<div class="panel-heading"><b><?= _LBL_VISITOR ?></b></div>
						<div class="panel-body">
						<?php
							$sql = "INSERT INTO tbl_visitor_counter (visit_time) VALUES (NOW())";
							//var_dump($sql);
							$res = $db->sql_query($sql,END_TRANSACTION) or die(print_r($db->sql_error()));
							$counter_all = _get_RowExist('tbl_visitor_counter');
							$counter_today = _get_RowExist('tbl_visitor_counter', array("AND DATE_FORMAT(visit_time, '%d-%m-%Y')" => " = ". quote_smart(date('d-m-Y'))));
						?>
							<?php /*
							<ul class="list-group">
								<li class="list-group-item">
									<span class="badge"><?php echo number_format($counter_all,0) ?></span>
									<h5><strong><?php echo _LBL_TOTAL_VISITOR ?></strong></h5>
								</li>
								<li class="list-group-item">
									<span class="badge"><?php echo number_format($counter_today,0) ?></span>
									<h5><strong><span class="text_primary"><?php echo _LBL_TODAY_VISITOR ?></span></strong></h5>
								</li>
							</ul>
							*/ ?>
							<div class="col-sm-6">
								<div class="well text-center">
									<span class="badge"><?php echo number_format($counter_all,0) ?></span>
									<div class="text-primary h5"><strong><?php echo _LBL_TOTAL_VISITOR ?></strong></div>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="well text-center">
									<span class="badge"><?php echo number_format($counter_today,0) ?></span>
									<div class="text-primary h5"><strong><?php echo _LBL_TODAY_VISITOR ?></strong></div>
								</div>
							</div>
						</div>
					</div>
			</div>
			
			<div class="col-xs-7 col-sm-8 col-md-8 col-lg-8">				
				<?php
				$bannerInfo	= _get_StrFromCondition("sys_Info","Info_Desc","Info_ID","30");
				if(!empty($bannerInfo)){
				?>
				<div class="panel panel-default">
					<div class="panel-body">
						<div class="slides-container">
						
							<div id="slides">
								<?php echo $bannerInfo ?>
							<?php /* ?>
							  <img src="http://www.gettyimages.pt/gi-resources/images/Homepage/Hero/PT/PT_hero_42_153645159.jpg" alt="Photo by: Missy S Link: http://www.flickr.com/photos/listenmissy/5087404401/">
							  <img src="https://www.planwallpaper.com/static/images/Frozen-Logo-Symbol-HD-Images.jpg" alt="Photo by: Daniel Parks Link: http://www.flickr.com/photos/parksdh/5227623068/">
							  <img src="http://www.planwallpaper.com/static/images/magic-of-blue-universe-images.jpg" alt="Photo by: Mike Ranweiler Link: http://www.flickr.com/photos/27874907@N04/4833059991/">
							  <img src="http://msnbcmedia.msn.com/j/MSNBC/Components/Photo/_new/120215-coslog-galaxy-830a.660;660;7;70;0.jpg" alt="Photo by: Stuart SeegerLink: http://www.flickr.com/photos/stuseeger/97577796/">
							<?php */ ?>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				<div class="panel panel-default">
					<div class="panel-body">
					<?php
						$pageInfo	= _get_StrFromCondition('sys_Info','Info_Desc','Info_ID','2');
						echo $pageInfo;
					?>
					</div>
				</div>
			</div>
		</div>
		<div class="container well text-center footerdisclaimer"><?= _LBL_DISCLAIMER_INFORMATION ?></div>
	</form>