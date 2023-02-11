<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="CIMS - Jabatan Keselamatan & Kesihatan Pekerjaan">
    <meta name="author" content="bahagian kimia , dosh">
    <meta name="keyword" content="bahagian kimia,cims system,malaysia">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>CIMS - Jabatan Keselamatan & Kesihatan Pekerjaan</title>

 


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


    <style>
.glyphicon-chevron-left:before {
  content: "<";
}
.glyphicon-chevron-right:before {
  content: ">";
}
.carousel-inner > .item > img,
.carousel-inner > .item > a > img {
  display: block;
  max-width: 100%;
  height: auto;
}
.carousel {
  position: relative;
}
.carousel-inner {
  position: relative;
  width: 100%;
  overflow: hidden;
}
.carousel-inner > .item {
  position: relative;
  display: none;
  -webkit-transition: .6s ease-in-out left;
       -o-transition: .6s ease-in-out left;
          transition: .6s ease-in-out left;
}
.carousel-inner > .item > img,
.carousel-inner > .item > a > img {
  line-height: 1;
}
@media all and (transform-3d), (-webkit-transform-3d) {
  .carousel-inner > .item {
    -webkit-transition: -webkit-transform .6s ease-in-out;
         -o-transition:      -o-transform .6s ease-in-out;
            transition:         transform .6s ease-in-out;

    -webkit-backface-visibility: hidden;
            backface-visibility: hidden;
    -webkit-perspective: 1000px;
            perspective: 1000px;
  }
  .carousel-inner > .item.next,
  .carousel-inner > .item.active.right {
    left: 0;
    -webkit-transform: translate3d(100%, 0, 0);
            transform: translate3d(100%, 0, 0);
  }
  .carousel-inner > .item.prev,
  .carousel-inner > .item.active.left {
    left: 0;
    -webkit-transform: translate3d(-100%, 0, 0);
            transform: translate3d(-100%, 0, 0);
  }
  .carousel-inner > .item.next.left,
  .carousel-inner > .item.prev.right,
  .carousel-inner > .item.active {
    left: 0;
    -webkit-transform: translate3d(0, 0, 0);
            transform: translate3d(0, 0, 0);
  }
}
.carousel-inner > .active,
.carousel-inner > .next,
.carousel-inner > .prev {
  display: block;
}
.carousel-inner > .active {
  left: 0;
}
.carousel-inner > .next,
.carousel-inner > .prev {
  position: absolute;
  top: 0;
  width: 100%;
}
.carousel-inner > .next {
  left: 100%;
}
.carousel-inner > .prev {
  left: -100%;
}
.carousel-inner > .next.left,
.carousel-inner > .prev.right {
  left: 0;
}
.carousel-inner > .active.left {
  left: -100%;
}
.carousel-inner > .active.right {
  left: 100%;
}
.carousel-control {
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  width: 15%;
  font-size: 20px;
  color: #fff;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0, 0, 0, .6);
  background-color: rgba(0, 0, 0, 0);
  filter: alpha(opacity=50);
  opacity: .5;
}
.carousel-control.left {
  background-image: -webkit-linear-gradient(left, rgba(0, 0, 0, .5) 0%, rgba(0, 0, 0, .0001) 100%);
  background-image:      -o-linear-gradient(left, rgba(0, 0, 0, .5) 0%, rgba(0, 0, 0, .0001) 100%);
  background-image: -webkit-gradient(linear, left top, right top, from(rgba(0, 0, 0, .5)), to(rgba(0, 0, 0, .0001)));
  background-image:         linear-gradient(to right, rgba(0, 0, 0, .5) 0%, rgba(0, 0, 0, .0001) 100%);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#80000000', endColorstr='#00000000', GradientType=1);
  background-repeat: repeat-x;
}
.carousel-control.right {
  right: 0;
  left: auto;
  background-image: -webkit-linear-gradient(left, rgba(0, 0, 0, .0001) 0%, rgba(0, 0, 0, .5) 100%);
  background-image:      -o-linear-gradient(left, rgba(0, 0, 0, .0001) 0%, rgba(0, 0, 0, .5) 100%);
  background-image: -webkit-gradient(linear, left top, right top, from(rgba(0, 0, 0, .0001)), to(rgba(0, 0, 0, .5)));
  background-image:         linear-gradient(to right, rgba(0, 0, 0, .0001) 0%, rgba(0, 0, 0, .5) 100%);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00000000', endColorstr='#80000000', GradientType=1);
  background-repeat: repeat-x;
}
.carousel-control:hover,
.carousel-control:focus {
  color: #fff;
  text-decoration: none;
  filter: alpha(opacity=90);
  outline: 0;
  opacity: .9;
}
.carousel-control .icon-prev,
.carousel-control .icon-next,
.carousel-control .glyphicon-chevron-left,
.carousel-control .glyphicon-chevron-right {
  position: absolute;
  top: 50%;
  z-index: 5;
  display: inline-block;
  margin-top: -10px;
}
.carousel-control .icon-prev,
.carousel-control .glyphicon-chevron-left {
  left: 50%;
  margin-left: -10px;
}
.carousel-control .icon-next,
.carousel-control .glyphicon-chevron-right {
  right: 50%;
  margin-right: -10px;
}
.carousel-control .icon-prev,
.carousel-control .icon-next {
  width: 20px;
  height: 20px;
  font-family: serif;
  line-height: 1;
}
.carousel-control .icon-prev:before {
  content: '\2039';
}
.carousel-control .icon-next:before {
  content: '\203a';
}
.carousel-indicators {
  position: absolute;
  bottom: 10px;
  left: 50%;
  z-index: 15;
  width: 60%;
  padding-left: 0;
  margin-left: -30%;
  text-align: center;
  list-style: none;
}
.carousel-indicators li {
  display: inline-block;
  width: 10px;
  height: 10px;
  margin: 1px;
  text-indent: -999px;
  cursor: pointer;
  background-color: #000 \9;
  background-color: rgba(0, 0, 0, 0);
  border: 1px solid #fff;
  border-radius: 10px;
}
.carousel-indicators .active {
  width: 12px;
  height: 12px;
  margin: 0;
  background-color: #fff;
}
.carousel-caption {
  position: absolute;
  right: 15%;
  bottom: 20px;
  left: 15%;
  z-index: 10;
  padding-top: 20px;
  padding-bottom: 20px;
  color: #fff;
  text-align: center;
  text-shadow: 0 1px 2px rgba(0, 0, 0, .6);
}
.carousel-caption .btn {
  text-shadow: none;
}
@media screen and (min-width: 768px) {
  .carousel-control .glyphicon-chevron-left,
  .carousel-control .glyphicon-chevron-right,
  .carousel-control .icon-prev,
  .carousel-control .icon-next {
    width: 30px;
    height: 30px;
    margin-top: -10px;
    font-size: 30px;
  }
  .carousel-control .glyphicon-chevron-left,
  .carousel-control .icon-prev {
    margin-left: -10px;
  }
  .carousel-control .glyphicon-chevron-right,
  .carousel-control .icon-next {
    margin-right: -10px;
  }
  .carousel-caption {
    right: 20%;
    left: 20%;
    padding-bottom: 30px;
  }
  .carousel-indicators {
    bottom: 20px;
  }
}  
  .carousel-inner img {
          width: 100%; /* Set width to 100% */
      margin: auto;
  }
  .carousel-caption h3 {
      color: #fff !important;
  }
  @media (max-width: 600px) {
    .carousel-caption {
      display: none; /* Hide the carousel text when the screen is less than 600 pixels wide */
    }
  }


    </style>
      <!-- Icons -->
     <!-- Icons -->
    <link href="/theme/css/font-awesome.min.css" rel="stylesheet">
    <link href="/theme/css/simple-line-icons.css" rel="stylesheet">

    <!-- Premium Icons -->
    <link href="/theme/css/glyphicons.css" rel="stylesheet">
    <link href="/theme/css/glyphicons-filetypes.css" rel="stylesheet">
    <link href="/theme/css/glyphicons-social.css" rel="stylesheet">

    <!-- Main styles for this application -->

    <link href="/theme/css/style.css" rel="stylesheet">
