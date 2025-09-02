# 게시판 API (Laravel 12 + Docker)

Docker로 Laravel + MySQL 환경을 실행하는 게시판 API입니다.  
**DB 초기 데이터(init.sql)가 포함되어 있어 자동으로 세팅됩니다.**

---

## 1. 사전 준비

1. [Docker Desktop](https://www.docker.com/products/docker-desktop/) 설치  
   (설치 후 Docker Desktop을 실행해 두세요)
2. 이 프로젝트 zip 파일 다운로드 후 압축 해제

---

## 2. 실행 방법

### 1) 터미널 열기
- **Windows**: PowerShell  
- **Mac**: 터미널

### 2) 프로젝트 폴더로 이동
압축을 푼 위치(php-board-assignment 폴더)로 이동하세요.

```bash
cd php-board-assignment
```

### 3) 컨테이너 실행
```bash
docker-compose up -d --build
```

### 4) Laravel 준비
```bash
docker exec -it laravel_app composer install
cp .env.example .env
docker exec -it laravel_app php artisan key:generate
```

---

## 3. 접속

브라우저에서 접속:
```
http://localhost:8000
```

---

## 4. Postman 테스트 (선택)

`postman/Laravel Board API.postman_collection.json` 파일을 Postman에 Import 하면  
회원가입, 로그인, 게시글, 댓글, 좋아요 API를 바로 테스트할 수 있습니다.
