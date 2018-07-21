<?php
//torvista
//creates a file with a similar name, listing configuration keys so PhpStorm can find them for error inspections
///DO NOT PUT THIS FILE IN YOUR PRODUCTION SITE - LOCAL DEVELOPMENT USE ONLY
//
//put this file in the shop root, rename it and run via the browser

$show_on_screen = false;//default for security
$show_configuration_values = false;//default for security

//uncomment to override previous settings
//$show_on_screen = true;//temporary - for debugging only
//$show_configuration_values = true;//temporary - for debugging only

$filename = basename(__FILE__, '.php') . '_list_of _constants.php';//use the same filename as a stub

include('includes/application_top.php');
if (!isset($db)) {
    exit;
} // or die, return false, thrown exception,  to keep phpstorm inspects happy!
$db_constants_query = 'SELECT configuration_id, configuration_title, configuration_key, configuration_value from ' . TABLE_CONFIGURATION . ' WHERE configuration_key >"" ';//there is one with no key name

$db_constants_result = $db->Execute($db_constants_query);

$html_header = "<!DOCTYPE html>\n<html>\n<head>\n<title>Configuration Keys</title>\n</head>\n<body style='font-family: Verdana, Arial, Helvetica, sans-serif;font-size: small'>\n<h1>Configuration Keys</h1><table border='1' cellspacing='0' cellpadding='2' width='100%' style='table-layout: fixed;}'><tr><th style='text-align: center;' width='120px'>configuration_id</th><th style='text-align: left'>configuration_key</th>" . ($show_configuration_values ? "<th style='text-align: left;word-wrap: break-word'>configuration_value</th>" : '') . "</tr>\n";
$html_footer = "</body>\n</html>";

echo($show_on_screen ? $html_header : '');

$constants = "<?php //" . date('Y-m-d H:i:s') . "\n";
$count = 0;
foreach ($db_constants_result as $row) {
    $count++;
    $constant_id = $row['configuration_id'];
    $constant_title = $row['configuration_title'];
    $constant_name = $row['configuration_key'];
    $constant_value = $row['configuration_value'];
    $constants .= "define('" . strtoupper($constant_name) . "', '" . ($show_configuration_values ? ($constant_value == '\\' ? 'BACKSLASH replaced by file generator' : $constant_value) : '') . "');\t\t\t//configuration_id=" . $constant_id . " - $constant_title\n";//if a backslash is not replaced, it escapes the delimiter in the generated file and makes the rest of the file invalid.

    echo($show_on_screen ? '<tr><td style="text-align: center">' . $constant_id . '</td><td>' . strtoupper($constant_name) . '</td>' . ($show_configuration_values ? '<td>' . htmlentities($constant_value) . '</td>' : '') . '</tr>' . "\n" : '');
}

$fh = fopen($filename, 'w'); // create the file. Notice the 'w'. This is to be able to write to the file once.
fwrite($fh, $constants); // write the data to the file.

fclose($fh); // close our file

echo($show_on_screen ? "</table><h2>$count constants found in " . TABLE_CONFIGURATION . " table</h2>\n" . $html_footer : '');
require('includes/application_bottom.php');
