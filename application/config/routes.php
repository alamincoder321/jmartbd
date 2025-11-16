<?php
defined('BASEPATH') or exit('No direct script access allowed');

$route['default_controller'] = 'Page';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['logout'] = 'Login/logout';

$route['Administrator'] = 'Administrator/Page';
$route['module/(:any)'] = 'Administrator/Page/module/$1';
$route['brachAccess/(:any)'] = 'Administrator/Login/brach_access/$1';
$route['getBrachAccess'] = 'Administrator/Login/branch_access_main_admin';

$route['get_categories'] = 'Administrator/Page/getCategories';
$route['category'] = 'Administrator/Page/add_category';
$route['insertcategory'] = 'Administrator/Page/insert_category';
$route['catupdate'] = 'Administrator/Page/catupdate';

$route['catdelete'] = 'Administrator/page/catdelete';

$route['get_subcategories'] = 'Administrator/Page/getsubCategories';
$route['subcategory'] = 'Administrator/Page/add_subcategory';
$route['insertsubcategory'] = 'Administrator/Page/insert_subcategory';
$route['subcatdelete'] = 'Administrator/page/subcatdelete';




$route['get_brands'] = 'Administrator/Page/getBrands';
$route['brand'] = 'Administrator/Page/add_brand';
$route['insertbrand'] = 'Administrator/Page/insert_brand';
$route['editbrand/(:any)'] = 'Administrator/Page/brandedit/$1';
$route['updateBrand'] = 'Administrator/Page/Update_brand';
$route['branddelete'] = 'Administrator/Page/branddelete';


// Assets Info===========
$route['AssetsEntry']                 = 'Administrator/Assets';
$route['insertassets']                 = 'Administrator/Assets/insert_Assets';
$route['assetsEdit/(:any)']         = 'Administrator/Assets/Assets_edit/$1';
$route['assetsUpdate/(:any)']         = 'Administrator/Assets/Update_Assets/$1';
$route['assetsDelete/(:any)']         = 'Administrator/Assets/Assets_delete/$1';
$route['get_assets_cost']             = 'Administrator/Assets/getAssetsCost';
$route['assets_report']                = 'Administrator/Assets/assetsReport';
$route['get_group_assets']            = 'Administrator/Assets/getGroupAssets';
$route['get_assets_report']            = 'Administrator/Assets/getAssetsReport';

$route['unit'] = 'Administrator/Page/unit';
$route['insertunit'] = 'Administrator/Page/insert_unit';
$route['unitedit/(:any)'] = 'Administrator/Page/unitedit/$1';
$route['unitupdate'] = 'Administrator/Page/unitupdate';
$route['unitdelete'] = 'Administrator/Page/unitdelete';
$route['get_units'] = 'Administrator/Page/getUnits';

$route['area'] = 'Administrator/Page/area';
$route['insertarea'] = 'Administrator/Page/insert_area';
$route['areadelete'] = 'Administrator/Page/areadelete';
$route['areaedit/(:any)'] = 'Administrator/Page/areaedit/$1';
$route['areaupdate'] = 'Administrator/Page/areaupdate';
$route['get_districts'] = 'Administrator/Page/getDistricts';

$route['product'] = 'Administrator/Products';
$route['add_product'] = 'Administrator/Products/addProduct';
$route['update_product'] = 'Administrator/Products/updateProduct';
$route['delete_product'] = 'Administrator/Products/deleteProduct';
$route['productlist'] = 'Administrator/Reports/productlist';
$route['currentStock'] = 'Administrator/Products/current_stock';
$route['get_products']    =    'Administrator/Products/getProducts';
$route['get_product_stock']    =    'Administrator/Products/getProductStock';
$route['get_current_stock']    =    'Administrator/Products/getCurrentStock';
$route['get_total_stock']    =    'Administrator/Products/getTotalStock';
$route['product_ledger']    =    'Administrator/Products/productLedger';
$route['get_product_ledger']    =    'Administrator/Products/getProductLedger';
$route['reorder_list']    =    'Administrator/Reports/reOrderList';

$route['totalStock'] = 'Administrator/Products/total_stock';
$route['totalStockPrint'] = 'Administrator/Reports/total_stock';

// discount fixed target
$route['discount_entry']             = 'Administrator/DiscountFixed';
$route['add_product_discount']       = 'Administrator/DiscountFixed/addProductDiscount';
$route['get_check_discount_product'] = 'Administrator/DiscountFixed/checkDiscountProduct';


// $route['GenerateBarcode/(:any)'] = 'BarcodeController/barcode_create/$1';
$route['barcode/(:any)'] = 'BarcodeController/barcode_create/$1';


