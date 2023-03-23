import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import FormList from './list/Index.vue';
import FormCsvList from './csv/Index.vue';
import BasicSettings from './basic-settings/Index.vue';
import QuotationCreate from './quotation/Create.vue';
import QuotationDuplicate from './quotation/Duplicate.vue';
import QuotationEdit from './quotation/Edit.vue';
import ReceiptCreate from './receipt/Create.vue';
import ReceiptEdit from './receipt/Edit.vue';
import DeleteHistory from './list/DeleteHistory.vue';
import UpdateHistory from './list/UpdateHistory.vue';
import InvoiceCreate from './invoice/Create.vue';
import DeliverySlipCreate from './delivery-slip/Create.vue';
import DeliverySlipEdit from './delivery-slip/Edit.vue';
import DeliverySlipDuplicate from './delivery-slip/Duplicate.vue';
import InvoiceDuplicate from './invoice/Duplicate.vue';
import InvoiceEdit from './invoice/Edit.vue';
import DocumentListModal from './modals/DocumentListModal.vue';
import PurchaseOrderCreate from './purchase-order/Create.vue';
import PurchaseOrderEdit from './purchase-order/Edit.vue';
import ReceiptDuplicate from './receipt/Duplicate.vue';
import PurchaseDuplicate from './purchase-order/Duplicate.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('form-list', FormList);
app.component('form-csv-list', FormCsvList);
app.component('basic-settings', BasicSettings);
app.component('quotation-create', QuotationCreate);
app.component('quotation-duplicate', QuotationDuplicate);
app.component('quotation-edit', QuotationEdit);
app.component('receipt-create', ReceiptCreate);
app.component('receipt-edit', ReceiptEdit);
app.component('receipt-duplicate', ReceiptDuplicate);
app.component('delete-history', DeleteHistory);
app.component('update-history', UpdateHistory);
app.component('invoice-create', InvoiceCreate);
app.component('delivery-slip-create', DeliverySlipCreate);
app.component('delivery-slip-edit', DeliverySlipEdit);
app.component('delivery-slip-duplicate', DeliverySlipDuplicate);
app.component('invoice-duplicate', InvoiceDuplicate);
app.component('invoice-edit', InvoiceEdit);
app.component('document-list-modal', DocumentListModal);
app.component('purchase-order-create', PurchaseOrderCreate);
app.component('purchase-order-edit', PurchaseOrderEdit);
app.component('purchase-order-duplicate', PurchaseDuplicate);

// Directive to allow number inputs only
app.directive('numbers-only', (el) => {
  // Handle pasted values
  /* eslint-disable no-param-reassign */
  el.value = el.value.replace(/\D/g, '');

  // On input event listener
  el.addEventListener('input', () => {
    const regex = /^[0-9]*$/;
    if (!regex.test(el.value)) {
      el.value = el.value.slice(0, -1);
    }
  });
});

app.mount('#app');
