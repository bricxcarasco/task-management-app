<template>
  <div>
    <div class="container position-relative zindex-5 mb-md-3 home--height">
      <div class="row">
        <div style="display: flex; justify-content: flex-end">
          <a href="#">
            <i style="font-size: large" class="ai-alert-circle text-danger"></i>
            {{ $t('links.about_neo_document_management_options') }}
          </a>
        </div>

        <div class="table-responsive">
          <table class="table table table-bordered">
            <thead>
              <tr>
                <th class="text-center">
                  {{
                    $t('headers.total_gb_of_current_additional_subscriptions')
                  }}
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody class="text-dark">
              <tr>
                <td class="text-center">50GB</td>
                <td class="w-50">￥5,000/月</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th class="text-center">
                  {{
                    $t('headers.total_gb_of_additional_contracts_after_change')
                  }}
                </th>
                <th></th>
              </tr>
            </thead>
            <tbody class="text-dark">
              <tr>
                <td>
                  <select class="form-select w-auto mx-auto">
                    <option value="10GB" selected>10GB</option>
                    <option value="20GB">20GB</option>
                    <option value="30GB">30GB</option>
                    <option value="40GB">40GB</option>
                    <option value="50GB">50GB</option>
                    <option value="60GB">60GB</option>
                    <option value="70GB">70GB</option>
                    <option value="80GB">80GB</option>
                    <option value="90GB">90GB</option>
                    <option value="100GB">100GB</option>
                  </select>
                </td>
                <td class="w-50">￥2,000/月</td>
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
            <tbody class="table-primary">
              <tr>
                <td>プラン基本料金（ライトプラン)</td>
                <td class="w-50">￥10,000/月</td>
              </tr>
              <tr>
                <td>{{ $t('labels.total_additional_options') }}</td>
                <td class="w-50">￥8,000/月</td>
              </tr>
            </tbody>
            <tfoot class="table-primary">
              <tr>
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
            {{ $t('buttons.next') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddDocumentManagement',
};
</script>