$route['supplier'] = 'Administrator/Supplier';
$route['add_supplier'] = 'Administrator/Supplier/addSupplier';
$route['supplieredit'] = 'Administrator/Supplier/supplier_edit/';
$route['update_supplier'] = 'Administrator/Supplier/updateSupplier';
$route['supplierList'] = 'Administrator/Reports/supplierList';
$route['delete_supplier'] = 'Administrator/Supplier/deleteSupplier';

$route['customer'] = 'Administrator/Customer';
$route['add_customer'] = 'Administrator/Customer/addCustomer';
$route['customeredit/(:any)'] = 'Administrator/Customer/customeredit/$1';
$route['update_customer'] = 'Administrator/Customer/updateCustomer';
$route['customerlist'] = 'Administrator/Customer/customerlist';
$route['delete_customer'] = 'Administrator/Customer/deleteCustomer';
$route['get_customers'] = 'Administrator/Customer/getCustomers';
$route['get_customer_due'] = 'Administrator/Customer/getCustomerDue';
$route['get_customer_ledger'] = 'Administrator/Customer/getCustomerLedger';
$route['get_customer_payments'] = 'Administrator/Customer/getCustomerPayments';
$route['add_customer_payment'] = 'Administrator/Customer/addCustomerPayment';
$route['update_customer_payment'] = 'Administrator/Customer/updateCustomerPayment';
$route['delete_customer_payment'] = 'Administrator/Customer/deleteCustomerPayment';

$route['customerPaymentPage'] = 'Administrator/Customer/customerPaymentPage';
$route['customer_payment_history'] = 'Administrator/Customer/customerPaymentHistory';


$route['get_purchases'] = 'Administrator/Purchase/getPurchases';
$route['get_purchasedetails'] = 'Administrator/Purchase/getPurchaseDetails';
$route['get_purchasedetails_for_return'] = 'Administrator/Purchase/getPurchaseDetailsForReturn';
$route['add_purchase_return'] = 'Administrator/Purchase/addPurchaseReturn';
$route['update_purchase_return'] = 'Administrator/Purchase/updatePurchaseReturn';
$route['get_purchase_return_details'] = 'Administrator/Purchase/getPurchaseReturnDetails';
$route['purchase'] = 'Administrator/Purchase/order';
$route['purchase/(:any)'] = 'Administrator/Purchase/purchaseEdit/$1';
$route['purchaseExcel'] = 'Administrator/Purchase/purchaseExcel';
$route['excelFileFormate'] = 'Administrator/Purchase/excelFileFormate';
$route['createProductSheet'] = 'Administrator/Purchase/createProductSheet';
$route['productSheetCart'] = 'Administrator/Addcart/productSheetCart';
$route['ajaxCartRemoveProductSheet'] = 'Administrator/Addcart/ajax_cart_remove_productSheet';
$route['SelectSupplier'] = 'Administrator/Purchase/Selectsuplier';
$route['SelectCat'] = 'Administrator/Purchase/SelectCat';
$route['SelectPruduct'] = 'Administrator/Purchase/SelectPruduct';
$route['purchase_invoice_print/(:any)'] = 'Administrator/Purchase/purchaseInvoicePrint/$1';
$route['add_purchase'] = 'Administrator/Purchase/addPurchase';
$route['update_purchase'] = 'Administrator/Purchase/updatePurchase';
$route['purchaseInvoice'] = 'Administrator/Purchase/purchase_bill';
$route['PurchaseInvoicePrint'] = 'Administrator/Reports/Purchase_invoice';
$route['purchaseRecord'] = 'Administrator/Purchase/purchase_record';
$route['get_purchase_record'] = 'Administrator/Purchase/getPurchaseRecord';
$route['delete_purchase'] = 'Administrator/Purchase/deletePurchase';
$route['supplierDue'] = 'Administrator/Supplier/supplier_due';
$route['supplierPayment'] = 'Administrator/Supplier/supplierPaymentPage';
$route['supplierDuePrint'] = 'Administrator/Reports/search_supplier_due';
$route['searchSupplierDue'] = 'Administrator/Supplier/search_supplier_due';
$route['supplierPaymentReport'] = 'Administrator/Supplier/supplier_payment_report';
$route['searchSupplierPayments'] = 'Administrator/Supplier/search_supplier_payments';
$route['get_suppliers'] = 'Administrator/Supplier/getSuppliers';
$route['get_supplier_due'] = 'Administrator/Supplier/getSupplierDue';
$route['get_supplier_ledger'] = 'Administrator/Supplier/getSupplierLedger';
$route['get_supplier_payments'] = 'Administrator/Supplier/getSupplierPayments';
$route['add_supplier_payment'] = 'Administrator/Supplier/addSupplierPayment';
$route['update_supplier_payment'] = 'Administrator/Supplier/updateSupplierPayment';
$route['delete_supplier_payment'] = 'Administrator/Supplier/deleteSupplierPayment';
$route['supplierPaymentPrint'] = 'Administrator/Reports/supplier_payment_print';

