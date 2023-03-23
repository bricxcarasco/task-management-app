<?php

use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\Chat\RoomController;
use App\Http\Controllers\Connection\ApplicationRequestController;
use App\Http\Controllers\Connection\ConnectionController as ConnectionApplicationController;
use App\Http\Controllers\Api\DocumentController as DocumentApiController;
use App\Http\Controllers\Api\ElectronicContractController as ElectronicContractApiController;
use App\Http\Controllers\Api\ProfileImageUploadController;
use App\Http\Controllers\Api\FormApiController;
use App\Http\Controllers\Api\FormImageUploadController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Connection\ConnectionSearchController;
use App\Http\Controllers\Connection\GroupController;
use App\Http\Controllers\Form\BasicSettingController as FormBasicSettingContoller;
use App\Http\Controllers\Neo\GroupController as NeoGroupController;
use App\Http\Controllers\NeoController;
use App\Http\Controllers\Neo\AdministratorController;
use App\Http\Controllers\RioController;
use App\Http\Controllers\Rio\AccountInformationController;
use App\Http\Controllers\Rio\ConnectionController;
use App\Http\Controllers\Schedule\ScheduleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chat\NeoMessageController;
use App\Http\Controllers\Classified\ContactController;
use App\Http\Controllers\Classified\ContactMessageController;
use App\Http\Controllers\Classified\FavoriteController;
use App\Http\Controllers\Classified\PaymentController;
use App\Http\Controllers\Classified\SaleController;
use App\Http\Controllers\Classified\SettingController;
use App\Http\Controllers\Form\InvoiceController;
use App\Http\Controllers\Form\PurchaseOrderController;
use App\Http\Controllers\Form\QuotationController;
use App\Http\Controllers\Form\DeliverySlipController;
use App\Http\Controllers\Form\FormController;
use App\Http\Controllers\Notifications\BasicSettingController;
use App\Http\Controllers\Task\TaskController;
use App\Http\Controllers\ElectronicContract\ElectronicContractController;
use App\Http\Controllers\Workflow\WorkflowActionController;
use App\Http\Controllers\Workflow\WorkflowController;
use App\Http\Controllers\Form\ReceiptController;
use App\Http\Controllers\Knowledge\ArticleController;
use App\Http\Controllers\Knowledge\CommentController;
use App\Http\Controllers\Knowledge\KnowledgeController;
use App\Http\Controllers\Notifications\NotificationController;
use App\Http\Middleware\ServiceSelectionMiddleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['api.auth', 'service'])->group(function () {
    // RIO API routes
    Route::prefix('rio')->name('rio.')->group(function () {
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::prefix('update')->name('update.')->group(function () {
                Route::post('/image', [RioController::class, 'updateImage']);
                Route::delete('/image/{rio}', [RioController::class, 'deleteImage']);
                Route::put('/name', [RioController::class, 'updateName']);
                Route::put('/home', [RioController::class, 'updateHomeAddress']);
                Route::patch('/gender', [RioController::class, 'updateGender']);
                Route::patch('/birthdate', [RioController::class, 'updateBirthdate']);
                Route::patch('/telephone', [RioController::class, 'updateTelephone']);
                Route::patch('/present-address', [RioController::class, 'updatePresentAddress']);
                Route::patch('/self-introduction', [RioController::class, 'updateSelfIntroduction']);
                Route::post('service-selection', [RioController::class, 'createRIOServiceSession'])->name('service-selection')->withoutMiddleware([ServiceSelectionMiddleware::class]);
            });

            // Rio Profile - Profession Routes
            Route::prefix('profession')->name('profession.')->group(function () {
                Route::get('/', [RioController::class, 'getProfessions'])->name('get');
                Route::post('/', [RioController::class, 'createProfession'])->name('create');
                Route::put('/{profession}', [RioController::class, 'updateProfession'])->name('update');
                Route::delete('/{profession}', [RioController::class, 'deleteProfession'])->name('delete');
            });

            // Rio Profile - Industry Routes
            Route::prefix('industry')->name('industry.')->group(function () {
                Route::get('/', [RioController::class, 'getIndustries'])->name('get');
                Route::post('/', [RioController::class, 'createIndustry'])->name('create');
                Route::put('/{industry}', [RioController::class, 'updateIndustry'])->name('update');
                Route::delete('/{industry}', [RioController::class, 'deleteIndustry'])->name('delete');
            });

            // Rio Profile - Educational background Routes
            Route::prefix('educational-background')->name('educational-background.')->group(function () {
                Route::get('/', [RioController::class, 'getEducationalBackgrounds'])->name('get');
                Route::post('/', [RioController::class, 'createEducationalBackground'])->name('create');
                Route::put('/{educationalBackground}', [RioController::class, 'updateEducationalBackground'])->name('update');
                Route::delete('/{educationalBackground}', [RioController::class, 'deleteEducationalBackground'])->name('delete');
            });

            // Rio Profile - Award history Routes
            Route::prefix('award-history')->name('award-history.')->group(function () {
                Route::get('/', [RioController::class, 'getAwards'])->name('get');
                Route::post('/', [RioController::class, 'registerAward'])->name('create');
                Route::put('/{award}', [RioController::class, 'updateAward'])->name('update');
                Route::delete('/{award}', [RioController::class, 'deleteAward'])->name('delete');
            });

            // Rio Profile - Qualification Routes
            Route::prefix('qualification')->name('qualification.')->group(function () {
                Route::get('/', [RioController::class, 'getQualifications'])->name('get');
                Route::post('/', [RioController::class, 'createQualification'])->name('create');
                Route::put('/{qualification}', [RioController::class, 'updateQualification'])->name('update');
                Route::delete('/{qualification}', [RioController::class, 'deleteQualification'])->name('delete');
            });

            // Rio Profile - Skill Routes
            Route::prefix('skill')->name('skill.')->group(function () {
                Route::get('/', [RioController::class, 'getSkills'])->name('get');
                Route::post('/', [RioController::class, 'createSkill'])->name('create');
                Route::put('/{skill}', [RioController::class, 'updateSkill'])->name('update');
                Route::delete('/{skill}', [RioController::class, 'deleteSkill'])->name('delete');
            });

            // Rio Profile - Product Routes
            Route::prefix('product')->name('product.')->group(function () {
                Route::get('/', [RioController::class, 'getProducts'])->name('get');
                Route::post('/', [RioController::class, 'createProduct'])->name('create');
                Route::put('/{product}', [RioController::class, 'updateProduct'])->name('update');
                Route::delete('/{product}', [RioController::class, 'deleteProduct'])->name('delete');
            });

            // Rio Profile - Neo Affiliate Routes
            Route::prefix('affiliate')->name('affiliate.')->group(function () {
                Route::get('/', [RioController::class, 'getAffiliates'])->name('get');
                Route::put('/{affiliate}', [RioController::class, 'updateAffiliate'])->name('update');
            });

            // Rio Profile - Invitation Management
            Route::prefix('invitation-management')->name('invitation-management.')->group(function () {
                Route::patch('/accept-invitation/{neoBelong}', [RioController::class, 'acceptInvitation'])->name('accept-invitation');
                Route::patch('/decline-invitation/{neoBelong}', [RioController::class, 'declineInvitation'])->name('decline-invitation');
                Route::patch('/invite-lists', [RioController::class, 'getInviteLists'])->name('invite-lists');
                Route::patch('/pending-invitation', [RioController::class, 'updatePendingInvitation'])->name('pending-invitation');
            });
        });

        Route::prefix('information')->name('information.')->group(function () {
            Route::post('password-confirm', [AccountInformationController::class, 'confirmPassword'])->name('password-confirm');

            Route::prefix('update')->name('update.')->group(function () {
                Route::post('email', [AccountInformationController::class, 'updateEmail'])->name('email');
                Route::post('password', [AccountInformationController::class, 'updatePassword'])->name('password');
                Route::post('secret-question', [AccountInformationController::class, 'updateSecretQuestion'])->name('secret-question');
            });
        });

        Route::prefix('connection')->name('connection.')->group(function () {
            Route::post('connect', [ConnectionController::class, 'connect'])->name('connect');
            Route::post('cancel-disconnect', [ConnectionController::class, 'cancelDisconnect'])->name('cancel-disconnect');
        });
    });

    // NEO API routes
    Route::prefix('neo')->name('neo.')->group(function () {
        // Neo - Profile Page Routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::prefix('update')->name('update.')->group(function () {
                Route::post('/image', [NeoController::class, 'updateImage']);
                Route::delete('/image/{rio}', [NeoController::class, 'deleteImage']);
                Route::put('organization-name/{neo}', [NeoController::class, 'updateOrganizationName'])->name('update-organization-name');
                Route::patch('location/{neo}', [NeoController::class, 'updateLocation'])->name('update-location');
                Route::patch('telephone/{neo}', [NeoController::class, 'updateTelephone'])->name('update-telephone');
                Route::patch('introduction/{neo}', [NeoController::class, 'updateIntroduction'])->name('update-introduction');
                Route::patch('establishment-date/{neo}', [NeoController::class, 'updateEstablishmentDate'])->name('update-establishment-date');
                Route::post('business-holiday/{neo}', [NeoController::class, 'upsertBusinessHoliday'])->name('upsert-business-holiday');
                Route::post('service-selection', [NeoController::class, 'createNEOServiceSession'])->name('service-selection')->withoutMiddleware([ServiceSelectionMiddleware::class]);
                Route::post('participation/{id}', [NeoController::class, 'createParticipation'])->name('create-participation');
                Route::delete('/cancel-participation/{id}', [NeoController::class, 'cancelParticipation'])->name('cancel-participation');
                Route::patch('services', [NeoController::class, 'serviceSelections'])->name('services');
                Route::patch('overseas-support/{neo}', [NeoController::class, 'updateOverseasSupport'])->name('update-overseas-support');
            });

            // Neo Profile - Award history Routes
            Route::prefix('award-history')->name('award-history.')->group(function () {
                Route::get('/{id}', [NeoController::class, 'getAwards'])->name('get');
                Route::post('/{id}', [NeoController::class, 'createAward'])->name('create');
                Route::put('/{award}', [NeoController::class, 'updateAward'])->name('update');
                Route::delete('/{award}', [NeoController::class, 'deleteAward'])->name('delete');
            });

            // Neo Profile - Industry Routes
            Route::prefix('industry')->name('industry.')->group(function () {
                Route::get('/{id}', [NeoController::class, 'getIndustries'])->name('get');
                Route::post('/{id}', [NeoController::class, 'createIndustry'])->name('create');
                Route::put('/{industry}', [NeoController::class, 'updateIndustry'])->name('update');
                Route::delete('/{industry}', [NeoController::class, 'deleteIndustry'])->name('delete');
            });

            // Neo Profile - URL Routes
            Route::prefix('url')->name('url.')->group(function () {
                Route::get('/{id}', [NeoController::class, 'getUrls'])->name('get');
                Route::post('/{id}', [NeoController::class, 'createUrl'])->name('create');
                Route::put('/{url}', [NeoController::class, 'updateUrl'])->name('update');
                Route::delete('/{url}', [NeoController::class, 'deleteUrl'])->name('delete');
            });

            // Neo Profile - History Routes
            Route::prefix('history')->name('history.')->group(function () {
                Route::get('/{neo}', [NeoController::class, 'getHistories'])->name('get');
                Route::post('/{neo}', [NeoController::class, 'createHistory'])->name('create');
                Route::put('/{history}', [NeoController::class, 'updateHistory'])->name('update');
                Route::delete('/{history}', [NeoController::class, 'deleteHistory'])->name('delete');
            });

            // Neo Profile - Qualification Routes
            Route::prefix('qualification')->name('qualification.')->group(function () {
                Route::get('/{id}', [NeoController::class, 'getQualifications'])->name('get');
                Route::post('/{id}', [NeoController::class, 'createQualification'])->name('create');
                Route::put('/{qualification}', [NeoController::class, 'updateQualification'])->name('update');
                Route::delete('/{qualification}', [NeoController::class, 'deleteQualification'])->name('delete');
            });

            // Neo Profile - Skill Routes
            Route::prefix('skill')->name('skill.')->group(function () {
                Route::get('/{id}', [NeoController::class, 'getSkills'])->name('get');
                Route::post('/{id}', [NeoController::class, 'createSkill'])->name('create');
                Route::put('/{skill}', [NeoController::class, 'updateSkill'])->name('update');
                Route::delete('/{skill}', [NeoController::class, 'deleteSkill'])->name('delete');
            });

            // Neo Profile - Product Routes
            Route::prefix('product')->name('product.')->group(function () {
                Route::get('/{id}', [NeoController::class, 'getProducts'])->name('get');
                Route::post('/{id}', [NeoController::class, 'createProduct'])->name('create');
                Route::put('/{product}', [NeoController::class, 'updateProduct'])->name('update');
                Route::delete('/{product}', [NeoController::class, 'deleteProduct'])->name('delete');
            });

            // Neo Profile - Email Address Routes
            Route::prefix('email-address')->name('email-address.')->group(function () {
                Route::get('/{id}', [NeoController::class, 'getEmails'])->name('get');
                Route::post('/{id}', [NeoController::class, 'createEmail'])->name('create');
                Route::put('/{email}', [NeoController::class, 'updateEmail'])->name('update');
                Route::delete('/{email}', [NeoController::class, 'deleteEmail'])->name('delete');
            });

            // Neo Profile - Participant Management
            Route::prefix('participant-management')->name('participant-management.')->group(function () {
                Route::patch('/approve-participant', [NeoController::class, 'approveParticipant'])->name('approve-participant');
                Route::patch('/reject-participant', [NeoController::class, 'rejectParticipant'])->name('reject-participant');
                Route::patch('/pending-participants/{id}', [NeoController::class, 'updatePendingParticipants'])->name('pending-participants');
            });

            // Neo Profile - Email Address Routes
            Route::prefix('connection')->name('connection.')->group(function () {
                Route::post('/create-connection/{neo}/{message?}', [NeoController::class, 'createConnection'])->name('create-connection');
                Route::delete('/cancel-connection/{neo}', [NeoController::class, 'cancelConnection'])->name('cancel-connection');
                Route::delete('/disconnect-connection/{neo}', [NeoController::class, 'disconnectConnection'])->name('disconnect-connection');
            });

            // Neo Profile - Invitation Management
            Route::prefix('invitation-management')->name('invitation-management.')->group(function () {
                Route::patch('/invitation-lists/{id}', [NeoController::class, 'inviteLists'])->name('invitation-lists');
                Route::patch('/participation-invitation-lists/{id}', [NeoController::class, 'participationInvitationLists'])->name('participation-invitation-lists');
                Route::patch('/invite-connection/{id}', [NeoController::class, 'inviteToConnection'])->name('invite-connection');
                Route::patch('/cancel-invitation/{id}', [NeoController::class, 'cancelInvitation'])->name('cancel-invitation');
                Route::get('/connection-lists/{id}', [NeoController::class, 'getConnectionList'])->name('connection-lists');
                Route::patch('/search/{id}', [NeoController::class, 'searchInvitationConnection'])->name('search');
            });
        });

        // Neo Administrator/Owner Management
        Route::middleware('neo_owner')->prefix('administrator')->name('administrator.')->group(function () {
            Route::prefix('update')->name('update.')->group(function () {
                Route::post('administrator/{neo}', [AdministratorController::class, 'setRemoveAdministrator'])->name('administrator');
            });
        });
    });

    // Notification API routes
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/read/{notification}', [NotificationController::class, 'read'])->name('read');

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::prefix('mail-templates')->name('mail-templates.')->group(function () {
                Route::get('/', [BasicSettingController::class, 'getMailTemplates'])->name('get');
                Route::post('/', [BasicSettingController::class, 'updateMailTemplates'])->name('update');
            });
            Route::post('/general', [BasicSettingController::class, 'updateGeneralSettings'])->name('general-update');
        });
    });

    // Connection API Routes
    Route::prefix('connection')->name('connection.')->group(function () {
        Route::patch('/', [ConnectionApplicationController::class, 'connectionListItem'])->name('connection-list-item');
        Route::get('/applications', [ConnectionApplicationController::class, 'applicationListItem'])->name('application-list-item');

        // Connection Group Routes
        Route::prefix('groups')->name('groups.')->group(function () {
            Route::get('/invite-members/{group}/search', [GroupController::class, 'searchInviteMembers'])->name('search-invite-members');
            Route::post('/invite-members/{group}', [GroupController::class, 'inviteMember'])->name('invite-member');
            Route::delete('/invite-members/{invite}', [GroupController::class, 'deleteInvite'])->name('delete-invite');
        });

        Route::prefix('request')->name('request.')->group(function () {
            Route::post('accept', [ApplicationRequestController::class, 'accept'])->name('accept');
            Route::post('decline', [ApplicationRequestController::class, 'decline'])->name('decline');
        });

        Route::prefix('search')->name('search.')->group(function () {
            Route::get('/results', [ConnectionSearchController::class, 'searchListItem'])->name('search-list-item');
        });
    });

    // Document Management API Routes
    Route::prefix('document')->name('document.')->middleware('plan_access')->group(function () {
        Route::get('/list/{id?}', [DocumentApiController::class, 'list'])->name('document-list');
        Route::post('/folder', [DocumentApiController::class, 'createFolder'])->name('create-folder');
        Route::put('/rename/{id?}', [DocumentApiController::class, 'rename'])->name('document-rename');
        Route::post('/save-setting', [DocumentApiController::class, 'saveSetting'])->name('save-setting');
        Route::delete('/delete/{id?}', [DocumentApiController::class, 'delete'])->name('document-delete');
        Route::get('/shared', [DocumentApiController::class, 'shared'])->name('shared-list');
        Route::get('/file-preview/{id}', [DocumentApiController::class, 'filePreview'])->name('file-preview');
        Route::get('/shared-link/{document}', [DocumentApiController::class, 'sharedLink'])->name('shared-link');
        Route::post('/file', [DocumentApiController::class, 'uploadFile'])->name('upload-file');
        Route::get('/download/{id?}', [DocumentApiController::class, 'download'])->name('document-download');
        Route::get('/check-content/{id?}', [DocumentApiController::class, 'checkContent'])->name('check-folder-content');

        // Upload-related API Routes
        Route::prefix('file')->name('file.')->group(function () {
            Route::post('/process', [DocumentApiController::class, 'processUpload'])->name('process-upload');
            Route::patch('/', [DocumentApiController::class, 'processChunk'])->name('chunk');
            Route::match('head', '/', [DocumentApiController::class, 'retryChunk'])->name('retry-chunk');
            Route::delete('/', [DocumentApiController::class, 'revertUpload'])->name('revert');
        });

        // Share-setting-related API Routes
        Route::prefix('share-setting')->name('share-setting.')->group(function () {
            Route::get('/', [DocumentApiController::class, 'shareSetting'])->name('document-share-setting');
            Route::get('/connected-list/{id?}', [DocumentApiController::class, 'connectedList'])->name('connected-list');
            Route::get('/permitted-list/{id?}', [DocumentApiController::class, 'permittedList'])->name('permitted-list');
            Route::delete('/unshare/{id?}', [DocumentApiController::class, 'unshare'])->name('unshare');
        });
    });

    // Chat Service API Routes
    Route::prefix('chat')->name('chat.')->group(function () {
        // Chat Room API Routes
        Route::prefix('room')->name('room.')->group(function () {
            Route::get('/search', [RoomController::class, 'searchChatRooms'])->name('search-chat');
            Route::patch('/talk-subject', [RoomController::class, 'updateTalkSubject'])->name('talk-subject');
            Route::patch('/archive-room/{chat}', [RoomController::class, 'archiveRoom'])->name('archive-room');
            Route::patch('/restore-room', [RoomController::class, 'restoreRoom'])->name('restore-room');

            Route::prefix('group')->name('group.')->group(function () {
                Route::prefix('neo')->name('neo.')->group(function () {
                    Route::post('/create-group/{neo}', [NeoGroupController::class, 'chatCreateNeoGroup'])->name('create-neo-group');
                });
            });
        });

        // Chat Message API Routes
        Route::prefix('message')->name('message.')->group(function () {
            Route::get('/{id}', [MessageController::class, 'getMessages'])->name('get');
            Route::post('/', [MessageController::class, 'sendMessage'])->name('create');
            Route::post('/delete', [MessageController::class, 'deleteMessage'])->name('delete');
        });

        Route::prefix('neo-message')->name('neo-message.')->group(function () {
            Route::patch('/filter-list', [NeoMessageController::class, 'filterList'])->name('filter-list');
            Route::post('/create-neo-message', [NeoMessageController::class, 'createNeoMessage'])->name('create-neo-message');
            Route::patch('/select-page', [NeoMessageController::class, 'getSelectedPage'])->name('select-page');
            Route::patch('/search', [NeoMessageController::class, 'updateList'])->name('search');
        });
    });

    // Schedule service API Routes
    Route::prefix('schedules')->name('schedules.')->group(function () {
        Route::post('/', [ScheduleController::class, 'store'])->name('store');
        Route::get('/month/{date}', [ScheduleController::class, 'getSchedulesByMonth'])->name('get-by-month');
        Route::get('/day/{date}', [ScheduleController::class, 'getSchedulesByDay'])->name('get-by-day');
        Route::patch('/update-connection-list', [ScheduleController::class, 'updateConnectionList'])->name('update-connection-list');
        Route::patch('/update-guest-list', [ScheduleController::class, 'updateExistingScheduleList'])->name('update-guest-list');
        Route::delete('/delete-schedule/{schedule}', [ScheduleController::class, 'destroy'])->name('schedule.delete');
        Route::put('/update-schedule/{schedule}', [ScheduleController::class, 'update'])->name('schedule.update');
        Route::post('/export-schedule', [ScheduleController::class, 'exportSchedule'])->name('export-schedule');
    });

    // Profile image upload service API Routes
    Route::prefix('image')->name('image.')->group(function () {
        Route::post('/process', [ProfileImageUploadController::class, 'processUpload'])->name('process-upload');
        Route::delete('/process', [ProfileImageUploadController::class, 'revertUpload'])->name('revert');
    });

    // Task service API Routes
    Route::prefix('tasks')->name('tasks.')->group(function () {
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::put('/{id}', [TaskController::class, 'update'])->name('update');
        Route::patch('/complete', [TaskController::class, 'completeTasks'])->name('complete-tasks');
        Route::patch('/delete', [TaskController::class, 'deleteTasks'])->name('delete-tasks');
        Route::patch('/task-lists', [TaskController::class, 'getLists'])->name('tasks.lists');
        Route::get('/month/{date}', [TaskController::class, 'getTasksByMonth'])->name('get-by-month');
        Route::get('/day/{date}', [TaskController::class, 'getTasksByDay'])->name('get-by-day');
        Route::patch('/return-task/{task}', [TaskController::class, 'returnTask'])->name('return.task');
    });

    // Classified Service API Routes
    Route::prefix('classifieds')->name('classifieds.')->middleware('plan_access')->group(function () {
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::patch('/products-list', [SaleController::class, 'getProducts'])->name('get-products');
            Route::patch('/sales-categories-list', [SaleController::class, 'getSalesCategories'])->name('get-sales-categories');

            // Registered Products Routes
            Route::prefix('/registered-products')->name('registered-products.')->group(function () {
                Route::patch('/', [SaleController::class, 'getRegisteredProducts'])->name('get');
                Route::delete('/delete-registered-products/{product}', [SaleController::class, 'destroy'])->name('delete');

                // Registered Products - Images Routes
                Route::prefix('/images')->name('images.')->group(function () {
                    Route::post('/process', [SaleController::class, 'processUpload'])->name('process-upload');
                    Route::patch('/', [SaleController::class, 'processChunk'])->name('chunk');
                    Route::match('head', '/', [SaleController::class, 'retryChunk'])->name('retry-chunk');
                    Route::delete('/', [SaleController::class, 'revertUpload'])->name('revert');
                    Route::get('/restore', [SaleController::class, 'restoreUpload'])->name('restore');
                    Route::get('/load', [SaleController::class, 'loadFile'])->name('load');
                });
            });

            Route::prefix('update')->name('update.')->group(function () {
                Route::put('/accessibility/{product}', [SaleController::class, 'updateAccessibility'])->name('update-accessibility');
            });
        });
        Route::prefix('favorites')->name('favorites.')->group(function () {
            Route::patch('/', [FavoriteController::class, 'getFavoriteProducts'])->name('get-favorites');
            Route::patch('/favorite-product/{classifiedSale}', [FavoriteController::class, 'favoriteProduct'])->name('favorite-product');
            Route::patch('/unfavorite-product/{classifiedSale}', [FavoriteController::class, 'unfavoriteProduct'])->name('unfavorite-product');
        });
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [ContactController::class, 'getInquiries'])->name('get-inquries');
            Route::get('/{id}', [ContactMessageController::class, 'getMessages'])->name('get-messages');
            Route::post('/send', [ContactMessageController::class, 'sendMessage'])->name('send-message');

            Route::prefix('file')->name('file.')->group(function () {
                Route::post('/process', [ContactMessageController::class, 'processUpload'])->name('process-upload');
                Route::patch('/chunk', [ContactMessageController::class, 'chunkUpload'])->name('chunk-upload');
                Route::delete('/revert', [ContactMessageController::class, 'revertUpload'])->name('revert-upload');
            });
        });
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::post('/send-inquiry', [ContactController::class, 'sendInquiry'])->name('send-inquiry');
        });
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingController::class, 'getSettings'])->name('get-settings');
            Route::post('/register-setting', [SettingController::class, 'registerSetting'])->name('register-setting');
            Route::patch('/edit-setting', [SettingController::class, 'editSetting'])->name('edit-setting');
            Route::patch('/save-accounts', [SettingController::class, 'saveAccountDetails'])->name('save-accounts');
        });
        Route::prefix('payments')->name('payments.')->group(function () {
            Route::post('/issuance', [PaymentController::class, 'issuePaymentUrl'])->name('issue-payment');
        });
    });

    // Form Service API Routes
    Route::prefix('forms')->name('forms.')->group(function () {
        Route::patch('/delete-history', [FormController::class, 'getDeleteHistoryLists'])->name('api-delete-history');
        Route::patch('/update-history', [FormController::class, 'getUpdateHistoryLists'])->name('api-update-history');
        Route::get('/update-history/{id}', [FormController::class, 'getUpdateHistoryDetails'])->name('api-update-history-details');
        Route::post('/file', [FormController::class, 'uploadFormPdfFormat'])->name('upload-form-pdf');
        Route::post('/history', [FormController::class, 'getFormHistory'])->name('get-form-history');

        // Basic Settings API routes
        Route::prefix('basic-settings')->name('basic-settings.')->group(function () {
            Route::post('/save', [FormBasicSettingContoller::class, 'saveBasicSettings'])->name('save');

            // Registered Products - Images Routes
            Route::prefix('/images')->name('images.')->group(function () {
                Route::post('/process', [FormBasicSettingContoller::class, 'processUpload'])->name('process-upload');
                Route::patch('/', [FormBasicSettingContoller::class, 'processChunk'])->name('chunk');
                Route::match('head', '/', [FormBasicSettingContoller::class, 'retryChunk'])->name('retry-chunk');
                Route::delete('/', [FormBasicSettingContoller::class, 'revertUpload'])->name('revert');
                Route::get('/restore', [FormBasicSettingContoller::class, 'restoreUpload'])->name('restore');
                Route::get('/load', [FormBasicSettingContoller::class, 'loadFile'])->name('load');
            });
        });

        // Quotations API routes
        Route::prefix('quotations')->name('quotations.')->middleware('plan_access')->group(function () {
            Route::post('/', [FormApiController::class, 'createQuotation'])->name('create-quotation');
            Route::post('/update/{form}', [FormApiController::class, 'updateQuotation'])->name('update-quotation');
            Route::patch('/quotation-lists', [QuotationController::class, 'getQuotationLists'])->name('quotation.lists');
            Route::post('/validate-quotation-search', [QuotationController::class, 'validateQuotationSearch'])->name('validate.quotation.search');
            Route::delete('/delete/{form}/{withAlert?}', [QuotationController::class, 'destroy'])->name('destroy');
        });

        // Upload-related API Routes
        Route::prefix('file')->name('file.')->group(function () {
            Route::post('/process', [FormImageUploadController::class, 'processUpload'])->name('process-upload');
            Route::patch('/', [FormImageUploadController::class, 'processChunk'])->name('chunk');
            Route::delete('/', [FormImageUploadController::class, 'revertUpload'])->name('revert');
            Route::get('/restore', [FormImageUploadController::class, 'restoreUpload'])->name('restore');
        });

        // Connection lists route
        Route::prefix('connection-list')->name('connection-list.')->group(function () {
            Route::get('/', [FormApiController::class, 'connectionList'])->name('connection-list-item-api');
        });

        // Quotation product routes
        Route::prefix('product')->name('product.')->group(function () {
            Route::post('validate', [FormApiController::class, 'validateProduct'])->name('validate');
        });

        // Invoices API routes
        Route::prefix('invoices')->name('invoices.')->middleware('plan_access')->group(function () {
            Route::post('/', [InvoiceController::class, 'createInvoice'])->name('create-invoice');
            Route::post('/update/{form}', [InvoiceController::class, 'updateInvoice'])->name('update-invoice');
            Route::patch('/invoice-lists', [InvoiceController::class, 'getInvoiceLists'])->name('invoices.lists');
            Route::post('/validate-invoice-search', [InvoiceController::class, 'validateInvoiceSearch'])->name('validate.invoice.search');
            Route::delete('/delete/{form}/{withAlert?}', [InvoiceController::class, 'destroy'])->name('destroy');
            Route::post('/confirm', [InvoiceController::class, 'confirm'])->name('invoice.confirm');

            Route::prefix('/images')->name('images.')->group(function () {
                Route::get('/restore', [InvoiceController::class, 'restoreUpload'])->name('restore');
            });
        });

        // Delivery Slips API routes
        Route::prefix('delivery-slips')->name('delivery-slips.')->middleware('plan_access')->group(function () {
            Route::post('/', [DeliverySlipController::class, 'createDeliverySlip'])->name('create-delivery-slip');
            Route::post('/confirm', [DeliverySlipController::class, 'confirm'])->name('delivery-slip.confirm');
            Route::post('/update/{form}', [DeliverySlipController::class, 'updateDeliverySlip'])->name('update-delivery-slip');
            Route::prefix('/images')->name('images.')->group(function () {
                Route::get('/restore', [DeliverySlipController::class, 'restoreUpload'])->name('restore');
            });
            Route::patch('/delivery-slip-lists', [DeliverySlipController::class, 'getDeliverySlipLists'])->name('delivery-slips.lists');
            Route::post('/validate-delivery-slip-search', [DeliverySlipController::class, 'validateDeliverySlipSearch'])->name('validate.delivery-slip.search');
            Route::delete('/delete/{form}/{withAlert?}', [DeliverySlipController::class, 'destroy'])->name('destroy');
        });

        // Receipts API routes
        Route::prefix('receipts')->name('receipts.')->middleware('plan_access')->group(function () {
            Route::post('/', [FormApiController::class, 'createReceipt'])->name('create-receipt');
            Route::patch('/receipt-lists', [ReceiptController::class, 'getReceiptLists'])->name('receipts.lists');
            Route::post('/validate-receipt-search', [ReceiptController::class, 'validateReceiptSearch'])->name('validate.receipt.search');
            Route::delete('/delete/{form}/{withAlert?}', [ReceiptController::class, 'destroy'])->name('destroy');
            Route::post('/confirm', [ReceiptController::class, 'confirm'])->name('receipt.confirm');
            Route::post('/update/{form}', [ReceiptController::class, 'updateReceipt'])->name('update-receipt');
        });

        // Purchase Orders API routes
        Route::prefix('purchase-orders')->name('purchase-orders.')->middleware('plan_access')->group(function () {
            Route::patch('/purchase-order-lists', [PurchaseOrderController::class, 'getPurchaseOrderLists'])->name('purchase-orders.lists');
            Route::post('/validate-purchase-order-search', [PurchaseOrderController::class, 'validatePurchaseOrderSearch'])->name('validate.purchase-orders.search');
            Route::post('/', [PurchaseOrderController::class, 'createPurchaseOrder'])->name('create-purchase-order');
            Route::post('/confirm', [PurchaseOrderController::class, 'confirm'])->name('purchase-order-confirm');
            Route::post('/update/{form}', [PurchaseOrderController::class, 'updatePurchaseOrder'])->name('update.purchase-order');
            Route::delete('/delete/{form}/{withAlert?}', [PurchaseOrderController::class, 'destroy'])->name('destroy');

            Route::prefix('/images')->name('images.')->group(function () {
                Route::get('/restore', [PurchaseOrderController::class, 'restoreUpload'])->name('restore');
            });
        });
    });

    // Electronic contract service API Routes
    Route::prefix('electronic-contracts')->name('electronic-contracts.')->middleware('plan_access')->group(function () {
        Route::prefix('list')->name('list.')->group(function () {
            Route::get('/', [ElectronicContractApiController::class, 'connectionList'])->name('connection-list-item-api');
            Route::get('/search', [ElectronicContractApiController::class, 'searchConnections'])->name('search-Connections-api');
            Route::get('/recipient-email', [ElectronicContractApiController::class, 'emailList'])->name('recipient-email-api');
        });

        Route::post('/', [ElectronicContractApiController::class, 'store'])->name('store');
        Route::post('manual-recipient-register', [ElectronicContractController::class, 'manualRecipientRegister'])->name('manual-recipient-register');

        // Upload-related API Routes
        Route::prefix('file')->name('file.')->group(function () {
            Route::post('/process', [ElectronicContractController::class, 'processUpload'])->name('process-upload');
            Route::patch('/', [ElectronicContractController::class, 'processChunk'])->name('chunk');
            Route::delete('/', [ElectronicContractController::class, 'revertUpload'])->name('revert');
        });
    });

    // Knowledge Service API Routes
    Route::prefix('knowledges')->name('knowledges.')->group(function () {
        //Article api routes
        Route::prefix('articles')->name('articles.')->group(function () {
            Route::post('/', [ArticleController::class, 'store'])->name('store');
            Route::put('/{id?}', [ArticleController::class, 'update'])->name('update');
            Route::patch('/comments/{knowledge}', [ArticleController::class, 'loadComments'])->name('load-comments');

            // Draft routes
            Route::prefix('drafts')->name('drafts.')->group(function () {
                Route::delete('/delete/{knowledge}', [ArticleController::class, 'delete'])->name('delete');
                Route::post('/', [ArticleController::class, 'createDraft'])->name('create-draft');
                Route::put('/{id}', [ArticleController::class, 'updateDraft'])->name('update-draft');
            });

            //Comment routes
            Route::prefix('comments')->name('comments.')->group(function () {
                Route::post('/', [CommentController::class, 'store'])->name('store-comment');
                Route::delete('/{knowledgeComment}', [CommentController::class, 'destroy'])->name('destroy');
                Route::put('/{knowledgeComment}', [CommentController::class, 'update'])->name('update');
            });

            // Article search routes
            Route::prefix('search')->name('search.')->group(function () {
                Route::get('/session/{keyword?}', [ArticleController::class, 'saveSearchToSession'])->name('save-search-to-session');
                Route::get('/', [ArticleController::class, 'search'])->name('search');
            });
        });

        Route::get('/get-folders/{id?}', [KnowledgeController::class, 'getFolders'])->name('get-folders');
        Route::get('/get-articles/{id?}', [KnowledgeController::class, 'getArticles'])->name('get-articles');
        Route::post('/folder', [KnowledgeController::class, 'createFolder'])->name('create-folder');
        Route::put('/rename/{knowledge}', [KnowledgeController::class, 'renameFolder'])->name('rename-folder');
        Route::put('/move/{knowledge}', [KnowledgeController::class, 'moveKnowledge'])->name('move-knowledge');
        Route::delete('/delete/{knowledge}', [KnowledgeController::class, 'delete'])->name('delete');
    });

    // Workflows API Routes
    Route::prefix('workflows')->name('workflows.')->group(function () {
        Route::get('/created-workflow-lists', [WorkflowController::class, 'getCreatedWorkflowLists'])->name('created-workflow-lists');
        Route::get('/workflow-for-you-lists', [WorkflowController::class, 'getWorkflowForYouLists'])->name('workflow-for-you-lists');
        Route::patch('/{id}/cancel-application', [WorkflowController::class, 'cancelApplication'])->name('cancel-application');
        Route::post('save', [WorkflowController::class, 'saveWorkflow'])->name('save-workflow');

        Route::prefix('approver')->name('approver.')->group(function () {
            Route::get('list/{neo}', [WorkflowController::class, 'getApproverList'])->name('list');
        });

        Route::prefix('validate')->name('validate.')->group(function () {
            Route::post('/', [WorkflowController::class, 'validateWorkflow'])->name('validate-workflow');
            Route::post('approver', [WorkflowController::class, 'validateWorkflowApprover'])->name('validate-workflow-approver');
        });

        // Upload-related API Routes
        Route::prefix('file')->name('file.')->group(function () {
            Route::post('/process', [WorkflowController::class, 'processUpload'])->name('process-upload');
            Route::patch('/', [WorkflowController::class, 'processChunk'])->name('chunk');
            Route::delete('/', [WorkflowController::class, 'revertUpload'])->name('revert');
        });

        Route::get('/get-attachment-link/{id}/{type}', [WorkflowController::class, 'getLinkDisplayForAttachment'])->name('get-display-attachment-link');

        // Workflow action
        Route::patch('/{id}/action', [WorkflowActionController::class, 'updateReaction'])->name('updateReaction');
    });
});

// Authentication API Routes
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('check', [LoginController::class, 'checkAuth'])->name('check');
});

// CM Sign API Routes
Route::prefix('cm-sign')->name('cm-sign.')->group(function () {
    Route::post('update-contract-status', [ElectronicContractApiController::class, 'updateContractStatus'])->name('update-contract-status')->middleware('cm_com_sign_service');
});
