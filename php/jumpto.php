<html>
<head>
<title>Jump to</title>
<script language="JavaScript">
function jumpto(loc) {
	document.location = loc;
}
</script>
</head>
<?php
print <<<EOT
<body onload="javascript:jumpto('$loc');">
<p>Please <a href="$loc">click here</a> if you are not taken to the right page.
</p>

EOT;
?>
</body>
</html>
