<template>
  <div>
    <div class="container position-relative zindex-5 mb-md-3 home--height">
      <div class="row">
        <div class="table-responsive">
          <table class="table table table-bordered">
            <thead>
              <tr>
                <th class="text-center">
                  {{ $t('headers.current_contract_details') }}
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody class="text-dark">
              <tr>
                <td class="text-center">{{ $t('labels.no_contract') }}</td>
                <td class="w-50">￥0/月</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center">
                  {{ $t('headers.details_of_the_contract_after_the_change') }}
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody class="text-dark">
              <tr>
                <td class="text-center">{{ $t('labels.contract') }}</td>
                <td class="w-50">￥5,000/月</td>
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
              {{ $t('paragraphs.upgrade_paragraph_notes_on_option_changes')
              }}<br /><br />
              {{ $t('paragraphs.downgrade_paragraph_notes_on_option_changes')
              }}<br /><br />
              {{
                $t(
                  'paragraphs.retain_subscription_paragraph_notes_on_option_changes'
                )
              }}<br />
            </p>
          </div>
          <button type="button" class="btn btn-primary">
            {{ $t('buttons.apply_with_this_content') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'InvoiceConfirmation',
};
</script>
