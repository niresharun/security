##########Manually activity start before running this script######################################
#0. Make sure media backup is taken day before migration
#0. Verify if any new destination ignore needs to be added
#0. /var/www/tempdb - this should be mounted inside docker
#0. Update M1, M2 and Elasticsearch credentials variables in this script
#1. Take backup of m1_log_customer
	#mysqldump -uroot -p --single-transaction --routines --triggers --quick wam2_nonprod m1_log_customer | sed -r 's/DEFINER=`[^`]+`@`[^`]+`/DEFINER=CURRENT_USER/g'| gzip -9 > /home/perficient/hosting/prft-hosting-sftp/m1_log_customer.sql.gz
#2. Place this file in setup_files/m2_datamigration_script/sql/m1_log_customer.sql
#3. CMS contents  - cms backup of required tables need to be present in setup_files/m2_datamigration_script/sql/m2_cms_backup.sql
	#mysqldump -uroot -p --single-transaction --routines --triggers --quick wam2_nonprod m1_log_customer | sed -r 's/DEFINER=`[^`]+`@`[^`]+`/DEFINER=CURRENT_USER/g'| gzip -9 > /home/perficient/hosting/prft-hosting-sftp/m1_log_customer.sql.gz
#4. Take M1 Live DB backup and dump on server at /var/www/tempdb
#5. Copy document root files to some folder outside document root. -- This is just to make sure we have files for quick restore. 
#6. Take backup of env.php and put at DOCUMENT_ROOT/setup_files/m2_backup_files/


#7. Unmount: env.php, var/log, var/report - 2 mins
#8. Container (up/down) - 2 mins
#9. Deployment - Not Needed
#10. ###Issue: Load balancer - not able to check if default instance is properly setup or not. Solved: Removed web 2 from load balancer and executed below command on web1 document - 10 mins
#11. Run automation script till default magento setup:install	
	#chmod --preserve-root -R 755 /var/www/public_html
	#chown --preserve-root -R root:root /var/www/public_html/
	#chown --preserve-root -R nginx:nginx /var/www/public_html/var /var/www/public_html/pub/media /var/www/public_html/pub/static /var/www/public_html/generated
	#php -d memory_limit=-1 /var/www/public_html/bin/magento setup:di:compile
	#php -d memory_limit=-1 /var/www/public_html/bin/magento setup:static-content:deploy -f -j 1
	#chown --preserve-root -R nginx:nginx /var/www/public_html/var /var/www/public_html/pub/media /var/www/public_html/pub/static /var/www/public_html/generated
	#chown root:nginx /var/lib/php/fpm -R
	
	###Added the web2 node back in LB - 5 mins	
#12. Run automation script till from DM
#13. mount: env.php, var/log, var/report 
#14. container (up/down)
#15. make sure M2 DB name is updated in env.php in base machine, web1 and web2 docker container
#16. make sure web1 and web2 has proper permission for M2 DB
#15. deployment
#16. Remove the shell script
##########Manually activity end before running this script######################################


####Issue: Unable to apply data patch Perficient\Base\Setup\Patch\Data\CmsPagesBlog    
####Solution: This file is not executing


##########Currently Mounted Folders list######################################
	#./media:				/var/www/public_html/pub/media				- no need to unmount as we are not deleting
	#./.env/env.php:		/var/www/public_html/app/etc/env.php		- unmount needed
	#./var/log:				/var/www/public_html/var/log				- unmount needed
	#./var/report:			/var/www/public_html/var/report			
	#./logs:				/var/log									- no need to unmount folders from outside doc root
	#/sys/fs/cgroup:		/sys/fs/cgroup:ro
	#/tmp/wendoverdemo:		/run
	#./tmp/fastcgi_params:	/etc/nginx/fastcgi_params
	#./tempdb:				/var/www/tempdb
##########Currently Mounted Folders list######################################

#!/bin/sh

set -o nounset
set -o errexit

#DOCUMENT_ROOT="../../../../.."
DOCUMENT_ROOT="."
PWD="."

M2_VERSION="2.4.0"
EXECUTE_SQL_FILES="/var/www/tempdb"

