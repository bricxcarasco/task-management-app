import { createApp } from 'vue';
import Axios from 'axios';
import i18n from '../../i18n';
import TestIndex from './index.vue';
import ChangePlanConfirmation from './change-plan/ConfirmationSection.vue';
import DocumentManagementConfirmation from './add-option/DocumentManagementConfirmation.vue';
import AddStaffConfirmation from './add-option/AddStaffConfirmation.vue';
import NetShopConfirmation from './add-option/NetShopConfirmation.vue';
import InvoiceConfirmation from './add-option/InvoiceConfirmation.vue';
import PurchaseOrderConfirmation from './add-option/PurchaseOrderConfirmation.vue';
import DeliveryNoteConfirmation from './add-option/DeliveryNoteConfirmation.vue';
import ReceiptConfirmation from './add-option/ReceiptConfirmation.vue';
import WorkflowConfirmation from './add-option/WorkflowConfirmation.vue';
import AddDocumentManagement from './add-option/AddDocumentManagement.vue';
import AddStaff from './add-option/AddStaff.vue';
import AddNetShop from './add-option/AddNetShop.vue';
import AddInvoice from './add-option/AddInvoice.vue';
import AddPurchaseOrder from './add-option/AddPurchaseOrder.vue';
import AddDeliveryNote from './add-option/AddDeliveryNote.vue';
import AddReceipt from './add-option/AddReceipt.vue';
import AddWorkflow from './add-option/AddWorkflow.vue';

Axios.defaults.baseURL = '/';

// Initialize app
const app = createApp({});

// App packages
app.use(i18n);

// App components
app.component('test-index', TestIndex);
app.component('change-plan-confirmation', ChangePlanConfirmation);
app.component('add-document-management', AddDocumentManagement);
app.component(
  'document-management-confirmation',
  DocumentManagementConfirmation
);
app.component('add-staff', AddStaff);
app.component('add-staff-confirmation', AddStaffConfirmation);
app.component('add-net-shop', AddNetShop);
app.component('net-shop-confirmation', NetShopConfirmation);
app.component('add-invoice', AddInvoice);
app.component('invoice-confirmation', InvoiceConfirmation);
app.component('add-purchase-order', AddPurchaseOrder);
app.component('purchase-order-confirmation', PurchaseOrderConfirmation);
app.component('add-delivery-note', AddDeliveryNote);
app.component('delivery-note-confirmation', DeliveryNoteConfirmation);
app.component('add-receipt', AddReceipt);
app.component('receipt-confirmation', ReceiptConfirmation);
app.component('add-workflow', AddWorkflow);
app.component('workflow-confirmation', WorkflowConfirmation);

app.mount('#app');
