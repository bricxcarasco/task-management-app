<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetEmailController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\Chat\NeoMessageController;
use App\Http\Controllers\Chat\RoomController;
use App\Http\Controllers\Chat\TestController;
use App\Http\Controllers\Classified\ContactController;
use App\Http\Controllers\Classified\ContactMessageController;
use App\Http\Controllers\Classified\FavoriteController;
use App\Http\Controllers\Classified\PaymentController;
use App\Http\Controllers\Classified\SaleController;
use App\Http\Controllers\Classified\SettingController;
use App\Http\Controllers\Connection\ConnectionSearchController;
use App\Http\Controllers\Connection\ConnectionController;
use App\Http\Controllers\Connection\GroupController;
use App\Http\Controllers\Connection\GroupUserController;
use App\Http\Controllers\Document\DocumentController;
use App\Http\Controllers\Form\BasicSettingController as FormBasicSettingContoller;
use App\Http\Controllers\Form\FormController;
use App\Http\Controllers\Form\InvoiceController;
use App\Http\Controllers\Form\PurchaseOrderController;
use App\Http\Controllers\Form\QuotationController;
use App\Http\Controllers\Form\DeliverySlipController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Neo\PrivacyController as NeoPrivacyController;
use App\Http\Controllers\Neo\GroupController as NeoGroupController;
use App\Http\Controllers\RioController;
use App\Http\Controllers\Rio\AccountInformationController;
use App\Http\Controllers\Neo\RegistrationController;
use App\Http\Controllers\Neo\ProfileController;
use App\Http\Controllers\Neo\AdministratorController;
use App\Http\Controllers\NeoController;
use App\Http\Controllers\Notifications\BasicSettingController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Controllers\Rio\HeroIntroduceController;
use App\Http\Controllers\Rio\PrivacyController;
use App\Http\Controllers\Schedule\ScheduleController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\Common\StorageController;
use App\Http\Controllers\ElectronicContract\ElectronicContractController;
use App\Http\Controllers\Knowledge\KnowledgeController;
use App\Http\Controllers\Knowledge\ArticleController;
use App\Http\Controllers\Workflow\WorkflowController;
use App\Http\Controllers\Plan\PlanController;
use App\Http\Controllers\Form\ReceiptController;
use App\Http\Controllers\Plan\SubscriptionController;
use App\Http\Controllers\PaidPlan\PaidPlanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// Auth::routes();

