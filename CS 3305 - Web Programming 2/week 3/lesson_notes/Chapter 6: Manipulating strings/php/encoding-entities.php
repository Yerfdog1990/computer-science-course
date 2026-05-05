<?php

// Initialize a variable with an HTML code snippet
$html = '<a href="login_process.php">Go to Index</a>';
echo $html;
echo '<br>';

// Display encoded version — browser shows code as text, not as a link
echo htmlspecialchars($html);
echo '<br>';

// Demonstrate htmlentities() on a string with special/international characters
$special = 'Caf\u00e9 & "Bistro" > <Restaurant>';
echo htmlentities($special);
echo '<br>';

// View the htmlspecialchars translation table
$table = get_html_translation_table(HTML_SPECIALCHARS);
echo '<hr>HTML Special Characters Translation Table:<br>';
foreach ($table as $char => $entity)
{
    echo htmlspecialchars($char) . ' → ' . $entity . '<br>';
}

