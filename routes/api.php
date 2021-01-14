<?php

use Illuminate\Http\Request;

Route::middleware('auth:api')->get(
    '/user',
    function (Request $request) {
        return $request->user();
    }
);


// routes/api.php

Route::group(
    ['middleware' => ['jwt.auth', 'api-header']],
    function () {
        Route::get('status/{task_type}', 'TaskStatusController@index');
        Route::get('dashboard', 'DashboardController@index');
        Route::get('activity', 'ActivityController@index');

        //support
        Route::post('support/messages/send', 'SupportController');

        // company ledger
        Route::get('company_ledger', 'CompanyLedgerController@index')->name('company_ledger.index');

        // tokens
        Route::resource(
            'tokens',
            'TokenController'
        );// name = (tokens. index / create / show / update / destroy / edi

        Route::resource('promocodes', 'PromocodeController');

        Route::post('deals/{deal}/{action}', 'DealController@action')->name('invoices.action');
        Route::post('deals/sort', 'TaskController@sortTasks');
        Route::resource('deals', 'DealController');

        // subscription
        Route::resource('subscriptions', 'SubscriptionController');

        // expense categories
        Route::resource('expense-categories', 'ExpenseCategoryController');

        // brands
        Route::resource('brands', 'BrandController');

        // bank accounts
        Route::post('bank_accounts/ofx/preview', 'BankAccountController@preview');
        Route::post('bank_accounts/ofx/import', 'BankAccountController@import');
        Route::resource('bank_accounts', 'BankAccountController');

        // import
        Route::post('import', 'ImportController@import');
        Route::post('import/preview', 'ImportController@importPreview');
        Route::post('export', 'ImportController@export');

        // banks
        Route::resource('banks', 'BankController');

        //design
        Route::resource(
            'designs',
            'DesignController'
        );// name = (payments. index / create / show / update / destroy / edit

        // messages
        Route::get('messages/customers', 'MessageController@getCustomers');
        Route::get('messages/{customer_id}', 'MessageController@index');
        Route::post('messages', 'MessageController@store');

        //companies
        Route::get('companies', 'CompanyController@index');
        Route::post('companies/restore/{id}', 'CompanyController@restore');
        Route::post('companies', 'CompanyController@store');
        Route::delete('companies/archive/{company_id}', 'CompanyController@archive');
        Route::delete('companies/{company_id}', 'CompanyController@destroy');
        Route::get('companies/{company_id}', 'CompanyController@show');
        Route::put('companies/{company_id}', 'CompanyController@update');
        Route::get('industries', 'CompanyController@getIndustries');

//categories
        Route::get('categories', 'CategoryController@index');
        Route::post('categories', 'CategoryController@store');
        Route::delete('categories/{category_id}', 'CategoryController@destroy');
        Route::get('categories/{category_id}', 'CategoryController@edit');
        Route::put('categories/{category_id}', 'CategoryController@update');

// comments
        Route::get('comments/{entity}/{task_id}', 'CommentController@index');
        Route::delete('comments/{comment_id}', 'CommentController@destroy');
        Route::put('comments/{comment_id}', 'CommentController@update');
        Route::post('comments', 'CommentController@store');

// events
        Route::get('events', 'EventController@index');
        Route::delete('events/archive/{event_id}', 'EventController@archive');
        Route::delete('events/{event_id}', 'EventController@destroy');
        Route::put('events/{event_id}', 'EventController@update');
        Route::get('events/{event_id}', 'EventController@show');
        Route::post('events', 'EventController@store');
        Route::get('events/tasks/{task_id}', 'EventController@getEventsForTask');
        Route::get('events/users/{user_id}', 'EventController@getEventsForUser');
        Route::get('event-types', 'EventController@getEventTypes');
        Route::post(
            'events/filterEvents',
            'EventController@filterEvents'
        );
        Route::post('event/status/{event_id}', 'EventController@updateEventStatus');
        Route::post('events/restore/{id}', 'EventController@restore');

// products
        Route::get('products', 'ProductController@index');
        Route::post('products', 'ProductController@store');
        Route::post('products/bulk', 'ProductController@bulk');
        Route::delete('products/archive/{product_id}', 'ProductController@archive');
        Route::delete('products/{product_id}', 'ProductController@destroy');
        Route::post('products/removeImages', 'ProductController@removeThumbnail');
        Route::put('products/{product_id}', 'ProductController@update');
        Route::get('products/tasks/{task_id}/{status}', 'OrderController@getOrderForTask');
        Route::post('products/filterProducts', 'ProductController@filterProducts');
        Route::get('product/{slug}', 'ProductController@getProduct');
        Route::post('products/restore/{id}', 'ProductController@restore');

// projects
        Route::get('projects', 'ProjectController@index');
        Route::get('projects', 'ProjectController@index');
        Route::post('projects', 'ProjectController@store');
        Route::get('projects/{id}', 'ProjectController@show');
        Route::put('projects/{project_id}', 'ProjectController@update');
        Route::delete('projects/archive/{project_id}', 'ProjectController@archive');
        Route::delete('projects/{project_id}', 'ProjectController@destroy');
        Route::post('projects/restore/{id}', 'ProjectController@restore');

        //order
        Route::get('order', 'OrderController@index');
        Route::put('order/{order_id}', 'OrderController@update');
        Route::post('order', 'OrderController@store');
        Route::post('order/{order}/{action}', 'OrderController@action')->name('invoices.action');

// uploads
        Route::get('uploads/{entity}/{entity_id}', 'UploadController@index');
        Route::delete('uploads/{file_id}', 'UploadController@destroy');

// task status
        Route::get('taskStatus/search', 'TaskStatusController@search');
        Route::get('taskStatus', 'TaskStatusController@index');
        Route::post('taskStatus', 'TaskStatusController@store');
        Route::put('taskStatus/{id}', 'TaskStatusController@update');
        Route::delete('taskStatus/{status_id}', 'TaskStatusController@destroy');

        // invoice
        Route::post('invoice', 'InvoiceController@store');
        Route::delete('invoice/archive/{invoice_id}', 'InvoiceController@archive');
        Route::delete('invoice/{invoice_id}', 'InvoiceController@destroy');
        Route::post('invoice/restore/{id}', 'InvoiceController@restore');
        Route::post('invoice/bulk', 'InvoiceController@bulk');
        Route::get('invoice', 'InvoiceController@index');
        Route::get(
            'invoice/task/{task_id}',
            'InvoiceController@getInvoiceLinesForTask'
        );
        Route::get('invoice/{invoice_id}', 'InvoiceController@show');

        Route::put('invoice/{invoice_id}', 'InvoiceController@update');
        Route::get(
            'invoice/getInvoicesByStatus/{status}',
            'InvoiceController@getInvoicesByStatus'
        );
        Route::post('invoice/{invoice}/{action}', 'InvoiceController@action')->name('invoices.action');


//recurring invoice
        Route::post(
            'recurring-invoice',
            'RecurringInvoiceController@store'
        );
        Route::post(
            'recurring-invoice/bulk',
            'RecurringInvoiceController@bulk'
        );
        Route::put(
            'recurring-invoice/{id}',
            'RecurringInvoiceController@update'
        );
        Route::delete(
            'recurring-invoice/archive/{id}',
            'RecurringInvoiceController@archive'
        );
        Route::delete(
            'recurring-invoice/{id}',
            'RecurringInvoiceController@destroy'
        );
        Route::get(
            'recurring-invoice',
            'RecurringInvoiceController@index'
        );
        Route::post('recurringInvoice/restore/{id}', 'RecurringInvoiceController@restore');
        Route::post('recurring-invoice/{recurring_invoice}/{action}', 'RecurringInvoiceController@action')->name(
            'invoices.action'
        );

//recurring quote
        Route::put(
            'recurring-quote/{id}',
            'RecurringQuoteController@update'
        );
        Route::get('recurring-quote', 'RecurringQuoteController@index');
        Route::post('recurring-quote', 'RecurringQuoteController@store');
        Route::post(
            'recurring-quote/bulk',
            'RecurringQuoteController@bulk'
        );
        Route::delete(
            'recurring-quote/archive/{id}',
            'RecurringQuoteController@archive'
        );
        Route::delete(
            'recurring-quote/{id}',
            'RecurringQuoteController@destroy'
        );
        Route::post('recurringQuote/restore/{id}', 'RecurringQuoteController@restore');
        Route::post('recurring-quote/{recurring_quote}/{action}', 'RecurringQuoteController@action')->name(
            'invoices.action'
        );

//credit
        Route::post('credit', 'CreditController@store');
        Route::delete('credits/archive/{credit_id}', 'CreditController@archive');
        Route::delete('credits/{credit_id}', 'CreditController@destroy');
        Route::get('credits', 'CreditController@index');
        Route::put('credit/{credit_id}', 'CreditController@update');
        Route::post('credits/restore/{id}', 'CreditController@restore');
        Route::post('credit/{credit}/{action}', 'CreditController@action')->name('credits.action');
        Route::get(
            'credits/getCreditsByStatus/{status}',
            'CreditController@getCreditsByStatus'
        );


//expenses
        Route::post('expense', 'ExpenseController@store');
        Route::delete('expenses/archive/{expense_id}', 'ExpenseController@archive');
        Route::delete('expenses/{expense_id}', 'ExpenseController@destroy');
        Route::get('expenses', 'ExpenseController@index');
        Route::get('expenses/{expense_id}', 'ExpenseController@show');
        Route::put('expense/{expense_id}', 'ExpenseController@update');
        Route::post('expenses/restore/{id}', 'ExpenseController@restore');
        Route::post('expense/bulk', 'ExpenseController@bulk');

// quotes
        Route::get(
            'quotes/convert/{invoice_id}',
            'QuoteController@convert'
        );
        Route::delete('quote/archive/{quote_id}', 'QuoteController@archive');
        Route::delete('quote/{quote_id}', 'QuoteController@destroy');
        Route::get(
            'quotes/approve/{invoice_id}',
            'QuoteController@approve'
        );
        Route::post('quote', 'QuoteController@store');

        Route::put('quote/{quote_id}', 'QuoteController@update');
        Route::get('quote', 'QuoteController@index');
        Route::get('quote/{quote_id}', 'QuoteController@show');
        Route::post('quote/{quote}/{action}', 'QuoteController@action')->name('quotes.action');
        Route::get(
            'quotes/task/{task_id}',
            'QuoteController@getQuoteLinesForTask'
        );
        Route::post('quotes/restore/{id}', 'QuoteController@restore');

// purchase orders
        Route::delete('purchase_order/archive/{quote_id}', 'PurchaseOrderController@archive');
        Route::delete('purchase_order/{quote_id}', 'PurchaseOrderController@destroy');
        Route::post('purchase_order', 'PurchaseOrderController@store');

        Route::put('purchase_order/{purchase_order_id}', 'PurchaseOrderController@update');
        Route::get('purchase_order', 'PurchaseOrderController@index');
        Route::get('purchase_order/{quote_id}', 'PurchaseOrderController@show');
        Route::post('purchase_order/{purchase_order}/{action}', 'PurchaseOrderController@action')->name('quotes.action');
        Route::post('purchase_order/restore/{id}', 'PurchaseOrderController@restore');

//accounts
        Route::post('accounts', 'AccountController@store');
        Route::post(
            'accounts/fields',
            'AccountController@saveCustomFields'
        );
        Route::put('accounts/{id}', 'AccountController@update');
        Route::get('accounts/refresh', 'AccountController@refresh');
        Route::get(
            'accounts/fields/getAllCustomFields',
            'AccountController@getAllCustomFields'
        );
        Route::get('accounts/fields/{entity}', 'AccountController@getCustomFields');
        Route::get('accounts', 'AccountController@index');
        Route::get('accounts/{id}', 'AccountController@show');
        Route::get('dates', 'AccountController@getDateFormats');
        Route::delete('account/{account_id}', 'AccountController@destroy');


// email
        Route::post('emails', 'EmailController@send')->name('email.send');

        Route::post(
            'account/change',
            'AccountController@changeAccount'
        );

// groups
        Route::get('groups', 'GroupController@index');
        Route::get('group/{group_id}', 'GroupController@show');
        Route::delete('groups/archive/{group_id}', 'GroupController@archive');
        Route::delete('groups/{group_id}', 'GroupController@destroy');
        Route::put('groups/{group_id}', 'GroupController@update');
        Route::post('groups', 'GroupController@store');
        Route::post('groups/restore/{id}', 'GroupController@restore');

//template
        Route::post('template', 'TemplateController@show');

// company gateways
        Route::get('company_gateways', 'CompanyGatewayController@index');
        Route::get('company_gateways/{id}', 'CompanyGatewayController@show');
        Route::put('company_gateways/{id}', 'CompanyGatewayController@update');
        Route::post('company_gateways/', 'CompanyGatewayController@store');

// tax rates
        Route::get('taxRates', 'TaxRateController@index');
        Route::post('taxRates', 'TaxRateController@store');
        Route::delete('taxRates/archive/{taxRate_id}', 'TaxRateController@archive');
        Route::delete('taxRates/{taxRate_id}', 'TaxRateController@destroy');
        Route::get('taxRates/{taxRate_id}', 'TaxRateController@edit');
        Route::put('taxRates/{taxRate_id}', 'TaxRateController@update');
        Route::post('taxRate/restore/{id}', 'TaxRateController@restore');

//payments
        Route::get('payments', 'PaymentController@index');
        Route::get('payments/{payment_id}', 'PaymentController@show');
        Route::post('payments', 'PaymentController@store');
        Route::post('payments/bulk', 'PaymentController@bulk');
        Route::delete('payments/archive/{payment_id}', 'PaymentController@archive');
        Route::delete('payments/{payment_id}', 'PaymentController@destroy');
        Route::put('payments/{payment_id}', 'PaymentController@update');
        Route::post('payments/restore/{id}', 'PaymentController@restore');
        Route::post('payments/{payment}/{action}', 'PaymentController@action')->name('payments.action');

        Route::resource('payment_terms', 'PaymentTermsController');

//payment method
        Route::get('paymentType', 'PaymentTypeController@index');

        Route::get('health_check', 'SetupController@healthCheck');

// customers
        Route::get('customers', 'CustomerController@index');
        Route::get(
            'customers/dashboard',
            'CustomerController@dashboard'
        );
        Route::get('customers/{customer_id}', 'CustomerController@show');
        Route::put(
            'customers/{customer_id}',
            'CustomerController@update'
        );
        Route::post('customers', 'CustomerController@store');
        Route::post('customers/bulk', 'CustomerController@bulk');
        Route::delete(
            'customers/archive/{customer_id}',
            'CustomerController@archive'
        );
        Route::delete(
            'customers/{customer_id}',
            'CustomerController@destroy'
        );
        Route::get(
            'customer-types',
            'CustomerController@getCustomerTypes'
        );
        Route::post('customers/restore/{id}', 'CustomerController@restore');

// tasks
        Route::post('tasks/restore/{id}', 'TaskController@restore');
        Route::put('tasks/{task_id}', 'TaskController@update');
        Route::post('tasks', 'TaskController@store');
        Route::get(
            'tasks/getTasksForProject/{project_id}',
            'TaskController@getTasksForProject'
        );
        Route::put(
            'tasks/complete/{task}',
            'TaskController@markAsCompleted'
        );
        Route::delete('tasks/{task}', 'TaskController@destroy');

        Route::put(
            'tasks/status/{task_id}',
            'TaskController@updateStatus'
        );
//Route::get('deals', 'TaskController@getDeals');
        Route::get('tasks', 'TaskController@index');
        Route::get(
            'tasks/subtasks/{task_id}',
            'TaskController@getSubtasks'
        );
        Route::get(
            'tasks/products/{task_id}',
            'TaskController@getProducts'
        );
        Route::get('tasks/products', 'TaskController@getTasksWithProducts');
        Route::get('tasks/source-types', 'TaskController@getSourceTypes');
        Route::get('tasks/task-types', 'TaskController@getTaskTypes');
        Route::get('tasks/convertToDeal/{task_id}', 'TaskController@convertToDeal');
        Route::put('tasks/timer/{task_id}', 'TaskController@updateTimer');
        Route::delete('tasks/archive/{task_id}', 'TaskController@archive');
        Route::post('tasks/{task}/{action}', 'TaskController@action')->name('invoices.action');
        Route::post('task/bulk', 'TaskController@bulk');
        Route::get('tasks/{task_id}', 'TaskController@show');
        Route::post('tasks/sort', 'TaskController@sortTasks');


// leads
        Route::get('leads', 'LeadController@index');
        Route::put('lead/{lead_id}', 'LeadController@update');
        Route::delete('leads/archive/{lead_id}', 'LeadController@archive');
        Route::delete('leads/{lead}', 'LeadController@destroy');
        Route::post('leads/restore/{id}', 'LeadController@restore');
        Route::post('lead/{lead}/{action}', 'LeadController@action')->name('invoices.action');
        Route::post('leads/sort', 'LeadController@sortTasks');

// users
        Route::delete('users/archive/{user_id}', 'UserController@archive');
        Route::delete('users/{user_id}', 'UserController@destroy');
        Route::post('users', 'UserController@store');
        Route::get('users/dashboard', 'UserController@dashboard');
        Route::get('users/edit/{user_id}', 'UserController@edit');
        Route::put('users/{user_id}', 'UserController@update');
        Route::get('users', 'UserController@index');
        Route::post('user/upload', 'UserController@upload');
        Route::post('user/bulk', 'UserController@bulk');
        Route::get('user/profile/{username}', 'UserController@profile');
        Route::get('users/department/{department_id}', 'UserController@filterUsersByDepartment');
        Route::post('users/restore/{id}', 'UserController@restore');

// permissions
        Route::get('permissions', 'PermissionController@index');
        Route::post('permissions', 'PermissionController@store');
        Route::delete('permissions/{permission_id}', 'PermissionController@destroy');
        Route::get('permissions/{permission_id}', 'PermissionController@edit');
        Route::put('permissions/{permission_id}', 'PermissionController@update');

// roles
        Route::get('roles', 'RoleController@index');
        Route::post('roles', 'RoleController@store');
        Route::delete('roles/{role_id}', 'RoleController@destroy');
        Route::get('roles/{role_id}', 'RoleController@edit');
        Route::put('roles/{role_id}', 'RoleController@update');

//departments
        Route::get('departments', 'DepartmentController@index');
        Route::post('departments', 'DepartmentController@store');
        Route::delete('departments/{department_id}', 'DepartmentController@destroy');
        Route::get('departments/{department_id}', 'DepartmentController@edit');
        Route::put('departments/{department_id}', 'DepartmentController@update');

        Route::get('countries', 'CountryController@index');
        Route::get('currencies', 'CurrencyController@index');

        Route::resource('timer', 'TimerController');

        Route::resource('attributes', 'AttributeController');
        Route::resource('attributeValues', 'AttributeValueController');
    }
);


