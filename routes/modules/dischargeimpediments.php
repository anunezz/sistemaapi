<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DischargeImpedimentsController;

Route::group([
    'middleware'    => ['auth:api'],
    'prefix'        => 'discharge_of_impediments',
    'namespace'     => 'App\Http\Controllers'
], function(){
    Route::post('get_cats', [DischargeImpedimentsController::class,'get_cats']);
    Route::post('save', [DischargeImpedimentsController::class,'save']);
    Route::post('get_data', [DischargeImpedimentsController::class,'get_data']);
    Route::post('get_data_inbox_authorization_requests', [DischargeImpedimentsController::class,'get_data_inbox_authorization_requests']);
    Route::post('upload_file', [DischargeImpedimentsController::class,'upload_file']);
    Route::post('get_impediment', [DischargeImpedimentsController::class,'get_impediment']);
    Route::post('update', [DischargeImpedimentsController::class,'update']);
    Route::post('change_status', [DischargeImpedimentsController::class,'change_status']);
    Route::post('search_curp_user', [DischargeImpedimentsController::class,'searchCurpUser']);
    Route::post('get_cats_index', [DischargeImpedimentsController::class,'get_cats_index']);
    Route::post('get_cat_type_impediment', [DischargeImpedimentsController::class,'get_cat_type_impediment']);
    Route::post('send_to_validate', [DischargeImpedimentsController::class,'send_to_validate']);
    Route::post('get_data_inbox_validate_high', [DischargeImpedimentsController::class,'get_data_inbox_validate_high']);
    Route::post('send_to_authorize', [DischargeImpedimentsController::class,'send_to_authorize']);
    Route::post('search_impediment', [DischargeImpedimentsController::class,'search_impediment']);
    Route::post('get_data_inbox_validate_low', [DischargeImpedimentsController::class,'get_data_inbox_validate_low']);
    Route::post('get_data_authorization_high_impediments', [DischargeImpedimentsController::class,'get_data_authorization_high_impediments']);
    Route::post('send_to_active', [DischargeImpedimentsController::class,'send_to_active']);
    Route::post('get_data_response_impediments', [DischargeImpedimentsController::class,'get_data_response_impediments']);
    Route::post('select_impediment', [DischargeImpedimentsController::class,'select_impediment']);
    ################# RECHAZOS  ###########
    Route::post('rejection_validates', [DischargeImpedimentsController::class,'rejection_validates']);
    Route::post('reject_cancel', [DischargeImpedimentsController::class,'reject_cancel']);
    Route::post('reject_authorize', [DischargeImpedimentsController::class,'reject_authorize']);
    Route::post('reject_authorization_impediment', [DischargeImpedimentsController::class,'reject_authorization_impediment']);
    Route::post('reject_response_impediment', [DischargeImpedimentsController::class,'reject_response_impediment']);
    Route::post('send_to_pending', [DischargeImpedimentsController::class,'send_to_pending']);
    Route::post('send_to_for_rejecting', [DischargeImpedimentsController::class,'send_to_for_rejecting']);
    Route::post('search_impediment_low', [DischargeImpedimentsController::class,'search_impediment_low']);
    Route::post('create_impediment_low', [DischargeImpedimentsController::class,'create_impediment_low']);
    Route::post('get_data_authorization_low_impediments', [DischargeImpedimentsController::class,'get_data_authorization_low_impediments']);
    Route::post('send_to_active_low', [DischargeImpedimentsController::class,'send_to_active_low']);
    Route::post('send_to_confirm_low', [DischargeImpedimentsController::class,'send_to_confirm_low']);
    Route::post('create_impediment', [DischargeImpedimentsController::class,'create_impediment']);
    Route::post('get_data_inbox_validate_high_modify', [DischargeImpedimentsController::class,'get_data_inbox_validate_high_modify']);
    Route::post('create_impediment_modify', [DischargeImpedimentsController::class,'create_impediment_modify']);
    Route::post('get_data_inbox_verification', [DischargeImpedimentsController::class,'get_data_inbox_verification']);
    Route::post('print_impediment', [DischargeImpedimentsController::class,'print_impediment']);
    Route::post('get_data_consult_impediment', [DischargeImpedimentsController::class,'get_data_consult_impediment']);
    Route::post('get_only_impediment', [DischargeImpedimentsController::class,'get_only_impediment']);
    Route::post('get_data_inbox_work_assignation', [DischargeImpedimentsController::class,'get_data_inbox_work_assignation']);
    Route::post('update_request_impediments', [DischargeImpedimentsController::class,'update_request_impediments']);
    Route::post('assign_requests', [DischargeImpedimentsController::class,'assign_requests']);
    Route::post('desassign_requests', [DischargeImpedimentsController::class,'desassign_requests']);
    Route::post('delete_impediment', [DischargeImpedimentsController::class,'delete_impediment']);
    Route::post('if_exists_impediment', [DischargeImpedimentsController::class,'if_exists_impediment']);
    Route::get('get_cats_impediment', [DischargeImpedimentsController::class,'get_cats_impediment']);
    Route::post('impediment_update', [DischargeImpedimentsController::class,'impediment_update']);
    Route::post('download_request_report', [DischargeImpedimentsController::class,'download_request_report']);
    Route::post('getCausalSubCausalFromImpedimentsID', [DischargeImpedimentsController::class,'get_causal_subcausal_from_impediments_id']);
    Route::post('send_to_dictaminate', [DischargeImpedimentsController::class,'send_to_dictaminate']);
    Route::post('update_cuerpo_correo', [DischargeImpedimentsController::class,'update_cuerpo_correo']);
    Route::post('get_subcausal_plantilla', [DischargeImpedimentsController::class,'get_subcausal_plantilla']);
    Route::post('send_to_verify', [DischargeImpedimentsController::class,'send_to_verify']);
    Route::post('get_data_inbox_authorization_rejection', [DischargeImpedimentsController::class,'get_data_inbox_authorization_rejection']);
});

