<?php
     $from = '2017-04-01 00:00:00';
     $to = '2017-05-01 00:00:00';

     $sql  = "SELECT date(dateEntered), dayname(dateEntered) as day ";
     $sql .=      ", sum(Water) ";
     $sql .=      ", sum(Calories) ";
     $sql .=      ", sum(Protein) ";
     $sql .=      ", sum(Fat) ";
     $sql .=      ", sum(Carbs) ";
     $sql .=      ", sum(Fiber) ";
     $sql .=      ", sum(Sugars) ";
     $sql .=      ", sum(Phosphorus) ";
     $sql .=      ", sum(Potassium) ";
     $sql .=      ", sum(Sodium) ";
     $sql .= "FROM userLog ";
     $sql .= "WHERE dateEntered BETWEEN ? AND ? ";
     $sql .= "AND userID=? ";
     $sql .= "GROUP BY DATE(dateEntered) ";
     $sql .= "ORDER BY dateEntered ";


?>

