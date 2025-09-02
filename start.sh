#!/bin/bash
set -e
echo "Docker로 Laravel + MySQL 환경 띄우는 중..."
docker compose up --build -d
echo "컨테이너 상태 확인: docker compose ps"
echo "로그 보기: docker compose logs -f app"
echo "http://localhost:8000 로 접속하세요."
