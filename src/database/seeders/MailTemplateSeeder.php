<?php

namespace Database\Seeders;

use App\Enums\MailTemplates;
use App\Models\MailTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate Mail Templates
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('mail_templates')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Insert pre-defined attributes
        MailTemplate::insert([
            [
                'template_id' => MailTemplates::SIGNUP_EMAIL_VERIFICATION,
                'name' => "I't HERO・イッツヒーローへの会員登録をご希望頂きありがとうございます",
                'content' => <<< CONTENT
                    {{email}}様

                    以下のURLにアクセスして頂くと、会員情報登録画面が開きます。
                    {{verify_url}}


                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::REGISTRATION_VERIFIED,
                'name' => "I't HERO・イッツヒーローへの会員登録が完了しました",
                'content' => <<< CONTENT
                    {{rio_name}}様

                    {{login_url}}

                    以下メールアドレスと、登録したパスワードにてログインしてください。
                    {{email}}

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::EMAIL_RESET_VERIFICATION,
                'name' => "I't HERO・イッツヒーロー アカウントのメールアドレス変更リクエストがありました",
                'content' => <<< CONTENT
                    {{rio_name}}様

                    この操作があなたのリクエストによるものの場合、以下のURLにアクセスしてください。
                    メールアドレスの認証操作が行われ、登録メールアドレスが変更されます。
                    {{change_email_url}}


                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::CONNECTION_APPLICATION,
                'name' => "[I't HERO] {{sender_name}}よりつながり申請が届いています",
                'content' => <<< CONTENT
                    {{receiver_name}}様

                    {{sender_name}}よりつながり申請が届いています。
                    申請の承認は、I't HEROにログイン後
                    「つながり管理」の「申請リクエスト」よりおこなってください。

                    「I't HEROを開く」
                    {{bphero_url}}

                    お知らせメールの通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::CONNECTION_GROUP_INVITATION,
                'name' => "[I't HERO] {{group_name}}グループより招待が届いています",
                'content' => <<< CONTENT
                    {{rio_name}}様

                    {{group_name}}グループから招待が届いています。
                    参加の承認は、I't HEROにログイン後
                    「つながりグループ」の「招待リクエスト」よりおこなってください。

                    「I't HEROを開く」
                    {{bphero_url}}

                    お知らせメールの通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::NEO_INVITATION,
                'name' => "[I't HERO] {{neo_name}}より参加招待が届いています",
                'content' => <<< CONTENT
                    {{rio_name}}様

                    {{neo_name}}より参加招待が届いています。
                    参加の承認は、I't HEROにログイン後
                    「招待管理」よりおこなってください。

                    「I't HEROを開く」
                    {{bphero_url}}

                    メッセージの通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::CHAT_MESSAGE,
                'name' => "[I't HERO] {{name}}よりメッセージが届いています",
                'content' => <<< CONTENT
                    {{rio_name}}様

                    {{name}}より新しいメッセージが届いています。
                    メッセージのご確認及びご返信は、I't HEROにログイン後
                    「〇〇」機能をご利用ください。

                    「I't HEROを開く」
                    {{bphero_url}}

                    メッセージの通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::ADD_DOCUMENT_SHARING,
                'name' => "[I't HERO] {{rio_name}}があなたとドキュメントを共有しました",
                'content' => <<< CONTENT
                    {{rio_name}}があなたとドキュメントを共有しました。
                    共有されたファイルのご確認は、I't HEROにログイン後
                    「文書管理」機能をご利用ください。


                    「I't HEROを開く」
                    {{bphero_url}}

                    文書管理の通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::SCHEDULE_INVITATION,
                'name' => "[I't HERO] {{sender_name}}よりスケジュールの参加申請が届いています。",
                'content' => <<< CONTENT
                    {{receiver_name}}様

                    {{sender_name}}よりスケジュールの参加申請が届いています。
                    この申請を受け入れる場合は、I't HEROにログイン後
                    「スケジュール管理」機能にアクセスして承認をおこなってください。

                    「I't HEROを開く」
                    {{bphero_url}}

                    スケジュールの通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::CHANGE_PASSWORD,
                'name' => "I't HERO・イッツヒーローのパスワードが変更されました",
                'content' => <<< CONTENT
                    {{rio_name}}様

                    I't HEROアカウントのパスワードが変更されました。
                    {{email}}

                    「I't HEROを開く」
                    {{bphero_url}}

                    パスワード変更の通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::NETSHOP_PURCHASE,
                'name' => "I't HERO・ご購入ありがとうございます",
                'content' => <<< CONTENT
                    {{buyer_name}}様

                    I't HEROをご利用いただきありがとうございます。
                    ご購入の内容につき下記の通り、ご連絡させていただきます。

                    ■商品情報
                    商品名：<a href ="{{product_url}}">{{product}}</a>
                    価格（税/送料込み）:{{price}}
                    出品者：{{seller_name}}

                    以上

                    問い合わせへの返信及び以後のやり取りはI't HEROネットショップメニューの「メッセージ」より行うことができます。

                    https://app.hero.ne.jp/classifieds/messages


                    お知らせメールの通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::NETSHOP_CHAT_MESSAGE,
                'name' => "[I't HERO] ネットショップ　{{sender_name}}様よりメッセージが届いています",
                'content' => <<< CONTENT
                    {{receiver_name}}様

                    {{sender_name}}様よりメッセージが届いています。
                    問い合わせへの返信及び以後のやり取りはI't HEROネットショップメニューの「メッセージ」より行うことができます。

                    https://app.hero.ne.jp/classifieds/messages/{{message_id}}

                    お知らせメールの通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
            [
                'template_id' => MailTemplates::FORM_RECIPIENT_CONNECTION,
                'name' => "[I't HERO] {{issuer_name}}様より{{form_type}}が発行されています",
                'content' => <<< CONTENT
                    {{recipient_name}}様

                    {{issuer_name}}様より{{form_type}}が発行されています。

                    ■帳票情報
                    {{form_type}}No：{{form_no}}
                    件名：{{form_title}}
                    発行日：{{form_issue_date}}
                    有効期限：{{form_expiration_date}}

                    以上

                    お知らせメールの通知設定は、I't HERO内の「プライバシー設定」より変更できます。

                    ※本メールは送信専用アドレスから送信しています。このメールには返信できません。

                    --
                    ヒーロー（仲間）が集うクラウドコミュニティー
                    I't HERO・イッツ ヒーロー
                    https://app.hero.ne.jp/
                    CONTENT
            ],
        ]);
    }
}
