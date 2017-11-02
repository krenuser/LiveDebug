# LiveDebug
Portable PHP Sandbox

# Prerequisites
- Apache/nginx HTTP server
- PHP v5+

# Installation
1) Download, unpack to HTTP server WWW directory (e.g., _/var/www/html/livedebug_)
2) Edit _lib/config.php_ file
3) Use it!

# Config file
- _LIVEDEBUG_LANG_INI_FILE_ - point to INI file containing translations for interface elements
- _LIVEDEBUG_TMP_RUN_PATH_ - dir used to put PHP-files to be run (can be out of HTTP-folder of your server, e.g. _/var/tmp_)
- _LIVEDEBUG_RUN_MODE_ - determines in what mode PHP code used. _eval_ - force using eval() function to run code. _file_ - force using PHP files to run PHP code. _user_ - user can select run mode from interface "Run mode" menu.
- _LIVEDEBUG_FILESTORAGE_TYPE_ - determines the type of file storage implementation. Now only _FS_ (_File System_) type implemented (_FileStorage_FS_ class). To implement another type of file storage, create class, name it, e.g. "FileStorage_PG" and set _LIVEDEBUG_FILESTORAGE_TYPE_ param to "PG".
- _LIVEDEBUG_FILES_PATH_ - option for _FS_ file storage implementation only. Determines folder to save files to and load files from.

# Usage
- open LiveDebug page, e.g. http://127.0.0.1/livedebug/
- write your PHP code
- press F4 or "Run code" menu item to run