<!-- Piwik -->
<script type="text/javascript">
  var _paq = _paq || [];
  /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
  _paq.push(['trackPageView']);
  _paq.push(['enableLinkTracking']);
  (function() {
    var u="//cims.dosh.gov.my/piwik/";
    _paq.push(['setTrackerUrl', u+'piwik.php']);
    _paq.push(['setSiteId', '1']);
    var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
    g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
  })();
</script>
<!-- End Piwik Code -->
</head>


<body class="app header-fixed aside-menu-fixed aside-menu-hidden">
    <header class="app-header navbar ">
        <button class="navbar-toggler mobile-sidebar-toggler hidden-lg-up" type="button">☰</button>
        <a class="navbar-brand" href="/"></a>
       
        <ul class="nav navbar-nav ml-auto hidden-sm-down" >
            
            <li class="nav-item dropdown hidden-md-down ">
                <a class="" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <i class="icon-list"></i> Related Links

                </a>
                
                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg">

                    <div class="dropdown-header text-center">
                        <strong>Select Related Link</strong>
                    </div>
                    <?php echo $link->Info_Desc; ?>
               
                    
                </div>
            </li>
            <li class="nav-item px-1">
                <a id="cari" class="" href="#">  <i class="icon-magnifier "></i> Search Chemical Information </a>
            </li>
            <li class="nav-item px-1">
                <a class="" href="/download/CIMS_USER_MANUAL_FOR_SUPPLIER_VERSION_2021.pdf">  <i class="icon-doc"></i> Download Manual</a>
            </li>
            <li class="nav-item px-1">
                <a id="about" class="" href="#"> <i class="icon-info"></i> About CIMS</a>
            </li>
            <li class="nav-item">
                <a class="nav-link navbar-toggler aside-menu-toggler" href="#"></a>
            </li>

        </ul>
        <ul class="nav navbar-nav ml-auto hidden-md-up" >
        	<a id="cari2" class="nav-link" href="#">  <i class="icon-magnifier "></i>  </a>
        </ul>

    </header>
    

        
    <div id="menu-m" class="sidebar hidden-md-up">

            <nav class="sidebar-nav">
                <ul class="nav">
                	<br /><br /><br /><br /> 
                	
            		<li class="nav-item">
                		<a id="link-m" class="nav-link" href="#">  <i class="icon-magnifier "></i> Related Links</a>
		            </li>
          			<li class="nav-item">
                		<a id="cari-m" class="nav-link" href="#">  <i class="icon-magnifier "></i> Search Chemical</a>
		            </li>
		            <li class="nav-item ">
		                <a class="nav-link" href="/download/CIMS_USER_MANUAL_FOR_SUPPLIER_VERSION_2021.pdf">  <i class="icon-doc"></i> Download Manual</a>
		            </li>
		            <li class="nav-item ">
		                <a id="about-m" class="nav-link" href="#"> <i class="icon-info"></i> About CIMS</a>
		            </li>
                </ul>
            </nav>
        </div>
 
   <main class="main">
   	<br /><br />	<br /><br />
    <div class="container" id="papar">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card-group mb-0">
                    <div class="card p-2">
                        <div class="card-block">
                            <form name="myForm" id="myForm" action="" method="post">
                            <h1>Login DEV_CIMS</h1>
                            <p class="text-muted">Sign In to your Dev Account</p>
                            <div class="input-group mb-1">
                                <span class="input-group-addon"><i class="icon-user"></i>
                                </span>
                                <input type="text" id="userName" name="userName" class="form-control" placeholder="Username">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-addon"><i class="icon-lock"></i>
                                </span>
                                <input type="password" id="<?=$_SESSION['password_field']?>" name="<?=$_SESSION['password_field']?>" class="form-control" placeholder="Password">
                                <input type="hidden" value="<?=$_SESSION['magic_string']?>" id="txt_magic_str" name="txt_magic_str" >
                            </div>
                            <div class="row">
                                <?php $rbType = 2; ?>
                                <div class="col-4">
                               <input type="radio" name="rbType" id="rbType1" value="1" <?= ($rbType==1)?'checked':'' ?> /> 
                               <label for="rbType1">DOSH Staff</label><br />
                                </div>
                               <div class="col-6">
                               <input type="radio" name="rbType" id="rbType2" value="2" <?= ($rbType==2)?'checked':'' ?>  /> 
                               <label for="rbType2"> Importer/Manufacturer</label>
                               </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit"  name="login" id="login" class="btn btn-primary px-2">Login</button>
                                </div>
                                <div class="col-6 text-right">
                                    <button id="lupapass" type="button" class="btn btn-link px-0">Forgot password?</button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <div class="card card-inverse card-primary py-3 hidden-md-down" style="width:44%;font-size: 100%;">
                        <div class="card-block text-center">
                            <div>
                                <h2>CHEMICAL INFORMATION MANAGEMENT SYSTEM (CIMS)</h2>
                                <br/>
                                <p>

