<?php

use App\Models\Design;
use Illuminate\Database\Seeder;

class DesignSeeder extends Seeder
{
    public function run()
    {
        $this->createDesigns();
    }

    private function createDesigns()
    {
        $designs = [
            [
                'id'         => 1,
                'name'       => 'Basic',
                'user_id'    => null,
                'account_id' => null,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 2,
                'name'       => 'Danger',
                'user_id'    => null,
                'account_id' => null,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 3,
                'name'       => 'Dark',
                'user_id'    => null,
                'account_id' => null,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 4,
                'name'       => 'Happy',
                'user_id'    => null,
                'account_id' => null,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 5,
                'name'       => 'Info',
                'user_id'    => null,
                'account_id' => null,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 6,
                'name'       => 'Jazzy',
                'user_id'    => null,
                'account_id' => null,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 7,
                'name'       => 'Picture',
                'user_id'    => null,
                'account_id' => null,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 8,
                'name'       => 'Secondary',
                'user_id'    => null,
                'account_id' => null,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 9,
                'name'       => 'Simple',
                'user_id'    => null,
                'account_id' => null,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 11,
                'name'       => 'Warning',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],

            [
                'id'         => 12,
                'name'       => 'Simple',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 13,
                'name'       => 'Warning',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 14,
                'name'       => 'Secondary',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 15,
                'name'       => 'Picture',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 16,
                'name'       => 'Jazzy',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 17,
                'name'       => 'Info',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 18,
                'name'       => 'Happy',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 19,
                'name'       => 'Dark',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 20,
                'name'       => 'Danger',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
            [
                'id'         => 21,
                'name'       => 'Basic',
                'user_id'    => null,
                'account_id' => 1,
                'is_custom'  => false,
                'design'     => '',
                'is_active'  => true
            ],
        ];

        foreach ($designs as $design) {
            $d = Design::find($design['id']);

            if (!$d) {
                Design::create($design);
            }
        }

        foreach (Design::all() as $design) {
            $class = 'App\Designs\\' . $design->name;
            $invoice_design = new $class();

            $design_object = new \stdClass;
            $design_object->header = $invoice_design->header() ?: '';
            $design_object->body = $invoice_design->body() ?: '';
            $design_object->totals = $invoice_design->totals() ?: '';
            $design_object->table = $invoice_design->table() ?: '';
            $design_object->task_table = $invoice_design->task_table() ?: '';
            $design_object->statement_table = $invoice_design->statement_table() ?: '';
            $design_object->footer = $invoice_design->footer() ?: '';

            $design->design = $design_object;
            $design->save();
        }
    }
}