$route['purchaseReturns'] = 'Administrator/Purchase/returns';
$route['purchaseReturns/(:any)'] = 'Administrator/Purchase/purchaseReturnEdit/$1';
$route['PurchasereturnSearch'] = 'Administrator/Purchase/PurchasereturnSearch';
$route['PurchaseReturnInsert'] = 'Administrator/Purchase/PurchaseReturnInsert';
$route['returnsList'] = 'Administrator/Purchase/returns_list';
$route['purchaseReturnRecord'] = 'Administrator/Purchase/purchase_return_record';
$route['purchaseReturnlist'] = 'Administrator/Reports/purchase_returnlist';
$route['get_purchase_returns'] = 'Administrator/Purchase/getPurchaseReturns';
$route['purchase_return_invoice/(:any)'] = 'Administrator/Purchase/purchaseReturnInvoice/$1';
$route['delete_purchase_return'] = 'Administrator/Purchase/deletePurchaseReturn';
$route['purchase_return_details'] = 'Administrator/Purchase/purchaseReturnDetails';
$route['check_purchase_return/(:any)'] = 'Administrator/Purchase/checkPurchaseReturn/$1';

$route['damageEntry'] = 'Administrator/Purchase/damage_entry';
$route['add_damage'] = 'Administrator/Purchase/addDamage';
$route['update_damage'] = 'Administrator/Purchase/updateDamage';
$route['delete_damage'] = 'Administrator/Purchase/deleteDamage';
$route['get_damages'] = 'Administrator/Purchase/getDamages';
$route['damageList'] = 'Administrator/Purchase/damage_product_list';
$route['SelectDamageProduct'] = 'Administrator/Purchase/damage_select_product';

// hold sale
$route['hold_sale/(:any)'] = 'Administrator/Sales/holdSale/$1';
$route['add_hold_sale'] = 'Administrator/Sales/addHoldSale';
$route['get_hold_sale'] = 'Administrator/Sales/getHoldSale';

$route['sales/(:any)'] = 'Administrator/Sales/index/$1';
$route['sales/(:any)/(:any)'] = 'Administrator/Sales/salesEdit/$1/$2';
$route['salesinvoice'] = 'Administrator/Sales/sales_invoice';
$route['cheque/entry_from'] = 'Administrator/Check/view_check_entry_from';
$route['sale_cheque_store'] = 'Administrator/Check/sale_cheque_store';
$route['salesInvoicePrint'] = 'Administrator/Reports/sales_invoice';
$route['add_sales'] = 'Administrator/Sales/addSales';
$route['get_sales'] = 'Administrator/Sales/getSales';
$route['order_status'] = 'Administrator/Sales/orderStatus';
$route['get_sales_record'] = 'Administrator/Sales/getSalesRecord';
$route['get_saledetails'] = 'Administrator/Sales/getSaleDetails';
$route['update_sales'] = 'Administrator/Sales/updateSales';
$route['delete_sales'] = 'Administrator/Sales/deleteSales';
$route['get_saledetails_for_return'] = 'Administrator/Sales/getSaleDetailsForReturn';
$route['add_sales_return'] = 'Administrator/Sales/addSalesReturn';
$route['update_sales_return'] = 'Administrator/Sales/updateSalesReturn';
$route['delete_sale_return'] = 'Administrator/Sales/deleteSaleReturn';
$route['get_sale_returns'] = 'Administrator/Sales/getSaleReturns';
$route['get_sale_return_details'] = 'Administrator/Sales/getSaleReturnDetails';
$route['sale_return_invoice/(:any)'] = 'Administrator/Sales/saleReturnInvoice/$1';
$route['sale_return_details'] = 'Administrator/Sales/saleReturnDetails';
$route['check_sale_return/(:any)'] = 'Administrator/Sales/checkSaleReturn/$1';

// Combo Package
$route['combo_package'] = 'Administrator/Combo_package';
$route['combo_package/(:any)'] = 'Administrator/Combo_package/combo_package/$1';
$route['add_combo_package'] = 'Administrator/Combo_package/addComboPackage';
$route['update_combo_package'] = 'Administrator/Combo_package/updateComboPackage';
$route['delete_combo_package'] = 'Administrator/Combo_package/deleteComboPackage';
$route['get_combo_packages'] = 'Administrator/Combo_package/getComboPackages';
$route['combo_package_details'] = 'Administrator/Combo_package/combo_package_details';
$route['get_combo_packages'] = 'Administrator/Combo_package/getComboPackageDetails';
$route['update_combo_package_stock'] = 'Administrator/Combo_package/update_combo_package_stock';

