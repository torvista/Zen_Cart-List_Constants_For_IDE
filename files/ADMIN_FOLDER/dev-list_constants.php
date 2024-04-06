<?php

declare(strict_types=1);
// https://github.com/torvista/zen-cart_list-configuration-constants
// This script creates a file with a similar name AS THIS FILE (whatever you name it), listing the configuration keys so
// an IDE can find them and prevent error inspections flagging "missing constants"
// DO NOT PUT THIS FILE IN YOUR PRODUCTION SITE, it is for LOCAL DEVELOPMENT USE ONLY. Put it in your "admin" folder.
// If the admin login screen appears...login!

/*
* @copyright torvista
* @license http://www.zen-cart.com/license/2_0.txt GNU Public Licence V2.0
* @updated 6 April 2024
 */

// Add your custom template name(s) here
$templates_custom = ['my_template'];

// for phpstorm inspections
/* @var queryFactory $db
*/
$debug = false;
$parse_db_configuration_constants = true;
$show_constant_values = true; // default=false for security: just produces a list of constants with no values listed

$parse_lang_define_arrays = true;
$filename_separator = true; // prefix each block of constants with the source filename
///////////////////////////////////////////
$filename_file_constants = basename(__FILE__, '.php') . '-file_constants.php'; // use this file's name as a prefix for
// the output file
$filename_db_constants = basename(__FILE__, '.php') . '-db_constants.php'; // use this file's name as a prefix for the
// output file
if (file_exists('includes/application_top.php')) {
    include 'includes/application_top.php';
} else {
    die('ERROR: application_top.php not found');
}
if (!isset($db)) {
    exit('ERROR: $db not set');
}
if (!function_exists('mv_printVar')) {
    /**
     * @param $a
     * function generates formatted debugging output
     */
    function mv_printVar($a): void
    {
        $backtrace = debug_backtrace()[0];
        $fh = fopen($backtrace['file'], 'rb');
        $line = 0;
        $code = '';
        while (++$line <= $backtrace['line']) {
            $code = fgets($fh);
        }
        fclose($fh);
        if ($code !== false) {
            preg_match('/' . __FUNCTION__ . '\s*\((.*)\)\s*;/u', $code, $name);
        } else {
            $name = '';
        }
        echo '<pre>';
        if (!empty($name[1])) {
            echo '<strong>' . trim($name[1]) . '</strong> (' . gettype($a) . "):\n";
        }
        print_r($a);
        echo '</pre><br>';
    }
}
/**
 * parse filename and optional array_names. If array_names is empty, assume it is a lang file and using $define as the array name.
 */
