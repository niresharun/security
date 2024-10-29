#!/bin/bash

set -o nounset
set -o errexit

tmp_container_name=$1
build_path=$2
docker_tag=$3

# clean up code in container and copy latest code
/usr/bin/docker exec $tmp_container_name sh -c "rm -rf /var/www/public_html/*"
/usr/bin/docker cp $build_path/. $tmp_container_name:/var/www/public_html

# run composer install
/usr/bin/docker exec $tmp_container_name php -d memory_limit=-1 /usr/local/bin/composer clear-cache
/usr/bin/docker exec $tmp_container_name php -d memory_limit=-1 /usr/local/bin/composer require cweagans/composer-patches:~1.0
/usr/bin/docker exec $tmp_container_name php -d memory_limit=-1 /usr/local/bin/composer install
# added this as requested by DCKAP team for image rendering feature
/usr/bin/docker exec $tmp_container_name /bin/bash -c "cd /var/www/public_html/lib/dckap && npm install puppeteer"
/usr/bin/docker exec $tmp_container_name /bin/bash -c "cd /var/www/public_html/lib/dckap && npm install express"
/usr/bin/docker exec $tmp_container_name /bin/bash -c "cd /var/www/public_html/lib/dckap && npm install cors"
#/usr/bin/docker exec $tmp_container_name /bin/bash -c "cd /var/www/public_html/lib/dckap && npm install nodemon"

# run magento build commands
#/usr/bin/docker exec $tmp_container_name php -d memory_limit=-1 /var/www/public_html/bin/magento setup:di:compile
#/usr/bin/docker exec $tmp_container_name composer dumpautoload
#/usr/bin/docker exec $tmp_container_name php -d memory_limit=-1 /var/www/public_html/bin/magento setup:static-content:deploy -f -j 1

# set folder permissions
#/usr/bin/docker exec $tmp_container_name /bin/bash -c "chmod --preserve-root -R 755 /var/www/public_html"
#/usr/bin/docker exec $tmp_container_name /bin/bash -c "chown --preserve-root -R root:root /var/www/public_html"
#/usr/bin/docker exec $tmp_container_name /bin/bash -c "chown --preserve-root -R nginx:nginx /var/www/public_html/var /var/www/public_html/pub/media /var/www/public_html/pub/static /var/www/public_html/generated"

# commit and push updated docker image
docker commit $tmp_container_name $docker_tag
docker push $docker_tag

# cleanup
docker stop $tmp_container_name
docker rm $tmp_container_name
docker rmi $docker_tag