$route['get_packages'] = 'Administrator/Combo_package/getPackages';



$route['sale_invoice_print/(:any)'] = 'Administrator/Sales/saleInvoicePrint/$1';
$route['craditlimit'] = 'Administrator/Sales/craditlimit/';
$route['salesrecord'] = 'Administrator/Sales/sales_record';
$route['sales_record_print/(:any)'] = 'Administrator/Reports/sales_record_print/$1';
$route['customerwiseSalesPrint'] = 'Administrator/Reports/customerwise_sales';
$route['productwiseSales'] = 'Administrator/Sales/productwise_sales';
$route['productwiseSalesPrint'] = 'Administrator/Reports/productwise_sales';
$route['customerPaymentReport'] = 'Administrator/Customer/customer_payment_report';
$route['customerPaymentReport/(:any)'] = 'Administrator/Customer/customer_payment_report/$1';
$route['customerPaymentReportPrint'] = 'Administrator/Reports/customer_payment_print';
$route['invoiceProductDetails'] = 'Administrator/Sales/invoice_product_list';
$route['invoiceProductList'] = 'Administrator/Sales/invoice_product_list_search';
$route['invoiceProductPrint'] = 'Administrator/Reports/branchwise_invoice_product_list';
$route['chalan/(:any)'] = 'Administrator/Sales/chalan/$1';



//Quotation================
$route['quotation']         = 'Administrator/Quotation';
$route['quotation/(:any)']         = 'Administrator/Quotation/editQuotation/$1';
$route['add_quotation']  = 'Administrator/Quotation/addQuotation';
$route['update_quotation']  = 'Administrator/Quotation/updateQuotation';
$route['delete_quotation']  = 'Administrator/Quotation/deleteQuotation';
$route['quotation_record']  = 'Administrator/Quotation/quotationRecord';
$route['get_quotations']  = 'Administrator/Quotation/getQuotations';
$route['quotationReport']   = 'Administrator/Quotation/quotation_report';
$route['quotation_invoice/(:any)']   = 'Administrator/Quotation/quotationInvoice/$1';
$route['quotation_invoice_report'] = 'Administrator/Quotation/quotationInvoiceReport';
$route['DeleteQuotationInvoice'] = 'Administrator/Quotation/delete_quotation_invoice';




$route['salesReturn'] = 'Administrator/Sales/salesreturn';
$route['salesReturn/(:any)'] = 'Administrator/Sales/salesReturnEdit/$1';
$route['salesreturnSearch'] = 'Administrator/Sales/salesreturnSearch';
$route['SalesReturnInsert'] = 'Administrator/Sales/SalesReturnInsert';
$route['returnList'] = 'Administrator/Sales/return_list';
$route['salesReturnRecord'] = 'Administrator/Sales/sales_return_record';
$route['salesreturnlist'] = 'Administrator/Reports/salesreturnlist';

$route['profitLoss'] = 'Administrator/Sales/profitLoss';
$route['profitLossSearch'] = 'Administrator/Sales/profitLossSearch';
$route['get_profit_loss'] = 'Administrator/Sales/getProfitLoss';
$route['profitLossPrint'] = 'Administrator/Reports/profitLossPrint';

$route['customerDue'] = 'Administrator/Customer/customer_due';
$route['cusDuePrint/(:any)'] = 'Administrator/Reports/cusDuePrint/$1';
$route['searchCustomerPayments'] = 'Administrator/Customer/search_customer_payments';
$route['paymentAndReport/(:any)'] = 'Administrator/Customer/paymentAndReport/$1';
$route['customerDuePaymentPrint'] = 'Administrator/Reports/customer_due_payment';

$route['user'] = 'Administrator/User_management';
$route['get_users'] = 'Administrator/User_management/getUsers';
$route['get_all_users'] = 'Administrator/User_management/getAllUsers';
$route['add_user'] = 'Administrator/User_management/user_Insert';
$route['update_user'] = 'Administrator/User_management/userupdate';
$route['delete_user'] = 'Administrator/User_management/userDelete';
$route['change_user_status'] = 'Administrator/User_management/userstatusChange';
$route['check_username'] = 'Administrator/User_management/check_user_name';
$route['access/(:any)'] = 'Administrator/User_management/user_access/$1';
$route['get_user_access'] = 'Administrator/User_management/getUserAccess';
$route['profile'] = 'Administrator/User_management/profile';
$route['profile_update'] = 'Administrator/User_management/profileUpdate';
$route['password_change'] = 'Administrator/User_management/password_change';
$route['define_access/(:any)'] = 'Administrator/User_management/define_access/$1';
$route['add_user_access'] = 'Administrator/User_management/addUserAccess';
$route['user_activity'] = 'Administrator/User_management/userActivity';
$route['get_user_activity'] = 'Administrator/User_management/getUserActivity';
$route['delete_user_activity'] = 'Administrator/User_management/deleteUserActivity';
$route['userName'] = 'Administrator/User_management/all_user_name';