Submit your inventory online! <br /> Submission of hazardous chemicals inventory every year is one of the CLASS Regulations 2013 requirements 
. Ensure submission is done between 1st January and 31st Mac every year to fulfil the requirement.

</p>
<button id="daftar" type="button" class="btn btn-primary active mt-1">Register Now!</button>
                            </div>
                            <div>
                                <div id="info">
 <?php foreach ($splash as $s) { ?>
 
                             	
 <div><?=$s->ayat?></div>

  <?php    
 } ?>  
</div>
                            </div>
                        </div>
                    </div>
                </div> 
          
                <div class="card">
          	
                	<div id="myCarousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
    	 <?php
    	   $kira =0;
      	   foreach ($banner as $b) {
			?>
			<?php if ($kira == 0) { ?>
      <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
      	<?php } 
			else {
      	?>
      	
      <li data-target="#myCarousel" data-slide-to="<?=$kira?>"></li>
    
       <?php
			}
			$kira++;
			}
		   ?>
    </ol>

    <div class="carousel-inner" role="listbox">
    	
   
      <?php
      	$count = 0;
      	   foreach ($banner as $b) {
			
			if ($count == 0){
				$first = "active";
			}
			else{
				$first = "";
			}
			
			?>
				
			 
			 
			 <div class="item <?=$first?>">
      	<a href="<?=$b->url?>" >
        <img src="<?=$b->Image_URL?>" alt="dosh" width="1200" height="700">
        </a>
        <div class="carousel-caption">
          	
          <h3></h3>
          <p></p>
          	
        </div>      
      </div>
			
			 <?php
			 $count++;
			 } ?>
        
      </div>
    </div>

    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
