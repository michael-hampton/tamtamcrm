<?php

namespace Tests\Unit;

use App\Components\Import\ImportFactory;
use App\Factory\ExpenseCategoryFactory;
use App\Models\Account;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\CustomerContact;
use App\Models\ExpenseCategory;
use App\Models\Industry;
use App\Models\Invoice;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\Project;
use App\Models\User;
use App\Repositories\ExpenseCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ImportTest extends TestCase
{

    use DatabaseTransactions, WithFaker;

    private Customer $customer;

    /**
     * @var Account
     */
    private Account $account;

    /**
     * @var User|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed
     */
    private User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginDatabaseTransaction();
        $this->payment_type = PaymentMethod::first();
        $this->customer = Customer::factory()->create(['account_id' => 1]);
        $this->company = Company::factory()->create(['account_id' => 1]);
        $this->invoice = Invoice::factory()->create(['customer_id' => $this->customer->id, 'account_id' => 1]);
        $this->project = Project::factory()->create(['customer_id' => $this->customer->id, 'account_id' => 1]);
        $this->product = Product::factory()->create(['account_id' => 1]);
        $this->category = Category::factory()->create(['account_id' => 1]);
        $this->contact = CustomerContact::factory()->create(['account_id' => 1, 'customer_id' => $this->customer->id]);
        $this->customer->contacts()->save($this->contact);
        $this->main_account = Account::where('id', 1)->first();
        $this->account = Account::factory()->create();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_import_products()
    {
        $brand = Brand::first();

        $data = [
            'name'          => $this->faker->name,
            'description'   => $this->faker->sentence,
            'category name' => $this->category->name,
            'brand name'    => $brand->name,
            'quantity'      => 100,
            'price'         => 9.99,
            'cost'          => 7.25,
            'width'         => 2,
            'height'        => 2,
            'weight'        => 2,
            'length'        => 2
        ];

        $this->doImport($data, 'product');

        $this->assertDatabaseHas(
            'products',
            [
                'name'        => $data['name'],
                'description' => $data['description'],
                'quantity'    => $data['quantity'],
                'price'       => $data['price']
            ]
        );
    }

    /** @test */
    public function it_can_import_expenses()
    {
        $factory = (new ExpenseCategoryFactory())->create($this->main_account, $this->user);

        $data = [
            'name' => $this->faker->name
        ];

        $expenseRepo = new ExpenseCategoryRepository(new ExpenseCategory());
        $expense_category = $expenseRepo->create($data, $factory);

        $data = [
            'expense category name' => $expense_category->name,
            'customer name'         => $this->customer->name,
            'company name'          => $this->company->name,
            'payment type'          => $this->payment_type->name,
            'reference number'      => 'abcd',
            'project name'          => $this->project->name,
            'date'                  => $this->faker->date(),
            'amount'                => $this->faker->randomFloat(2),
            'currency code'         => $this->faker->currencyCode,
            'public notes'          => $this->faker->sentence,
            'private notes'         => $this->faker->sentence,
            'custom value1'         => $this->faker->sentence,
            'custom value2'         => $this->faker->sentence,
            'custom value3'         => $this->faker->sentence,
            'custom value4'         => $this->faker->sentence
        ];

        $this->doImport($data, 'expense');

        $this->assertDatabaseHas(
            'expenses',
            [
                'project_id'        => $this->project->id,
                'customer_id'       => $this->customer->id,
                'company_id'        => $this->company->id,
                'payment_method_id' => $this->payment_type->id,
                'amount'            => $data['amount'],
                'reference_number'  => $data['reference number'],
                'public_notes'      => $data['public notes'],
                'private_notes'     => $data['private notes']
            ]
        );
    }

    /** @test */
    public function it_can_import_companies()
    {
        $industry = Industry::where('name', '=', 'Internet')->first();

        $data = [
            'name'          => $this->faker->name,
            'first_name'    => $this->faker->firstName,
            'last_name'     => $this->faker->lastName,
            'email'         => 'tamtamcrm@yahoo.com',
            'contact phone' => $this->faker->phoneNumber,
            'website'       => $this->faker->url,
            'address 1'     => $this->faker->streetAddress,
            'address 2'     => 'test',
            'postcode'      => $this->faker->postcode,
            'town'          => $this->faker->state,
            'city'          => $this->faker->city,
            'vat number'    => '12345a',
            'currency_code' => $this->faker->currencyCode,
            'public notes'  => $this->faker->sentence,
            'private notes' => $this->faker->sentence,
            'country'       => 'afghanistan',
            'industry'      => strtolower($industry->name)
        ];

        $this->doImport($data, 'company');

        $this->assertDatabaseHas(
            'companies',
            [
                'name'          => $data['name'],
                'address_1'     => $data['address 1'],
                'address_2'     => $data['address 2'],
                'town'          => $data['town'],
                'city'          => $data['city'],
                'vat_number'    => $data['vat number'],
                'country_id'    => 1,
                'public_notes'  => $data['public notes'],
                'private_notes' => $data['private notes'],
                'industry_id'   => $industry->id
            ]
        );

        $this->assertDatabaseHas(
            'company_contacts',
            [
                'email'      => $data['email'],
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'phone'      => $data['contact phone']
            ]
        );
    }

    /** @test */
    public function it_can_import_invoices()
    {
        $data = [
            'number'        => $this->faker->randomNumber(4),
            'customer name' => $this->customer->name,
            'contact email' => $this->contact->email,
            'description'   => $this->product->description,
            'product'       => $this->product->name,
            'unit_price'    => $this->faker->randomFloat(2),
            'unit_discount' => 2,
            'quantity'      => 1,
            'project name'  => $this->project->name,
            'date'          => $this->faker->date(),
            'due date'      => $this->faker->date(),
            'exchange_rate' => 2,
            'terms'         => $this->faker->sentence,
            'private notes' => $this->faker->sentence,
            'public notes'  => $this->faker->sentence,
            'custom value1' => $this->faker->sentence,
            'custom value2' => $this->faker->sentence,
            'custom value3' => $this->faker->sentence,
            'custom value4' => $this->faker->sentence
        ];

        $this->doImport($data, 'invoice');

        $this->assertDatabaseHas(
            'invoices',
            [
                'number'        => $data['number'],
                'customer_id'   => $this->customer->id,
                'project_id'    => $this->project->id,
                //'exchange_rate' => $data['exchange_rate'],
                'terms'         => $data['terms'],
                'date'          => $data['date'],
                'due_date'      => $data['due date'],
                'public_notes'  => $data['public notes'],
                'private_notes' => $data['private notes']
            ]
        );

        $invoice = Invoice::where('number', $data['number'])->first();

        $this->assertEquals($invoice->invitations->count(), 1);
    }

    /** @test */
    public function it_can_import_payments()
    {
        $data = [
            'number'        => $this->faker->randomNumber(6),
            'customer name' => $this->customer->name,
            'amount'        => $this->invoice->total,
            'date'          => $this->faker->date(),
            'invoices'      => $this->invoice->number,
            'payment_type'  => $this->payment_type->name,
        ];

        $this->doImport($data, 'payment');

        $this->assertDatabaseHas(
            'payments',
            [
                'customer_id' => $this->customer->id,
                'amount'      => $data['amount'],
                'date'        => $data['date'],
                'applied'     => $this->invoice->total
            ]
        );

        $this->assertDatabaseHas(
            'paymentables',
            [
                'paymentable_id' => $this->invoice->id,
                'amount'         => $this->invoice->total
            ]
        );
    }

    /** @test */
    public function it_can_import_customers()
    {
        $data = [
            'name'               => $this->faker->name,
            'vat_number'         => '1234a',
            'first_name'         => $this->faker->firstName,
            'last_name'          => $this->faker->lastName,
            'email'              => 'tamtamcrm@yahoo.com',
            'phone'              => $this->faker->phoneNumber,
            //'contact phone' => $this->faker->phoneNumber,
            'website'            => $this->faker->url,
            'currency_code'      => $this->faker->currencyCode,
            'billing address 1'  => $this->faker->streetAddress,
            'billing address 2'  => 'test',
            'billing zip'        => $this->faker->postcode,
            'billing city'       => $this->faker->city,
            'billing country'    => 'afghanistan',
            'shipping address 1' => $this->faker->streetAddress,
            'shipping address 2' => 'test',
            'shipping zip'       => $this->faker->postcode,
            'shipping city'      => $this->faker->city,
            'shipping country'   => 'afghanistan',
            'public notes'       => $this->faker->sentence,
            'private notes'      => $this->faker->sentence
        ];

        $this->doImport($data, 'customer');

        $this->assertDatabaseHas(
            'customers',
            [
                'name'          => $data['name'],
                'website'       => $data['website'],
                'vat_number'    => $data['vat_number'],
                'public_notes'  => $data['public notes'],
                'private_notes' => $data['private notes']
            ]
        );

        $this->assertDatabaseHas(
            'customer_contacts',
            [
                'email'      => $data['email'],
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'phone'      => $data['phone']
            ]
        );

        $this->assertDatabaseHas(
            'addresses',
            [
                'address_type' => 1,
                'address_1'    => $data['billing address 1'],
                'address_2'    => $data['billing address 2'],
                'zip'          => $data['billing zip'],
                'country_id'   => 1,
            ]
        );

        $this->assertDatabaseHas(
            'addresses',
            [
                'address_type' => 2,
                'address_1'    => $data['shipping address 1'],
                'address_2'    => $data['shipping address 2'],
                'zip'          => $data['shipping zip'],
                'country_id'   => 1,
            ]
        );
    }

    /** @test */
    public function it_can_import_leads()
    {
        $data = [
            'name'        => $this->faker->name,
            'description' => $this->faker->sentence,
            'first_name'  => $this->faker->firstName,
            'last_name'   => $this->faker->lastName,
            'email'       => $this->faker->safeEmail,
            'phone'       => $this->faker->phoneNumber,
            'website'     => $this->faker->url,
            'address_1'   => $this->faker->streetAddress,
            'address_2'   => 'test',
            'zip'         => $this->faker->postcode,
            'city'        => $this->faker->city,
            'task status' => 'partner leads'
        ];

        $this->doImport($data, 'lead');

        $this->assertDatabaseHas(
            'leads',
            [
                'address_1'  => $data['address_1'],
                'address_2'  => $data['address_2'],
                'zip'        => $data['zip'],
                'city'       => $data['city'],
                'email'      => $data['email'],
                'first_name' => $data['first_name'],
                'last_name'  => $data['last_name'],
                'phone'      => $data['phone']
            ]
        );
    }

    /** @test */
    public function it_can_import_deals()
    {
        $data = [
            'name'          => $this->faker->name,
            'description'   => $this->faker->sentence,
            'valued_at'     => $this->faker->randomDigit,
            'due_date'      => $this->faker->date(),
            'customer name' => $this->customer->name,
            'task status'   => 'qualification'
        ];

        $this->doImport($data, 'deal');

        $this->assertDatabaseHas(
            'deals',
            [
                'name'        => $data['name'],
                'description' => $data['description'],
                'valued_at'   => $data['valued_at'],
                'due_date'    => $data['due_date'],
                'customer_id' => $this->customer->id
            ]
        );
    }

    private function parameterize_array($array)
    {
        $out = array();
        foreach ($array as $key => $value) {
            $out[$key] = $key;
        }
        return $out;
    }

    private function array2csv($array, &$title, &$data)
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $title .= $key . ",";
                $data .= "" . ",";
                $this->array2csv($value, $title, $data);
            } else {
                $title .= $key . ",";
                $data .= '"' . $value . '",';
            }
        }
    }

    private function str_putcsv($file_path, $data)
    {
        $header = null;
        $createFile = fopen($file_path, "w+");
        foreach ($data as $row) {
            if (!$header) {
                fputcsv($createFile, array_keys($row));
                fputcsv($createFile, $row);   // do the first row of data too
                $header = true;
            } else {
                fputcsv($createFile, $row);
            }
        }

        fclose($createFile);
    }

    private function doImport($data, $import_type)
    {
        $mappings = $this->parameterize_array($data);

        $path = public_path('storage/import_tests/' . $import_type . '.csv');

        $this->str_putcsv($path, [$data]);

        try {
            $importer = (new ImportFactory())->loadImporter(
                $import_type,
                $this->main_account,
                $this->user
            );


            $importer->setCsvFile($path);

            $importer->setColumnMappings($mappings);

            $importer->run(true);

            unlink($path);
        } catch (\Exception $e) {
            echo $e->getMessage();
            die('here');
        }
    }
}