$route['brunch'] = 'Administrator/Page/brunch';
$route['add_branch'] = 'Administrator/Page/addBranch';
$route['update_branch'] = 'Administrator/Page/updateBranch';
$route['brunchEdit'] = 'Administrator/Page/brunch_edit';
$route['brunchUpdate'] = 'Administrator/Page/brunch_update';
$route['brunchDelete'] = 'Administrator/Page/brunch_delete';
$route['get_branches'] = 'Administrator/Page/getBranches';
$route['get_current_branch'] = 'Administrator/Page/getCurrentBranch';
$route['change_branch_status'] = 'Administrator/Page/changeBranchStatus';

$route['companyProfile'] = 'Administrator/Page/company_profile';
$route['company_profile_Update'] = 'Administrator/Page/company_profile_Update';
$route['company_profile_insert'] = 'Administrator/Page/company_profile_insert';
$route['get_company_profile'] = 'Administrator/Page/getCompanyProfile';

$route['employee'] = 'Administrator/employee';
$route['get_employees'] = 'Administrator/Employee/getEmployees';
$route['employeeInsert'] = 'Administrator/Employee/employee_insert/';
$route['emplists/(:any)'] = 'Administrator/Employee/emplists/$1';
$route['employeeEdit/(:any)'] = 'Administrator/Employee/employee_edit/$1';
$route['employeeUpdate'] = 'Administrator/Employee/employee_Update';
$route['employeeDelete'] = 'Administrator/Employee/employee_Delete';
$route['employeeActive'] = 'Administrator/Employee/active';

//salary Payment
$route['salary_payment']                = 'Administrator/Employee/employeePayment';
$route['check_payment_month']           = 'Administrator/Employee/checkPaymentMonth';
$route['get_payments']                  = 'Administrator/Employee/getPayments';
$route['get_salary_details']            = 'Administrator/Employee/getSalaryDetails';
$route['add_salary_payment']            = 'Administrator/Employee/saveSalaryPayment';
$route['update_salary_payment']         = 'Administrator/Employee/updateSalaryPayment';
$route['salary_payment_report']         = 'Administrator/Employee/employeePaymentReport';
$route['delete_payment']                = 'Administrator/Employee/deletePayment';

$route['designation'] = 'Administrator/Employee/designation/';
$route['insertDesignation'] = 'Administrator/Employee/insert_designation';
$route['designationedit/(:any)'] = 'Administrator/Employee/designationedit/$1';
$route['designationUpdate'] = 'Administrator/Employee/designationupdate/';
$route['designationdelete'] = 'Administrator/Employee/designationdelete';

$route['depertment'] = 'Administrator/Employee/depertment';
$route['insertDepertment'] = 'Administrator/Employee/insert_depertment';
$route['depertmentdelete'] = 'Administrator/Employee/depertmentdelete/';
$route['depertmentedit/(:any)'] = 'Administrator/Employee/depertmentedit/$1';
$route['depertmentupdate'] = 'Administrator/Employee/depertmentupdate';

$route['month'] = 'Administrator/Employee/month';
$route['insertMonth'] = 'Administrator/Employee/insert_month';
$route['editMonth/(:any)'] = 'Administrator/Employee/editMonth/$1';
$route['updateMonth'] = 'Administrator/Employee/updateMonth';
$route['get_months'] = 'Administrator/Employee/getMonths';

$route['get_cash_transactions'] = 'Administrator/Account/getCashTransactions';
$route['cashTransaction'] = 'Administrator/Account/cash_transaction';
$route['get_cash_transaction_code'] = 'Administrator/Account/getCashTransactionCode';
$route['add_cash_transaction'] = 'Administrator/Account/addCashTransaction';
$route['update_cash_transaction'] = 'Administrator/Account/updateCashTransaction';
$route['delete_cash_transaction'] = 'Administrator/Account/deleteCashTransaction';
$route['transactionEdit'] = 'Administrator/Account/cash_transaction_edit';
$route['viewTransaction/(:any)'] = 'Administrator/Account/viewTransaction/$1';

