#!/bin/bash

set -o nounset
set -o errexit

deploy_host=$1
deploy_user=$2
docker_container=$3
docker_tag=$4
docker_compose_file=$5

# test ssh connection with remote server
ssh -o StrictHostKeyChecking=no $deploy_user@$deploy_host "echo 2>&1" && echo $deploy_user@$deploy_host OK || echo $deploy_user@$deploy_host NOTOK

# start release process
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/local/bin/docker-compose -f $docker_compose_file down
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo systemctl restart docker.service
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker rmi -f $docker_tag
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/local/bin/docker-compose -f $docker_compose_file up -d
#Added as requested by IT since newrelic service is getting down after every deployment and not getting up (HTTP request took too long to complete error in deployment)
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container systemctl restart newrelic-daemon
##Removed this was trial for image generation not loading image from magento server
#/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container /bin/bash -c "echo '172.18.0.2 prodnode.perficientdcsdemo.com' >> /etc/hosts"
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container php /var/www/public_html/bin/magento maintenance:enable
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container php /var/www/public_html/bin/magento deploy:mode:set production --skip-compilation
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container php -d memory_limit=1G /var/www/public_html/bin/magento setup:upgrade
#Commented as RabbitMq new queue addition failed deployment as it refers to old generated/meta/global.php file which dont have new queue nodes
#/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container php -d memory_limit=1G /var/www/public_html/bin/magento setup:upgrade --keep-generated
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container php -d memory_limit=1G /var/www/public_html/bin/magento setup:di:compile
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container composer dumpautoload
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container php -d memory_limit=1G /var/www/public_html/bin/magento setup:static-content:deploy -f -j 1 
#Commented and remove media ownership from deployment to avoid issues in deployment because of some folder mounting. Separate cron added for this by IT.
#Even if we keep media ownership code in deployment along with mounting it dont break for web1. For web2 we removed this and tested deployment.
#/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container chown --preserve-root -R nginx:nginx /var/www/public_html/var /var/www/public_html/pub/media /var/www/public_html/pub/static /var/www/public_html/generated
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container chown --preserve-root -R nginx:nginx /var/www/public_html/var /var/www/public_html/pub/static /var/www/public_html/generated
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container php /var/www/public_html/bin/magento cache:flush
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container php /var/www/public_html/bin/magento maintenance:disable
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/local/bin/docker-compose -f $docker_compose_file up -d

#As per suggested by IT Team, added fixes to generate the bloomreach feed on server.
#This was commented as the user magentom2 is anyhow not used when cron creates feed. Ever after commenting feed is auto generated in folder by cron
#/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -R -m u:magentom2:rwx /var/www/public_html/pub/media/
#/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -R -m u:magentom2:rwx /var/www/public_html/pub/media/
#/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -d -m u:magentom2:rwx /var/www/public_html/pub/media/

#As suggested by IT Team, for permission issues
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -R -m m::rwx /var/www/public_html/pub/media/
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -d -m m::rwx /var/www/public_html/pub/media/
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -R -d -m m::rwx /var/www/public_html/pub/media/