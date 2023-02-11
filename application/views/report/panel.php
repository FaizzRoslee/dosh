<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="author" content="Łukasz Holeczek">
    <link rel="shortcut icon" href="img/favicon.png">

    <title>CIMS JKKP REPORTING SYSTEM</title>

    <!-- Icons -->
    <link href="/theme/css/font-awesome.min.css" rel="stylesheet">
    <link href="/theme/css/simple-line-icons.css" rel="stylesheet">

    <!-- Premium Icons -->
    <link href="/theme/css/glyphicons.css" rel="stylesheet">
    <link href="/theme/css/glyphicons-filetypes.css" rel="stylesheet">
    <link href="/theme/css/glyphicons-social.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="/theme/css/style.css" rel="stylesheet">

</head>


<body class="app header-fixed aside-menu-fixed aside-menu-hidden">
    <header class="app-header navbar">
        <button class="navbar-toggler mobile-sidebar-toggler hidden-lg-up" type="button">☰</button>
        <a class="navbar-brand" href="#"></a>
        <ul class="nav navbar-nav hidden-md-down">
            <li class="nav-item">
                <a class="nav-link navbar-toggler sidebar-toggler" href="#">☰</a>
            </li>
	    <!--
            <li class="nav-item px-1">
                <a class="nav-link" href="#">Dashboard</a>
            </li>
            <li class="nav-item px-1">
                <a class="nav-link" href="#">Users</a>
            </li>
            <li class="nav-item px-1">
                <a class="nav-link" href="#">Settings</a>
            </li>
	    -->
        </ul>
        <ul class="nav navbar-nav ml-auto">
            <li class="nav-item dropdown hidden-md-down">
                <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                    <img src="/theme/img/avatars/6.jpg" class="img-avatar" alt="">
                    <span class="hidden-md-down">admin</span>
                </a>
                <div class="dropdown-menu dropdown-menu-right">

                    <div class="dropdown-header text-center">
                        <strong>Account</strong>
                    </div>

                    <a class="dropdown-item" href="#"><i class="fa fa-bell-o"></i> Profile</a>

                    <div class="divider"></div>
                    <a class="dropdown-item" href="/reportadmin/logout"><i class="fa fa-lock"></i> Logout</a>
                </div>
            </li>

        </ul>
    </header>

    <div class="app-body">
        <div class="sidebar">

            <nav class="sidebar-nav">
                <ul class="nav">
                    <li class="nav-title">
                        Dashboard
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dosh"><i class="icon-speedometer"></i> Dashboard </a>
                    </li>

                    <li class="divider"></li>
                    <li class="nav-title">
                        Sistem Laporan 
                    </li>
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link nav-dropdown-toggle" href="#"><i class="icon-printer"></i> Jenis Laporan</a>
                        <ul class="nav-dropdown-items">
                            <li  class="nav-item">
                                <a id="laporank1" class="nav-link" href="/laporan1">    Laporan Keseluruhan</a>
                            </li>
			   <li  class="nav-item">
                                <a id="laporank2" class="nav-link" href="/laporan2">    Laporan Data CIMS </a>
                           </li>
                           <li  class="nav-item">
                                <a id="laporank3" class="nav-link" href="/laporan3">    Laporan Data CIMS V2 </a>
                            </li> 
                        </ul>
                    </li>
                  
                   
                  
                  
                 

                </ul>
            </nav>
        </div>

        <!-- Main content -->
        <main class="main">

            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Report</li>
                <li id="namaatas" class="breadcrumb-item active">Dashboard</li>

                <!-- Breadcrumb Menu-->
		<!--
                <li class="breadcrumb-menu">
                    <div class="btn-group" role="group" aria-label="Button group with nested dropdown">
                        <a class="btn btn-secondary" href="#"><i class="icon-speech"></i></a>
                        <a class="btn btn-secondary" href="./"><i class="icon-graph"></i> &nbsp;Dashboard</a>
                        <a class="btn btn-secondary" href="#"><i class="icon-settings"></i> &nbsp;Settings</a>
                    </div>
                </li>
		-->
            </ol>


            <div id="isi" class="container-fluid">





                <div class="animated fadeIn">
                    <div class="row">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="card-block">
                                    <div class="h4 m-0"><?=$cham?></div>
                                    <div>Total Chemical</div>
                                    <div class="progress progress-xs my-1">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">Total Chemical Submit</small>
                                </div>
                            </div>
                        </div>
                        <!--/.col-->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="card-block">
                                    <div class="h4 m-0"><?=$subm?></div>
                                    <div>Total Submission</div>
                                    <div class="progress progress-xs my-1">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">Total Submission Approve by user</small>
                                </div>
                            </div>
                        </div>
                        <!--/.col-->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="card-block">
                                    <div class="h4 m-0"><?=$visit?></div>
                                    <div>Total Visit</div>
                                    <div class="progress progress-xs my-1">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">Total Visit CIMS.</small>
                                </div>
                            </div>
                        </div>
                        <!--/.col-->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card">
                                <div class="card-block">
                                    <div class="h4 m-0"><?=$client?></div>
                                    <div>Total User</div>
                                    <div class="progress progress-xs my-1">
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <small class="text-muted">Total User Register</small>
                                </div>
                            </div>
                        </div>
                        <!--/.col-->
                    </div>
                    <!--/.row-->

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    Malumat Terkini
                                </div>
                                <div class="card-block">
                                    <div class="row">
                                        <div class="col-md-12 px-2">
						<table class="table table-bordered table-striped table-condensed">
                                        <thead>
                                            <tr>
                                                <th>Date registered</th>
						<th>Submission ID</th>
                                                <th>Remark</th>
                                            </tr>
                                        </thead>
                                        <tbody>
					     <?php foreach ($terkini as $t){ ?>
					    <tr>
                                                <td><?=$t->RecordDate?></td>
                                                <td><?=$t->Submission_Submit_ID?></td>
                                                <td><?=$t->Submission_Remark?></td>
                                            </tr>
						<?php } ?>
                                        </tbody>
                                    </table>
	
					</div>
                                    </div>
                                </div>
                            </div>
                            <!--/.card-->
                        </div>
                    </div>

                   
                </div>


            </div>
            <!-- /.conainer-fluid -->
        </main>

    </div>

    <footer class="app-footer">
        <a href="https://www.dosh.gov.my">DOSH Malaysia</a> © 2021 CIMS Report.
        <span class="float-right">
             Develope by Seksyen Teknilogi Maklumat , BKP
        </span>
    </footer>

    <!-- Bootstrap and necessary plugins -->
    <script src="/theme/js/libs/jquery.min.js"></script>
    <script src="/theme/js/libs/tether.min.js"></script>
    <script src="/theme/js/libs/bootstrap.min.js"></script>
    <script src="/theme/js/libs/pace.min.js"></script>


    <!-- Plugins and scripts required by all views -->
    <script src="/theme/js/libs/Chart.min.js"></script>


    <!-- GenesisUI main scripts -->

    <script src="/theme/js/app.js"></script>





    <!-- Plugins and scripts required by this views -->
    <script src="/theme/js/libs/toastr.min.js"></script>
    <script src="/theme/js/libs/gauge.min.js"></script>
    <script src="/theme/js/libs/moment.min.js"></script>
    <script src="/theme/js/libs/daterangepicker.js"></script>


    <!-- Custom scripts required by this view -->
<script>
		$("#laporank1").click(function(d){
			d.preventDefault();
			$("#namaatas").text("Laporan 1");
			$('#isi').text('Sila tunggu, memuat ...').show();
			var page_url=$(this).prop('href');
			$('#isi').load(page_url).show();
		});
		$("#laporank2").click(function(d){
                        d.preventDefault();
                        $("#namaatas").text("Laporan Data CIMS");
                        $('#isi').text('Sila tunggu, memuat ...').show();
                        var page_url=$(this).prop('href');
                        $('#isi').load(page_url).show();
                });
		$("#laporank3").click(function(d){
                        d.preventDefault();
                        $("#namaatas").text("Laporan Data CIMS V2");
                        $('#isi').text('Sila tunggu, memuat ...').show();
                        var page_url=$(this).prop('href');
                        $('#isi').load(page_url).show();
                });

</script>
</body>

</html>
