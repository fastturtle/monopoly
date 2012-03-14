<?php

//Array containing the names of each space Go = 0,...
$properties = array("Go", "Mediterranean Ave.", "Community Chest", "Baltic Ave.", "Income Tax", "Reading Railroad", "Oriental Ave.", "Chance", "Vermont Ave.", "Connecticut Ave.", "Jail", "St. Charles Place", "Electric Company", "States Ave.", "Virginia Ave.", "Pennsylvania Railroad", "St. James Place", "Community Chest", "Tennessee Ave.", "New York Ave.", "Free Parking", "Kentucky Ave.", "Chance", "Indiana Ave.", "Illinios Ave.", "B. & O. Railroad", "Atlantic Ave.", "Ventor Ave.", "Water Works", "Marvin Gardens", "Go to Jail", "Pacific Ave.", "North Carolina Ave.", "Community Chest", "Pennsylvania Ave.", "Short Line Railroad", "Chance", "Park Place,", "Luxury Tax", "Boardwalk");

$utilities = array("Electric Company", "Water Works");
$railroads = array("Reading Railroad", "Pennsylvania Railroad", "B. & O. Railroad", "Short Line Railroad");

//Array of the Chance Cards. If the Card doesn't change the player's locatation, it is considered to be null
$chanceCard = array("Go", "Illinios Ave.", "Utility", "Railroad", "St. Charles Place", null, null, "BackThree", "Jail", null, null, "Reading Railroad", "Boardwalk", null, null, null);

//Array of the Community Chest Cards. If the Card doesn't change the player's locatation, it is considered to be null
$communityCard = array("Go", null, null, null, "Jail", null, null, null, null, null, null, null, null, null, null, null);

//Array mapping the ownable properties to different colors
$colors = array(
	$dblue = array($properties[1], $properties[3]),
	$lblue = array($properties[6], $properties[8], $properties[9]),
	$purp = array($properties[11], $properties[13], $properties[14]),
	$orange = array($properties[16], $properties[18], $properties[19]),
	$red = array($properties[21], $properties[23], $properties[24]),
	$yellow = array($properties[26], $properties[27], $properties[29]),
	$green = array($properties[31], $properties[32], $properties[34]),
	$mblue = array($properties[37], $properties[39]));

//Array containing the different colors of ownable properties
$color_names = array("Dark Blue", "Light Blue", "Purple", "Orange", "Red", "Yellow", "Green", "Medium Blue");

//Array mapping the index of $properties to the corresponding rent value of that property
$rent = array(0,2,0,4,0,0,6,0,6,8,0,10,0,10,12,0,14,0,14,16,0,18,0,18,20,0,22,22,0,22,0,26,26,0,28,0,0,35,0,50);

$diceRolls = 3;
$position = 0;
$doubleCounter = 0;
//$roll = 0;
function calculate(){
	global $counter;
	global $properties;
	global $colors;
	global $color_names;
	global $rent;
	
	movePiece();
	for($i=0;$i<40;$i++){

		for($j = 0; $j < 8; $j++){
			if(in_array($properties[$i], $colors[$j])){
				$color_total[$j] += $counter[$i];
				$totalValue[$j] += ($rent[$i] * $counter[$i]);
				//echo $properties[$i];
				//echo $properties[$i]. '  ' . $color_total[$j] . '<br>';
			}
		}
	}
	
	for($i=0;$i<count($color_names);$i++){
		echo $color_names[$i] . ' was landed on ' . $color_total[$i]/1000 . '% of the time, and its EV is '. $totalValue[$i]*($color_total[$i]/100000) .'<br>';
		$grandtotal +=$color_total[$i];
	}
	echo $grandtotal;
}

calculate();

function movePiece(){
	global $roll;
	global $position;
	global $counter;

	for($i = 0; $i < 100000; $i++){
	
	    //Roll two six-sided dice
	    $die1 = rand(1,6);
	    $die2 = rand(1,6);
	
	    //Sum the two dice
	    $roll = $die1 + $die2;
	    //echo $roll;
		//Set the new position of the player
		$position += $roll;
		//There are only 40 possible positions
        $position = $position%40;

		//echo ($position.'<br>');
		
		//If the player rolls 3 doubles in row
		threeDoubles($die1,$die2);

		//If you land on a community chest position, draw a community chest card (only important for cards that change position)	
		if ($position === 2 || $position === 17 || $position === 33){
			communityChest();
		}
		
		// If you land on a chance position, draw a chance card (only important for cards that change position)
		if ($position === 7 || $position === 22 || $position === 36){
			chance();
		}
		
		$counter[$position]++;
		//echo ("\n Position: " . $position . "<br>");
	}

}



//Checks if the player rolls three doubles in a row
function threeDoubles($first,$second){
	global $position;
	global $doubleCounter;
		
	//If you roll doubles 3 times, go to jail
	if ($first === $second){
		$doubleCounter++;
		$doubleCounter = $doubleCounter%4;

		if($doubleCounter === 3){
			$position = 10;
			return $position;
		}
	}
	else{
		$doubleCounter = 0;
	}
	
}

//Called if the player lands on a Community Chest location
function communityChest(){
	global $position;
	global $communityCard;
	global $properties;

	//Randomly select a Community Chest Card
	$communitySelection = $communityCard[rand(0,15)];
		
	//If the card changes the player's location
	if($communitySelection){
	    //Change their location
		$position = array_search($communitySelection, $properties);
		return $position;
		
	}
	else{

		return $position;
	}
	
}

//Called if the player lands on a Chance location
function chance(){
	global $position;
	global $chanceCard;
	global $properties;
	global $utilities;
	global $railroads;
	//randomly select a chance card
	$chanceSelection = $chanceCard[rand(0,15)];
	
	switch ($chanceSelection){
		case "Utility":
			distanceToClosest($utilities);
			break;
		
		case "Railroad":
			distanceToClosest($railroads);
			break;
		
		case "BackThree":
			return backThree($position);
			break;
			
		default:
			if($chanceSelection){
			
				$position = array_search($chanceSelection, $properties);
				return $position;
			
			}
			else{
			
				return $position;
			
			}
	}
	
	/*
	if($chanceSelection === "Utility"){
		distanceToClosest($position,$utilities);
	}
	
	elseif($chanceSelection === "Railroad"){
		distanceToClosest($position,$railroads);
	}
	
	elseif($chanceSelection === "BackThree"){
		backThree($position);
	}
	else{
	
	}	
	//If the card changes the player's location
	if($chanceSelection){
	    //Change their location
		$position = array_search($chanceSelection, $properties);
		return $position;
	}
	*/
}

//Calculate the distance to the closest utility or railroad
function distanceToClosest($locType){
	global $properties;
	global $utilities;
	global $railroads;
	global $position;
		
	for ($i = 0; $i < count($locType); $i++){
		$utilityLoc[$i] = array_search($locType[$i], $properties);
		$distance[$i] = $utilityLoc[$i] - $position;

		if($utilityLoc[$i] > $position){
			$distance[$i] = $utilityLoc[$i] - $position;
		}
		else{
			$distance[$i] = 40 - ($position - $utilityLoc[$i]);
		}
		
	}

	$position += min($distance);
	$position = $position%40;
	return $position;
}

//Send the player back three spaces
function backThree(){
	global $position;
	$position = $position - 3;
	return $position;
}

?>
