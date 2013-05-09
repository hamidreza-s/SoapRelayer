<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SMS Relay: SMS(es)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le javascript -->
    <script src="./js/jquery-1.7.2.min.js"></script>	
    <script src="./js/jquery-form.js"></script>	
    <script src="./js/query-to-json.js"></script>	
	
    <!-- Le styles -->
    <link href="./css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
    </style>
    <link href="./css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="./js/html5shiv.js"></script>
    <![endif]-->

    <!-- Fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="./ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="./ico/apple-touch-icon-114-precomposed.png">
      <link rel="apple-touch-icon-precomposed" sizes="72x72" href="./ico/apple-touch-icon-72-precomposed.png">
                    <link rel="apple-touch-icon-precomposed" href="./ico/apple-touch-icon-57-precomposed.png">
                                   <link rel="shortcut icon" href="./ico/favicon.png">
  </head>

  <body>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="brand" href="#">Armaghan (User)</a>
          <div class="nav-collapse collapse">
            <ul class="nav">

            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
		<!-- Start > First Row -->		
		<div class="row">
			<div class="span2" id="signin-or-welcome">
				<form class="form-signin" id="form-signin" method="post">
					<input type="text" class="input-block-level" name="username" placeholder="Username">
					<input type="password" class="input-block-level" name="password" placeholder="Password">
					<button class="btn btn-large btn-primary" type="submit">Sign in</button>
					<div id="signin-status"></div>
				</form>
			</div>
			<div class="span10" id="sms-list"></div>
		</div>
			

		
		</div>
		<!-- Start > First Row -->
	</div>

	<script type="text/javascript">

	jQuery(function($){

	});
	</script>
	


  </body>
</html>
