<html>
<head><title>Foodsharing Hannover - Wahlformular</title></head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
<body>
<h1>Foodsharing Wahl 2020</h1>
<?php 

require_once 'formr/class.formr.php';

$form = new Formr('bootstrap');
$form->required = '*';

if($form->submit()) {
    
    // process and validate the POST data
    $code = $form->post('code','Wahl-Code');
    // check if there were any errors
    if(!$form->errors()) {
        // no errors
        // user has entered a valid email address, username, and confirmed their password
        echo $form->success_message('Success!');
    }
} else {
    echo $form->form_open();
    echo $form->input_text('code','Wahl-Code');
    echo $form->input_submit();
    
    
}
?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>


</body>
</html>
