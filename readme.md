# TamTam CRM

TamTam CRM is an all in one invoicing, order management and CRM system. It was built to prevent a common issue often seen with other CRM's where you need to pay for multiple subscriptions and have data spread across multiple databases. TamTam CRM is a functionality rich system providing you with a full package all in one place. The system is under continuous development and is updated regularly. Some of the features that can be found in TamTam CRM are as follows

* Features
* Multiple Accounts
* Customer Management with contacts
* Invoices / Recurring Invoices
* Quotes / Recurring Quotes
* Payments includes stripe, PayPal and authorize.net integration
* Credits
* Refunds
* Expenses
* Document Management 
* Vendor Management with contacts
* Events
* Tasks with time management
* Projects
* Leads
* Deals
* Customer Portal
* Purchase Orders
* Shippo shipping integration
* Case Management
* User Management with roles, permissions and departments
* Product Management with categories, images, reviews, features, variations and attributes, brands
* Voucher/Coupon Management
* Order Management System

## Getting Started

git clone https://github.com/michael-hampton/tamtamcrm.git
composer update
npm i
npm run production
php artisan migrate:fresh --seed && php artisan db:seed --class=DatabaseSeeder

## Running the tests

Tests can be found in the tests folder 
To run then php ./vendor/phpunit/phpunit/phpunit

## Built With

* Laravel 6.0 - The backend web framework used
* React - Frontend JavaScript framework
* reference - https://laravel-taiwan.github.io/php-the-right-way/pages/Design-Patterns.html

## Contributing

We welcome all contributions. Please create a branch from master and create a pull request to master on completion. 
Please ensure that all tests work prior to creating the pull request and that there are migrations for any database changes. 
All code must follow PSR-2 guidelines.

## Versioning

All pull requests made from and to master. 

## Authors

* Michael Hampton
## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
