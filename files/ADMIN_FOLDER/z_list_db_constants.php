<?php
declare(strict_types=1);
// https://github.com/torvista/zen-cart_list-configuration-constants
// This script creates a file with a similar name AS THIS FILE (whatever you name it), listing the configuration keys so an IDE can find them and prevent error inspections flagging "missing constants"
// DO NOT PUT THIS FILE IN YOUR PRODUCTION SITE, it is for LOCAL DEVELOPMENT USE ONLY. Put it in your "admin" folder only.
// If the admin login screen appears...login!
//for phpstorm inspections
/* @var queryFactory $db
 * */

$show_on_screen = false;//default = false for security
$show_configuration_values = false;//default= false for security. Just produce a list of constants without any values listed

$filename = basename(__FILE__, '.php') . '_list_of_constants.php';//use this file's name as a stub for the output file

if (file_exists('includes/application_top.php')) {
    include 'includes/application_top.php';
} else {
    die('ERROR: application_top.php not found');
}
if (!isset($db)) {
    exit('ERROR: $db not set');
} // or die, return false, thrown exception, to keep phpstorm inspects happy!
$db_constants_query = 'SELECT configuration_id, configuration_title, configuration_key, configuration_value FROM ' . TABLE_CONFIGURATION . ' WHERE configuration_key >""';//there is one with no key name

$db_constants_result = $db->Execute($db_constants_query);
$style = '
<style>
body {font-family: Verdana, sans-serif;font-size: small;}
table {width:100%;}
table, th, td {border:solid thin black;border-collapse: collapse;}
th, td {padding:3px;}
th {text-align: left;}
td {width: auto;}
tr td:first-child{text-align: center;width:1%;white-space:nowrap;}
tr td:last-child{word-wrap:break-word;}
</style>';
$html_header = "<!DOCTYPE html>\n<html lang='en'>\n<head>\n" . $style . "\n<title>Configuration Keys</title>\n</head>\n<body>\n<h1>Configuration Keys</h1><table><tr><th>configuration_id</th><th>configuration_key</th>" . ($show_configuration_values ? "<th>configuration_value</th>" : '') . "</tr>\n";
$html_footer = "</body>\n</html>";

echo($show_on_screen ? $html_header : '');

$constants = '<?php //' . date('Y-m-d H:i:s') . "\n";
$constants .= '//$show_configuration_values = ' . ($show_configuration_values ? 'true' : 'false') . "\n";
$count = 0;
foreach ($db_constants_result as $row) {
    $count++;
    $constant_id = $row['configuration_id'];
    $constant_title = $row['configuration_title'];
    $constant_name = $row['configuration_key'];
    $constant_value = $row['configuration_value'];
    $constants .= "define('" . strtoupper($constant_name) . "', '" . ($show_configuration_values ? ($constant_value === '\\' ? 'BACKSLASH replaced by file generator' : $constant_value) : '') . "');\t\t\t//configuration_id=" . $constant_id . " - $constant_title\n";//if a backslash is not replaced, it escapes the delimiter in the generated file and makes the rest of the file invalid.

    echo($show_on_screen ? '<tr><td style="text-align: center">' . $constant_id . '</td><td>' . strtoupper($constant_name) . '</td>' . ($show_configuration_values ? '<td>' . htmlentities($constant_value) . '</td>' : '') . '</tr>' . "\n" : '');
}

$fh = fopen($filename, 'wb'); // create the file. Notice the 'w'. This is to be able to write to the file once.
fwrite($fh, $constants); // write the data to the file.

fclose($fh); // close file

echo($show_on_screen ? "</table><h2>$count constants found in " . TABLE_CONFIGURATION . " table</h2>\n" . $html_footer : '');
require('includes/application_bottom.php');
