<?php

/*
|--------------------------------------------------------------------------
| Start Session
|--------------------------------------------------------------------------
*/

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/*
|--------------------------------------------------------------------------
| Application Paths
|--------------------------------------------------------------------------
*/

define(
    'BASE_PATH',
    dirname(__DIR__)
);

define(
    'APP_PATH',
    BASE_PATH . '/app'
);


/*
|--------------------------------------------------------------------------
| Load Core Classes
|--------------------------------------------------------------------------
*/

require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/core/AuthMiddleware.php';


/*
|--------------------------------------------------------------------------
| Load Controllers
|--------------------------------------------------------------------------
*/

require_once APP_PATH . '/controllers/AuthController.php';
require_once APP_PATH . '/controllers/DashboardController.php';
require_once APP_PATH . '/controllers/BusinessUserController.php';
require_once APP_PATH . '/controllers/ExpenseController.php';
require_once APP_PATH . '/controllers/BorrowerController.php';
require_once APP_PATH . '/controllers/AccountController.php';
require_once APP_PATH . '/controllers/LoanController.php';
require_once APP_PATH . '/controllers/CategoryController.php';
require_once APP_PATH . '/controllers/CollectionController.php';
require_once APP_PATH . '/controllers/ReportController.php';
require_once APP_PATH . '/controllers/SettingsController.php';
require_once APP_PATH . '/controllers/PenaltyController.php';
require_once APP_PATH . '/controllers/SuperAdminBusinessController.php';


/*
|--------------------------------------------------------------------------
| Load Models
|--------------------------------------------------------------------------
*/

require_once APP_PATH . '/models/Borrower.php';
require_once APP_PATH . '/models/Loan.php';
require_once APP_PATH . '/models/Account.php';
require_once APP_PATH . '/models/Expense.php';
require_once APP_PATH . '/models/Category.php';
require_once APP_PATH . '/models/Setting.php';
require_once APP_PATH . '/models/Penalty.php';


/*
|--------------------------------------------------------------------------
| Get Requested URL
|--------------------------------------------------------------------------
*/

$url = $_GET['url'] ?? 'auth/login';


/*
|--------------------------------------------------------------------------
| Clean URL
|--------------------------------------------------------------------------
*/

$url = trim($url, '/');


/*
|--------------------------------------------------------------------------
| Route
|--------------------------------------------------------------------------
*/

