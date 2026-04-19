<?php
# three exam scores as input
echo "-------------------------------\n";
echo "Enter the first exam score: ";
$score1 = trim(fgets(STDIN));
echo "Enter the second exam score: ";
$score2 = trim(fgets(STDIN));
echo "Enter the third exam score: ";
$score3 = trim(fgets(STDIN));

echo "-------------------------------\n";
echo "Score 1: $score1\n";
echo "Score 2: $score2\n";
echo "Score 3: $score3\n";

echo "-------------------------------\n";
# calculate the average score
$average = round(($score1 + $score2 + $score3) / 3);
echo "Average Score: $average\n";

echo "-------------------------------\n";
# calculate and display the percentage score
$percentage = round((($score1 + $score2 + $score3) / 300) * 100);
echo "Percentage Score: $percentage%\n";

echo "-------------------------------\n";
# take as input marks for five subjects
echo "Enter the Mathematics score: ";
$maths = trim(fgets(STDIN));
echo "Enter the English score: ";
$english = trim(fgets(STDIN));
echo "Enter the Physics score: ";
$physics = trim(fgets(STDIN));
echo "Enter the Biology score: ";
$biology = trim(fgets(STDIN));
echo "Enter the Chemistry score: ";
$chemistry = trim(fgets(STDIN));

echo "-------------------------------\n";
echo "Mathematics: $maths\n";
echo "English: $english\n";
echo "Physics: $physics\n";
echo "Biology: $biology\n";
echo "Chemistry: $chemistry\n";

echo "-------------------------------\n";
# count how many subjects have a score below 50 (fail)
$scoreList = array($maths, $english, $physics, $biology, $chemistry);
$total = 0;
for ($i = 0; $i < count($scoreList); $i++){
    if ($scoreList[$i] < 50)
        $total++;
}
echo "Academic status:\n";
$passed = 5 - $total;
echo "Total subjects passed: $passed\n";
echo "Total subjects failed: $total\n";

# if a student fails in more than two subjects,
# display a warning message: "Student is placed on academic probation."

if ($total > 2){
    echo "Your are placed on academic probation.\n";
} else {
    echo "Congratulations! You are in good academic standing\n\n";
}
echo "-------------------------------\n";
# If the average score is 50 or above, display "Pass."
# If the average is below 50, display "Fail."

$averageScore = round(($maths + $english + $physics + $biology + $chemistry) / 5);
echo "Average score: $averageScore \n";
echo "Teacher's remark: " . (($averageScore >= 50) ? "Pass" : "Fail") . "\n";

# If a student has an A grade (average is above 90) and has scored above 95 in at least one exam,
# display a message saying they qualify for the Honor Roll.

echo "Academic honorship: ";
if ($averageScore > 90 && in_array(95, $scoreList)) {
    echo "You qualify for the Honor Roll.\n";
} else {
    echo "You do not qualify for the Honor Roll.\n";
}
echo "-------------------------------\n";

# Use a loop to process grades for 5 students,
# prompting for their three exam scores and displaying their results.

$totalStudents = 5;
 while($totalStudents > 0){
     echo "-------------------------------\n";
     echo "Welcome to the grade processor\n";
     echo "-------------------------------\n";
     echo "Enter your name: ";
     $studentName = trim(fgets(STDIN));

     # valida name
     if($studentName == '' || is_numeric($studentName)){
         echo "Name cannot be empty or numeric. Please enter a valid name.\n";
         continue;
     }

     # Initialize the array for this student
     $studentScore = array();

     # Set the total number of subjects
     $totalSubjects = 3;
     while($totalSubjects > 0){
         echo "Enter course name: ";
         $courseName = trim(fgets(STDIN));
         # validate course
         if($courseName == '' || is_numeric($courseName)){
             echo "Course name cannot be empty or numeric. Please enter a valid name.\n";
             continue;
         }
         echo "Enter $courseName exam score: ";
         $score = trim(fgets(STDIN));
         if($score < 0 || $score > 100){
             echo "Invalid score. Please enter a score between 0 and 100.\n";
             continue;
         }

         # add scores to array
         $studentScore[$courseName] = $score;

         # Move to the next subject
         $totalSubjects--;
     }
     # process grades and display results
     echo "\n$studentName below are your grades:\n";
     foreach($studentScore as $key => $value){
         switch ($value){
             case $value >= 98: echo "$key: A+\n"; break;
             case $value >= 93: echo "$key: A\n"; break;
             case $value >= 90: echo "$key: A-\n"; break;
             case $value >= 88: echo "$key: B+\n"; break;
             case $value >= 83: echo "$key: B\n"; break;
             case $value >= 80: echo "$key: B-\n"; break;
             case $value >= 78: echo "$key: C+\n"; break;
             case $value >= 73: echo "$key: C\n"; break;
             case $value >= 70: echo "$key: C-\n"; break;
             case $value >= 68: echo "$key: D+\n"; break;
             case $value >= 63: echo "$key: D\n"; break;
             case $value >= 60: echo "$key: D-\n"; break;
             case $value < 60: echo "$key: F\n"; break;
             default: echo "W\n"; break;
         }

     }
     # move to the next student
     $totalStudents--;
 }