// cash transfer
$route['cash_transfer'] = 'Administrator/Account/cash_transfer';
$route['get_cash_transfers'] = 'Administrator/Account/getCashTransfer';
$route['add_cash_transfer'] = 'Administrator/Account/addCashTransfer';
$route['update_cash_transfer'] = 'Administrator/Account/updateCashTransfer';
$route['delete_cash_transfer'] = 'Administrator/Account/deleteCashTransfer';
$route['approve_cash_transfer'] = 'Administrator/Account/approveCashTransfer';


$route['account'] = 'Administrator/Account';
$route['add_account'] = 'Administrator/Account/addAccount';
$route['accountEdit'] = 'Administrator/Account/account_edit';
$route['update_account'] = 'Administrator/Account/updateAccount';
$route['delete_account'] = 'Administrator/Account/deleteAccount';
$route['get_accounts'] = 'Administrator/Account/getAccounts';
$route['get_cash_and_bank_balance'] = 'Administrator/Account/getCashAndBankBalance';

$route['TransactionReport'] = 'Administrator/Account/all_transaction_report';
$route['TransactionReportSearch'] = 'Administrator/Account/transaction_report_search';
$route['transactionReportPrint'] = 'Administrator/Reports/transaction_report_print';
$route['bank_transaction_report'] = 'Administrator/Account/bankTransactionReprot';
$route['get_other_income_expense'] = 'Administrator/Account/getOtherIncomeExpense';

$route['cashView'] = 'Administrator/Account/cash_view';
$route['cash_ledger'] = 'Administrator/Account/cashLedger';
$route['get_cash_ledger'] = 'Administrator/Account/getCashLedger';
$route['cashPrint'] = 'Administrator/Reports/cashview_print';
$route['cashStatment'] = 'Administrator/Reports/cashStatment';
$route['cashStatmentList'] = 'Administrator/Reports/cashStatmentList';
$route['cashStatmentListPrint'] = 'Administrator/Reports/cashStatmentListPrint';
$route['day_book'] = 'Administrator/Reports/dayBook';
$route['special_report1'] = 'Administrator/Reports/specialReport';
$route['special_report'] = 'Administrator/Reports/specialReport1';
$route['special_report_all_data'] = 'Administrator/Reports/special_report_all_data';


$route['BalanceSheet'] = 'Administrator/Reports/balanceSheet';
$route['balance_sheet'] = 'Administrator/Reports/balance_sheet';
$route['get_balance_sheet'] = 'Administrator/Reports/getBalanceSheet';
$route['balanceSheetList'] = 'Administrator/Reports/balanceSheetList';
$route['balanceSheetListPrint'] = 'Administrator/Reports/balanceSheetListPrint';


$route['price_list'] = 'Administrator/Reports/price_list';
$route['price_list_report'] = 'Administrator/Reports/price_list_report';
$route['price_listprint'] = 'Administrator/Reports/price_list_print';


$route['bank'] = 'Administrator/Account/add_bank';
$route['insertBank'] = 'Administrator/Account/insert_Bank';
$route['bankEdit/(:any)'] = 'Administrator/Account/Bankedit/$1';
$route['updateBank'] = 'Administrator/Account/Update_Bank';
$route['bankDelete'] = 'Administrator/Account/Bankdelete';

$route['check/pending/list']           = 'Administrator/Check/check_pendaing_date_list';
$route['check/reminder/list']          = 'Administrator/Check/check_reminder_date_list';
$route['check/dis/list']               = 'Administrator/Check/check_dishonor_date_list';
$route['check/paid/list']              = 'Administrator/Check/check_paid_date_list';
$route['check/list']                   = 'Administrator/Check/check_list';
$route['check/paid/submit/(:any)']     = 'Administrator/Check/check_paid_submission/$1';
$route['check/dishonor/submit/(:any)'] = 'Administrator/Check/check_dishonor_submission/$1';
$route['check/entry']                  = 'Administrator/Check/check_entry_page';
$route['check/store']                  = 'Administrator/Check/check_date_store';
$route['check/view/(:any)']            = 'Administrator/Check/check_view_page/$1';
$route['check/edit/(:any)']            = 'Administrator/Check/check_edit_page/$1';
$route['check/update/(:any)']          = 'Administrator/Check/check_update_info/$1';
$route['check/delete/(:any)']          = 'Administrator/Check/check_delete_info/$1';

