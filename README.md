# List Zen Cart Constants for IDE
Your IDE may not index (find) the constants stored in the database, nor those in the .lang language file arrays (introduced in Zen Cart 158), so code inspections may flag these constants as "undefined".

This script generates two local php files containing generated defines from 
a) the configuration_keys/constants in the database
b) the language constants from the arrays in .lang language files

The script should be placed in the admin folder root of your development server and certainly never uploaded to a production site.

The file is prefixed dev- so this file and the two output files will be ignored by the Zen Cart GitHub configuration if used in a Zen Cart repository.
You may rename the script as you wish: the output files use the script name as a stub to keep them together.

Options are available in the script to show/not show the actual values of the constants, which are not strictly required for indexing.

Please report bugs in GitHub and if there is something missing, add it!