switch ($url) {


    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATION
    |--------------------------------------------------------------------------
    */

    case 'auth/login':

        $controller = new AuthController();

        $controller->login();

        break;


    case 'auth/register':

        $controller = new AuthController();

        $controller->register();

        break;


    case 'auth/logout':

        $controller = new AuthController();

        $controller->logout();

        break;


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    case 'dashboard':

        $controller = new DashboardController();

        $controller->index();

        break;


    /*
    |--------------------------------------------------------------------------
    | BUSINESS USERS
    |--------------------------------------------------------------------------
    */

    case 'business-users':

        $controller = new BusinessUserController();

        $controller->index();

        break;


    case 'business-users/create':

        $controller = new BusinessUserController();

        $controller->create();

        break;


    case 'business-users/store':

        $controller = new BusinessUserController();

        $controller->store();

        break;


    /*
    |--------------------------------------------------------------------------
    | CHANGE PASSWORD MODAL / FORM
    |--------------------------------------------------------------------------
    */

    case 'business-users/change-password':

        $controller = new BusinessUserController();

        $controller->changePassword();

        break;


    /*
    |--------------------------------------------------------------------------
    | UPDATE PASSWORD
    |--------------------------------------------------------------------------
    */

    case 'business-users/update-password':

        $controller = new BusinessUserController();

        $controller->updatePassword();

        break;


    /*
    |--------------------------------------------------------------------------
    | DISABLE BUSINESS USER
    |--------------------------------------------------------------------------
    */

    case 'business-users/disable':

        $controller = new BusinessUserController();

        $controller->disable();

        break;


    /*
    |--------------------------------------------------------------------------
    | ENABLE BUSINESS USER
    |--------------------------------------------------------------------------
    */

    case 'business-users/enable':

        $controller = new BusinessUserController();

        $controller->enable();

        break;


    /*
    |--------------------------------------------------------------------------
    | BORROWERS
    |--------------------------------------------------------------------------
    */

    case 'borrowers':

        $controller = new BorrowerController();

        $controller->index();

        break;


    case 'borrowers/create':

        $controller = new BorrowerController();

        $controller->create();

        break;


    case 'borrowers/store':

        $controller = new BorrowerController();

        $controller->store();

        break;


    case 'borrowers/edit':

        $controller = new BorrowerController();

        $controller->edit();

        break;


    case 'borrowers/update':

        $controller = new BorrowerController();

        $controller->update();

        break;


    case 'borrowers/delete':

        $controller = new BorrowerController();

        $controller->delete();

        break;


    case 'borrowers/details':

        $controller = new BorrowerController();

        $controller->details();

        break;


    /*
    |--------------------------------------------------------------------------
    | CATEGORIES
    |--------------------------------------------------------------------------
    */

    case 'categories':

        $controller = new CategoryController();

        $controller->index();

        break;


    case 'categories/create':

        $controller = new CategoryController();

        $controller->create();

        break;


    case 'categories/store':

        $controller = new CategoryController();

        $controller->store();

        break;


    case 'categories/edit':

        $controller = new CategoryController();

        $controller->edit();

        break;


    case 'categories/update':

        $controller = new CategoryController();

        $controller->update();

        break;


    case 'categories/delete':

        $controller = new CategoryController();

        $controller->delete();

        break;


    /*
    |--------------------------------------------------------------------------
    | LOANS
    |--------------------------------------------------------------------------
    */

    case 'loans':

        $controller = new LoanController();

        $controller->index();

        break;


    case 'loans/create':

        $controller = new LoanController();

        $controller->create();

        break;


    case 'loans/store':

        $controller = new LoanController();

        $controller->store();

        break;


    case 'loans/show':

        $controller = new LoanController();

        $controller->show();

        break;


    case 'loans/edit':

        $controller = new LoanController();

        $controller->edit();

        break;


    case 'loans/update':

        $controller = new LoanController();

        $controller->update();

        break;


    case 'loans/delete':

        $controller = new LoanController();

        $controller->delete();

        break;


    /*
    |--------------------------------------------------------------------------
    | LOAN PAYMENTS
    |--------------------------------------------------------------------------
    */

    case 'payments':

        $controller = new LoanController();

        $controller->payments();

        break;


    case 'loans/payment':

        $controller = new LoanController();

        $controller->payment();

        break;


    case 'loans/payment/store':

        $controller = new LoanController();

        $controller->storePayment();

        break;


    /*
    |--------------------------------------------------------------------------
    | APPROVE LOAN
    |--------------------------------------------------------------------------
    */

    case 'loans/approve':

        $controller = new LoanController();

        $controller->approve();

        break;


    /*
    |--------------------------------------------------------------------------
    | REJECT LOAN
    |--------------------------------------------------------------------------
    */

    case 'loans/reject':

        $controller = new LoanController();

        $controller->reject();

        break;


    /*
    |--------------------------------------------------------------------------
    | RELEASE LOAN
    |--------------------------------------------------------------------------
    */

    case 'loans/release':

        $controller = new LoanController();

        $controller->release();

        break;


    /*
    |--------------------------------------------------------------------------
    | EXPENSES
    |--------------------------------------------------------------------------
    */

    case 'expenses':

        $controller = new ExpenseController();

        $controller->index();

        break;


    case 'expenses/create':

        $controller = new ExpenseController();

        $controller->create();

        break;


    case 'expenses/store':

        $controller = new ExpenseController();

        $controller->store();

        break;


    case 'expenses/edit':

        $controller = new ExpenseController();

        $controller->edit();

        break;


    case 'expenses/update':

        $controller = new ExpenseController();

        $controller->update();

        break;


    case 'expenses/delete':

        $controller = new ExpenseController();

        $controller->delete();

        break;


    /*
    |--------------------------------------------------------------------------
    | ACCOUNTS
    |--------------------------------------------------------------------------
    */

    case 'accounts':

        $controller = new AccountController();

        $controller->index();

        break;


    case 'accounts/create':

        $controller = new AccountController();

        $controller->create();

        break;


    case 'accounts/store':

        $controller = new AccountController();

        $controller->store();

        break;


    case 'accounts/view':

        $controller = new AccountController();

        $controller->view();

        break;


    case 'accounts/edit':

        $controller = new AccountController();

        $controller->edit();

        break;


    case 'accounts/update':

        $controller = new AccountController();

        $controller->update();

        break;


    case 'accounts/delete':

        $controller = new AccountController();

        $controller->delete();

        break;


    /*
    |--------------------------------------------------------------------------
    | ADJUST ACCOUNT BALANCE
    |--------------------------------------------------------------------------
    */

    case 'accounts/adjust-balance':

        $controller = new AccountController();

        $controller->adjustBalance();

        break;


    /*
    |--------------------------------------------------------------------------
    | TRANSFER ACCOUNT BALANCE
    |--------------------------------------------------------------------------
    */

    case 'accounts/transfer-balance':

        $controller = new AccountController();

        $controller->transferBalance();

        break;


    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */

    case 'reports':

    case 'reports/index':

        $controller = new ReportController();

        $controller->index();

        break;


    /*
    |--------------------------------------------------------------------------
    | COLLECTIONS
    |--------------------------------------------------------------------------
    |
    | Collection management is separate from individual loan payments.
    |
    */

    case 'collections':

        $controller = new CollectionController();

        $controller->index();

        break;


    case 'collections/view':

        $controller = new CollectionController();

        $controller->view();

        break;


    /*
    |--------------------------------------------------------------------------
    | SETTINGS
    |--------------------------------------------------------------------------
    |
    | SaaS business/system settings.
    |
    | The settings page can be used for things such as:
    |
    | - Business logo
    | - Business name
    | - Business email
    | - Business phone
    | - Business address
    | - Currency
    | - Date format
    | - Timezone
    | - Loan settings
    | - Collection settings
    | - Receipt settings
    | - Other tenant-specific configuration
    |
    */

    case 'settings':

        $controller = new SettingsController();

        $controller->index();

        break;


    /*
    |--------------------------------------------------------------------------
    | SAVE SETTINGS
    |--------------------------------------------------------------------------
    */

    case 'settings/update':

        $controller = new SettingsController();

        $controller->update();

        break;


    /*
    |--------------------------------------------------------------------------
    | SETTINGS LOGO UPLOAD
    |--------------------------------------------------------------------------
    |
    | If your SettingsController handles the logo upload separately,
    | this route can be used for it.
    |
    */

    case 'settings/logo':

        $controller = new SettingsController();

        $controller->updateLogo();

        break;



    /*
    |--------------------------------------------------------------------------
    | COLLECTIONS
    |--------------------------------------------------------------------------
    |
    | Collection management is separate from individual loan payments.
    |
    */

    case 'penalties':

        $controller = new PenaltyController();

        $controller->index();

        break;


    case 'penalties/view':

        $controller = new PenaltyController();

        $controller->view();

        break;
        

        case 'super_admin/businesses':

    $controller = new SuperAdminBusinessController();

    $controller->index();

    break;




    /*
    |--------------------------------------------------------------------------
    | DEFAULT / 404
    |--------------------------------------------------------------------------
    */

    default:

        http_response_code(404);

        echo '<h1>404 - Page Not Found</h1>';

        echo '<p>The requested page does not exist.</p>';

        break;

}