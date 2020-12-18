# List Configuration (database) Constants for Zen Cart
Your IDE may not be clever enough to index (find) the constants stored in the database, so file/code inspections may flag these constants as "undefined".
This script generates a local php file that lists the configuration_keys/constants in the database so the IDE may index them.

The script should be placed in the admin folder root of your development server and certainly not uploaded to a production site.

Options are in the script to show/not show the file on screen and show/not show the actual values of the constants, which are not strictly required for indexing.
