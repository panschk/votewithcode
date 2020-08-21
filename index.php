<html>
<head><title>Foodsharing Hannover - Wahlformular</title></head>

<body>
<?php 

require_once 'formr/class.formr.php';
$form = new Formr('bootstrap');

echo $form->create_form('Name, Email, Comments|textarea');
?>

</body>
</html>
