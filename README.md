# zen-cart_list-configuration-constants
Your IDE may not be clever enough to index (find) constants in the database, so file/code inspections may flag the use of these constants as "undefined".
This script generates a local php file listing the configuration_keys/constants in the database so the IDE may index them.

Although the script may be placed anywhere and run from the browser, I would put it in the Admin root and certainly not upload it to a production site
Options are in the script to show/not show the file on screen and show/not show the actual values of the constants.
