STRUCTURE OF THE CODE:


LoanManagement_SaaS/
│
├── app/
│   ├── config/
│   │   └── config.php
│   │
│   ├── controllers/
│   │   ├── AccountController.php
│   │   ├── AuthController.php
│   │   ├── BorrowerController.php
│   │   ├── BusinessUserController.php
│   │   ├── CategoryController.php
│   │   ├── CollectionController.php
│   │   ├── DashboardController.php
│   │   ├── ExpenseController.php
│   │   ├── LoanController.php
│   │   ├── PaymentController.php
│   │   ├── RegistrationApprovalController.php
│   │   ├── RegistrationController.php
│   │   ├── ReportController.php
│   │   ├── SettingsController.php
│   │   └── StaffController.php
│   │
│   ├── core/
│   │   ├── Auth.php
│   │   ├── AuthMiddleware.php
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Model.php
│   │   └── Router.php
│   │
│   ├── helpers/
│   │   ├── functions.php
│   │   ├── loan_helpers.php
│   │   └── permission_helpers.php
│   │
│   ├── middleware/
│   │   ├── AdminMiddleware.php
│   │   ├── AuthMiddleware.php
│   │   └── SuperAdminMiddleware.php
│   │
│   ├── models/
│   │   ├── Account.php
│   │   ├── Borrower.php
│   │   ├── Category.php
│   │   ├── Expense.php
│   │   ├── Loan.php
│   │   ├── LoanAccount.php
│   │   ├── LoanCollateral.php
│   │   ├── LoanSchedule.php
│   │   ├── Organization.php
│   │   ├── Registration.php
│   │   ├── RegistrationApproval.php
│   │   ├── Setting.php
│   │   └── User.php
│   │
│   └── views/
│       ├── accounts/
│       │   ├── create.php
│       │   ├── edit.php
│       │   ├── index.php
│       │   ├── transactions.php
│       │   └── view.php
│       ├── auth/
│       │   ├── forgot_password.php
│       │   ├── login.php
│       │   └── register.php
│       ├── borrowers/
│       │   ├── create.php
│       │   ├── details.php
│       │   ├── edit.php
│       │   ├── index.php
│       │   └── view.php
│       ├── business_users/
│       │   ├── change_password.php
│       │   ├── create.php
│       │   └── index.php
│       ├── categories/
│       │   ├── create.php
│       │   ├── edit.php
│       │   └── index.php
│       ├── collections/
│       │   └── index.php
│       ├── dashboard/
│       │   ├── index.php
│       │   └── super_admin.php
│       ├── expenses/
│       │   ├── create.php
│       │   └── index.php
│       ├── layouts/
│       │   ├── footer.php
│       │   ├── header.php
│       │   ├── navbar.php
│       │   └── sidebar.php
│       ├── loans/
│       │   ├── edit.php
│       │   ├── index.php
│       │   ├── payment.php
│       │   └── view.php
│       ├── partials/
│       │   └── sidebar.php
│       ├── payments/
│       │   └── index.php
│       ├── reports/
│       │   ├── borrowers.php
│       │   ├── collections.php
│       │   ├── index.php
│       │   ├── loans.php
│       │   └── payments.php
│       ├── settings/
│       │   ├── index.php
│       │   └── profile.php
│       ├── staff/
│       │   ├── create.php
│       │   ├── edit.php
│       │   └── index.php
│       └── super_admin/
│           └── registrations.php
│
├── database/
│   ├── loan_saas.sql
│   └── NotTruncated.sql
│
├── public/
│   ├── assets/
│   │   ├── css/
│   │   │   ├── app.css
│   │   │   ├── sidebar.css
│   │   │   └── style.css
│   │   └── js/
│   │       └── app.js
│   ├── uploads/
│   │   └── settings/
│   │       └── logo/
│   │           ├── business_1_1787690538.jpg
│   │           └── business_1_1787691294.jpg
│   ├── hash-generator.php
│   └── index.php
│
└── README.md