Route::prefix('/')->group(function () {
    Route::middleware('guest')->prefix('login')->name('login.')->group(function () {
        Route::get('/', [LoginController::class, 'showLoginForm'])->name('get');
        Route::post('/', [LoginController::class, 'login'])->name('post');
    });

    // Landing Page route
    Route::get('/lp', [SignUpController::class, 'landingPage'])->name('landing-page');

    Route::prefix('registration')->name('registration.')->group(function () {
        Route::get('google', [RegisterController::class, 'google'])->name('google');

        Route::prefix('email')->group(function () {
            Route::get('/{referralCode?}', [SignUpController::class, 'email'])->name('email');
            Route::post('complete/{referralCode?}', [SignUpController::class, 'emailPost'])->name('email.post');
        });

        // SMS Authentication-related routes
        Route::prefix('sms-authentication')->name('sms.')->group(function () {
            Route::post('/generate', [RegisterController::class, 'smsAuthenticateSendOtp'])->name('send-otp');
            Route::get('/', [RegisterController::class, 'smsAuthenticateForm'])->name('index');
        });

        Route::get('verify/{email}/{token}', [RegisterController::class, 'verifyUrl'])->name('email.verify');
        Route::post('confirm', [RegisterController::class, 'confirm'])->name('confirm');
        Route::post('complete', [RegisterController::class, 'complete'])->name('complete');
    });

    // Reset Email Verification Routes
    Route::prefix('email')->name('email.')->group(function () {
        Route::prefix('reset')->name('reset.')->group(function () {
            Route::get('verify/{verification?}/{user?}/{token?}', [ResetEmailController::class, 'verify'])->name('verify');
            Route::get('complete', [ResetEmailController::class, 'complete'])->name('complete');
        });
    });

    Route::middleware(['auth', 'service'])->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::get('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('basic-settings', [BasicSettingController::class, 'index'])->name('basic-settings');

        // Notification routes
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/unread', [NotificationController::class, 'unread'])->name('unread');
        });

        Route::prefix('rio')->name('rio.')->group(function () {
            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('introduction/{id?}', [RioController::class, 'introduction'])->name('introduction');
                Route::get('information/{id?}', [RioController::class, 'information'])->name('information');
                Route::get('basic_settings', [RioController::class, 'basicSettings'])->name('basic_settings');
                Route::get('skills', [RioController::class, 'skills'])->name('skills');
                Route::get('privacy', [RioController::class, 'privacy'])->name('privacy');
                Route::get('profile/{id}', [RioController::class, 'otherProfile'])->name('other_profile');
                Route::get('/rio_invitation', [RioController::class, 'invitationList'])->name('invitation-list');
                Route::prefix('edit')->group(function () {
                    Route::get('/', [RioController::class, 'edit'])->name('edit');
                });
            });

            Route::prefix('information')->name('information.')->group(function () {
                Route::get('edit', [AccountInformationController::class, 'edit'])->name('edit');
            });

            Route::prefix('privacy')->name('privacy.')->group(function () {
                Route::get('/', [PrivacyController::class, 'edit'])->name('edit');
                Route::post('/update', [PrivacyController::class, 'update'])->name('update');
            });

            Route::prefix('introduce_hero')->name('introduce_hero.')->group(function () {
                Route::get('/', [HeroIntroduceController::class, 'index'])->name('introduce');
                Route::post('/update', [HeroIntroduceController::class, 'update'])->name('update');
            });
        });

        Route::prefix('neo')->name('neo.')->group(function () {
            Route::prefix('registration')->name('registration.')->group(function () {
                Route::get('/{group?}', [RegistrationController::class, 'index'])->name('index');
                Route::post('/confirm/{group?}', [RegistrationController::class, 'confirm'])->name('confirm');
                Route::post('/complete/{group?}', [RegistrationController::class, 'complete'])->name('complete');
            });

            Route::prefix('profile')->name('profile.')->group(function () {
                Route::get('/', [NeoController::class, 'profile'])->name('profile');
                Route::get('/edit/{neo}', [NeoController::class, 'edit'])->name('edit');
                Route::get('/introduction/{neo}', [ProfileController::class, 'introduction'])->name('introduction');
                Route::get('information/{neo}', [ProfileController::class, 'information'])->name('information');
                Route::post('/exit/{affiliate}', [NeoController::class, 'exitNeo'])->name('exit-neo');
                Route::get('/application_management/participants/{neo}', [ProfileController::class, 'participants'])->name('participants');
                Route::get('/participants/{neo}', [ProfileController::class, 'participantsList'])->name('participants-list');
                Route::get('/groups/{neo}', [ProfileController::class, 'groupsList'])->name('groups-list');
                Route::get('/application_management/invitation/{neo}', [ProfileController::class, 'invitation'])->name('invitation');
                Route::get('/neo_participation_invitation/{neo}', [ProfileController::class, 'participationInvitation'])->name('participation-invitation');


                Route::prefix('group')->name('group.')->group(function () {
                    Route::post('/create/{neo}', [NeoGroupController::class, 'create'])->name('create');
                    Route::put('/update/{group}', [NeoGroupController::class, 'update'])->name('edit');
                    Route::delete('/delete/{group}', [NeoGroupController::class, 'delete'])->name('delete');
                    Route::get('/members-list/{group}', [NeoGroupController::class, 'membersList'])->name('members-list');
                    Route::post('/join/{id}', [NeoGroupController::class, 'join'])->name('join');
                    Route::delete('/leave/{id}', [NeoGroupController::class, 'leave'])->name('leave');
                    Route::delete('/remove-member/{id}', [NeoGroupController::class, 'removeMember'])->name('remove-member');
                    Route::get('/participant-user-list/{group}', [NeoGroupController::class, 'participatingUserList'])->name('participant-user-list');
                    Route::post('/add-members/{id}', [NeoGroupController::class, 'addGroupMember'])->name('add-members');
                });
            });

            Route::prefix('privacy')->name('privacy.')->group(function () {
                Route::get('/{neo}', [NeoPrivacyController::class, 'edit'])->name('edit');
                Route::post('/update/{id}', [NeoPrivacyController::class, 'update'])->name('update');
            });

            Route::middleware('neo_owner')->prefix('administrator')->name('administrator.')->group(function () {
                Route::get('owner/{neo}', [AdministratorController::class, 'owner'])->name('owner');
                Route::get('{neo}', [AdministratorController::class, 'index'])->name('index');

                Route::prefix('update')->name('update.')->group(function () {
                    Route::post('owner/{neo}', [AdministratorController::class, 'setOwner'])->name('owner');
                });
            });
        });

        Route::prefix('connection')->name('connection.')->group(function () {
            Route::get('/', [ConnectionController::class, 'connectionList'])->name('connection-list');
            Route::get('/applications', [ConnectionController::class, 'applicationList'])->name('application-list');
            Route::get('search/', [ConnectionController::class, 'search'])->name('search');

            Route::prefix('groups')->name('groups.')->group(function () {
                Route::get('/', [GroupController::class, 'index'])->name('index');
                Route::get('/invitations', [GroupController::class, 'invitations'])->name('invitations');
                Route::get('/add', [GroupController::class, 'create'])->name('create');
                Route::post('/store', [GroupController::class, 'store'])->name('store');
                Route::put('/update/{group}', [GroupController::class, 'update'])->name('update');
                Route::delete('/delete/{group}', [GroupController::class, 'delete'])->name('delete');
                Route::get('/member-list/{group}', [GroupController::class, 'membersList'])->name('member-list');
                Route::get('/invite-members/{group}', [GroupController::class, 'inviteMembers'])->name('invite-members');
            });

            Route::prefix('group-user')->name('group-user.')->group(function () {
                Route::delete('/delete/{user}', [GroupUserController::class, 'delete'])->name('delete');
                Route::put('/accept-invitation/{user}', [GroupUserController::class, 'acceptInvitation'])->name('accept-invitation');
                Route::delete('/decline-invitation/{user}', [GroupUserController::class, 'declineInvitation'])->name('decline-invitation');
            });

            Route::prefix('search')->name('search.')->group(function () {
                Route::get('/', [ConnectionSearchController::class, 'search'])->name('search');
                Route::get('/results', [ConnectionSearchController::class, 'result'])->name('result');
                Route::get('/results/store-search-filters-to-session', [ConnectionSearchController::class, 'storeSearchFiltersToSession'])->name('store-search-filters-to-session');
            });
        });

        // Document Management Routes
        Route::prefix('document')->name('document.')->middleware('plan_access')->group(function () {
            //Remove temporary routes after testing
            Route::get('/create', [DocumentController::class, 'createFile'])->name('create-file');
            Route::get('/update', [DocumentController::class, 'updateFile'])->name('update-file');
            Route::get('/delete', [DocumentController::class, 'deleteFile'])->name('delete-file');

            Route::get('/', [DocumentController::class, 'documentList'])->name('default-list');
            Route::get('/folders/{directory_id}', [DocumentController::class, 'folderFileList'])->name('folder-file-list');
            Route::get('/files/{file_id}', [DocumentController::class, 'filePreview'])->name('file-preview-route');
            Route::get('/shared', [DocumentController::class, 'sharedDocumentList'])->name('shared-list');
            Route::get('/shared/folders/{directory_id}', [DocumentController::class, 'sharedFolderFileList'])->name('shared-folder-file-list');
            Route::get('/shared/files/{file_id}/{file_name?}', [DocumentController::class, 'sharedFilePreview'])->name('shared-file-preview-route');
            Route::get('/shared-link/check-access/{id}', [DocumentController::class, 'sharedLinkAccessCheck'])->name('shared-link-checker');
            Route::get('/shared-link/redirect', [DocumentController::class, 'sharedLinkAccess'])->name('shared-link-access');
        });

        // Chat Service Routes
        Route::prefix('messages')->name('chat.')->group(function () {
            // Test Chat Message Routes
            Route::prefix('test')->name('message.')->group(function () {
                Route::get('/', [TestController::class, 'index'])->name('test');
            });

            //Neo Message Routes
            Route::prefix('neo-message')->name('neo-message.')->group(function () {
                Route::get('/', [NeoMessageController::class, 'index'])->name('index');
            });

            // Chat Room Routes
            Route::name('room.')->group(function () {
                Route::get('/', [RoomController::class, 'index'])->name('index');
            });

            //Chat Message Routes
            Route::name('message.')->group(function () {
                Route::get('/{chat}', [MessageController::class, 'index'])->name('index');
            });
        });

        // Schedule Service Routes
        Route::prefix('/')->group(function () {
            Route::prefix('schedules')->name('schedule.')->group(function () {
                Route::get('/export', [ScheduleController::class, 'export'])->name('export');
                Route::get('/{schedule}/edit', [ScheduleController::class, 'edit'])->name('edit');
                Route::get('/notifications', [ScheduleController::class, 'notifications'])->name('notifications');
            });

            Route::patch('/accept-participation/{id}', [ScheduleController::class, 'acceptParticipation'])->name('schedule.accept-participation');
            Route::patch('/decline-participation/{id}', [ScheduleController::class, 'declineParticipation'])->name('schedule.decline-participation');
            Route::resource('schedules', ScheduleController::class)
                ->except(['store', 'edit', 'update']);
        });

        // Task Service Routes
        Route::prefix('/')->group(function () {
            Route::resource('tasks', TaskController::class);
        });

        // Classified Service Routes
        Route::prefix('/')->group(function () {
            Route::prefix('/classifieds')->name('classifieds.')->middleware('plan_access')->group(function () {
                Route::prefix('/favorites')->name('favorites.')->group(function () {
                    Route::get('/', [FavoriteController::class, 'index'])->name('index');
                });
                Route::prefix('/settings')->name('settings.')->group(function () {
                    Route::get('/', [SettingController::class, 'index'])->name('index');
                    Route::post('/card-payment', [SettingController::class, 'saveCardPaymentSetting'])->name('save-card-payment');
                    Route::get('/card-setup-success', [SettingController::class, 'stripeAccountSetupSuccess'])->name('card-payment-success');
                    Route::get('/card-setup-failed', [SettingController::class, 'stripeAccountSetupFailed'])->name('card-payment-failed');
                    Route::get('/account-transfer', [SettingController::class, 'accountTransferSetting'])->name('account-transfer');
                    Route::post('/unset-card-payment', [SettingController::class, 'unsetCardPayment'])->name('unset-card-payment');
                });
                Route::prefix('/messages')->name('messages.')->group(function () {
                    Route::get('/', [ContactController::class, 'index'])->name('index');
                    Route::get('/{contact}', [ContactMessageController::class, 'index'])->name('conversation');
                });
                Route::prefix('/payments')->name('payments.')->group(function () {
                    Route::get('/{accessKey}', [PaymentController::class, 'payment'])->name('access-payment');
                    Route::post('/process', [PaymentController::class, 'processPayment'])->name('process-payment');
                });
                Route::prefix('/')->name('sales.')->group(function () {
                    Route::get('/', [SaleController::class, 'index'])->name('index');
                    Route::get('/create', [SaleController::class, 'create'])->name('create');
                    Route::get('/{classifiedSale}/edit', [SaleController::class, 'edit'])->name('edit');
                    Route::match(['GET', 'POST'], '/create/confirmation', [SaleController::class, 'confirmation'])->name('confirmation');
                    Route::match(['GET', 'POST'], '/{classifiedSale}/edit/confirmation', [SaleController::class, 'editConfirmation'])->name('edit-confirmation');
                    Route::match(['GET', 'POST'], '/create/complete', [SaleController::class, 'complete'])->name('complete');
                    Route::match(['GET', 'POST'], '/{classifiedSale}/edit/complete', [SaleController::class, 'editComplete'])->name('edit-complete');
                    Route::get('/registered-products', [SaleController::class, 'registered'])->name('registered');
                    Route::get('/{product}', [SaleController::class, 'show'])->name('show');
                });
            });
        });

        // Form Service Routes
        Route::prefix('forms')->name('forms.')->group(function () {
            Route::get('/{form_type}/delete-history', [FormController::class, 'deleteHistory'])->name('delete-history');
            Route::post('/csv-export', [FormController::class, 'exportCsv'])->name('csv-export');
            Route::get('/pdf-download/{form}', [FormController::class, 'pdfDownload'])->name('pdf-download');

            // Basic setting routes
            Route::prefix('basic-settings')->name('basic-settings.')->group(function () {
                Route::get('/', [FormBasicSettingContoller::class, 'index'])->name('index');
                Route::post('/save', [FormBasicSettingContoller::class, 'saveBasicSettings'])->name('save');
                Route::get('/success', [FormBasicSettingContoller::class, 'saveSuccess'])->name('success');
            });

            // Quotation routes
            Route::prefix('quotations')->name('quotations.')->middleware('plan_access')->group(function () {
                Route::get('/', [QuotationController::class, 'index'])->name('index');
                Route::get('/create', [QuotationController::class, 'create'])->name('create');
                Route::get('/csv-download', [QuotationController::class, 'csvDownloadList'])->name('csv-download-list');
                Route::get('/{form}', [QuotationController::class, 'show'])->name('show');
                Route::get('/{form}/edit', [QuotationController::class, 'editQuotation'])->name('edit');
                Route::get('/{form}/duplicate', [QuotationController::class, 'duplicate'])->name('duplicate');
                Route::get('/{form}/update-history', [FormController::class, 'updateHistory'])->name('update-history');
            });

            // Purchase Order routes
            Route::prefix('purchase-orders')->name('purchase-orders.')->middleware('plan_access')->group(function () {
                Route::get('/', [PurchaseOrderController::class, 'index'])->name('index');
                Route::get('/create', [PurchaseOrderController::class, 'create'])->name('create');
                Route::get('/csv-download', [PurchaseOrderController::class, 'csvDownloadList'])->name('csv-download-list');
                Route::get('/{form}', [PurchaseOrderController::class, 'show'])->name('show');
                Route::get('/{form}/edit', [PurchaseOrderController::class, 'edit'])->name('edit');
                Route::get('/{form}/duplicate', [PurchaseOrderController::class, 'duplicate'])->name('duplicate');
                Route::get('/{form}/update-history', [FormController::class, 'updateHistory'])->name('update-history');
            });

            // Delivery Slip routes
            Route::prefix('delivery-slips')->name('delivery-slips.')->middleware('plan_access')->group(function () {
                Route::get('/create', [DeliverySlipController::class, 'create'])->name('create');
                Route::get('/', [DeliverySlipController::class, 'index'])->name('index');
                Route::get('/csv-download', [DeliverySlipController::class, 'csvDownloadList'])->name('csv-download-list');
                Route::get('/{form}', [DeliverySlipController::class, 'show'])->name('show');
                Route::get('/{form}/edit', [DeliverySlipController::class, 'edit'])->name('edit');
                Route::get('/{form}/duplicate', [DeliverySlipController::class, 'duplicate'])->name('duplicate');
                Route::get('/{form}/update-history', [FormController::class, 'updateHistory'])->name('update-history');
            });

            // Invoice routes
            Route::prefix('invoices')->name('invoices.')->middleware('plan_access')->group(function () {
                Route::get('/', [InvoiceController::class, 'index'])->name('index');
                Route::get('/create', [InvoiceController::class, 'create'])->name('create');
                Route::get('/csv-download', [InvoiceController::class, 'csvDownloadList'])->name('csv-download-list');
                Route::get('/{form}', [InvoiceController::class, 'show'])->name('show');
                Route::get('/{form}/edit', [InvoiceController::class, 'edit'])->name('edit');
                Route::get('/{form}/duplicate', [InvoiceController::class, 'duplicate'])->name('duplicate');
                Route::get('/{form}/update-history', [FormController::class, 'updateHistory'])->name('update-history');
            });

            // Receipt routes
            Route::prefix('receipts')->name('receipts.')->middleware('plan_access')->group(function () {
                Route::get('/', [ReceiptController::class, 'index'])->name('index');
                Route::get('/create', [ReceiptController::class, 'create'])->name('create');
                Route::get('/csv-download', [ReceiptController::class, 'csvDownloadList'])->name('csv-download-list');
                Route::get('/{form}', [ReceiptController::class, 'show'])->name('show');
                Route::get('/{form}/edit', [ReceiptController::class, 'edit'])->name('edit');
                Route::get('/{form}/duplicate', [ReceiptController::class, 'duplicate'])->name('duplicate');
                Route::get('/{form}/update-history', [FormController::class, 'updateHistory'])->name('update-history');
            });
        });

        // Electronic Contract Service Routes
        Route::prefix('electronic-contracts')->name('electronic-contracts.')->middleware('plan_access')->group(function () {
            Route::get('/', [ElectronicContractController::class, 'index'])->name('index');
        });

        // Knowledge Service Routes
        Route::prefix('knowledges')->name('knowledges.')->group(function () {
            Route::get('/{id?}', [KnowledgeController::class, 'index'])->name('index');

            // Article routes
            Route::prefix('articles')->name('articles.')->group(function () {
                Route::get('/draft', [ArticleController::class, 'index'])->name('index');
                Route::get('/create/{id?}', [ArticleController::class, 'create'])->name('create-article');
                Route::get('/search', [ArticleController::class, 'initialSearch'])->name('initial-search');
                Route::get('/{knowledge}', [ArticleController::class, 'show'])->name('show');
                Route::get('/pdf-download/{knowledge}', [ArticleController::class, 'pdfDownload'])->name('pdf-download');
            });
        });

        // Workflows Routes
        Route::prefix('workflows')->name('workflows.')->group(function () {
            Route::get('/', [WorkflowController::class, 'index'])->name('index');
            Route::get('/{workflow}', [WorkflowController::class, 'show'])->name('show');
        });

        // Plans & subscription Service Routes
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/', [PlanController::class, 'index'])->name('index');
            Route::get('/show/{plan}', [PlanController::class, 'show'])->name('show');
            Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription');
            Route::prefix('subscription')->name('subscription.')->group(function () {
                Route::post('/save-payment-method', [SubscriptionController::class, 'savePaymentMethod'])->name('save-payment-method');
                Route::delete('/delete-payment-method', [SubscriptionController::class, 'deletePaymentMethod'])->name('delete-payment-method');
            });
        });

        // Paid Plan Service Routes
        Route::prefix('paid-plan')->name('paid-plan.')->group(function () {
            Route::get('/', [PaidPlanController::class, 'index'])->name('index');

            // Payment Process
            Route::prefix('payments')->name('payments.')->group(function () {
                Route::post('/payment', [PaidPlanController::class, 'processPayment'])->name('process-payment');
            });

            Route::post('/change-plan-confirmation', [SubscriptionController::class, 'verifyIncompleteSubscription'])->name('verify-incomplete-subscription');
            Route::get('/add-document-management', [PaidPlanController::class, 'addDocumentManagement'])->name('add-document-management');
            Route::get('/document-management-confirmation', [PaidPlanController::class, 'documentManagementConfirmation'])->name('document-management-confirmation');
            Route::get('/add-staff', [PaidPlanController::class, 'addStaff'])->name('add-staff');
            Route::get('/add-staff-confirmation', [PaidPlanController::class, 'addStaffConfirmation'])->name('add-staff-confirmation');
            Route::get('/add-net-shop', [PaidPlanController::class, 'addNetShop'])->name('add-net-shop');
            Route::get('/net-shop-confirmation', [PaidPlanController::class, 'netShopConfirmation'])->name('net-shop-confirmation');
            Route::get('/add-invoice', [PaidPlanController::class, 'addInvoice'])->name('add-invoice');
            Route::get('/invoice-confirmation', [PaidPlanController::class, 'invoiceConfirmation'])->name('invoice-confirmation');
            Route::get('/add-purchase-order', [PaidPlanController::class, 'addPurchaseOrder'])->name('add-purchase-order');
            Route::get('/purchase-order-confirmation', [PaidPlanController::class, 'purchaseOrderConfirmation'])->name('purchase-order-confirmation');
            Route::get('/add-delivery-note', [PaidPlanController::class, 'addDeliveryNote'])->name('add-delivery-note');
            Route::get('/delivery-note-confirmation', [PaidPlanController::class, 'deliveryNoteConfirmation'])->name('delivery-note-confirmation');
            Route::get('/add-receipt', [PaidPlanController::class, 'addReceipt'])->name('add-receipt');
            Route::get('/receipt-confirmation', [PaidPlanController::class, 'receiptConfirmation'])->name('receipt-confirmation');
            Route::get('/add-workflow', [PaidPlanController::class, 'addWorkflow'])->name('add-workflow');
            Route::get('/workflow-confirmation', [PaidPlanController::class, 'workflowConfirmation'])->name('workflow-confirmation');
        });
    });

    // Hero Storage Routes
    Route::prefix('hero-storage')->name('hero-storage.')->group(function () {
        Route::get('/public/{path}', [StorageController::class, 'viewPublic'])->name('public')->where('path', '.*');
    });
});