function parse_file($filename, $array_names = []): void
{
    global $debug, $fh, $filename_separator, $show_constant_values;
    if ($debug) {
        mv_printVar($array_names);
    }

    echo '<p>Reading file <b>' . $filename . '</b>: ';
    include($filename);
    if (count($array_names) === 0) {
        $array_names[] = 'define';
    }
    $filename = $filename_separator ? "// filename=$filename;\n" : '';
    fwrite($fh, $filename); // write data
    foreach ($array_names as $array_name) {
        $defines_list = "// array name=$$array_name\n";
        if ($debug) {
            echo '<br>array_name=$' . $array_name . '<br>';
            mv_printVar($$array_name);
        }
        foreach ($$array_name as $k => $v) {
            if ($show_constant_values) {
                $v = str_replace("'", "\'", (string)$v);
                $v = str_replace(["\r", "\n"], '', $v);
                //   $v = str_replace("\n", "' . PHP_EOL . '", $v);
            } else {
                $v = '';
            }
            $defines_list .= "define('$k', '$v');\n";
        }
        fwrite($fh, $defines_list); // write data
    }
    echo 'constants added.</p>';
}

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <style>
            body {
                font-family: Verdana, sans-serif;
                font-size: 70%;
            }
            table, th, td {
                border: solid thin black;
                border-collapse: collapse;
            }
            th, td {
                text-align: left;
                padding: 3px;
            }
            .columnID {
                text-align: center;
            }
        </style>
        <title>List Constants</title>
    </head>
    <body>
    <h1>Generate Files of all Constants</h1>
    <?php
    if ($parse_db_configuration_constants) { ?>
        <h2>Parsing of Database Configuration Constants</h2>
        <table>
            <tr>
                <th>Table</th>
                <th class="columnID">configuration_id</th>
                <th>configuration_key</th><?= ($show_constant_values ? '<th>configuration_value</th>' : ''); ?></tr>
            <?php
            $db_constants_query = '
            SELECT "'. TABLE_CONFIGURATION . '" AS tableName, configuration_id, configuration_title, configuration_key, configuration_value
            FROM ' . TABLE_CONFIGURATION . '
            UNION ALL
            SELECT "'. TABLE_PRODUCT_TYPE_LAYOUT . '" AS tableName, configuration_id, configuration_title, configuration_key, configuration_value
            FROM ' . TABLE_PRODUCT_TYPE_LAYOUT . '
            WHERE configuration_key >""';//there is one with no key name

            $db_constants_result = $db->Execute($db_constants_query);

            $constants = '<?php // generated ' . date('Y-m-d H:i:s') . ' (' . date_default_timezone_get() . ') ' . "\n";
            $constants .= '// $show_configuration_values = ' . ($show_constant_values ? 'true' : 'false') . "\n";
            $count = 0;
            foreach ($db_constants_result as $row) {
                $count++;
                $tableName = $row['tableName'];
                $constant_id = $row['configuration_id'];
                $constant_title = $row['configuration_title'];
                $constant_name = $row['configuration_key'];
                $constant_value = $row['configuration_value'];

                $constants .= "define('" . strtoupper($constant_name) . "', '";
                // if a backslash is not replaced, it escapes the delimiter in the generated file and makes the rest of the file invalid.
                if ($show_constant_values) {
                    if ($constant_value === '\\') {
                        $constants .= 'BACKSLASH replaced by file generator';
                    } else {
                        $constants .= $constant_value;
                    }
                }
                $constants .= "');\t\t\t// configuration_id=" . $constant_id . " - $constant_title\n";
                ?>

                <tr>
                    <td><?= $tableName; ?></td>
                    <td class="columnID"><?= $constant_id; ?></td>
                    <td><?= strtoupper($constant_name); ?></td>
                    <?= ($show_constant_values ? '<td>' . htmlentities($constant_value) . '</td>' : ''); ?>
                </tr>
                <?php
            }

            $fh = fopen($filename_db_constants, 'wb'); // create the file. Notice the 'w'. This is to be able to write to the file once.
            fwrite($fh, $constants); // write the data to the file.
            fclose($fh); // close file
            ?>
        </table>
        <h2><?= $count; ?> constants found in <?= TABLE_CONFIGURATION; ?> table</h2>
        <?php
    } else { ?>
        <h2>Parsing of Database Configuration Constants: not enabled</h2>
    <?php
    } ?>
    <hr>
    <?php
    if ($parse_lang_define_arrays) {
        $discrete_files = [];
        // add individual files to this array as per the format below, using the array names
        $discrete_files = array_merge(
            [DIR_FS_CATALOG . DIR_WS_INCLUDES . 'init_includes/init_non_db_settings.php' => ['site_specific_non_db_settings', 'non_db_settings']]  // the admin version just includes this one
        );
        if ($debug) {
            mv_printVar($discrete_files);
        }
        ?>
        <h2>Parsing of .lang and other define arrays</h2>
        <?php
        $language = 'english';
// admin
        $paths_to_scan_lang = [
            DIR_FS_ADMIN . DIR_WS_LANGUAGES,
            DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/',
            DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/extra_definitions/',
            DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/modules/newsletters/',
        ];
// shopfront
        array_push(
            $paths_to_scan_lang,
            DIR_FS_CATALOG_LANGUAGES,
            DIR_FS_CATALOG_LANGUAGES . $language . '/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/extra_definitions/',
            //DIR_FS_CATALOG_LANGUAGES . $language . '/html_includes/',
            //DIR_FS_CATALOG_LANGUAGES . $language . '/html_includes/classic/',
            //DIR_FS_CATALOG_LANGUAGES . $language . '/html_includes/responsive_classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/',
        );
        /* template paths */
        $templates_core = ['classic', 'responsive_classic'];
        $templates = array_merge($templates_core, $templates_custom);
        foreach ($templates as $tpl) { //cannot use name "$template"
            array_push(
                $paths_to_scan_lang,
                DIR_FS_CATALOG_LANGUAGES . $language . '/' . $tpl . '/',
                DIR_FS_CATALOG_LANGUAGES . $language . '/extra_definitions/' . $tpl . '/',
                DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/' . $tpl . '/',
                DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/' . $tpl . '/',
                DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/' . $tpl . '/',
                DIR_FS_CATALOG_LANGUAGES . $language . '/' . $tpl . '/',
            );
        }

        if ($debug) {
            mv_printVar($paths_to_scan_lang);
        }
        $fh = fopen($filename_file_constants, 'wb'); // open/create the file.
        $defines_header = '<?php // generated ' . date('Y-m-d H:i:s') . ' (' . date_default_timezone_get() . ') ' . "\n";
        $defines_header .= '// $show_constant_values = ' . ($show_constant_values ? 'true' : 'false') . "\n";
        fwrite($fh, $defines_header);

        foreach ($paths_to_scan_lang as $path_to_scan) { ?>
            <h2>Path to scan: "<?= $path_to_scan; ?>"</h2>
            <?php
            $file_list = glob($path_to_scan . 'lang.*.php');
            if ($file_list === false || count($file_list) === 0) { ?>
                <h3>No files found in: "<?= $path_to_scan; ?>"</h3>
                <?php
                continue;
            }
            if ($debug) {
                mv_printVar($file_list);
            }

            foreach ($file_list as $filename) {
                // lang.gv_redeem.php and lang.gv_send.php reference other pre-defined constants in english.php and script chokes on that.
                // todo
                if ($filename === DIR_FS_CATALOG_LANGUAGES . $language . '/' . 'lang.gv_redeem.php'
                    || $filename === DIR_FS_CATALOG_LANGUAGES . $language . '/' . 'lang.gv_send.php'
                ) {
                    echo '<p style="background-color:red">FILE "' . $filename . '" SKIPPED (see script line ' . __LINE__ . ')</p>';
                    continue;
                }
                parse_file($filename);
            }
        } ?>
        <h2>Parsing discrete files</h2>
        <?php
        foreach ($discrete_files as $key => $array_names) {
            parse_file($key, $array_names);
        }
        fclose($fh); // close file
    } else { ?>
        <h2>Parsing of .lang define arrays: not enabled</h2>
        <?php
    } ?>
    <hr>
    <h4>Script complete</h4>
    <body>
    </html>
<?php
require('includes/application_bottom.php');
