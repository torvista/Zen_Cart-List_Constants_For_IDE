# List Zen Cart Constants for IDE
Your IDE may not index (find) the constants stored in the databas+) e, nor those in the .lang language file arrays (introduced from Zen Cart 158), so code inspections may flag these constants as "undefined".

This script generates two local php files containing generated defines from 
a) the configuration_keys/constants in the database
b) the language constants from the arrays in .lang language files

The script should be placed in the admin folder root of your development server and certainly never uploaded to a production site.

You may rename the script as you wish: the output files with use the same name as a stub.

Options are available in the script to show/not show the actual values of the constants, which are not strictly required for indexing.

Please report bugs in GitHub and if there is something missing, add it!