PUB_MEDIA=$DOCUMENT_ROOT/"pub/media"
SETUP_FILES=$DOCUMENT_ROOT/"setup_files"
SETUP_FILES_DIR_NAME="setup_files"
M2_CORE_FILES=$SETUP_FILES/"m2_core_files"
M2_CUSTOM_FILES=$SETUP_FILES/"m2_custom_files"
M2_BACKUP_FILES=$SETUP_FILES/"m2_backup_files"
M2_DATAMIGRATION_SCRIPT=$SETUP_FILES/"m2_datamigration_script"

DATA_MIGRATION_SHELL_SCRIPT="datamigration.sh"
DATA_MIGRATION_PHP_SCRIPT="Datamigration.php"

SQL_PATH="sql"
SQL_PRE_MIGRATION=$SQL_PATH/"wendover-pre-migration.sql"
SQL_POST_MIGRATION=$SQL_PATH/"wendover-post-migration.sql"
SQL_M1_LOG_CUSTOMER=$SQL_PATH/"m1_log_customer.sql"
SQL_M2_CMS_BACKUP=$SQL_PATH/"m2_cms_backup.sql"
SQL_CUSTOM_TABLES_FIELDS=$SQL_PATH/"custom-tables-and-fields.sql"

#BACKUP USED FOR RESTORING
SQL_CORE_CONFIG_AFTER_FRESH_M2="core_config_data_after_fresh_install.sql"

#ONLY FOR REFERENCE
SQL_CORE_CONFIG_BEFORE_STARTING="m2_core_config_data.sql"
SQL_M2_CORE_CONFIG_DATA="m2_migrated_core_config_data.sql"


# Domain name
DOMAIN_NAME=""

# M1 db credentials
M1_DB_HOST=""
M1_DB_NAME=""
M1_DB_USER=""
M1_DB_PASS="$(echo '')"

# M2 db credentials
M2_DB_HOST=""
M2_DB_NAME=""
M2_DB_USER=""
M2_DB_PASS="$(echo '')"

# Elasticsearch credentials
SEARCH_ENGINE=""
SEARCH_ENGINE_HOST=""
SEARCH_ENGINE_PORT=""

# Start
echo "DATA MIGRATION - STARTED"

echo ""
echo "DOMAIN_NAME : $DOMAIN_NAME"
echo ""
echo "M1_DB_HOST : $M1_DB_HOST"
echo "M1_DB_NAME : $M1_DB_NAME"
echo "M1_DB_USER : $M1_DB_USER"
echo "M1_DB_PASS : $M1_DB_PASS"
echo ""
echo "M2_DB_HOST : $M2_DB_HOST"
echo "M2_DB_NAME : $M2_DB_NAME"
echo "M2_DB_USER : $M2_DB_USER"
echo "M2_DB_PASS : $M2_DB_PASS"
echo ""

read -p "Settings Ok? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac

# M1 database drop available on this server
echo "M1 DATABASE DROP - STARTED"
mysql -h$M1_DB_HOST -u$M1_DB_USER -p$M1_DB_PASS -e "DROP DATABASE IF EXISTS $M1_DB_NAME; CREATE DATABASE $M1_DB_NAME;"
echo "M1 DATABASE DROP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac

# M1 DB restore with M1 Live DB backup store on this server
echo "M1 DB RESTORE - STARTED"
mysql -h$M1_DB_HOST -u$M1_DB_USER -p$M1_DB_PASS $M1_DB_NAME < $EXECUTE_SQL_FILES/$M1_DB_NAME.sql
echo "M1 DB RESTORE - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# Take backup of M2 DB's core_config_data table
echo "BACKUP OF M2 DB's core_config_data TABLE - STARTED"
mysqldump -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS --no-tablespaces --single-transaction --routines --triggers --quick $M2_DB_NAME core_config_data | sed -r 's/DEFINER=`[^`]+`@`[^`]+`/DEFINER=CURRENT_USER/g'  >> $DOCUMENT_ROOT/$SQL_CORE_CONFIG_BEFORE_STARTING
echo "BACKUP OF M2 DB's core_config_data TABLE - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# M2 database drop for fresh install
echo "M2 DATABASE DROP - STARTED"
mysql -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS -e "DROP DATABASE IF EXISTS $M2_DB_NAME; CREATE DATABASE $M2_DB_NAME;"
echo "M2 DATABASE DROP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac
	

