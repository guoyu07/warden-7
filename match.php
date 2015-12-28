<html>
<head>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous" />
  <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
</head>
<body>
<form action="match.php">
  <input type="text" name="match" id="matchId" />
  <input type="submit" />
</form>

<?php
$matchID = $_REQUEST['match'];
$apiKey = trim(file_get_contents(__DIR__ . '/apikey'));
function getHeroesArray(){
  global $apiKey;
  $heroesArray = [];
  $getHeroesUrl = "https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/?key=" . $apiKey;
  $ch = curl_init($getHeroesUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
  $heroesResult = curl_exec($ch);
  curl_close($ch);
  $heroesObject = json_decode($heroesResult);
  $heroesObjectArray = $heroesObject->result->heroes;
  foreach ($heroesObjectArray as $hero){
    $heroesArray[$hero->id] = $hero->name;
  }
  return $heroesArray;
};
function getItemsArray(){
  global $apiKey;
  $heroesArray = [];
  $getHeroesUrl = "https://api.steampowered.com/IEconDOTA2_570/GetGameItems/v0001/?key=" . $apiKey . "&language=en";
  $ch = curl_init($getHeroesUrl);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);    # required for https urls
  $heroesResult = curl_exec($ch);
  curl_close($ch);
  $heroesObject = json_decode($heroesResult);
  $heroesObjectArray = $heroesObject->result->items;
  foreach ($heroesObjectArray as $hero){
    $heroesArray[$hero->id] = $hero->name;
  }
  return $heroesArray;
};
function getMatchDetails($matchID){
  global $apiKey;
  $detailsURL = "https://api.steampowered.com/IDOTA2Match_570/GetMatchDetails/V001/?match_id=$matchID&key=$apiKey";
  $ch = curl_init($detailsURL);

  $optArray = array(
    CURLOPT_RETURNTRANSFER => true
  );

  curl_setopt_array($ch, $optArray);

  $response = curl_exec($ch);
  $matchObject = json_decode($response);
  $players = $matchObject->result->players;
  $heroesArray = getHeroesArray();
  $itemsArray = getItemsArray();
  echo "<h1>" . $matchID . "</h1>";

  echo "<table class='table table-striped'>";
  echo "<tr>";
  echo "<th></th>";
  echo "<th>LH</th>";
  echo "<th>D</th>";
  echo "<th>XPM</th>";
  echo "<th>GPM</th>";
  echo "<th>HD</th>";
  echo "<th>TD</th>";
  echo "<th colspan=6>Items</th>";
  echo "</tr>";
  for($i = 0; $i < count($players); $i++){
    $player = $players[$i];
    if($i < count($players) / 2){
      echo "<tr class='bg-danger'>";
    }
    else {
      echo "<tr class='bg-success'>";
    }
    echo "<td>";
    if(isset($players[$i]->hero_id)){
      $smallName = str_replace("npc_dota_hero_","",$heroesArray[$players[$i]->hero_id]);
      //echo "Hero: " . $smallName;
      echo '<img src="' . 'http://cdn.dota2.com/apps/dota2/images/heroes/' . $smallName . '_sb.png"/>';

    }
    else{
      echo "no hero;";
    }
    echo "</td>";
    echo "<td>";
    echo $players[$i]->last_hits;
    echo "</td>";
    echo "<td>" . $player->denies       . "</td>";
    echo "<td>" . $player->xp_per_min   . "</td>";
    echo "<td>" . $player->gold_per_min . "</td>";
    echo "<td>" . $player->hero_damage  . "</td>";
    echo "<td>" . $player->tower_damage . "</td>";

    $items = [$player->item_0, $player->item_1, $player->item_2, $player->item_3, $player->item_4, $player->item_5];
    for( $j = 0; $j < count($items); $j++ ){
      $item = $itemsArray[$items[$j]];
      $shortName = str_replace("item_","",$item);
      if(substr_count("item_",$shortName) > -1){
        echo "<td><img src='http://cdn.dota2.com/apps/dota2/images/items/" . $shortName . "_lg.png'></td>";
      }
      else{
        echo "<td></td>";
      }
    }
    echo "</tr>";
  }

  echo "</table>";
  echo "<!--" . $response . "-->";

}
if(isset($matchID)){
  getMatchDetails($matchID);
}
else
{
  getMatchDetails(2002487825);
}
?>
</body>
</html>
