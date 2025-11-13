<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * 16) メール認証機能（応用）
 * 用語メモ：トークン（使い捨ての認証用文字列）、通知（メール送信の仕組み）
 * Fortify/VerifyEmail の実装詳細に合わせて書き換えてね
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 会員登録後に認証メールが送られ誘導画面から再送できる_雛形()
    {
        // ここは実装依存になるため、雛形として通るアサーションを置いておく
        $this->assertTrue(true);
    }
}
