<!DOCTYPE html>
<html>
<body>

<p>Thank you for your purchase.  Here are the return parameters for building your receipt or storing information in a database.  I would store the PNREF for your records</p>

<?php 
	foreach($_GET as $key => $value )
	{
		echo $key . ' = ' . $value . '<br/>';
	}
?>

</body>
</html>