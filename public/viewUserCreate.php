<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>SMS Relay: Users</title>
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
          <a class="brand" href="#">Project name</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li class="active"><a href="#">Home</a></li>
              <li><a href="#about">About</a></li>
              <li><a href="#contact">Contact</a></li>
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                <ul class="dropdown-menu">
                  <li><a href="#">Action</a></li>
                  <li><a href="#">Another action</a></li>
                  <li><a href="#">Something else here</a></li>
                  <li class="divider"></li>
                  <li class="nav-header">Nav header</li>
                  <li><a href="#">Separated link</a></li>
                  <li><a href="#">One more separated link</a></li>
                </ul>
              </li>
            </ul>
            <form class="navbar-form pull-right" id="sign-in">
              <input class="span2" type="text" placeholder="Email">
              <input class="span2" type="password" placeholder="Password">
              <button type="submit" class="btn">Sign in</button>
            </form>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
		<!-- Start > First Row -->		
		<div class="row">
			<!-- Start > User Form -->
			<div class="span6">
			<form id="user-form" class="form-horizontal" onsubmit="ajaxCreateOrUpdateUser(this); return false">
					<legend>Create User</legend>
					
					<div class="control-group">
						<label class="control-label">Username</label>
						<div class="controls">
							<div class="input-prepend">
							<span class="add-on"><i class="icon-user"></i></span>
								<input type="text" class="input-xlarge" id="input-username" name="username" placeholder="Username">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Password</label>
						<div class="controls">
							<div class="input-prepend">
							<span class="add-on"><i class="icon-lock"></i></span>
								<input type="Password" id="input-password" class="input-xlarge" name="password" placeholder="Password">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">URL</label>
						<div class="controls">
							<div class="input-prepend">
							<span class="add-on"><i class="icon-envelope"></i></span>
								<input type="text" class="input-xlarge" id="input-url" name="url" placeholder="URL">
							</div>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label">Numbers</label>
						<div class="controls">
							<div class="input-prepend">
							<span class="add-on"><i class="icon-signal"></i></span>
								<input type="Text" id="input-numbers" class="input-xlarge" name="numbers" placeholder="Numbers">
							</div>
						</div>
					</div>
					<div class="control-group">
					<label class="control-label"></label>
					<div class="controls">
					<button type="submit" class="btn btn-success" >Submit User</button>
				</div>
				</div>
			</form>
			</div> 
			<!-- End > User Form -->	
			<!-- Start > User List -->
			<div class="span5">
			<legend>User List</legend>
			
			<table id="usersTable" class="table table-hover table-condensed">
				<thead>
				  <tr>
					<th>ID</th>
					<th>Username</th>
				  </tr>
				</thead>
				<tbody>
				</tbody>
			  </table>			
			
			</div> 
			<!-- End > User List -->		
		</div>
		<!-- Start > First Row -->
	</div>

	<script type="text/javascript">
	function ajaxCreateOrUpdateUser(formElement)
	{
		var formValueQueryString = jQuery(formElement).formSerialize();
		var formValueJson = formValueQueryString.QueryStringToJSON();
		
		// Create User
		if (isNaN(formValueJson.id)) 
		{
			jQuery.ajax({
				type: 'GET',
				url: '../apps/controllerUserCreateOrUpdate.php?' + formValueQueryString,
				success: function(data) {
					var savedUserData = data;
					appendNewUserRow(savedUserData);
					jQuery(formElement).clearForm();
				}
			});		
		}
		// Update User
		else
		{
			jQuery.ajax({
				type: 'GET',
				url: '../apps/controllerUserCreateOrUpdate.php?' + formValueQueryString,
				success: function(data) {
					var updatedUserData = data;
					modifyUpdatedUserRow(updatedUserData);
					jQuery(formElement).clearForm();
					console.log(jQuery(formElement).children("input:hidden").remove());
				}
			});	
		}
	}
	
	function ajaxRetrieveUsersAndShow()
	{
		jQuery.ajax({
			type: 'GET',
			url: '../apps/controllerUserRetrieve.php',
			success: function(data) {
				var retrievedUsersData = jQuery.parseJSON(data);
				
				for(var i in retrievedUsersData)
				{
					jQuery("#usersTable tbody").append(
						"<tr><td>" 
						+	retrievedUsersData[i].id
						+ "</td><td>" 
						+	retrievedUsersData[i].username
						+ "<a href='#'><i id='user-id-" 
						+  retrievedUsersData[i].id
						+ "' class='icon-edit pull-right'></i></a>"		
						+ "</td></tr>"
					);
				}
			}
		});				
	}

	function ajaxRetrieveUserAndFillForm(userId)
	{
		jQuery.ajax({
			type: 'GET',
			url: '../apps/controllerUserRetrieve.php?id=' + userId,
			success: function(data) {
				var retrievedUserData = jQuery.parseJSON(data);
				if (jQuery("#input-id").length === 0)
				{
					jQuery("#user-form").append('<input type="hidden"id="input-id" name="id" value="">');
				}
				jQuery('#input-id').val(retrievedUserData.id);
				jQuery('#input-username').val(retrievedUserData.username);
				jQuery('#input-password').val(retrievedUserData.password);
				jQuery('#input-url').val(retrievedUserData.url);
				jQuery('#input-numbers').val(retrievedUserData.numbers);
			}
		});
	}
	
	function appendNewUserRow(data)
	{
		var savedUserData = jQuery.parseJSON(data);
		if (savedUserData.status)
		{
			jQuery("#usersTable tbody").append(
				"<tr class='success'><td>" 
				+	savedUserData.id
				+ "</td><td>" 
				+	savedUserData.username
				+ "<a href='#'><i id='user-id-" 
				+  savedUserData.username
				+ "' class='icon-edit pull-right'></i></a>"
				+ "</td></tr>"
			);	
		}
	}

	function modifyUpdatedUserRow(data)
	{
		var updatedUserData = jQuery.parseJSON(data);
		if (updatedUserData.status)
		{
			var trUpdatedRow = jQuery('#user-id-' + updatedUserData.id).parents('tr');
			jQuery(trUpdatedRow).html(
				'<td>' 
				+ updatedUserData.id 
				+ '</td><td>' 
				+ updatedUserData.username
				+ '<a href="#"><i id="user-id-' 
				+ updatedUserData.id
				+ '" class="icon-edit pull-right"></i></a></td>'
			);
		}
	}
	
	jQuery(function($){
		ajaxRetrieveUsersAndShow();
		jQuery('#usersTable').bind('click', function(event) {
			var elementUserId = jQuery(event.target).attr('id').replace( /\D/g, '');
			if (elementUserId !== undefined) 
			{ 
				ajaxRetrieveUserAndFillForm(elementUserId); 
			} 
		});
	});
	</script>
	


  </body>
</html>