</div>


                </div>
            </div>
           
        </div>
        </main>
    </div>

    <!-- Bootstrap and necessary plugins -->
    <script src="/theme/js/libs/jquery.min.js"></script>
    <script src="/theme/js/libs/tether.min.js"></script>
    <script src="/theme/js/libs/bootstrap.min.js"></script>

    <script src="/theme/js/textition.js"></script>
    <script>
        $(document).ready(function() {
            
            $.get( "/authentication/kira.php", function( data ) {
                $("#jum_all").text(data.count.all);
                $("#jum_today").text(data.count.today);
            }, "json" );
            
            $('#info').textition({
                autoplay: true,
                speed: 1
            });
            
            $('#myForm').submit(function(e){
               
               $.post('/authentication/login.php',{userName:$('#userName').val(),<?=$_SESSION['password_field']?>:$("#<?=$_SESSION['password_field']?>").val(),rbType:$("input[name=rbType]:checked").val(),txt_magic_str:$("#txt_magic_str").val()},function(data){
                  
                  if(data == 1){
                      var redirect = '/authentication/home.php'; 
		      window.location = redirect;
                  }
                  else {
                     alert(data);   
                  }   
               });
               
               e.preventDefault();
            });
        });
        
        $("#about").click(function(event){
            
            $("#papar").load('/index.php/page/about');
            event.preventDefault();
        });
        $("#about-m").click(function(event){
            
            $("#papar").load('/index.php/page/about');
            //$("#menu-m").hide();
            $('body').addClass('');
            document.body.className = document.body.className.replace("app header-fixed aside-menu-fixed aside-menu-hidden sidebar-mobile-show","app header-fixed aside-menu-fixed aside-menu-hidden");

            event.preventDefault();
        });
        
        $("#link-m").click(function(event){
            
            $("#papar").html('<div class="dropdown-header text-center"><strong>Select Related Link</strong></div><?php echo preg_replace('/\s+/', '', $link->Info_Desc); ?>');
            document.body.className = document.body.className.replace("app header-fixed aside-menu-fixed aside-menu-hidden sidebar-mobile-show","app header-fixed aside-menu-fixed aside-menu-hidden");
            event.preventDefault();
        });

        $("#cari").click(function(event){
            
            $("#papar").load('/authentication/complete_search.php');
            event.preventDefault();
        });
        $("#cari2").click(function(event){
            
            $("#papar").load('/authentication/complete_search.php');
            event.preventDefault();
        });
        $("#cari-m").click(function(event){
            
            $("#papar").load('/authentication/complete_search.php');
            document.body.className = document.body.className.replace("app header-fixed aside-menu-fixed aside-menu-hidden sidebar-mobile-show","app header-fixed aside-menu-fixed aside-menu-hidden");
            event.preventDefault();
        });
        
         $("#daftar").click(function(event){
            
            $("#papar").load('/authentication/registration.php');
            event.preventDefault();
        });
         $("#lupapass").click(function(event){
            
            $("#papar").load('/authentication/forgotPassword.php');
            event.preventDefault();
        });
    </script>
    
    <!-- GenesisUI main scripts -->

    <script src="/theme/js/app.js"></script>





    <!-- Plugins and scripts required by this views -->
    <script src="/theme/js/libs/toastr.min.js"></script>
    <script src="/theme/js/libs/gauge.min.js"></script>
    <script src="/theme/js/libs/moment.min.js"></script>
    <script src="/theme/js/libs/daterangepicker.js"></script>


    <!-- Custom scripts required by this v_i_e_w -->

</body>

</html>
