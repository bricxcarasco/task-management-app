<!doctype html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" media="screen" href="{{ asset('css/style.min.css') }}">
    <title>イッツヒーロー</title>
    <link rel="shortcut icon" href="" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP&display=swap" rel="stylesheet">
</head>

<body>
    <header class="show">
        <div class="header-inner">
            <div class="header-logo">
                <img src="{{ asset('images/landing-page/logo.svg') }}" alt="">
            </div>
            <div class="header-item">
                <div class="header-signup-btn">
                    <span class="free">無料</span>
                    <span class="to-signup">{{ __('New Member Registration') }}</span>
                    <img src="{{ asset('images/landing-page/signup-icon.png') }}" alt="">
                </div>
                <div class="header-document-btn">
                    <span class="to-form">資料請求</span>
                    <img src="{{ asset('images/landing-page/arrow.png') }}" alt="">
                </div>
            </div>
        </div>
    </header>
    <div class="fv-wrapper">
        <div class="fv">
            <div class="bg">
                <img src="{{ asset('images/landing-page/fv03.jpeg') }}" alt="bg">
            </div>
            <div class="logo">
                <img src="{{ asset('images/landing-page/logo.svg') }}" alt="logo">
            </div>
        </div>
        <div class="sign-up to-signup">
            <p>新規会員登録はこちら</p>
        </div>
        <div class="btn to-form">
            <p>無料トライアル・資料ダウンロードしてみる</p>
        </div>
    </div>
    <div class="main-wrapper">
        <section class="introduction">
            <div class="section-title">
                <h3>こんな<strong>お悩み</strong>ありませんか？</h3>
            </div>
            <div class="section-content">
                <div class="content-inner1">
                    <ul>
                        <li>電子帳簿保存法やインボイス制度に対応できていない</li>
                        <li>会社でグループウェアの仕組みが無いのですべて個人対応</li>
                        <li>クレジット決済ができず集金や振込のみしかない</li>
                        <li>月に10回も使わないのに電子契約だけで毎月1万円以上払っている</li>
                        <li>議事録・マニュアル・ルールは誰かに聞かないとわからない</li>
                        <li>常連のお客様や取引先には自動的にネットで注文してほしい</li>
                        <li>アプリがバラバラで業務が煩雑になっている</li>
                    </ul>
                    <div class="signature-phrase">
                        <h2><strong>イッツヒーロー</strong>によって上記のようなお悩み、<br><strong class="st">すべて解決できます！</strong></h2>
                    </div>
                </div>
                <div class="content-inner2">
                    <h2><strong>イッツヒーロー</strong>なら、</h2>
                    <div class="content-inner2-layout">
                        <div class="el">
                            <div class="el-layout">
                                <img src="{{ asset('images/landing-page/introduction1.jpg') }}" alt="">
                                <p><strong>電子帳簿保存法</strong>や<strong>インボイス制度に対応！</strong></p>
                            </div>
                        </div>
                        <div class="el">
                            <div class="el-layout">
                                <img src="{{ asset('images/landing-page/introduction2.jpg') }}" alt="">
                                <p>社内の人とも社外の人ともつながれて<br><strong>情報共有が可能</strong></p>
                            </div>
                        </div>
                        <div class="el">
                            <div class="el-layout">
                                <img src="{{ asset('images/landing-page/introduction3.jpg') }}" alt="">
                                <p>仕事で使うアプリが<br><strong>一気通貫されているから便利！</strong></p>
                            </div>
                        </div>
                        <div class="el">
                            <div class="el-layout">
                                <img src="{{ asset('images/landing-page/introduction4.jpg') }}" alt="">
                                <p><strong>初期0円・月額無料</strong>のECサイト</p>
                            </div>
                        </div>
                        <div class="el">
                            <div class="el-layout">
                                <img src="{{ asset('images/landing-page/introduction5.jpg') }}" alt="">
                                <p><strong>初期0円・月額無料</strong>の電子契約</p>
                            </div>
                        </div>
                        <div class="el">
                            <div class="el-layout">
                                <img src="{{ asset('images/landing-page/introduction6.jpg') }}" alt="">
                                <p>毎月の顧問料や会話で決まった作業代金も<br><strong>クレジット決済</strong></p>
                            </div>
                        </div>
                    </div>
                    <p>によって、<br>すべて解決できます！</p>
                    <div class="btn to-form">
                        <p>無料トライアル・資料ダウンロードしてみる</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="feature">
            <div class="section-title">
                <h3><strong>イッツヒーロー</strong>の特長</h3>
                <div class="feature-content">
                    <div class="feature-content-inner">
                        <div class="feature-layout">
                            <div class="feature-number"><img src="{{ asset('images/landing-page/number1.png') }}" alt=""></div>
                            <div class="feature-img"><img src="{{ asset('images/landing-page/feature1.png') }}" alt=""></div>
                            <div class="feature-sentence"><p>SNS×ネットショップ×電子契約×グループウェアが統合している<br><strong>日本初のクラウドコミュニティ</strong></p></div>
                        </div>
                    </div>
                    <div class="feature-content-inner">
                        <div class="feature-layout">
                            <div class="feature-number"><img src="{{ asset('images/landing-page/number2.png') }}" alt=""></div>
                            <div class="feature-img"><img src="{{ asset('images/landing-page/feature2.png') }}" alt=""></div>
                            <div class="feature-sentence"><p>様々な法改正に対応した社内も取引先にも使える<br><strong>ビジネスアプリ</strong></p></div>
                        </div>
                    </div>
                    <div class="feature-content-inner">
                        <div class="feature-layout">
                            <div class="feature-number"><img src="{{ asset('images/landing-page/number3.png') }}" alt=""></div>
                            <div class="feature-img"><img src="{{ asset('images/landing-page/feature3.png') }}" alt=""></div>
                            <div class="feature-sentence"><p>ECサイトや電子契約の月額費用が<strong>無料</strong></p></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="tb">
            <div class="section-title">
                <h3>他社との比較</h3>
            </div>
            <div class="tb-content">
                <table>
                    <tbody>
                        <tr>
                            <th class="first">サービス名</th>
                            <td class="itshero first">イッツヒーロー</td>
                            <td class="company first">C社</td>
                            <td class="company first">C社</td>
                            <td class="company first">M社</td>
                            <td class="company first">N社</td>
                        </tr>
                        <tr>
                            <th>属性</th>
                            <td class="itshero_txt itshero_txt_a">SNS×EC×電子契約×グループウェア</td>
                            <td>SNS</td>
                            <td>電子契約</td>
                            <td>EC</td>
                            <td>グループウェア</td>
                        </tr>
                        <tr>
                            <th>初期費用</th>
                            <td class="itshero_txt">-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>¥10,000</td>
                            <td>¥50,000</td>
                        </tr>
                        <tr>
                            <th>月額費用</th>
                            <td class="itshero_txt">¥10,000/月〜</td>
                            <td>１名あたり<br>500円/月〜960円/月</td>
                            <td>¥10,000/月〜</td>
                            <td>¥10,000/月〜</td>
                            <td>１名あたり<br>360円/月</td>
                        </tr>
                        <tr>
                            <th>文書管理<br>(ファイル共有)</th>
                            <td class="itshero_txt">〇</td>
                            <td>〇</td>
                            <td>〇</td>
                            <td>×</td>
                            <td>〇</td>
                        </tr>
                        <tr>
                            <th>電子帳簿保存法</th>
                            <td class="itshero_txt">〇</td>
                            <td>×</td>
                            <td>×</td>
                            <td>〇</td>
                            <td>〇</td>
                        </tr>
                        <tr>
                            <th>インボイス制度</th>
                            <td class="itshero_txt">〇</td>
                            <td>×</td>
                            <td>×</td>
                            <td>〇</td>
                            <td>〇</td>
                        </tr>
                        <tr>
                            <th>ワークフロー</th>
                            <td class="itshero_txt">〇</td>
                            <td>×</td>
                            <td>〇</td>
                            <td>×</td>
                            <td>〇</td>
                        </tr>
                        <tr>
                            <th>ナレッジ共有</th>
                            <td class="itshero_txt">〇</td>
                            <td>〇</td>
                            <td>×</td>
                            <td>×</td>
                            <td>〇</td>
                        </tr>
                        <tr>
                            <th>ネットショップ</th>
                            <td class="itshero_txt">〇</td>
                            <td>×</td>
                            <td>×</td>
                            <td>〇</td>
                            <td>×</td>
                        </tr>
                        <tr>
                            <th>外部との電子契約</th>
                            <td class="itshero_txt">〇</td>
                            <td>×</td>
                            <td>〇</td>
                            <td>×</td>
                            <td>×</td>
                        </tr>
                        <tr>
                            <th>外部との<br>スケジュール共有</th>
                            <td class="itshero_txt">〇</td>
                            <td>〇</td>
                            <td>×</td>
                            <td>×</td>
                            <td>×</td>
                        </tr>
                        <tr>
                            <th>他組織への切替</th>
                            <td class="itshero_txt">〇</td>
                            <td>△他組織へ移行のみ</td>
                            <td>×</td>
                            <td>〇</td>
                            <td>×</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="before-after">
            <div class="section-title">
                <h3>イッツヒーローを利用するとこうなります</h3>
            </div>
            <div class="before-after-content">
                <table>
                    <tbody>
                        <tr>
                            <th class="first"></th>
                            <td class="first">今まで</td>
                            <td class="first-after">利用後</td>
                        </tr>
                        <tr>
                            <th>連絡方法</th>
                            <td>電話・メール・FAX</td>
                            <td class="after">基本はチャット。<br>プッシュ通知でお知らせ</td>
                        </tr>
                        <tr>
                            <th>スケジュール</th>
                            <td>出席状況の把握困難</td>
                            <td class="after">スマホやPCで出席状況の把握可能</td>
                        </tr>
                        <tr>
                            <th>タスク</th>
                            <td>社外とは進捗管理ができない</td>
                            <td class="after">社内外問わずに進捗管理が<br>プロジェクトごとに可能</td>
                        </tr>
                        <tr>
                            <th>資料の共有</th>
                            <td>基本的に個人管理</td>
                            <td class="after">必要な時に必要なだけデータ共有</td>
                        </tr>
                        <tr>
                            <th>回覧（決裁）</th>
                            <td>印鑑が必要。<br>決裁者不在時だと数日止まる</td>
                            <td class="after">スマホで決裁。ハンコ不要</td>
                        </tr>
                        <tr>
                            <th>ナレッジ</th>
                            <td>基本的にアナログ個人管理。<br>技術の伝承（デジタル化）が課題</td>
                            <td class="after">基本的にデジタル文書で検索可能。<br>画像や動画でもノウハウの伝承</td>
                        </tr>
                        <tr>
                            <th>新規顧客紹介</th>
                            <td>基本的に個人管理・個人発信</td>
                            <td class="after">組織外の人を入れて紹介グループを作れる（新規会員獲得）</td>
                        </tr>
                        <tr>
                            <th>売上</th>
                            <td>基本的に個人や企業努力</td>
                            <td class="after">団体や組織として仲間の商品を<br>みんなで販売してあげられる</td>
                        </tr>
                        <tr>
                            <th>決済</th>
                            <td>集金・振込</td>
                            <td class="after">役務やチケットもクレジットカードで<br>少額でも高額でも電子取引</td>
                        </tr>
                        <tr>
                            <th>法改正<br>（電子帳簿保存法・インボイス制度）</th>
                            <td>基本的に個別に各々対応</td>
                            <td class="after">両方まとめて対応済み</td>
                        </tr>
                        <tr>
                            <th>帳票作成</th>
                            <td>PCで作業。<br>見積書・請求書作成は連携しない</td>
                            <td class="after">スマホだけで作成可能。<br>見積書データから連携可能</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
        <section class="achievement">
            <div class="section-title">
                <h3>導入実績</h3>
            </div>
            <div class="achievement-content">
                <div class="achievement-layout">
                    <div class="achievement-content-title">
                        <p>製造業</p>
                    </div>
                    <div class="achievement-content-inner">
                        <div class="achievement-bg">
                            <img src="{{ asset('images/landing-page/achievement1.jpg') }}" alt="">
                        </div>
                        <div class="achievement-sentence">
                            <div class="achievement-task">
                                <div class="hd">
                                    <p>当初の課題</p>
                                </div>
                                <ul class="ct">
                                    <li>・社員や取引先とSNSのつながりがない</li>
                                    <li>・インボイス対応ができていない</li>
                                    <li>・取引先と契約書を交わしていない</li>
                                </ul>
                            </div>
                            <div class="achievement-measure">
                                <div class="hd">
                                    <p>当社が行った施策</p>
                                </div>
                                <ul class="ct">
                                    <li>・仕事専用のSNSを社員も取引先もつながりをもつ</li>
                                    <li>・請求書の発行をイッツヒーローで作成</li>
                                    <li>・取引先との契約は電子契約で即対応</li>
                                </ul>
                            </div>
                            <div class="achievement-result">
                                <div class="hd">
                                    <p>イッツヒーローで解決したこと</p>
                                </div>
                                <ul class="ct">
                                    <li>・仕事専用のSNSで連絡がスムーズ</li>
                                    <li>・インボイス対応・電子帳簿保存法にも対応</li>
                                    <li>・取引先との契約がスムーズで印紙代のコスト削減</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="achievement-layout">
                    <div class="achievement-content-title">
                        <p>病院・クリニック</p>
                    </div>
                    <div class="achievement-content-inner">
                        <div class="achievement-bg">
                            <img src="{{ asset('images/landing-page/achievement2.jpg') }}" alt="">
                        </div>
                        <div class="achievement-sentence">
                            <div class="achievement-task">
                                <div class="hd">
                                    <p>当初の課題</p>
                                </div>
                                <ul class="ct">
                                    <li>・広告の効果が得られていない</li>
                                    <li>・扱うツールが多くてスタッフが定着しない</li>
                                    <li>・自費診療や店販・オンライン通販が売れない</li>
                                </ul>
                            </div>
                            <div class="achievement-measure">
                                <div class="hd">
                                    <p>当社が行った施策</p>
                                </div>
                                <ul class="ct">
                                    <li>・患者さんとつながりをもち接触頻度UP</li>
                                    <li>・社内外のツールを一本化でき効率UP</li>
                                    <li>・チャットができるネットショップ機能の利用</li>
                                </ul>
                            </div>
                            <div class="achievement-result">
                                <div class="hd">
                                    <p>イッツヒーローで解決したこと</p>
                                </div>
                                <ul class="ct">
                                    <li>・院内も患者さんともSNSで連絡がスムーズ</li>
                                    <li>・組織として業務効率が向上。退職率も低下</li>
                                    <li>・患者さんと連絡とりながら物販が可能</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="achievement-layout">
                    <div class="achievement-content-title">
                        <p>協同組合</p>
                    </div>
                    <div class="achievement-content-inner">
                        <div class="achievement-bg">
                            <img src="{{ asset('images/landing-page/achievement3.jpg') }}" alt="">
                        </div>
                        <div class="achievement-sentence">
                            <div class="achievement-task">
                                <div class="hd">
                                    <p>当初の課題</p>
                                </div>
                                <ul class="ct">
                                    <li>・会員との連絡がFAXかメールで大変</li>
                                    <li>・会議の議事録やスケジュールの情報共有ができていない</li>
                                    <li>・会員を増やして会員の売上を上げたいが何もできていない</li>
                                </ul>
                            </div>
                            <div class="achievement-measure">
                                <div class="hd">
                                    <p>当社が行った施策</p>
                                </div>
                                <ul class="ct">
                                    <li>・組合専用のグループウェアを導入</li>
                                    <li>・文書・マニュアル・スケジュールを共有</li>
                                    <li>・組合としてネットショップを利用</li>
                                </ul>
                            </div>
                            <div class="achievement-result">
                                <div class="hd">
                                    <p>イッツヒーローで解決したこと</p>
                                </div>
                                <ul class="ct">
                                    <li>・会員との連絡がSNSでスムーズになった</li>
                                    <li>・組合に係る資料が一元管理され便利になった</li>
                                    <li>・会員の代わりに売ってあげることができ満足度UP</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="price">
            <div class="section-title">
                <h3>料金</h3>
            </div>
            <div class="section-inner">
                <div class="price-content">
                    <div class="price-layout">
                        <div class="pt">
                            <p>ECサイト月額</p>
                        </div>
                        <div class="pc">
                            <p><strong class="zero">0</strong>円</p>
                        </div>
                    </div>
                    <div class="price-layout">
                        <div class="pt">
                            <p>電子契約月額</p>
                        </div>
                        <div class="pc">
                            <p><strong class="zero">0</strong>円</p>
                        </div>
                    </div>
                    <div class="price-layout">
                        <div class="pt">
                            <p>帳票発行月額</p>
                        </div>
                        <div class="pc">
                            <p><strong class="zero">0</strong>円</p>
                        </div>
                    </div>
                    <div class="price-layout">
                        <div class="pt">
                            <p>初期費用</p>
                        </div>
                        <div class="pc">
                            <p><strong class="zero">0</strong>円</p>
                        </div>
                    </div>
                    <div class="price-layout">
                        <div class="pt">
                            <p>個人：月額</p>
                        </div>
                        <div class="pc">
                            <p><strong>0～5,000</strong>円</p>
                        </div>
                    </div>
                    <div class="price-layout">
                        <div class="pt">
                            <p>組織：月額</p>
                        </div>
                        <div class="pc">
                            <p><strong>0～30,000</strong>円</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Email Sign Up Function -->
        <section class="free-signup">
            <div class="section-title">
                <h3>{{ __('New Member Registration') }}</h3>
            </div>
            <div class="signup-form">
                <form method="POST" action="{{ route('registration.email.post') }}">
                    @csrf
                    <input type="hidden" name="rd_code" value="{{ $affiliateCode['moshimo'] ?? null }}">
                    <input type="hidden" name="a8" value="{{ $affiliateCode['a8'] ?? null }}">
                    <dl class="clearfix">
                        <dt>
                            <span class="required">{{ __('required') }}</span>{{ __('Email Address') }}
                        </dt>
                        <dd>
                            <input id="email" type="text" class="txt-input form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" placeholder="メールアドレスを入力してください" @error('email') autofocus @enderror>
                            @error('email')
                                <div class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </dd>
                    </dl>
                    <div class="btn">
                        <input type="submit" class="btn_next" value="{{ __('Send') }}">
                        <p>{{ __('Send') }}</p>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <footer>
        <p>Copyright © ビジネスSNSのI'tHERO（イッツヒーロー）All Rights Reserved.</p>
    </footer>
    <div class="to-page-top fix-btn">
        <span>to top</span>
    </div>
    <div class="trigger"></div>
    <div class="trigger2"></div>

    <!-- Set the needed Js files/functions -->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/index.js') }}"></script>
</body>
</html>
