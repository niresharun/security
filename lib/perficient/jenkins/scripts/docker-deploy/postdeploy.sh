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


#Moved media outside of release process to avoid slow deployment issue because of NYS mount from web1 to web2
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container chown --preserve-root -R nginx:nginx /var/www/public_html/pub/media

#As per suggested by IT Team, added fixes to generate the bloomreach feed on server.
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -R -m u:magentom2:rwx /var/www/public_html/pub/media/ 
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -R -m u:magentom2:rwx /var/www/public_html/pub/media/
/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -d -m u:magentom2:rwx /var/www/public_html/pub/media/

#/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -R -m u:magentom2:rwx /var/www/public_html/pub/media/ 
#/usr/bin/ssh -o StrictHostKeyChecking=no $2@$1 sudo /usr/bin/docker exec $docker_container setfacl -R -d -m u:magentom2:rwx /var/www/public_html/pub/media/