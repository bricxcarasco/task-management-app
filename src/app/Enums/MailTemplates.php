<?php

/**
 * Mail Templates
 */

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * App\Enums\MailTemplates
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class MailTemplates extends Enum
{
    /**
     * Signup Email Verification
     * 新規会員登録メール認証
     *
     * @var int
     */
    public const SIGNUP_EMAIL_VERIFICATION = 1;

    /**
     * Registration Verified
     * 新規会員登録完了
     *
     * @var int
     */
    public const REGISTRATION_VERIFIED = 2;

    /**
     * Reset Email Verification
     * 既存会員メール変更によるメール認証
     *
     * @var int
     */
    public const EMAIL_RESET_VERIFICATION = 3;

    /**
     * Connection application
     * つながり申請
     *
     * @var int
     */
    public const CONNECTION_APPLICATION = 4;

    /**
     * Connection group invitation
     * つながりグループ招待
     *
     * @var int
     */
    public const CONNECTION_GROUP_INVITATION = 5;

    /**
     * NEO Invitation
     * NEO招待
     *
     * @var int
     */
    public const NEO_INVITATION = 6;

    /**
     * Chat Message
     * チャットメッセージ
     *
     * @var int
     */
    public const CHAT_MESSAGE = 7;

    /**
     * Add sharing to document management
     * 文書管理への共有追加
     *
     * @var int
     */
    public const ADD_DOCUMENT_SHARING = 8;

    /**
     * Schedule invitation
     * スケジュールの参加申請
     *
     * @var int
     */
    public const SCHEDULE_INVITATION = 9;

    /**
     * Change Password
     * パスワードの変更
     *
     * @var int
     */
    public const CHANGE_PASSWORD = 10;

    /**
     * NETSHOP Purchase
     * ネットショップ購入
     *
     * @var int
     */
    public const NETSHOP_PURCHASE = 11;

    /**
     * NETSHOP Chat Message
     * ネットショップ・チャット・メッセージ
     *
     * @var int
     */
    public const NETSHOP_CHAT_MESSAGE = 12;

    /**
     * Forms Recipient Connection
     * フォーム受信者接続
     *
     * @var int
     */
    public const FORM_RECIPIENT_CONNECTION = 13;
}
