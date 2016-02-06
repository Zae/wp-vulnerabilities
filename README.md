#Wordpress vulnerabilities scanner
Scan your wordpress installation and find out if there are any vulnerable plugins installed.

#Scanners
scan:wordpress Scan your wordpress version
scan:plugins Scan your installed plugins
scan:wordpress Scan your installed themes

#Return code
The command will return 1 if there are vulnerable plugins, themes or wordpresses found and show a table with explanations,
the command will return 0 if no vulnerable plugins were found.

#Providers
Most of the functionality is provided by providers using a Pipeline.

## Pre
All the filters that create a list of the installed plugins should mutate the $plugins array before calling $next

## Post
All the filters that check for vulnerabilities should mutate the $plugins array after calling $next

#Pipeline
This project uses the Laravel 5 Pipeline project.

#Config
You can configure the plugin using the wp_scan.yml file.

#TODO
Add more providers to add capabilities like reading composer files.
Add a scanner that checks wordpress itself
Add a scanner that checks themes
Cleanup
Tests