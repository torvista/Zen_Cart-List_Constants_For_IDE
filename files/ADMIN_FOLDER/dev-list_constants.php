<?php
declare(strict_types=1);
// https://github.com/torvista/zen-cart_list-configuration-constants
// This script creates a file with a similar name AS THIS FILE (whatever you name it), listing the configuration keys so
// an IDE can find them and prevent error inspections flagging "missing constants"
// DO NOT PUT THIS FILE IN YOUR PRODUCTION SITE, it is for LOCAL DEVELOPMENT USE ONLY. Put it in your "admin" folder.
// If the admin login screen appears...login!
//for phpstorm inspections
/* @var queryFactory $db
 * @var $define
 * */
$debug = false;
$parse_db_configuration_constants = true;
$show_constant_values = true;//default=false for security: just produces a list of constants with no values listed

$parse_lang_define_arrays = true;
$filename_separator = true; //prefix each block of constants with the source filename
//////////////////////////////////////
$filename_db_constants = basename(__FILE__, '.php') . '-db_constants.php';//use this file's name as a prefix for the
// output file
$filename_array_constants = basename(__FILE__, '.php') . '-array_constants.php';//use this file's name as a prefix for
// the output file
if (file_exists('includes/application_top.php')) {
    include 'includes/application_top.php';
} else {
    die('ERROR: application_top.php not found');
}
if (!isset($db)) {
    exit('ERROR: $db not set');
} // or die, return false, thrown exception, to keep phpstorm inspects happy!
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
        preg_match('/' . __FUNCTION__ . '\s*\((.*)\)\s*;/u', $code, $name);
        echo '<pre>';
        if (!empty($name[1])) {
            echo '<strong>' . trim($name[1]) . '</strong> (' . gettype($a) . "):\n";
        }
        //var_export($a);
        print_r($a);
        echo '</pre><br>';
    }
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
                text-align:left;
                padding: 3px;
            }
        </style>
        <title>Generate Files of Constants for an IDE</title>
    </head>
    <body>
    <h1>Generate Files of Constants for an IDE</h1>
    <?php
    if ($parse_db_configuration_constants) { ?>
        <h2>Parsing of Database Configuration Constants</h2>
        <table>
            <tr>
                <th>configuration_id</th>
                <th>configuration_key</th><?php echo($show_constant_values ? '<th>configuration_value</th>' : ''); ?></tr>
            <?php
            $db_constants_query = 'SELECT configuration_id, configuration_title, configuration_key, configuration_value 
            FROM ' . TABLE_CONFIGURATION . ' 
            WHERE configuration_key >""';//there is one with no key name

            $db_constants_result = $db->Execute($db_constants_query);

            $constants = '<?php // generated ' . date('Y-m-d H:i:s') . "\n";
            $constants .= '//$show_configuration_values = ' . ($show_constant_values ? 'true' : 'false') . "\n";
            $count = 0;
            foreach ($db_constants_result as $row) {
                $count++;
                $constant_id = $row['configuration_id'];
                $constant_title = $row['configuration_title'];
                $constant_name = $row['configuration_key'];
                $constant_value = $row['configuration_value'];
                
                $constants .= "define('" . strtoupper($constant_name) . "', '";
                //if a backslash is not replaced, it escapes the delimiter in the generated file and makes the rest of the file invalid.
                //this seems not be not used now
                if ($show_constant_values) {
                    if ($constant_value === '\\') {
                        $constants .= 'BACKSLASH replaced by file generator';
                    } else {
                        $constants .= $constant_value;
                    }
                }
                $constants .= "');\t\t\t//configuration_id=" . $constant_id .  " - $constant_title\n";
                ?>

                <tr>
                    <td><?php echo $constant_id; ?></td>
                    <td><?php echo strtoupper($constant_name); ?></td>
                    <?php echo($show_constant_values ? '<td>' . htmlentities($constant_value) . '</td>' : ''); ?>
                </tr>
                <?php
            }

            $fh = fopen($filename_db_constants, 'wb'); // create the file. Notice the 'w'. This is to be able to write to the file once.
            fwrite($fh, $constants); // write the data to the file.
            fclose($fh); // close file
            ?>
        </table>
        <h2><?php echo $count; ?> constants found in <?php echo TABLE_CONFIGURATION; ?> table</h2>
        <?php
    } else { ?>
        <h2>Parsing of Database Configuration Constants: not enabled</h2>
    <?php } ?>
    <hr>
    <?php
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    if ($parse_lang_define_arrays) { ?>
        <h2>Parsing of .lang define arrays</h2>
        <?php
        $language = 'english';
//admin
        $paths_to_scan = [
            //DIR_FS_ADMIN . 'extra_configures/',
            //DIR_FS_ADMIN . 'extra_datafiles/',
            DIR_FS_ADMIN . DIR_WS_LANGUAGES,
            DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/',
            DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/extra_definitions/',
            DIR_FS_ADMIN . DIR_WS_LANGUAGES . $language . '/modules/newsletters/',
        ];

//shopfront
        array_push($paths_to_scan,
            DIR_FS_CATALOG_LANGUAGES,
            DIR_FS_CATALOG_LANGUAGES . $language . '/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/extra_definitions/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/extra_definitions/classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/extra_definitions/responsive_classic/',
            //DIR_FS_CATALOG_LANGUAGES . $language . '/html_includes/',
            //DIR_FS_CATALOG_LANGUAGES . $language . '/html_includes/classic/',
            //DIR_FS_CATALOG_LANGUAGES . $language . '/html_includes/responsive_classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/order_total/responsive_classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/payment/responsive_classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/modules/shipping/responsive_classic/',
            DIR_FS_CATALOG_LANGUAGES . $language . '/responsive_classic/',
        );
        if ($debug) {
            mv_printVar($paths_to_scan);
        }
        $fh = fopen($filename_array_constants, 'wb'); // open/create the file.
        $defines_header = '<?php // generated' . date('Y-m-d H:i:s') . "\n";
        $defines_header .= '//$show_constant_values = ' . ($show_constant_values ? 'true' : 'false') . "\n";
        fwrite($fh, $defines_header);

        foreach ($paths_to_scan as $path_to_scan) { ?>
            <h2>Path to scan: "<?php echo $path_to_scan; ?>"</h2>
            <?php
            $file_list = glob($path_to_scan . 'lang.*.php');
            if ($file_list === false || count($file_list) === 0) { ?>
                <h3>No files found in: "<?php echo $path_to_scan; ?>"</h3>
                <?php
                continue;
            }
            if ($debug) {
                mv_printVar($file_list);
            }
            foreach ($file_list as $filename) {
                $defines_list = '';
                echo '<p>Reading file <b>' . $filename . '</b>: ';

                //lang.gv_redeem.php and lang.gv_send.php reference other pre-defined constants in english.php and script chokes on that.
                //todo
                if ($filename === DIR_FS_CATALOG_LANGUAGES . $language . '/' . 'lang.gv_redeem.php'
                    || $filename === DIR_FS_CATALOG_LANGUAGES . $language . '/' . 'lang.gv_send.php'
                ) {
                    echo '**<u>FILE SKIPPED (see script)</u>**</p>';
                    continue;
                }

                include($filename);
                if ($debug) {
                    mv_printVar($define);
                }
                $defines_list .= $filename_separator ? "//$filename;\n" : '';
                $count = 0;
                foreach ($define as $k => $v) {
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
                echo 'constants added.</p>';
            }

        }
        fclose($fh); // close file
    } else { ?>
        <h2>Parsing of .lang define arrays: not enabled</h2>
        <?php
    } ?>
    <body>
    </html>
<?php
require('includes/application_bottom.php');
