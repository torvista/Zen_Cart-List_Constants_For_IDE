# List Zen Cart Database Constants/Language arrays as Constants for IDE Inspections
Your IDE may not index (find) the constants stored in the database, nor those in the .lang language file arrays (introduced in Zen Cart 158), so code inspections will flag these as "undefined constants" errors.

This script extracts the constants defined in the database and in the language arrays (and other misc. files) to create two .php files that the IDE can index.

## How to use the script
If you wish to include constants from your custom template, add the template name in the array:
```
// Add your custom template name(s) here
$templates_custom = ['my_template'];
```

If you wish to include the constants from your plugins add the filenames to the array.
e.g.:
```
//site-specific plugins
        array_push(
            $paths_to_scan_lang,
            DIR_FS_CATALOG . 'zc_plugins/' . "BackupMySQL/v2.0.0/admin/includes/languages/$language/extra_definitions/",
            DIR_FS_CATALOG . 'zc_plugins/' . "BackupMySQL/v2.0.0/admin/includes/languages/$language/",
        );
```
The script should be copied to the admin folder of your **development** server and certainly never uploaded to a production site.

In your browser, login to the admin, then manually enter the name of the file "dev-list-constants.php" after the admin directory name in the browser address bar to run the script.

These three files are prefixed dev- so this file and the two output files will be ignored if used in a Zen Cart GitHub repository.
You may rename the script as you wish: the output files will use the script name as a stub, to keep them together in a file listing.

Options are available in the script to show/not show the actual values of the constants, which are not strictly required for indexing.

Please report bugs in GitHub and if there is something missing, add it!

### Changelog
11/08/2024: added core zc_plugins language files  
06/04/2024: add support for custom templates  
11/12/2023: added constants from product_type_layout, added column to identify table name.  
31/03/2023: moved file parsing to a function. Expanded function to allow parsing of files with array names not "$define".
