<?php

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

Route::get('/', 'Auth\LoginController@index');
Route::post('/loginuser', 'Auth\LoginController@ceklogin');
Route::post('/otpvalidate', 'ValidateOtpController@otpvalidate');
Route::post('/resetpassword', 'ValidateOtpController@resetpassword');
Route::get('/scankey', 'ValidateOtpController@firstlogin');
Route::get('/authenticate', 'ValidateOtpController@authenticate');
Route::get('logout', 'Auth\LoginController@logout');
Route::get('logout_v2', 'Auth\LoginController@logoutBaru');

Auth::routes();

Route::middleware(['auth', 'SingleSession'])->group(function () {

    Route::get('/home', 'HomeController@index');
    Route::post('/get_mories', 'HomeController@getMories');
    Route::get('/createuser', 'CreateUserController@index');
    Route::post('/admin/adduser', 'CreateUserController@create');

    // -- MEMBER -- //
    // All Member
    Route::middleware(['MenuPermission:1'])->group(function () {
        Route::get('/member/all_member', 'member\AllMemberController@index');
        Route::post('/member/get_all_member', 'member\AllMemberController@getAllMember');
        Route::get('/member/get_detail_member/{mbr_code}', 'member\AllMemberController@getDetailMember');
        Route::get('/member/block_member/{mbr_code}', 'member\AllMemberController@blockMember');
        Route::post('/member/ubah_member/', 'member\AllMemberController@ubahMember');
        Route::get('/member/active_member/{mbr_code}', 'member\AllMemberController@activeMember');
        Route::get('/member/get_detail_member/{mbr_code}', 'member\AllMemberController@getDetailMember');
    });

    // Request Upgrade Member
    Route::middleware(['MenuPermission:2'])->group(function () {
        Route::get('/member/request_upgrade_member', 'member\UpgradeMemberController@index');
        Route::post('/member/get_all_upgrade_member', 'member\UpgradeMemberController@getAllUpgradeMember');
        Route::post('/member/add_upgrade_member','member\UpgradeMemberController@addUpgradeMember');
        Route::post('/member/approve_member','member\UpgradeMemberController@approveMember');
        Route::post('/member/reject_member','member\UpgradeMemberController@rejectMember');
    });

    // Upgrade Limit Transaksi
    Route::middleware(['MenuPermission:3'])->group(function () {
        Route::get('/member/request_upgrade_limit', 'member\UpgradeLimitController@index');
        Route::post('/member/get_request_transaksi', 'member\UpgradeLimitController@getDataTable');
        Route::post('/member/approve_request_transaksi', 'member\UpgradeLimitController@Approve');
        Route::post('/member/reject_request_transaksi', 'member\UpgradeLimitController@Reject');
        Route::post('/member/add_upgrade_limit', 'member\UpgradeLimitController@addUpgrade');
    });

    // Member Reseller
    Route::middleware(['MenuPermission:4'])->group(function () {
        Route::get('/member/member_reseller', 'member\MemberResellerController@index');
        Route::post('/member/get_member_reseller', 'member\MemberResellerController@getMemberReseller');
        Route::get('/member/block_member_reseller/{mbr_code}', 'member\MemberResellerController@blockReseller');
        Route::get('/member/active_member_reseller/{mbr_code}', 'member\MemberResellerController@activeReseller');
        Route::get('/member/surrender_member_reseller/{mbr_code}', 'member\MemberResellerController@surrenderReseller');
        Route::get('/member/get_detail_member_reseller/{mbr_code}', 'member\MemberResellerController@getDetailMemberReseller');
        Route::post('/member/ubah_member_reseller', 'member\MemberResellerController@ubahMemberReseller');
        Route::post('/member/tambah_member_reseller', 'member\MemberResellerController@tambahMemberReseller');
    });

    // Member Device Info
    Route::middleware(['MenuPermission:16'])->group(function () {
        Route::get('/member/device_info', 'member\DeviceInfoController@index');
        Route::post('/member/get_all_device_info', 'member\DeviceInfoController@getAllDeviceInfo');
    });

    // Member Device Info
    Route::middleware(['MenuPermission:19'])->group(function () {
        Route::get('/member/log_login_member', 'member\LogLoginMemberController@index');
        Route::post('/member/get_all_log_login_member', 'member\LogLoginMemberController@getAllLogLoginMember');
        Route::post('/member/get_detail_log_login_member', 'member\LogLoginMemberController@getDetailLogLoginMember');
    });
    // -- END MEMBER -- //

    // -- PRODUCT -- //
    // Product Lists
    Route::middleware(['MenuPermission:5'])->group(function () {
        Route::get('/product/list_product', 'product\ListProductController@index');
        Route::post('/product/get_all_product', 'product\ListProductController@getAllProduct');
        Route::post('/product/add_product', 'product\ListProductController@addProduct');
        Route::post('/product/update_product', 'product\ListProductController@updateProduct');
        Route::post('/product/delete_product', 'product\ListProductController@deleteProduct');
        Route::get('/product/get_product_by_id/{id}', 'product\ListProductController@getProductById');
    });

    // Provider List
    Route::middleware(['MenuPermission:7'])->group(function () {
        Route::get('/product/list_provider', 'product\ListProviderController@index');
        Route::post('/product/get_all_provider', 'product\ListProviderController@getAllProvider');
        Route::get('/product/get_detail_provider/{id}', 'product\ListProviderController@getDetailProvider');
        Route::post('/product/insert_provider','product\ListProviderController@insertProvider');
        Route::post('/product/update_provider','product\ListProviderController@updateProvider');
        Route::post('/product/delete_provider','product\ListProviderController@deleteProvider');
    });

    // Supplier List
    Route::middleware(['MenuPermission:14'])->group(function () {
        Route::get('/product/list_supplier', 'product\ListSupplierController@index');
        Route::post('/product/get_all_supplier', 'product\ListSupplierController@getAllSupplier');
        Route::get('/product/get_detail_supplier/{id}', 'product\ListSupplierController@getDetailSupplier');
        Route::post('/product/insert_supplier','product\ListSupplierController@insertSupplier');
        Route::post('/product/update_supplier','product\ListSupplierController@updateSupplier');
        Route::post('/product/delete_supplier','product\ListSupplierController@deleteSupplier');
    });
    // -- END PRODUCT -- //

    // -- TRANSAKSI -- //
    // List Mutasi Saldo
    Route::middleware(['MenuPermission:8'])->group(function () {
        Route::get('/transaksi/list_mutasi_saldo', 'transaksi\ListMutasiSaldoController@index');
        Route::post('/transaksi/get_list_mutasi_saldo', 'transaksi\ListMutasiSaldoController@getListMutasiSaldo');
    });

    // List Deposit
    Route::middleware(['MenuPermission:9'])->group(function () {
        Route::get('/transaksi/list_deposit', 'transaksi\ListDepositController@index');
        Route::post('/transaksi/get_list_deposit', 'transaksi\ListDepositController@getListDeposit');
    });

    // List Deposit Approval
    Route::middleware(['MenuPermission:10'])->group(function () {
        Route::get('/transaksi/list_deposit_approval', 'transaksi\ListDepositApprovalController@index');
        Route::post('/transaksi/get_list_deposit_approval', 'transaksi\ListDepositApprovalController@getListDepositApproval');
        Route::get('/transaksi/approve_deposit_approval/{deposit_id}', 'transaksi\ListDepositApprovalController@approveDeposit');
        Route::get('/transaksi/reject_deposit_approval/{deposit_id}', 'transaksi\ListDepositApprovalController@rejectDeposit');
    });

    // List Transaksi
    Route::middleware(['MenuPermission:11'])->group(function () {
        Route::get('/transaksi/list_transaksi', 'transaksi\ListTransaksiController@index');
        Route::post('/transaksi/get_list_transaksi', 'transaksi\ListTransaksiController@getListTransaksi');
        Route::post('/transaksi/update_status_refund', 'transaksi\ListTransaksiController@updateStatusRefund');
    });
    // Antrian Transaksi
    Route::middleware(['MenuPermission:17'])->group(function () {
        Route::get('/transaksi/antrian_transaksi', 'transaksi\AntrianTransaksiController@getData');
        Route::post('/transaksi/get_antrian_transaksi', 'transaksi\AntrianTransaksiController@getAntrianTransaksi');
        Route::post('/transaksi/get_transaksi_sukses', 'transaksi\AntrianTransaksiController@getTransaksiSukses');
        Route::post('/transaksi/update_status_transaksi', 'transaksi\AntrianTransaksiController@updateStatusTransaksi');
        //Route::post('/transaksi/update_status_gagal', 'transaksi\AntrianTransaksiController@updateStatusTransaksiGagal');
    });
    // Input Manual Transaksi
    Route::middleware(['MenuPermission:24'])->group(function () {
        Route::get('/transaksi/input_manual_transaksi', 'transaksi\InputManualTransaksiController@index');
        Route::post('/transaksi/get_produk_prabayar', 'transaksi\InputManualTransaksiController@getProdukPrabayar');
        Route::post('/transaksi/get_produk_pascabayar', 'transaksi\InputManualTransaksiController@getProdukPascabayar');
        Route::post('/transaksi/buy_produk_prabayar', 'transaksi\InputManualTransaksiController@buyProdukPrabayar');
        Route::post('/transaksi/cek_produk_pascabayar', 'transaksi\InputManualTransaksiController@cekProdukPascabayarAPI');
        Route::post('/transaksi/buy_produk_pascabayar', 'transaksi\InputManualTransaksiController@buyProdukPascabayar');
        Route::post('/transaksi/mbr_code_check', 'transaksi\InputManualTransaksiController@mbrCodeCheck');
    });
    // -- END TRANSAKSI -- //

    // -- YAYASAN -- //
    // List Yayasan
    Route::middleware(['MenuPermission:12'])->group(function () {
        Route::get('/yayasan/list_yayasan', 'yayasan\ListYayasanController@index');
        Route::post('/yayasan/get_all_yayasan', 'yayasan\ListYayasanController@getDataYayasan');
        Route::get('/yayasan/get_yayasan_by_id/{id}', 'yayasan\ListYayasanController@getYayasanById');
        Route::get('/yayasan/get_saldo_by_id/{id}', 'yayasan\ListYayasanController@getSaldoById');
        Route::post('/yayasan/add_yayasan', 'yayasan\ListYayasanController@addYayasan');
        Route::post('/yayasan/update_yayasan', 'yayasan\ListYayasanController@updateYayasan');
        Route::post('/yayasan/delete_yayasan', 'yayasan\ListYayasanController@deleteYayasan');
    });
    // END YAYASAN //

    // -- USER CRM -- //
    // User CRM
    Route::middleware(['MenuPermission:13'])->group(function () {
        Route::get('/user/user_crm', 'user\ListUserController@index');
        Route::get('/user/get_user_by_id/{id}', 'user\ListUserController@getUserById');
        Route::get('/user/reset_auth/{id}', 'user\ListUserController@resetAuth');
        Route::post('/user/get_all_user', 'user\ListUserController@getDataUser');
        Route::post('/user/add_user', 'user\ListUserController@addDataUser');
        Route::post('/user/update_user', 'user\ListUserController@updateUser');
        Route::post('/user/delete_user', 'user\ListUserController@deleteDataUser');
        Route::post('/user/update_password', 'user\ListUserController@resetPassword');
    });

    // Create Role User
    Route::middleware(['MenuPermission:15'])->group(function () {
        Route::get('/user/role_user', 'user\CreateRoleController@index');
        Route::get('/user/get_role_id/{id}', 'user\CreateRoleController@getRoleID');
        Route::get('/user/delete_role/{id}', 'user\CreateRoleController@deleteRole');
        Route::get('/user/get_permission_by_id/{id}', 'user\CreateRoleController@getPermissionByID');
        Route::post('/user/get_all_role', 'user\CreateRoleController@getDataTable');
        Route::post('/user/add_role', 'user\CreateRoleController@addRole');
        Route::post('/user/add_permission', 'user\CreateRoleController@addPermission');
        Route::post('/user/update_role', 'user\CreateRoleController@updateRole');
    });

    // Create Role User
    Route::middleware(['MenuPermission:26'])->group(function () {
        Route::get('/user/manajemen_log_login', 'user\ManajemenLogLoginController@index');
        Route::post('/user/get_log_login', 'user\ManajemenLogLoginController@getData');
        
    });

    // Create Menu Crm
    Route::middleware(['MenuPermission:18'])->group(function () {
        Route::get('/user/list_menu', 'user\CreateMenuController@index');
        Route::get('/user/delete_menu/{id}', 'user\CreateMenuController@deleteMenu');
        Route::get('/user/delete_submenu/{id}', 'user\CreateMenuController@deleteSubMenu');
        Route::post('/user/get_data_menu', 'user\CreateMenuController@getDataTable');
        Route::post('/user/data_menu', 'user\CreateMenuController@getDataMenu');
        Route::post('/user/add_menu', 'user\CreateMenuController@addMenu');
        Route::post('/user/add_submenu', 'user\CreateMenuController@addSubMenu');
        Route::post('/user/update_menu', 'user\CreateMenuController@updateMenu');
        Route::post('/user/update_submenu', 'user\CreateMenuController@updateSubMenu');
    });

    // Manajamen Log Activity
    Route::middleware(['MenuPermission:25'])->group(function () {
        Route::get('/user/manajemen_log_activity', 'user\ManajemenLogActivityController@index');
        Route::post('/user/get_activity_detail', 'user\ManajemenLogActivityController@getActivityDetail');
        Route::post('/user/tambah_activity_detail', 'user\ManajemenLogActivityController@tambahActivityDetail');
        Route::post('/user/ubah_activity_detail', 'user\ManajemenLogActivityController@ubahActivityDetail');
        Route::post('/user/hapus_activity_detail', 'user\ManajemenLogActivityController@hapusActivityDetail');
        Route::post('/user/get_log_activity', 'user\ManajemenLogActivityController@getLogActivity');
    });
    // END USER CRM //

    // LAPORAN
    // Penjualan Berdasarkan Produk
    Route::middleware(['MenuPermission:20'])->group(function () {
        Route::get('/laporan/penjualan_berdasarkan_product', 'laporan\PenjualanBerdasarkanProdukController@index');
        Route::post('/laporan/get_penjualan_berdasarkan_product', 'laporan\PenjualanBerdasarkanProdukController@getPenjualanBerdasarkanProduk');
        Route::get('/laporan/penjualan_berdasarkan_supplier', 'laporan\PenjualanSupplierController@index');
        Route::post('/laporan/get_penjualan_berdasarkan_supplier', 'laporan\PenjualanSupplierController@getPenjualanSupplier');
    });
    // Refund Dana Saldo
    Route::middleware(['MenuPermission:22'])->group(function () {
        Route::get('/laporan/refund_dana_saldo', 'laporan\RefundDanaSaldoController@index');
        Route::post('/laporan/get_refund_dana', 'laporan\RefundDanaSaldoController@getDataRefund');
    });
    // LAPORAN Refund Transaksi
    Route::middleware(['MenuPermission:23'])->group(function () {
        Route::get('/laporan/refund_dana_transaksi','laporan\RefundTransaksiController@index');
        Route::post('/laporan/get_refund_transaksi', 'laporan\RefundTransaksiController@getRefundTransaksi');
    });
    // Mutasi Bonus Transaksi
    Route::middleware(['MenuPermission:27'])->group(function () {
        Route::get('/laporan/mutasi_bonus_transaksi','laporan\MutasiBonusTransaksiController@index');
        Route::post('/laporan/get_mutasi_bonus_transaksi','laporan\MutasiBonusTransaksiController@getMutasiBonusTransaksi');
    });
    // END LAPORAN //

    // Other
    Route::get('getcity','member\UpgradeMemberController@GetCity');

    // Example
    Route::get('example/sendemail', 'ExampleController@sendEmail');
    Route::get('example/library', 'ExampleController@cobaLib');
    Route::get('example/coba', 'ExampleController@index');
    Route::post('example/cek_respon', 'ExampleController@cekRespon');
    Route::post('/example/coba_key', 'ExampleController@cekKey');

    Route::get('example/mails', function(){
        return view('mails.mail_users');
    });
});
