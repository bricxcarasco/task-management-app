<template>
  <div>
    <div class="container position-relative zindex-5 mb-md-3 home--height">
      <div class="row">
        <div class="table-responsive">
          <table class="table table table-bordered">
            <thead>
              <tr>
                <th>{{ $t('headers.current_plan') }}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>フリープラン</td>
                <td class="w-50">無料</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered text-dark">
            <thead>
              <tr>
                <th>{{ $t('headers.new_plan') }}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>ライトプラン</td>
                <td class="w-50">￥10,000/月</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead class="table-active">
              <tr class="table-dark">
                <th colspan="2">
                  {{ $t('headers.total_monthly_amount_after_option_change') }}
                </th>
              </tr>
            </thead>
            <tbody>
              <tr class="table-primary">
                <td>プラン基本料金（ライトプラン)</td>
                <td class="w-50">￥10,000/月</td>
              </tr>
              <tr class="table-primary">
                <td>{{ $t('labels.total_additional_options') }}</td>
                <td class="w-50">￥8,000/月</td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="table-primary">
                <td>{{ $t('labels.total_monthly') }}</td>
                <td class="w-50">￥18,000/月</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <div class="text-center">
          <div class="border border-danger border-4 mb-3 p-2">
            <h5>
              {{ $t('labels.notes_on_option_changes') }}
            </h5>
            <p class="card-text">
              オプションを追加した場合やアップグレードの場合は、申請日より当月末まで変更された内容を【無料】でご利用いただけます。<br /><br />
              オプションを減らした場合やダウングレードの場合は、申請日時点でダウングレードサイズに減らしてあることが前提となり、ダウングレードサイズに減るまでエラー表示がなされます。<br /><br />
              月末日にダウングレードサイズに減らされていない場合は【現行のプランが継続】されるものとし、ご利用料金は継続致します。<br />
            </p>
          </div>
          <button type="button" class="btn btn-primary">
            {{ $t('buttons.check_the_precautions') }}<br />
            {{ $t('buttons.confirm_changes') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ChangePlanConfirmation',
};
</script>
