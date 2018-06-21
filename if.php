<?php

if (is_numeric($entry->city_driving) && isset($entry->city_driving)){
  $points += (isset($model["city_driving"]))? $entry->city_driving * $model["city_driving"] : 0
}

if (isset($entry->long_distance) && $entry->long_distance > 0) {
  $points += (isset($model["long_distance"]))? $entry->long_distance * $model["long_distance"] : 0;
}

//$points += (isset($model["long_distance"]))? $entry->long_distance * $model["long_distance"] : 0;
if (is_numeric($entry->off_road) && isset($entry->off_road)) {
  $points += (isset($model["off_road"]))? $entry->off_road * $model["off_road"] : 0;
}

if (is_numeric($entry->luggage) && isset($entry->luggage)) {
  $points += (isset($model["luggage"]))? $entry->luggage * $model["luggage"] : 0;
}

if (is_numeric($entry->fuel_capacity) && isset($entry->fuel_capacity)) {
  $points += (isset($model["fuel_capacity"]))? $entry->fuel_capacity * $model["fuel_capacity"] : 0;
}

if (is_numeric($entry->enjoyment) && isset($entry->enjoyment)) {
    $points += (isset($model["enjoyment"]))? $entry->enjoyment * $model["enjoyment"] : 0;
}

if (is_numeric($entry->practicality) && isset($entry->practicality)) {
  $points += (isset($model["practicality"]))? $entry->practicality * $model["practicality"] : 0;
}

if (is_numeric($entry->performance) && isset($entry->performance)) {
  $points += (isset($model["performance"]))? $entry->performance * $model["performance"] : 0;
}

if (is_numeric($entry->comfort) && isset($entry->comfort)) {
  $points += (isset($model["comfort"]))? $entry->comfort * $model["comfort"] : 0;
}

if(is_numeric($entry->reliability) && isset($entry->reliability)) {
  $points += (isset($model["reliability"]))? $entry->city_driving * $model["reliability"] : 0;
}
