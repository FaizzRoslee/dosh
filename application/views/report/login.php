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
                            <h1>Login CIMS</h1>
		            <p class="text-muted">Dosh CIMS Reporting System </p>
                            <div class="input-group mb-1">
                                <span class="input-group-addon"><i class="icon-user"></i>
                                </span>
                                <input type="text" id="userName" name="userName" class="form-control" placeholder="Username">
                            </div>
                            <div class="input-group mb-2">
                                <span class="input-group-addon"><i class="icon-lock"></i>
                                </span>
                                <input type="password" id="<?=$_SESSION['password_field']?>" name="<?=$_SESSION['password_field']?>" class="form-control" placeholder="Password">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit"  name="login" id="login" class="btn btn-primary px-2">Login</button>
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

Dosh Reporting System 
</p>
                            </div>
                            <div>
                                <div id="info">
 
                             	
 <div></div>

</div>
                            </div>
                        </div>
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
            $('#myForm').submit(function(e){
    		e.preventDefault();           
               $.post('/reportadmin/checklogin',{id:$('#userName').val(),pass:$('#<?=$_SESSION['password_field']?>').val()},function(data){
                  
                  if(data == 'Successfully Logged in...'){
                      var redirect = '/dosh'; 
		      window.location = redirect;
                  }
                  else {
                     alert(data);   
                  }   
               });
               
            });
        
    </script>
    
    <!-- GenesisUI main scripts -->

    <script src="/theme/js/app.js"></script>





    <!-- Plugins and scripts required by this views -->
    <script src="/theme/js/libs/toastr.min.js"></script>
    <script src="/theme/js/libs/gauge.min.js"></script>
    <script src="/theme/js/libs/moment.min.js"></script>
    <script src="/theme/js/libs/daterangepicker.js"></script>


    <!-- Custom scripts required by this view -->

</body>

</html>
