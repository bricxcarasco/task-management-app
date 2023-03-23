<?php

/**
 * Basic Notification Settings
 */

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * App\Enums\BasicSettings
 *
 * @see https://github.com/BenSampo/laravel-enum
 */
final class BasicSettings extends Enum implements LocalizedEnum
{
    /**
     * Connection application
     * つながり申請の受信
     *
     * @var int
     */
    public const CONNECTION_APPLICATION = 4;

    /**
     * Connection group invitation
     * つながりグループ招待の受信
     *
     * @var int
     */
    public const CONNECTION_GROUP_INVITATION = 5;

    /**
     * NEO Invitation
     * NEO招待の受信
     *
     * @var int
     */
    public const NEO_INVITATION = 6;

    /**
     * Chat Message
     * チャットメッセージの受信
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
     * Change Email address
     * メールアドレスの変更
     *
     * @var int
     */
    public const CHANGE_EMAIL = 3;

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

    /**
     * Get selectable mail templates
     *
     * @return array
     */
    public static function getSelectableTemplateValues()
    {
        return self::processExclusion([
            self::CONNECTION_APPLICATION,
            self::CONNECTION_GROUP_INVITATION,
            self::NEO_INVITATION,
            self::CHAT_MESSAGE,
            self::ADD_DOCUMENT_SHARING,
            self::SCHEDULE_INVITATION,
            self::NETSHOP_PURCHASE,
            self::NETSHOP_CHAT_MESSAGE,
            self::FORM_RECIPIENT_CONNECTION,
        ]);
    }

    /**
     * Get excluded constants
     *
     * @return array
     */
    public static function getExcluded()
    {
        return [
            self::CHAT_MESSAGE,
        ];
    }

    /**
     * Process exclusion of specified enums
     *
     * @param array $array
     * @return array
     */
    public static function processExclusion($array)
    {
        $exclude = self::getExcluded();

        return array_filter($array, function ($key) use (&$exclude) {
            return !(in_array($key, $exclude));
        });
    }

    /**
     * Get all of the constants defined on the class.
     *
     * @return array
     */
    protected static function getConstants(): array
    {
        $initialValues = parent::getConstants();

        return self::processExclusion($initialValues);
    }
}
