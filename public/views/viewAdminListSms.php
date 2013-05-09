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
          <a class="brand" href="#">Armaghan (Admin)</a>
          <div class="nav-collapse collapse">
            <ul class="nav">
              <li><a href="viewAdminManageUser.php">Manage User</a></li>
              <li class="active"><a href="#">Manage SMS</a></li>
            </ul>
          </div><!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container">
		<!-- Start > First Row -->		
		<div class="row">
			<!-- Start > User List -->
			<div class="span6">
			<legend>User List</legend>
			
			<table id="user-table" class="table table-hover table-condensed">
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
			<!-- Start > User SMSes -->
			<div class="span6">
				<legend>User SMSes</legend>
					<div class="user-sms">
					</div>			
			</div> 
			<!-- End > User SMSes -->	
	
		</div>
		<!-- Start > First Row -->
	</div>

	<script type="text/javascript">
	function ajaxRetrieveUsersAndShow()
	{
		jQuery.ajax({
			type: 'GET',
			url: '../apps/controllerUserRetrieve.php',
			success: function(data) {
				var retrievedUsersData = jQuery.parseJSON(data);
				for(var i in retrievedUsersData)
				{
					jQuery("#user-table tbody").append(
						"<tr><td>" 
						+	retrievedUsersData[i].id
						+ "</td><td>" 
						+	retrievedUsersData[i].username
						+ "<a href='#'><i id='user-id-" 
						+  retrievedUsersData[i].id
						+ "' class='icon-arrow-right pull-right'></i></a>"		
						+ "</td></tr>"
					);
				}
				bindClickEventToUsers();
			}
		});				
	}

	
	function ajaxRetrieveSmsAndShow(userId)
	{
		jQuery.ajax({
			type: 'GET',
			url: '../apps/controllerSmsRetrieve.php?id=' + userId,
			success: function(data) {
				var retrievedSmsData = jQuery.parseJSON(data);
				jQuery(".user-sms").empty();
				for(var i in retrievedSmsData)
				{
					jQuery(".user-sms").append(
						"<br/>ID:" 
						+	retrievedSmsData[i].id
						+ "<br/>User ID:" 
						+	retrievedSmsData[i].user_id
						+ "<br/>From:" 
						+  retrievedSmsData[i].from
						+ "<br/>To:"		
						+  retrievedSmsData[i].to
						+ "<br/>Text:"	
						+  retrievedSmsData[i].text
						+ "<br/>Encoding:"	
						+  retrievedSmsData[i].encoding
						+ "<br/>Byte Length:"			
						+  retrievedSmsData[i].byte_length
						+ "<br/>Char Lenght:"		
						+  retrievedSmsData[i].char_length
						+ "<br/>Date:"		
						+  retrievedSmsData[i].date
						+ "<br/>Recepient ID:"		
						+  retrievedSmsData[i].recipient_id
						+ "<br/>Status ID:"		
						+  retrievedSmsData[i].status_id
						+ "<br/>"								
					);
				}
			}
		});				
	}

	function bindClickEventToUsers()
	{
		jQuery('#user-table').find('i').bind('click', function(event) {
			var elementUserId = jQuery(event.target).attr('id').replace( /\D/g, '');
			if (elementUserId !== undefined) 
			{ 
				ajaxRetrieveSmsAndShow(elementUserId); 
			}		
		});
	}
	
	jQuery(function($){
		ajaxRetrieveUsersAndShow();
	});
	</script>
	


  </body>
</html>
