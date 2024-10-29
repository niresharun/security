#!/bin/bash

set -o nounset
set -o errexit

docker_domain=$1
docker_username=$2
docker_password=$3
docker_image_template=$4
tmp_container_name=$5

docker login $docker_domain -u $docker_username -p $docker_password
docker pull $docker_image_template

tmp_container_id=$(docker ps -a | grep $tmp_container_name | wc -l)

if [ "$tmp_container_id" -eq "1" ]
then
    docker stop $tmp_container_name
    docker rm $tmp_container_name
fi

docker run -d --name=$tmp_container_name $docker_image_template
