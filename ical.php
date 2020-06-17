<?php


$calData = "BEGIN:VCALENDAR\n"; 
$calData .="VERSION:2.0\n";
foreach(getOccupations() as $event){
  $calData.=getICALEntry($event['start'], $event['end'], 'space occupied', '');
}
$calData .="END:VCALENDAR";

print($calData);

function getOccupations(){
  $json = file_get_contents("occ.json");
  $dec = json_decode($json, true);

  $occupations = array();

  foreach($dec as $occ){
    $st = DateTime::createFromFormat("Y-m-d H:i:s.u", $occ['start']['date']);
    $end = DateTime::createFromFormat("Y-m-d H:i:s.u", $occ['end']['date']);

    $occupations[] = array(
      'start' => $st->format("Ymd\TH0000"),
      'end' => $end->format("Ymd\TH0000")
    );
  }

  return $occupations;
}

function getICALEntry($start, $end, $name, $desc)
{
	$entry ="BEGIN:VEVENT\n";
	$entry .="SUMMARY:".$name."\n";
	$entry .="DTSTART;TZID=Europe/Berlin:".$start."\n";
	$entry .="DTEND;TZID=Europe/Berlin:".$end."\n";
	$entry .="LOCATION:\n";
	$entry .="DESCRIPTION:".$desc."\n";
	$entry .="END:VEVENT\n";
	return $entry; 

}

?>
