AWS_REGION=ap-northeast-1
AWS_PROFILE=default
ECR_HOST=949140100595.dkr.ecr.ap-northeast-1.amazonaws.com
LEGACY_APP_HOST=49.212.164.219

# REVISION:=01
# IMAGE_TAG:=$(shell date '+%y%m.${REVISION}')
IMAGE_TAG:=latest

fetch_legacy_app:
	# ssh ${LEGACY_APP_HOST} "sudo cp -Lr bms /tmp/_bms"
	sftp -r ${LEGACY_APP_HOST}:/tmp/bms/ _legacy

build: build_legacy build_v2

build_legacy:
	docker build -t bms-legacy:${IMAGE_TAG} -f Dockerfile.legacy .

build_v2:
	docker build -t bms:${IMAGE_TAG} -f Dockerfile .

run_legacy:
	docker run --rm -p 8888:80 --name fuelphp  -it bms-legacy

ecr_login:
	aws --profile=${AWS_PROFILE} ecr get-login-password --region ${AWS_REGION} | docker login --username AWS --password-stdin ${ECR_HOST}

docker_push_legacy: build_legacy
	docker tag bms-legacy:${IMAGE_TAG} ${ECR_HOST}/bms-legacy:${IMAGE_TAG}
	docker push ${ECR_HOST}/bms-legacy:${IMAGE_TAG}
