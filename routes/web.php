<?php

use App\Http\Controllers\AccountsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EslonController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\McuController;
use App\Http\Controllers\Setting;
use App\Http\Controllers\Sp3Controller;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubLayananController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserManagementController;
use App\Models\Simrs\BillingSimrs;
use App\Models\Simrs\RegMultiPoliSimrs;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

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

/** for side bar menu active */
function set_active($route)
{
    if (is_array($route)) {
        return in_array(Request::path(), $route) ? 'active' : '';
    }
    return Request::path() == $route ? 'active' : '';
}

Route::get('/', function () {
    return view('auth.login');
});

Route::group(['middleware' => 'auth'], function () {
    Route::get('home', function () {
        return view('home');
    });
    Route::get('home', function () {
        return view('home');
    });
});
Route::get('/test-db', function () {
    try {
        $conn = RegMultiPoliSimrs::where('reg_no', "A012600138")->get();
        dd($conn);
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

Auth::routes();
Route::group(['namespace' => 'App\Http\Controllers\Auth'], function () {
    // ----------------------------login ------------------------------//
    Route::controller(LoginController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'authenticate');
        Route::get('/logout', 'logout')->name('logout');
        Route::post('change/password', 'changePassword')->name('change/password');
    });

    // ----------------------------- register -------------------------//
    Route::controller(RegisterController::class)->group(function () {
        Route::get('/register', 'register')->name('register');
        Route::post('/register', 'storeUser')->name('register');
    });
});

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    // -------------------------- main dashboard ----------------------//
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'index')->middleware('auth')->name('home');
        Route::get('user/profile/page', 'userProfile')->middleware('auth')->name('user/profile/page');
        Route::get('teacher/dashboard', 'teacherDashboardIndex')->middleware('auth')->name('teacher/dashboard');
        Route::get('student/dashboard', 'studentDashboardIndex')->middleware('auth')->name('student/dashboard');
    });

    // ----------------------------- user controller ---------------------//
    Route::controller(UserManagementController::class)->group(function () {
        Route::get('list/users', 'index')->middleware('auth')->name('list/users');
        Route::post('change/password', 'changePassword')->name('change/password');
        Route::get('view/user/edit/{id}', 'userView')->middleware('auth');
        Route::post('user/update', 'userUpdate')->name('user/update');
        Route::post('user/delete', 'userDelete')->name('user/delete');
        Route::get('get-users-data', 'getUsersData')->name('get-users-data');
        /** get all data users */
    });

    // ------------------------ setting -------------------------------//
    Route::controller(Setting::class)->group(function () {
        Route::get('setting/page', 'index')->middleware('auth')->name('setting/page');
    });

    // ------------------------ student -------------------------------//
    Route::controller(StudentController::class)->group(function () {
        Route::get('student/list', 'student')->middleware('auth')->name('student/list'); // list student
        Route::get('student/grid', 'studentGrid')->middleware('auth')->name('student/grid'); // grid student
        Route::get('student/add/page', 'studentAdd')->middleware('auth')->name('student/add/page'); // page student
        Route::post('student/add/save', 'studentSave')->name('student/add/save'); // save record student
        Route::get('student/edit/{id}', 'studentEdit'); // view for edit
        Route::post('student/update', 'studentUpdate')->name('student/update'); // update record student
        Route::post('student/delete', 'studentDelete')->name('student/delete'); // delete record student
        Route::get('student/profile/{id}', 'studentProfile')->middleware('auth'); // profile student
    });

    // ------------------------ sp3 -------------------------------//
    Route::controller(Sp3Controller::class)->group(function () {
        //sp3 verifikasi
        Route::get('sp3-verifikasi/list', 'index')->middleware('auth')->name('sp3-verifikasi/list'); // list sp3
        Route::get('sp3/add/page', 'create')->middleware('auth')->name('sp3/add/page'); // page sp3
        Route::get('sp3/add/page-tagihan-keluar', 'createSp3TagihanKeluar')->middleware('auth')->name('sp3/add/page/tagihan-keluar'); // page sp3
        Route::get('sp3/add/page-deposit', 'createSp3Deposit')->middleware('auth')->name('sp3/add/page/deposit'); // page sp3
        Route::get('sp3/add/page-deposit/{slug}', 'listAddDepositSp3')->middleware('auth')->name('sp3/add/page/list-deposit'); // page sp3
        Route::get('sp3/add/page-mcu', 'createSp3Mcu')->middleware('auth')->name('sp3/add/page/mcu'); // page sp3
        Route::get('sp3/add/page-mcu/{slug}', 'listAddMcuSp3')->middleware('auth')->name('sp3/add/page/list-mcu'); // page sp3
        Route::post('sp3/add/save', 'store')->middleware('auth')->name('sp3/add/save'); // save record sp3
        Route::post('sp3/add/save/tagihan-keluar', 'storeSp3TagihanKeluar')->middleware('auth')->name('sp3/add/save/tagihan-keluar'); // save record sp3
        Route::post('sp3/add/save/deposit', 'storeSp3Deposito')->middleware('auth')->name('sp3/add/save/deposit'); // save record sp3
        Route::post('sp3/add/save/mcu', 'storeSp3Mcu')->middleware('auth')->name('sp3/add/save/mcu'); // save record sp3
        Route::get('sp3/edit/{slug}', 'edit')->middleware('auth'); // view for edit
        Route::get('sp3/refresh/{slug}', 'updateDataBilling')->middleware('auth')->name('sp3/refresh'); // update record sp3
        Route::post('sp3/update/{slug}', 'update')->middleware('auth')->name('sp3/update'); // update record sp3
        Route::post('sp3/update/tagihan-keluar/{slug}', 'updateTagihanKeluar')->middleware('auth')->name('sp3/update/tagihan-keluar'); // update record sp3
        Route::post('sp3/update/deposito/{slug}', 'updateDeposito')->middleware('auth')->name('sp3/update/deposito'); // update record sp3
        Route::post('sp3/update/mcu/{slug}', 'updateMcu')->middleware('auth')->name('sp3/update/mcu'); // update record sp3
        Route::get('sp3/detail/{slug}', 'listBillSp3')->middleware('auth')->name('sp3/detail'); // view for edit
        Route::post('sp3/delete', 'destroy')->middleware('auth')->name('sp3/delete'); // delete record sp3
        Route::get('sp3/approve/{slug}', 'approveSp3')->middleware('auth'); // view for edit
        Route::get('sp3/unapprove/{slug}', 'unapproveSp3')->middleware('auth'); // view for edit
        Route::get('get-sp3-verifikasi-data', 'getSp3VerifikasiData')->middleware('auth')->name('get-sp3-verifikasi-data'); // get data sp3
        Route::get('get-deposit-data', 'getDepositData')->middleware('auth')->name('get-deposit-data'); // get data sp3
        Route::get('/sp3/{slug}/preview', 'previewSp3')->middleware('auth')->name('preview/pdf');

        //sp3 Keuangan
        Route::get('sp3-keuangan/list', 'index')->middleware('auth')->name('sp3-keuangan/list'); // list sp3
        Route::get('get-sp3-keuangan-data', 'getSp3KeuanganData')->middleware('auth')->name('get-sp3-keuangan-data'); // get data sp3
    });

    // ------------------------ billing -------------------------------//
    Route::controller(BillingController::class)->group(function () {
        Route::post('billing/save/cob', 'addCob')->middleware('auth')->name('billing/add/save/cob'); //add cob billing
        Route::get('billing/approve/{slug}', 'approveBill')->middleware('auth'); // view for edit
        Route::get('billing/unapprove/{slug}', 'unapproveBill')->middleware('auth')->name('billing/unapprove'); // view for edit
        Route::get('billing-verifikasi/list', 'index')->middleware('auth')->name('billing-verifikasi/list'); // list billing
        Route::get('billing-keuangan/list', 'index')->middleware('auth')->name('billing-keuangan/list'); // list billing
        Route::get('billing/add/page', 'create')->middleware('auth')->name('billing/add/page'); // page billing
        Route::post('billing/add/save', 'store')->middleware('auth')->name('billing/add/save'); // save record billing
        Route::get('billing/edit/{slug}', 'edit')->middleware('auth'); // view for edit
        Route::post('billing/update/{slug}', 'update')->middleware('auth')->name('billing/update'); // update record billing
        Route::post('billing/delete', 'destroy')->middleware('auth')->name('billing/delete'); // delete record billing
        Route::get('billing/{slugSp3}/{noReg}', 'storeDeposit')->middleware('auth')->name('billing/store-deposit'); // store deposit billing sp3
        Route::get('billing/mcu/{slugSp3}/{noReg}', 'storeMcu')->middleware('auth')->name('billing/store-mcu'); // store deposit billing sp3
        Route::get('get-billings-verifikasi-data', 'getBillingsVerifikasiData')->middleware('auth')->name('get-billings-verifikasi-data'); // get data billings
        Route::get('get-billings-billings-sp3/{slug}', 'getBillingsSp3Data')->middleware('auth')->name('get-billings-sp3-data'); // get data billings
        Route::get('detail-billing/{slug}', 'listTindakanBill')->middleware('auth')->name('detail-billing'); // get data billings
    });

    // ------------------------ MCU -------------------------------//
    Route::controller(McuController::class)->group(function () {
        Route::get('get-mcu-data', 'getRegMcuData')->middleware('auth')->name('get-mcu-data'); // get data sp3
    });




    // ------------------------ eselon -------------------------------//
    Route::controller(EslonController::class)->group(function () {
        Route::get('eselon/list', 'index')->middleware('auth')->name('eselon/list'); // list eselon
        Route::get('eselon/add/page', 'create')->middleware('auth')->name('eselon/add/page'); // page eselon
        Route::post('eselon/add/save', 'store')->middleware('auth')->name('eselon/add/save'); // save record eselon
        Route::get('eselon/edit/{slug}', 'edit')->middleware('auth'); // view for edit
        Route::post('eselon/update/{slug}', 'update')->middleware('auth')->name('eselon/update'); // update record eselon
        Route::post('eselon/delete', 'destroy')->middleware('auth')->name('eselon/delete'); // delete record eselon
        Route::get('get-eselons-data', 'getEslonsData')->middleware('auth')->name('get-eselons-data'); // get data eslons
    });
    // ------------------------ layanan -------------------------------//
    Route::controller(LayananController::class)->group(function () {
        Route::get('layanan/list', 'index')->middleware('auth')->name('layanan/list'); // list layanan
        Route::get('layanan/add/page', 'create')->middleware('auth')->name('layanan/add/page'); // page layanan
        Route::post('layanan/add/save', 'store')->middleware('auth')->name('layanan/add/save'); // save record layanan
        Route::get('layanan/edit/{slug}', 'edit')->middleware('auth'); // view for edit
        Route::post('layanan/update/{slug}', 'update')->middleware('auth')->name('layanan/update'); // update record layanan
        Route::post('layanan/delete', 'destroy')->middleware('auth')->name('layanan/delete'); // delete record layanan
        Route::get('get-layanans-data', 'getLayanansData')->middleware('auth')->name('get-layanans-data'); // get data layanans
    });
    // ------------------------ sub layanan -------------------------------//
    Route::controller(SubLayananController::class)->group(function () {
        Route::get('sub-layanan/list', 'index')->middleware('auth')->name('sub-layanan/list'); // list sub layanan
        Route::get('sub-layanan/add/page', 'create')->middleware('auth')->name('sub-layanan/add/page'); // page sub layanan
        Route::post('sub-layanan/add/save', 'store')->middleware('auth')->name('sub-layanan/add/save'); // save record sub layanan
        Route::get('sub-layanan/edit/{slug}', 'edit')->middleware('auth'); // view for edit
        Route::post('sub-layanan/update/{slug}', 'update')->middleware('auth')->name('sub-layanan/update'); // update record sub layanan
        Route::post('sub-layanan/delete', 'destroy')->middleware('auth')->name('sub-layanan/delete'); // delete record sub layanan
        Route::get('get-sub-layanans-data', 'getSubLayanansData')->middleware('auth')->name('get-sub-layanans-data'); // get data sub layanans
    });

    // ------------------------ teacher -------------------------------//
    Route::controller(TeacherController::class)->group(function () {
        Route::get('teacher/add/page', 'teacherAdd')->middleware('auth')->name('teacher/add/page'); // page teacher
        Route::get('teacher/list/page', 'teacherList')->middleware('auth')->name('teacher/list/page'); // page teacher
        Route::get('teacher/grid/page', 'teacherGrid')->middleware('auth')->name('teacher/grid/page'); // page grid teacher
        Route::post('teacher/save', 'saveRecord')->middleware('auth')->name('teacher/save'); // save record
        Route::get('teacher/edit/{user_id}', 'editRecord'); // view teacher record
        Route::post('teacher/update', 'updateRecordTeacher')->middleware('auth')->name('teacher/update'); // update record
        Route::post('teacher/delete', 'teacherDelete')->name('teacher/delete'); // delete record teacher
    });

    // ----------------------- department -----------------------------//
    Route::controller(DepartmentController::class)->group(function () {
        Route::get('department/list/page', 'departmentList')->middleware('auth')->name('department/list/page'); // department/list/page
        Route::get('department/add/page', 'indexDepartment')->middleware('auth')->name('department/add/page'); // page add department
        Route::get('department/edit/{department_id}', 'editDepartment'); // page add department
        Route::post('department/save', 'saveRecord')->middleware('auth')->name('department/save'); // department/save
        Route::post('department/update', 'updateRecord')->middleware('auth')->name('department/update'); // department/update
        Route::post('department/delete', 'deleteRecord')->middleware('auth')->name('department/delete'); // department/delete
        Route::get('get-data-list', 'getDataList')->name('get-data-list'); // get data list

    });

    // ----------------------- subject -----------------------------//
    Route::controller(SubjectController::class)->group(function () {
        Route::get('subject/list/page', 'subjectList')->middleware('auth')->name('subject/list/page'); // subject/list/page
        Route::get('subject/add/page', 'subjectAdd')->middleware('auth')->name('subject/add/page'); // subject/add/page
        Route::post('subject/save', 'saveRecord')->name('subject/save'); // subject/save
        Route::post('subject/update', 'updateRecord')->name('subject/update'); // subject/update
        Route::post('subject/delete', 'deleteRecord')->name('subject/delete'); // subject/delete
        Route::get('subject/edit/{subject_id}', 'subjectEdit'); // subject/edit/page
    });

    // ----------------------- invoice -----------------------------//
    Route::controller(InvoiceController::class)->group(function () {
        Route::get('invoice/list/page', 'invoiceList')->middleware('auth')->name('invoice/list/page'); // subjeinvoicect/list/page
        Route::get('invoice/paid/page', 'invoicePaid')->middleware('auth')->name('invoice/paid/page'); // invoice/paid/page
        Route::get('invoice/overdue/page', 'invoiceOverdue')->middleware('auth')->name('invoice/overdue/page'); // invoice/overdue/page
        Route::get('invoice/draft/page', 'invoiceDraft')->middleware('auth')->name('invoice/draft/page'); // invoice/draft/page
        Route::get('invoice/recurring/page', 'invoiceRecurring')->middleware('auth')->name('invoice/recurring/page'); // invoice/recurring/page
        Route::get('invoice/cancelled/page', 'invoiceCancelled')->middleware('auth')->name('invoice/cancelled/page'); // invoice/cancelled/page
        Route::get('invoice/grid/page', 'invoiceGrid')->middleware('auth')->name('invoice/grid/page'); // invoice/grid/page
        Route::get('invoice/add/page', 'invoiceAdd')->middleware('auth')->name('invoice/add/page'); // invoice/add/page
        Route::post('invoice/add/save', 'saveRecord')->name('invoice/add/save'); // invoice/add/save
        Route::post('invoice/update/save', 'updateRecord')->name('invoice/update/save'); // invoice/update/save
        Route::post('invoice/delete', 'deleteRecord')->name('invoice/delete'); // invoice/delete
        Route::get('invoice/edit/{invoice_id}', 'invoiceEdit')->middleware('auth')->name('invoice/edit/page'); // invoice/edit/page
        Route::get('invoice/view/{invoice_id}', 'invoiceView')->middleware('auth')->name('invoice/view/page'); // invoice/view/page
        Route::get('invoice/settings/page', 'invoiceSettings')->middleware('auth')->name('invoice/settings/page'); // invoice/settings/page
        Route::get('invoice/settings/tax/page', 'invoiceSettingsTax')->middleware('auth')->name('invoice/settings/tax/page'); // invoice/settings/tax/page
        Route::get('invoice/settings/bank/page', 'invoiceSettingsBank')->middleware('auth')->name('invoice/settings/bank/page'); // invoice/settings/bank/page
    });

    // ----------------------- accounts ----------------------------//
    Route::controller(AccountsController::class)->group(function () {
        Route::get('account/fees/collections/page', 'index')->middleware('auth')->name('account/fees/collections/page'); // account/fees/collections/page
        Route::get('add/fees/collection/page', 'addFeesCollection')->middleware('auth')->name('add/fees/collection/page'); // add/fees/collection
        Route::post('fees/collection/save', 'saveRecord')->middleware('auth')->name('fees/collection/save'); // fees/collection/save
    });
});
