<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>Wrongly entered</title>
</head>
<body>
<h1>File wrongly entered</h1>
<p>
This page has not been entered correctly. Please try again from a standard page.
</p>
<?php
if (strlen($mess) != 0)  {
	$qmess = htmlspecialchars($mess);
	print <<<EOT
<p>
The actual error message was $qmess.
</p>
EOT;
}
?>
</body>
</html>
