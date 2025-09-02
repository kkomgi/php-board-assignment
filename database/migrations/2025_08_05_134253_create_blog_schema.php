<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * 블로그 시스템 데이터베이스 스키마 생성 마이그레이션
 * 
 * 면접 포인트:
 * 1. Laravel Migration으로 데이터베이스 버전 관리
 * 2. 외래키 제약조건으로 데이터 무결성 보장
 * 3. CASCADE DELETE로 관련 데이터 일관성 유지
 * 4. 인덱스 최적화 (unique 제약조건)
 * 5. 계층형 데이터 구조 구현 (댓글의 parent_id)
 */
return new class extends Migration
{
    /**
     * 마이그레이션 실행: 데이터베이스 테이블 생성
     * 
     * 면접 설명: up() 메서드는 마이그레이션 적용 시 실행
     * php artisan migrate 명령으로 실행됨
     */
    public function up(): void
    {
        // ================================
        // 1. 👤 사용자 테이블 (users)
        // ================================
        /**
         * 사용자 정보를 저장하는 기본 테이블
         * 
         * 면접 포인트:
         * - username을 로그인 식별자로 사용 (email 대신)
         * - 패스워드는 해싱되어 저장
         * - 이메일과 사용자명 모두 unique 제약
         */
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');                     // AUTO_INCREMENT PRIMARY KEY
            $table->string('username', 20)->unique();     // 로그인용 고유 사용자명 (최대 20자)
            $table->string('name', 100);                  // 실명/닉네임 (최대 100자)
            $table->string('email')->unique();            // 이메일 주소 (고유값, 알림용)
            $table->string('password');                   // 해싱된 패스워드
            $table->timestamps();                         // created_at, updated_at 자동 관리
        });

        // ================================
        // 2. 📝 게시글 테이블 (posts)
        // ================================
        /**
         * 게시글 정보를 저장하는 테이블
         * 
         * 면접 포인트:
         * - user_id로 작성자와 연결
         * - title은 VARCHAR, body는 TEXT (긴 내용 지원)
         * - CASCADE DELETE로 사용자 삭제시 게시글도 삭제
         */
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');                     // 게시글 고유 ID
            $table->unsignedInteger('user_id');           // 작성자 ID (외래키)
            $table->string('title');                      // 게시글 제목 (VARCHAR 255)
            $table->text('body');                         // 게시글 내용 (TEXT 타입으로 긴 내용 지원)
            $table->timestamps();                         // 작성일시, 수정일시

            // 외래키 제약조건: 사용자 테이블과 연결
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');                  // 사용자 삭제시 게시글도 삭제
        });

        // ================================
        // 3. ❤️ 좋아요 테이블 (likes)
        // ================================
        /**
         * 게시글 좋아요 정보를 저장하는 테이블
         * 
         * 면접 포인트:
         * - 복합 유니크 키로 중복 좋아요 방지
         * - post_id, user_id 조합이 고유값
         * - 양방향 CASCADE DELETE로 데이터 일관성 보장
         */
        Schema::create('likes', function (Blueprint $table) {
            $table->increments('id');                     // 좋아요 고유 ID
            $table->unsignedInteger('post_id')->nullable(); // 게시글 ID (향후 댓글 좋아요 확장 고려)
            $table->unsignedInteger('user_id');           // 좋아요 누른 사용자 ID
            $table->timestamps();                         // 좋아요 누른 시간

            // 중복 좋아요 방지: (post_id, user_id) 조합 유니크
            $table->unique(['post_id', 'user_id']);

            // 외래키 제약조건: 게시글이 삭제되면 좋아요도 삭제
            $table->foreign('post_id')
                  ->references('id')
                  ->on('posts')
                  ->onDelete('cascade');
            
            // 외래키 제약조건: 사용자가 삭제되면 좋아요도 삭제
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        // ================================
        // 4. 💬 댓글 테이블 (comments)
        // ================================
        /**
         * 댓글과 대댓글을 저장하는 계층형 테이블
         * 
         * 면접 포인트:
         * - parent_id를 통한 계층형 데이터 구조
         * - 자기 참조 외래키로 대댓글 구현
         * - NULL parent_id는 최상위 댓글
         * - CASCADE DELETE로 상위 댓글 삭제시 하위 댓글도 삭제
         */
        Schema::create('comments', function (Blueprint $table) {
            $table->increments('id');                     // 댓글 고유 ID
            $table->unsignedInteger('post_id');           // 어느 게시글의 댓글인지
            $table->unsignedInteger('user_id');           // 댓글 작성자 ID
            $table->unsignedInteger('parent_id')->nullable(); // 상위 댓글 ID (NULL이면 최상위 댓글)
            $table->text('body');                         // 댓글 내용
            $table->timestamps();                         // 작성일시, 수정일시

            // 외래키 제약조건: 게시글 삭제시 모든 댓글 삭제
            $table->foreign('post_id')
                  ->references('id')
                  ->on('posts')
                  ->onDelete('cascade');
            
            // 외래키 제약조건: 사용자 삭제시 해당 사용자의 모든 댓글 삭제
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // 자기 참조 외래키: 상위 댓글 삭제시 하위 댓글들도 삭제
            $table->foreign('parent_id')
                  ->references('id')
                  ->on('comments')
                  ->onDelete('cascade');
        });
    }

    /**
     * 마이그레이션 롤백: 데이터베이스 테이블 삭제
     * 
     * 면접 설명: 
     * - down() 메서드는 마이그레이션 롤백 시 실행
     * - php artisan migrate:rollback 명령으로 실행
     * - 외래키 의존성을 고려한 역순 삭제
     */
    public function down(): void
    {
        // 외래키 의존성을 고려한 순서로 테이블 삭제
        // 의존성: comments -> posts -> users
        //        comments -> comments (자기참조)
        //        likes -> posts, users
        
        Schema::dropIfExists('comments');   // 가장 많은 외래키를 가진 테이블부터 삭제
        Schema::dropIfExists('likes');      // 게시글, 사용자에 의존하는 테이블
        Schema::dropIfExists('posts');      // 사용자에 의존하는 테이블
        Schema::dropIfExists('users');      // 가장 기본이 되는 테이블 마지막 삭제
    }
};