Route::group(
    ['middleware' => 'api-header'],
    function () {
        // login
        Route::get('login', 'LoginController@showLogin');
        Route::post('login', 'LoginController@doLogin');
        Route::get('logout', 'LoginController@doLogout');

        Route::post('passwordReset/create', 'Auth\PasswordResetController@create');
        Route::get('passwordReset/find/{token}', 'PasswordResetController@find');
        Route::post('passwordReset/reset', 'Auth\PasswordResetController@reset');

        // unprotected routes for website
        Route::get("category-list", 'CategoryController@getRootCategories');
        Route::get("categories/children/{slug}", 'CategoryController@getChildCategories');
        Route::get("category/form/{id}", 'CategoryController@getForm');
        Route::get("category/{slug}", 'CategoryController@getCategory');
        Route::post('tasks/products/{task_id}', 'TaskController@addProducts');
        Route::post('tasks/deal', 'TaskController@createDeal');
        Route::post('lead', 'LeadController@store');
        Route::get('lead/convert/{id}', 'LeadController@convert');

        Route::put('refund/{payment_id}', 'PaymentController@refund');

        Route::get('invoice/pdf/{key}', 'InvoiceController@markViewed');
        Route::get('quote/pdf/{key}', 'QuoteController@markViewed');
        Route::get('credit/pdf/{key}', 'CreditController@markViewed');
        Route::get('order/pdf/{key}', 'OrderController@markViewed');

        Route::post('invoice/download', 'InvoiceController@downloadPdf');
        Route::post('quote/download', 'QuoteController@downloadPdf');
        Route::post('order/download', 'OrderController@downloadPdf');
        Route::post('credit/download', 'CreditController@downloadPdf');

        Route::post('payment/completePayment', 'PaymentController@completePayment');
        Route::post('recurring-invoice/cancel', 'RecurringInvoiceController@requestCancellation');
        Route::post('quote/bulk', 'QuoteController@bulk');
        Route::post('order/bulk', 'OrderController@bulk');
        Route::get('products/{product_id}', 'ProductController@show');
        Route::get('products/find/{slug}', 'ProductController@find');
        Route::get("category/{id}/products", 'ProductController@getProductsForCategory');

        //vouchers
        Route::get('promocode/{code}', 'PromocodeController@show');
        Route::post('promocode/apply', 'PromocodeController@apply');
        Route::post('promocode/validate', 'PromocodeController@validateCode');

        //shipping
        Route::post('shipping/getRates', 'ShippingController@getRates');

        Route::post('preview', 'PreviewController@show');

        Route::post('customer/register', 'CustomerController@register');

        // cases
        Route::resource('cases', 'CaseController');
        Route::post('cases/{case}/{action}', 'CaseController@action')->name('invoices.action');

        // case categories
        Route::resource('case-categories', 'CaseCategoryController');

        // case templates
        Route::resource('case_template', 'CaseTemplateController');

        Route::post('uploads', 'UploadController@store');

        //statement
        Route::post('statement/{customer}/{action}', 'StatementController@download');
    }
);
