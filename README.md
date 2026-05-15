# List Zen Cart Database Constants and Language arrays as Constants (for IDE Inspections)
Your IDE may not index (find) the constants stored in the database, nor those in the .lang language file arrays (introduced in Zen Cart 158), so code inspections will flag these as "undefined constants" errors.

This script extracts the constants defined in the database and in the language arrays (and other misc. files) to create two .php files that the IDE can index / that you can find when searching for some elusive text.

## How to use the script
The script should be copied to the admin folder of your **development** server and certainly never uploaded to a production site.

In your browser, login to the admin, then manually enter the name of the file "dev-list-constants.php" after the admin directory name in the browser address bar to run the script.

This will create two more files: one from the database constants and one from the .lang files. 

These three files are prefixed "dev-"  
so this file and the two output files will be ignored if used in a Zen Cart GitHub repository.

You may rename the script as you wish: the output files will use the script name as a stub, to keep them together in a file listing.

Options are available in the script to show/not show the actual values of the constants, which are not strictly required for indexing.

Please report bugs in GitHub and if there is something missing, add it!

### Changelog
29/04/2026 BMH: make directories recursive for plugins, to catch any lang files in subdirectories.  
Use scandir and recursion instead of glob because glob with GLOB_ONLYDIR does not work on some servers and glob with GLOB_ONLYDIR does not do recursion. See function getAllSubdirectories($dir)   
27/07/2025 BMH: ln312 change glob("$plugin_directory_lang/*", GLOB_ONLYDIR); to glob("$plugin_directory_lang/**/*", GLOB_ONLYDIR); for extra recursive past empty directory
GLOB_ONLYDIR does not do recursion. See function getAllSubdirectories($dir): array
27/08/2024 torvista: auto-add template and plugin files  
11/08/2024 torvista: added core zc_plugins language files  
06/04/2024 torvista: add support for custom templates  
11/12/2023 torvista: added constants from product_type_layout, added column to identify table name.  
31/03/2023 torvista: moved file parsing to a function. Expanded function to allow parsing of files with array names not "$define".
