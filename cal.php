<?php

$DAYS = 14;
occupy();

canOpen();

function canOpen(){
  if(isset($_GET["open"]) && $_GET["open"]=="1"){
    $now = new DateTime();
    if(occupied($now)){
      die("true");
    }
    die("false");
  }
}

function occupy(){
  if( 
    isset($_POST['startd']) && 
    isset($_POST['startt']) && 
    isset($_POST['endd']) && 
    isset($_POST['endt'])
  ){

    $st = DateTime::createFromFormat("Y-m-d H:i", $_POST['startd'].$_POST['startt']);
    $end= DateTime::createFromFormat("Y-m-d H:i", $_POST['endd'].$_POST['endt']);

    if(!$st || !$end){
      die("sth. went wrong");
    }

    if(occupied($st) || occupied($end)){
      die("allready occupied");
    }

    $occupations = getOccupations();
    $occupations[] = array(
      "start"=>$st,
      "end"=>$end,
    );

    $fp = fopen("occ.json", "w");
    fwrite($fp, json_encode($occupations, JSON_PRETTY_PRINT));
    fclose($fp);

  }
}

function head(){
  global $DAYS;
  $dt = new DateTime("yesterday");
  $res = "<tr>";

  $res .= "<th></th>";
  for($i=0; $i<$DAYS; $i++){
    $class= $dt == ((new DateTime())->setTime(0,0,0)) ? "today" : "";
    $res .= "<th class='$class'>". $dt->format("D d.") ."</th>";
    $dt->add(new DateInterval("P1D"));
  }
  $res .= "</tr>";

  return $res;
}

function body(){
  global $DAYS;
  $res = "";
  $dt = (new DateTime())->setTime(0,0,0);
  for($h=0; $h<24; $h++){
    $dt->setTime($h,0,0);
    $res .= "<tr><td>". $dt->format("H:00") ."</td>";
    for($d=0; $d<$DAYS; $d++){
      $class = occupied(tblIndexToDateTime($d, $h)) ? "occupied" : "";
      $res .= "<td class='$class'>";
      $res .= "</td>";
    }
    $res .= "</tr>";
  }
  return $res;
}

function tbl(){
  return head() . body(); 
}

function tblIndexToDateTime($d, $h){
  $dt = new DateTime("yesterday");
  $dt->add(new DateInterval("P".$d."D"));
  $dt->setTime($h,0,0);
  return $dt;
}

function occupied($dt){
  foreach(getOccupations() as $occ){
    if(within($dt, $occ)){
      return true;
    }
  }
  return false;
}

function pDate($s="", $dt){
  print($s . $dt->format("Ymd-Hi "));
}

function within($dt, $occ){
  return $dt >= $occ['start'] and $dt < $occ['end'];
}

function getOccupations(){
  $json = file_get_contents("occ.json");
  $dec = json_decode($json, true);

  $occupations = array();

  foreach($dec as $occ){
    $st = DateTime::createFromFormat("Y-m-d H:i:s.u", $occ['start']['date']);
    $end = DateTime::createFromFormat("Y-m-d H:i:s.u", $occ['end']['date']);

    $occupations[] = array(
      'start' => $st,
      'end' => $end 
    );
  }

  return $occupations;
}


?>
<html>
<head>
 <link rel="stylesheet" type="text/css" href="./style.css">
</head>
<body>

<form action="" method=post>
Occupy Space:<br>
from:
<input type="date" name="startd" placeholder="startd">
<input type="time" name="startt" placeholder="startt">
<br>
to:
<input type="date" name="endd" placeholder="endd">
<input type="time" name="endt" placeholder="endt">
<br>
<input type="submit" name="submit" value="occupy">
</form>

<table>
<?php
print(tbl());
?>
</table>

</body>
</html>
