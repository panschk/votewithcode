<html>
<head><title>Foodsharing Hannover - Wahlformular</title></head>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
<body>
<h1>Foodsharing Wahl 2020</h1>
<style>
body {
    padding: 10px;
}
.alert {
    background-color:ffcccc;
    color: red;
    border: 1px solid red;
}
.alert-success {
    background-color:ccffcc;
    color: green;
    border: 1px solid green;
}
</style>
<?php 

require_once 'formr/class.formr.php';
require_once 'settings.php';


$form = new Formr('bootstrap');

// Create connection
$conn = new mysqli($DB_SERVER, $DB_USER, $DB_PASSWORD, $DB_NAME);
// process and validate the POST data
if ($conn->connect_error) {
    die("Verbindung zur Datenbank konnte nicht aufgebaut werden: " . $conn->connect_error);
}
$voteDone = false;
if($form->submit()) {

    $code = $form->post('code','Wahl-Code');
    $cleancode = $conn->real_escape_string($code);
    // validate code
    $sql = "SELECT used FROM " . $TABLE_CODES . " where code = '" . $cleancode . "' and system_id = " . $SYSTEM_ID;
    $result = $conn->query($sql);
    if($row = $result->fetch_assoc()) {
        if ($row["used"] != null) {
            $form->add_to_errors('Der Code wurde bereits verwendet.');
        }
    } else {
        $form->add_to_errors('Der angegebene Code ist nicht valide');
        //echo("<div class = 'alert'></div>");
    }
    
    // validate vote
    $vote = array();
    $electionIds = array();
    $sql = "SELECT system_id, election_id, name, description, options, numvotes FROM " . $TABLE_ELECTIONS . " where system_id = " . $SYSTEM_ID . "";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $options = explode(',', $row["options"]);
        $votesPossible = $row["numvotes"];
        $votesMade = 0;
        $choices = "";
        $electionId = $row["election_id"];
        for ($i = 0; $i < count($options); $i++) {
            $cbId = $electionId . "_" . $i;
            $formVal = $form->post($cbId);
            if ($formVal) {
                $choices=$choices . $formVal . ",";
                $votesMade++;
            }
        }
        $vote[$electionId] = $choices;
        array_push($electionIds, $electionId);
        if ($votesMade < 1) {
            $form->add_to_errors('Bitte eine Auswahl treffen für ' . $row["name"]);
        }
        if ($votesMade > $votesPossible) {
            $pluralN = "";
            if ($votesPossible > 1) {
                $pluralN = "n";
            }
            $form->add_to_errors('Nicht mehr als '.$votesPossible.' Stimme'.$pluralN.' für ' . $row["name"] . ' zul&auml;ssig');
        }
    }
    if ($form->errors()) {
        echo $form->messages();
    } else {
        $voteId = rand (100000, 999999 );
        mysqli_begin_transaction ($conn);
        for ($i = 0; $i < count($electionIds); $i++) {
            $electionId = $electionIds[$i];
            $insertSQL = "INSERT INTO ".$TABLE_VOTES." (system_id, vote_id, election_id, vote) 
            VALUES (".$SYSTEM_ID.", '".$voteId."', ".$electionId.", '".$vote[$electionId]."')";
            if (!($conn->query($insertSQL) === TRUE)) {
                $conn->rollBack();
                $form->add_to_errors($conn->error);
                die("Problem bei SQL-Abfrage " .$insertSQL. $conn->error);
            }
        }
        $used = $date = date('Y-m-d H:i:s');
        $updateSQL = "UPDATE " . $TABLE_CODES . " SET used = '" . $used . "' where code = '" . $code . "'";
        if (!($conn->query($updateSQL) === TRUE)) {
            $conn->rollBack();
            $form->add_to_errors($conn->error);
            die("Problem bei SQL-Abfrage " . $updateSQL . $conn->error);
        }
        mysqli_commit($conn);
        $voteDone = true;
        $form->success_message("Wahl erfolgreich! Referenz: " . $voteId);
        echo $form->messages();
        
    }

}
if (!$voteDone) {
    echo $form->form_open();
    echo $form->input_text('code','Wahl-Code');
    $sql = "SELECT system_id, election_id, name, description, options, numvotes FROM " . $TABLE_ELECTIONS . " where system_id = " . $SYSTEM_ID . "";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        echo "<h2>" . $row["name"] . "</h2>";
        echo "<p>" . $row["description"] . "</p>";
        $options = explode(',', $row["options"]);
        for ($i = 0; $i < count($options); $i++) {
            $cbId = $row["election_id"] . "_" . $i;
            $option = $options[$i];
            echo $form->input_checkbox($cbId,$option,$option,$cbId);
        }
    }
    echo $form->input_submit();
    
}

$conn->close();
?>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>


</body>
</html>