# M2 BACKUP CONFIGURATION FILES
echo "M2 BACKUP CONFIGURATION FILES - STARTED"
cp $DOCUMENT_ROOT/auth.json $M2_BACKUP_FILES/auth.json
#Below command is commented as app/etc/env.php is unmounted so, this will throw error for file not found.
#We are taking backup of env.php manually and putting it at $M2_BACKUP_FILES/env.php
#cp $DOCUMENT_ROOT/app/etc/env.php $M2_BACKUP_FILES/env.php
cp $DOCUMENT_ROOT/app/etc/config.php $M2_BACKUP_FILES/config.php
echo "M2 BACKUP CONFIGURATION FILES - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac		

# COPY OF CUSTOM CODE
echo "COPY OF CUSTOM CODE - STARTED"
#Backup custom code
mkdir $M2_CUSTOM_FILES/app
cp -r $DOCUMENT_ROOT/app/design $M2_CUSTOM_FILES/app/design
cp -r $DOCUMENT_ROOT/app/code $M2_CUSTOM_FILES/app/code
mkdir $M2_CUSTOM_FILES/app/etc
cp $DOCUMENT_ROOT/app/etc/config.php $M2_CUSTOM_FILES/app/etc/config.php
cp -r $DOCUMENT_ROOT/patches $M2_CUSTOM_FILES/patches
mkdir $M2_CUSTOM_FILES/lib
cp -r $DOCUMENT_ROOT/lib/perficient $M2_CUSTOM_FILES/lib/
mkdir $M2_CUSTOM_FILES/pub
cp -r $DOCUMENT_ROOT/pub/errors $M2_CUSTOM_FILES/pub/errors
cp $DOCUMENT_ROOT/composer.json $M2_CUSTOM_FILES/composer.json
cp $DOCUMENT_ROOT/composer.lock $M2_CUSTOM_FILES/composer.lock
cp $DOCUMENT_ROOT/auth.json $M2_CUSTOM_FILES/auth.json
cp $DOCUMENT_ROOT/.gitignore $M2_CUSTOM_FILES/.gitignore	
echo "COPY OF CUSTOM CODE - END"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac

# MOVE and REMOVE OPERATIONS ON REQUIRED FILES
echo "MOVE and REMOVE OPERATIONS ON REQUIRED FILES - STARTED"
#Tar to backup setup files
tar -czf $SETUP_FILES_DIR_NAME.tar.gz $SETUP_FILES_DIR_NAME
#Move Tar to pub
mv $SETUP_FILES_DIR_NAME.tar.gz $PUB_MEDIA
#Delete all folders except pub
rm -rf `find . -maxdepth 1 -type d -not -name 'pub' | grep -vE '^.$'`
#File all files except sh script
rm -rf `find . -maxdepth 1 -type f -not -name 'datamigration.sh'`
#Delete all folders from pub excpet media
cd pub; rm -rf `find . -maxdepth 1 -type d -not -name 'media' -not -name 'productimize_json' -not -name '.'`; cd ..
cd pub; rm -rf `find . -maxdepth 1 -type f -not -name 'media' -not -name 'productimize_json'`; cd ..
#Move setup_files to document root
mv $PUB_MEDIA/$SETUP_FILES_DIR_NAME.tar.gz $DOCUMENT_ROOT
#Untar setup files
tar xvzf $SETUP_FILES_DIR_NAME.tar.gz
echo "MOVE and REMOVE OPERATIONS ON REQUIRED FILES - END"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# M2 FRESH INSTALL
echo "M2 FRESH INSTALL - STARTED"
cp $M2_DATAMIGRATION_SCRIPT/$DATA_MIGRATION_PHP_SCRIPT $DOCUMENT_ROOT/$DATA_MIGRATION_PHP_SCRIPT
cp -r $M2_DATAMIGRATION_SCRIPT/$SQL_PATH $DOCUMENT_ROOT
cp $M2_CORE_FILES/auth.json $DOCUMENT_ROOT/auth.json
cp $M2_CORE_FILES/composer.json $DOCUMENT_ROOT/composer.json
cp $M2_CORE_FILES/composer.lock $DOCUMENT_ROOT/composer.lock

