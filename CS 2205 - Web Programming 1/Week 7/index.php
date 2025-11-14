<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
</head>
<body>
  #1.Write a program in PHP using a ‘for’ loop to add all the integers between 0 and 50 and display the total.<br>
  <?php
  $total = 0;
  for( $i = 0; $i < 50; $i++ ){
    $total += $i;
  }
  echo"Sum of numbers between 0-50 is $total";
  ?>
  <br>
  #2.Write a PHP program to check if a person is eligible to vote using control structure. (Criteria: Minimum age required to vote is 18)<br>
  <?php
  $currentAge = 25;
  if( $currentAge >= 18){
    echo "You are eligible to vote!";
  }else{ 
    echo "Sorry! You are not eligible to vote.";
  }
  ?>
  <br>
  #3.Write a PHP program to check whether a number is prime or not.<br>
  <?php
    $number = 169;
    function isPrime($number){
      if( $number <= 1){
      return false;
    }
    for($i = 2 ; $i <= sqrt($number) ; $i++ ){
      if( $number % $i == 0){
        return false;
        }
      }
    return true;
  }
  if(isPrime($number)){
    echo "$number is a prime number.";
  }else{
    echo "$number is not a prime number";
  }
  ?>
</body>
</html>