// Transfer
$route['product_transfer']        = 'Administrator/Transfer/productTransfer';
$route['product_transfer/(:any)'] = 'Administrator/Transfer/transferEdit/$1';
$route['add_product_transfer']    = 'Administrator/Transfer/addProductTransfer';
$route['update_product_transfer'] = 'Administrator/Transfer/updateProductTransfer';
$route['delete_transfer']         = 'Administrator/Transfer/deleteTransfer';
$route['transfer_list']           = 'Administrator/Transfer/transferList';
$route['get_transfers']           = 'Administrator/Transfer/getTransfers';
$route['get_transfer_details']    = 'Administrator/Transfer/getTransferDetails';
$route['received_list']           = 'Administrator/Transfer/receivedList';
$route['get_receives']            = 'Administrator/Transfer/getReceives';
$route['transfer_invoice/(:any)'] = 'Administrator/Transfer/transferInvoice/$1';
$route['receivedTransfer']        = 'Administrator/Transfer/receivedTransfer';

// Banks
$route['bank_accounts'] = 'Administrator/Account/bankAccounts';
$route['add_bank_account'] = 'Administrator/Account/addBankAccount';
$route['update_bank_account'] = 'Administrator/Account/updateBankAccount';
$route['get_bank_accounts'] = 'Administrator/Account/getBankAccounts';
$route['change_account_status'] = 'Administrator/Account/changeAccountStatus';

// Bank Transactions
$route['bank_transactions'] = 'Administrator/Account/bankTransactions';
$route['add_bank_transaction'] = 'Administrator/Account/addBankTransaction';
$route['update_bank_transaction'] = 'Administrator/Account/updateBankTransaction';
$route['get_bank_transactions'] = 'Administrator/Account/getBankTransactions';
$route['get_all_bank_transactions'] = 'Administrator/Account/getAllBankTransactions';
$route['remove_bank_transaction'] = 'Administrator/Account/removeBankTransaction';
$route['get_bank_balance'] = 'Administrator/Account/getBankBalance';

$route['cash_view'] = 'Administrator/Account/cashView';
$route['bank_ledger'] = 'Administrator/Account/bankLedger';

// Graph
$route['graph'] = 'Administrator/Graph/graph';
$route['get_graph_data'] = 'Administrator/Graph/getGraphData';

// SMS
$route['sms'] = 'Administrator/SMS';
$route['send_sms'] = 'Administrator/SMS/sendSms';
$route['send_bulk_sms'] = 'Administrator/SMS/sendBulkSms';
$route['sms_settings'] = 'Administrator/SMS/smsSettings';
$route['get_sms_settings'] = 'Administrator/SMS/getSmsSettings';
$route['save_sms_settings'] = 'Administrator/SMS/saveSmsSettings';

$route['user_login'] = 'Login/userLogin';
$route['database_backup'] = 'Administrator/Page/databaseBackup';

// Loan
$route['loan_transactions'] = 'Administrator/Loan/loanTransactions';
$route['get_loan_transactions'] = 'Administrator/Loan/getLoanTransactions';
$route['get_loan_initial_balance'] = 'Administrator/Loan/getLoanInitialBalance';
$route['add_loan_transaction'] = 'Administrator/Loan/addLoanTransaction';
$route['update_loan_transaction'] = 'Administrator/Loan/updateLoanTransaction';
$route['remove_loan_transaction'] = 'Administrator/Loan/removeLoanTransaction';
$route['get_loan_balance'] = 'Administrator/Loan/getLoanBalance';
$route['loan_view'] = 'Administrator/Loan/loanView';
$route['loan_transaction_report'] = 'Administrator/Loan/loanTransactionReprot';
$route['get_all_loan_transactions'] = 'Administrator/Loan/getAllLoanTransactions';
$route['loan_ledger'] = 'Administrator/Loan/loanLedger';


//loan account
$route['loan_accounts'] = 'Administrator/Loan/loanAccounts';
$route['add_loan_account'] = 'Administrator/Loan/addLoanAccount';
$route['update_loan_account'] = 'Administrator/Loan/updateLoanAccount';
$route['get_loan_accounts'] = 'Administrator/Loan/getLoanAccounts';
$route['change_loan_account_status'] = 'Administrator/Loan/changeLoanAccountStatus';


//investment
$route['investment_transactions'] = 'Administrator/Invest/investmentTransactions';
$route['get_investment_transactions'] = 'Administrator/Invest/getInvestmentTransactions';
$route['add_investment_transaction'] = 'Administrator/Invest/addInvestmentTransaction';
$route['update_investment_transaction'] = 'Administrator/Invest/updateInvestmentTransaction';
$route['remove_investment_transaction'] = 'Administrator/Invest/removeInvestmentTransaction';
$route['get_investment_balance'] = 'Administrator/Invest/getInvestmentBalance';
$route['investment_view'] = 'Administrator/Invest/investmentView';
$route['investment_transaction_report'] = 'Administrator/Invest/investmentTransactionReprot';
$route['get_all_investment_transactions'] = 'Administrator/Invest/getAllInvestmentTransactions';
$route['investment_ledger'] = 'Administrator/Invest/investmentLedger';