COMPOSER_MEMORY_LIMIT=-1 composer install --verbose

#php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:install --db-host=$M2_DB_HOST --db-name=$M2_DB_NAME --db-user=$M2_DB_USER --db-password=$M2_DB_PASS --admin-firstname=Suraj --admin-lastname=Jaiswal --admin-email=suraj.jaiswal@perficient.com --admin-user=suraj.jaiswal --admin-email=suraj.jaiswal@perficient.com --admin-password=prft@2020 --base-url=https://$DOMAIN_NAME/ --base-url-secure=https://$DOMAIN_NAME/ --backend-frontname=adminpanel --use-rewrites=1 --use-secure=1 --use-secure-admin=1

php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:install --db-host=$M2_DB_HOST --db-name=$M2_DB_NAME --db-user=$M2_DB_USER --db-password=$M2_DB_PASS --admin-firstname=Suraj --admin-lastname=Jaiswal --admin-email=suraj.jaiswal@perficient.com --admin-user=suraj.jaiswal --admin-email=suraj.jaiswal@perficient.com --admin-password=prft@2020 --base-url=https://$DOMAIN_NAME/ --base-url-secure=https://$DOMAIN_NAME/ --backend-frontname=adminpanel --use-rewrites=1 --use-secure=1 --use-secure-admin=1 --search-engine=$SEARCH_ENGINE --elasticsearch-host=$SEARCH_ENGINE_HOST --elasticsearch-port=$SEARCH_ENGINE_PORT
echo "M2 FRESH INSTALL - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# M2 COMMANDS AFTER DEFAULT SETUP
echo "M2 COMMANDS AFTER DEFAULT SETUP - STARTED"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:upgrade
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:di:compile
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:static-content:deploy -f
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento cache:flush
echo "M2 COMMANDS AFTER DEFAULT SETUP - DONE"
echo ""


read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# PULL DATA MIGRATION TOOL AND SETUP
echo "PULL DATA MIGRATION TOOL AND SETUP - STARTED"
COMPOSER_MEMORY_LIMIT=-1 composer require magento/data-migration-tool:$M2_VERSION --verbose
echo "PULL DATA MIGRATION TOOL AND SETUP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# M2 COMMANDS AFTER DATA MIGRATION TOOL SETUP
echo "M2 COMMANDS AFTER DATA MIGRATION TOOL SETUP - STARTED"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:upgrade
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:di:compile
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:static-content:deploy -f
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento cache:flush
echo "M2 COMMANDS AFTER DATA MIGRATION TOOL SETUP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# Take backup of core_config_data after fresh install
echo "BACKUP OF core_config_data FROM M2 DB after fresh install - STARTED"
mysqldump -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS --no-tablespaces --single-transaction --routines --triggers --quick $M2_DB_NAME core_config_data | sed -r 's/DEFINER=`[^`]+`@`[^`]+`/DEFINER=CURRENT_USER/g' >> $DOCUMENT_ROOT/$SQL_CORE_CONFIG_AFTER_FRESH_M2
echo "BACKUP OF core_config_data FROM M2 DB after fresh install - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# M1 data cleanup before migration
echo "M1 DATA CLEANUP - STARTED"
mysql -h$M1_DB_HOST -u$M1_DB_USER -p$M1_DB_PASS $M1_DB_NAME < $PWD/$SQL_PRE_MIGRATION
echo "M1 DATA CLEANUP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# M2 fields and tables creation
echo "M2 FIELDS AND TABLE CREATION - STARTED"
mysql -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS $M2_DB_NAME < $PWD/$SQL_CUSTOM_TABLES_FIELDS
echo "M2 FIELDS AND TABLE CREATION - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# Map migration tool files
echo "Map MIGRATION TOOL FILES - STARTED"
php -d memory_limit=-1 $PWD/$DATA_MIGRATION_PHP_SCRIPT "M1_DB_HOST=$M1_DB_HOST&M1_DB_NAME=$M1_DB_NAME&M1_DB_USER=$M1_DB_USER&M1_DB_PASS=$M1_DB_PASS&M2_DB_HOST=$M2_DB_HOST&M2_DB_NAME=$M2_DB_NAME&M2_DB_USER=$M2_DB_USER&M2_DB_PASS=$M2_DB_PASS"
echo "Map MIGRATION TOOL FILES - END"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# Settings migration
echo "SETTINGS MIGRATION - STARTED"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento migrate:settings --reset $DOCUMENT_ROOT/vendor/magento/data-migration-tool/etc/commerce-to-commerce/1.12.0.2/config.xml -vvv
echo "SETTINGS MIGRATION - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac

