<!DOCTYPE html>
<html>
<body>

There was an error with your request.  Here are the details.
<br/>

<?php 
	foreach($_GET as $key => $value )
	{
		echo $key . ' = ' . $value . '<br/>';
	}
?>

</body>
</html>