//investment account
$route['investment_account'] = 'Administrator/Invest/investmentAccount';
$route['add_investment_account'] = 'Administrator/Invest/addInvestmentAccount';
$route['update_investment_account'] = 'Administrator/Invest/updateInvestmentAccount';
$route['delete_investment_account'] = 'Administrator/Invest/deleteInvestmentAccount';
$route['get_investment_accounts'] = 'Administrator/Invest/getInvestmentAccounts';

//exchange route
$route['exchange'] = 'Administrator/Exchange';
$route['get_exchanges'] = 'Administrator/Exchange/getExchanges';
$route['add_exchange'] = 'Administrator/Exchange/addExchange';
$route['delete_exchange'] = 'Administrator/Exchange/deleteExchange';
$route['exchange_record'] = 'Administrator/Exchange/exchange_record';

// vehicle route
$route['vehicle'] = 'Administrator/Vehicle';
$route['get_vehicles'] = 'Administrator/Vehicle/getVehicles';
$route['add_vehicle'] = 'Administrator/Vehicle/addVehicle';
$route['update_vehicle'] = 'Administrator/Vehicle/updateVehicle';
$route['vehicleList'] = 'Administrator/Vehicle/vehicleList';
$route['delete_vehicle'] = 'Administrator/Vehicle/deleteVehicle';



$route['update_customer_profile'] = 'Administrator/UpdateCustomer/updateCustomerProfile';
$route['slider'] = 'Administrator/Slider';
$route['update_or_create_slider'] = 'Administrator/Slider/updateSlider';
$route['get_sliders'] = 'Administrator/Slider/getSliders';
$route['delete_slider'] = 'Administrator/Slider/deleteSlider';

$route['feedback'] = 'Administrator/Feedback';
$route['get_feedbacks'] = 'Administrator/Feedback/getFeedback';
$route['updateStatus'] = 'Administrator/Feedback/updateStatus';


// International bill route
$route['international_supplier'] = 'Administrator/InternationalSupplier';
$route['get_international_suppliers'] = 'Administrator/InternationalSupplier/getSuppliers';
$route['add_international_supplier'] = 'Administrator/InternationalSupplier/addSupplier';
$route['update_international_supplier'] = 'Administrator/InternationalSupplier/updateSupplier';
$route['supplierList'] = 'Administrator/Reports/supplierList';
$route['delete_supplier'] = 'Administrator/Supplier/deleteSupplier';
$route['get_international_supplier_due'] = 'Administrator/InternationalSupplier/getSupplierDue';
$route['international_supplier_ledger'] = 'Administrator/InternationalSupplier/supplierPaymentLedger';
$route['get_international_supplier_ledger'] = 'Administrator/InternationalSupplier/getSupplierLedger';

$route['international_supplierPayment'] = 'Administrator/InternationalSupplier/supplierPaymentPage';
$route['get_international_supplier_payments'] = 'Administrator/InternationalSupplier/getSupplierPayments';
$route['add_international_supplier_payment'] = 'Administrator/InternationalSupplier/addSupplierPayment';
$route['update_international_supplier_payment'] = 'Administrator/InternationalSupplier/updateSupplierPayment';
$route['delete_international_supplier_payment'] = 'Administrator/InternationalSupplier/deleteSupplierPayment';
$route['international_supplier_payment_history'] = 'Administrator/InternationalSupplier/supplierPaymentHistory';
//internationalsupplier payment Invoice
$route['internationalsupplierPaymentInvoice/(:any)'] = 'Administrator/InternationalSupplier/supplierPaymentInvoice/$1';

$route['get_international_purchases'] = 'Administrator/InternationalPurchase/getPurchases';
$route['international_purchase'] = 'Administrator/InternationalPurchase/order';
$route['international_purchase/(:any)'] = 'Administrator/InternationalPurchase/purchaseEdit/$1';
$route['international_purchase_invoice_print/(:any)'] = 'Administrator/InternationalPurchase/purchaseInvoicePrint/$1';
$route['add_international_purchase'] = 'Administrator/InternationalPurchase/addPurchase';
$route['update_international_purchase'] = 'Administrator/InternationalPurchase/updatePurchase';
$route['international_purchaseInvoice'] = 'Administrator/InternationalPurchase/purchase_bill';
$route['international_purchaseRecord'] = 'Administrator/InternationalPurchase/purchase_record';
$route['get_international_purchase_record'] = 'Administrator/InternationalPurchase/getPurchaseRecord';
$route['delete_international_purchase'] = 'Administrator/InternationalPurchase/deletePurchase';