# Take backup of core_config_data (latest which is migrated) from M2 DB. This will not be used anywhere but for reference
echo "BACKUP OF core_config_data FROM M2 DB - STARTED"
mysqldump -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS --no-tablespaces --single-transaction --routines --triggers --quick $M2_DB_NAME core_config_data | sed -r 's/DEFINER=`[^`]+`@`[^`]+`/DEFINER=CURRENT_USER/g' >> $DOCUMENT_ROOT/$SQL_M2_CORE_CONFIG_DATA
echo "BACKUP OF core_config_data FROM M2 DB - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# Data migration
echo "DATA MIGRATION - STARTED"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento migrate:data --reset $DOCUMENT_ROOT/vendor/magento/data-migration-tool/etc/commerce-to-commerce/1.12.0.2/config.xml -vvv
echo "DATA MIGRATION - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# m2 indexing
echo "M2 INDEXING - STARTED"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento indexer:reindex
echo "M2 INDEXING - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac

# m2 cache cleanup
echo "M2 CACHE CLEANUP - STARTED"
php $DOCUMENT_ROOT/bin/magento cache:flush
echo "M2 CACHE CLEANUP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# Truncate M2's core_config_data table
echo "TRUNCATE M2's core_config_data TABLE - STARTED"
mysql -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS -e "USE $M2_DB_NAME; TRUNCATE TABLE core_config_data;"
echo "TRUNCATE M2's core_config_data TABLE - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# Restore core_config_data table of M2 DB
echo "RESTORE core_config_data TABLE OF M2 DB - STARTED"
mysql -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS $M2_DB_NAME < $DOCUMENT_ROOT/$SQL_CORE_CONFIG_AFTER_FRESH_M2
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento cache:flush
echo "RESTORE core_config_data TABLE OF M2 DB - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# PULL M2 CUSTOM CODE
echo "PULL M2 CUSTOM CODE - STARTED"
cp -R $M2_CUSTOM_FILES/* $DOCUMENT_ROOT
echo "PULL M2 CUSTOM CODE - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# COMPOSER INSTALL FOR EXTENSION
echo "COMPOSER INSTALL FOR EXTENSION - STARTED"	
COMPOSER_MEMORY_LIMIT=-1 composer install --verbose	
echo "COMPOSER INSTALL FOR EXTENSION - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# M2 COMMANDS FOR CUSTOM MODULES AND EXTENSIONS
echo "M2 COMMANDS FOR CUSTOM MODULES AND EXTENSIONS - START"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:upgrade
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:di:compile
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento setup:static-content:deploy -f
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento cache:flush
echo "M2 COMMANDS FOR CUSTOM MODULES AND EXTENSIONS - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac
		
		
#To be removed on live instance
echo "Append perficienttest- to email id - STARTED"
mysql -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS -e "USE $M2_DB_NAME; UPDATE customer_entity set email = CONCAT('perficienttest-', email);"
mysql -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS -e "USE $M2_DB_NAME; UPDATE grandriver_cc_designers set email = CONCAT('perficienttest-', email);"
echo "Append perficienttest- to email id - END"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


#Enable B2B company setting manually


# m2 data cleanup after upgrade
echo "M2 DATA CLEANUP - STARTED"
mysql -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS $M2_DB_NAME < $PWD/$SQL_POST_MIGRATION
echo "M2 DATA CLEANUP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# m2 indexing
echo "M2 INDEXING - STARTED"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento indexer:reindex
echo "M2 INDEXING - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac

# m2 cache cleanup
echo "M2 CACHE CLEANUP - STARTED"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento cache:flush
echo "M2 CACHE CLEANUP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# CMS restore from repository
echo "CMS restore from repository - STARTED"
mysql -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS $M2_DB_NAME < $PWD/$SQL_M2_CMS_BACKUP
echo "CMS restore from repository - DONE"
echo ""


read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# m2 cache cleanup
echo "M2 CACHE CLEANUP - STARTED"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento cache:flush
echo "M2 CACHE CLEANUP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# Import M1 log_customer table data to M2 DB which will be use to migrate company 
echo "Import M1 log_customer table data to M2 DB - STARTED"
mysql -h$M2_DB_HOST -u$M2_DB_USER -p$M2_DB_PASS $M2_DB_NAME < $PWD/$SQL_M1_LOG_CUSTOMER
echo "Import M1 log_customer table data to M2 DB - END"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# Desginer/Employee - Company creation, designer as super admin and employee association to company
echo "Company Creation and Association - Start"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento perficient_data_migration:custom-data --type company
echo "Company Creation and Association - End"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac

# Customer's Customer Association
echo "Customer's Customer Association - Start"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento perficient_data_migration:custom-data --type customers_customer
echo "Customer's Customer Association - End"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# m2 cache cleanup
echo "M2 CACHE CLEANUP - STARTED"
php $DOCUMENT_ROOT/bin/magento cache:flush
echo "M2 CACHE CLEANUP - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# m2 indexing
echo "M2 INDEXING - STARTED"
php -d memory_limit=-1 $DOCUMENT_ROOT/bin/magento indexer:reindex
echo "M2 INDEXING - DONE"
echo ""

read -p "Continue? (y/n) " choice
case "$choice" in
  y|Y ) echo "";;
  n|N ) echo "Aborting."; exit 1;;
  * ) echo "Aborting."; exit 1;;
esac


# create admin users
echo "M2 ADMIN USER SETUP - STARTED"
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=monika.nemade --admin-password=prft@2020 --admin-email=monika.nemade@perficient.com --admin-firstname=Monika --admin-lastname=Nemade
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=suhas.dhoke --admin-password=prft@2020 --admin-email=suhas.dhoke@perficient.com --admin-firstname=Suhas --admin-lastname=Dhoke
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=harshal.dantalwar --admin-password=prft@2020 --admin-email=harshal.dantalwar@perficient.com --admin-firstname=Harshal --admin-lastname=Dantalwar
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=kunal.mahore --admin-password=prft@2020 --admin-email=kunal.mahore@perficient.com --admin-firstname=Kunal --admin-lastname=Mahore
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=sachin.badase --admin-password=prft@2020 --admin-email=sachin.badase@perficient.com --admin-firstname=Sachin --admin-lastname=Badase
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=sandeep.mude --admin-password=prft@2020 --admin-email=sandeep.mude@perficient.com --admin-firstname=Sandeep --admin-lastname=Mude
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=trupti.bobde --admin-password=prft@2020 --admin-email=trupti.bobde@perficient.com --admin-firstname=Trupti --admin-lastname=Bobde
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=brian.katke --admin-password=prft@2020 --admin-email=brian.katke@perficient.com --admin-firstname=Brian --admin-lastname=Katke
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=jason.madison --admin-password=prft@2020 --admin-email=jason.madison@perficient.com --admin-firstname=Jason --admin-lastname=Madison
php $DOCUMENT_ROOT/bin/magento admin:user:create --admin-user=bob.kwait --admin-password=prft@2020 --admin-email=bob.kwait@perficient.com --admin-firstname=Bob --admin-lastname=Kwait
echo "M2 ADMIN USER SETUP - DONE"
echo ""


#Few temporary actvitites which will be later automated
	#set bloomreach as search engine
	#Disable Catalog Permission
	#Enable Amasty Mena Menu
	#Set theme and if required other configurations of Content > Configurations - (ex: header logo)
	#CMS restore from repository
	#set no index no follow
	#disable email sending
	#generate bloomreach feed if required on server
	#enable login as customer


# end
echo "DATA MIGRATION - DONE"
echo ""

exit 0
