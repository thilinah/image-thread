## Image Thread
A Simple Web Application for uploading and displaying images

## Explanation on code structure
 * <code>src/api</code> - All core classes
 * <code>bower_components</code> - Third party JS components downloaded via [](http://bower.io/)
 * <code>composer</code> - PHP third party libs downloaded via composer 
 * <code>controller</code> - Controllers in MVC 
 * <code>css</code> - Application CSS files
 * <code>js</code> - Application JavaScript files
 * <code>model</code> - Model object for accessing DB
 * <code>scripts</code> - DB scripts
 * <code>themes</code> - Application themes. A new theme can be activated by adding a sub directory and setting APP_THEME constant in config.live.php
 
## Deployment
 * Install Ant and use ant build to build the project (set "destination" parameter to your web root)
 * You can also manually copy all files in src to your web root
 * If you are using apache change "image-thread" to your web folder name. If you are placing files in web root change it to "/"
 * If you are using nginx make sure to add following location element in your vhost
   
  
           location / {
            try_files $uri $uri/ /index.php?/$uri;
           }

       
 * if the app placed in a sub folder under nginx use 
 

            location /subdir/ {
               	try_files $uri $uri/ /subdir/index.php?/$uri;
            }
  
 * Create a DB and create the tables using script/db_structure.sql
 * Update config.inc.php and config.live.inc.php appropriately with DB connection details and application url