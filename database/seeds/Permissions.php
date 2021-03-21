<?php
use App\Models\Role;
use App\Models\Permission;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Permission
 *
 * @author michael.hampton
 */
class Permissions {

    public function create() {
        
        $role = Role::where('name', 'Admin')->first();
        
        /*for ($x = 1; $x <= 243; $x++) {
                        
            $permission = Permission::find($x);
            
            $role->permissions()->attach($permission);
        }

        $role = Role::find(1810);
        $arrPermissions = [];*/

        $permissions = array (
            0 =>
                array (
                    'name' => 'dashboardstatscontroller.index',
                    'description' => '',
                ),
            1 =>
                array (
                    'name' => 'workloadcontroller.index',
                    'description' => '',
                ),
            2 =>
                array (
                    'name' => 'mastersupervisorcontroller.index',
                    'description' => '',
                ),
            3 =>
                array (
                    'name' => 'monitoringcontroller.index',
                    'description' => '',
                ),
            4 =>
                array (
                    'name' => 'monitoringcontroller.store',
                    'description' => '',
                ),
            5 =>
                array (
                    'name' => 'monitoringcontroller.paginate',
                    'description' => '',
                ),
            6 =>
                array (
                    'name' => 'monitoringcontroller.destroy',
                    'description' => '',
                ),
            7 =>
                array (
                    'name' => 'jobmetricscontroller.index',
                    'description' => '',
                ),
            8 =>
                array (
                    'name' => 'jobmetricscontroller.show',
                    'description' => '',
                ),
            9 =>
                array (
                    'name' => 'queuemetricscontroller.index',
                    'description' => '',
                ),
            10 =>
                array (
                    'name' => 'queuemetricscontroller.show',
                    'description' => '',
                ),
            11 =>
                array (
                    'name' => 'batchescontroller.index',
                    'description' => '',
                ),
            12 =>
                array (
                    'name' => 'batchescontroller.show',
                    'description' => '',
                ),
            13 =>
                array (
                    'name' => 'batchescontroller.retry',
                    'description' => '',
                ),
            14 =>
                array (
                    'name' => 'pendingjobscontroller.index',
                    'description' => '',
                ),
            15 =>
                array (
                    'name' => 'completedjobscontroller.index',
                    'description' => '',
                ),
            16 =>
                array (
                    'name' => 'failedjobscontroller.index',
                    'description' => '',
                ),
            17 =>
                array (
                    'name' => 'failedjobscontroller.show',
                    'description' => '',
                ),
            18 =>
                array (
                    'name' => 'retrycontroller.store',
                    'description' => '',
                ),
            19 =>
                array (
                    'name' => 'jobscontroller.show',
                    'description' => '',
                ),
            20 =>
                array (
                    'name' => 'homecontroller.index',
                    'description' => '',
                ),
            21 =>
                array (
                    'name' => 'losure.closure',
                    'description' => '',
                ),
            22 =>
                array (
                    'name' => 'taskstatuscontroller.index',
                    'description' => '',
                ),
            23 =>
                array (
                    'name' => 'dashboardcontroller.index',
                    'description' => '',
                ),
            24 =>
                array (
                    'name' => 'reportcontroller.index',
                    'description' => '',
                ),
            25 =>
                array (
                    'name' => 'activitycontroller.index',
                    'description' => '',
                ),
            26 =>
                array (
                    'name' => 'supportcontroller.app\\http\\controllers\\supportcontroller',
                    'description' => '',
                ),
            27 =>
                array (
                    'name' => 'companyledgercontroller.index',
                    'description' => '',
                ),
            28 =>
                array (
                    'name' => 'tokencontroller.index',
                    'description' => '',
                ),
            29 =>
                array (
                    'name' => 'tokencontroller.create',
                    'description' => '',
                ),
            30 =>
                array (
                    'name' => 'tokencontroller.store',
                    'description' => '',
                ),
            31 =>
                array (
                    'name' => 'tokencontroller.show',
                    'description' => '',
                ),
            32 =>
                array (
                    'name' => 'tokencontroller.edit',
                    'description' => '',
                ),
            33 =>
                array (
                    'name' => 'tokencontroller.update',
                    'description' => '',
                ),
            34 =>
                array (
                    'name' => 'tokencontroller.destroy',
                    'description' => '',
                ),
            35 =>
                array (
                    'name' => 'promocodecontroller.index',
                    'description' => '',
                ),
            36 =>
                array (
                    'name' => 'promocodecontroller.create',
                    'description' => '',
                ),
            37 =>
                array (
                    'name' => 'promocodecontroller.store',
                    'description' => '',
                ),
            38 =>
                array (
                    'name' => 'promocodecontroller.show',
                    'description' => '',
                ),
            39 =>
                array (
                    'name' => 'promocodecontroller.edit',
                    'description' => '',
                ),
            40 =>
                array (
                    'name' => 'promocodecontroller.update',
                    'description' => '',
                ),
            41 =>
                array (
                    'name' => 'promocodecontroller.destroy',
                    'description' => '',
                ),
            42 =>
                array (
                    'name' => 'dealcontroller.action',
                    'description' => '',
                ),
            43 =>
                array (
                    'name' => 'taskcontroller.sorttasks',
                    'description' => '',
                ),
            44 =>
                array (
                    'name' => 'dealcontroller.index',
                    'description' => '',
                ),
            45 =>
                array (
                    'name' => 'dealcontroller.create',
                    'description' => '',
                ),
            46 =>
                array (
                    'name' => 'dealcontroller.store',
                    'description' => '',
                ),
            47 =>
                array (
                    'name' => 'dealcontroller.show',
                    'description' => '',
                ),
            48 =>
                array (
                    'name' => 'dealcontroller.edit',
                    'description' => '',
                ),
            49 =>
                array (
                    'name' => 'dealcontroller.update',
                    'description' => '',
                ),
            50 =>
                array (
                    'name' => 'dealcontroller.destroy',
                    'description' => '',
                ),
            51 =>
                array (
                    'name' => 'subscriptioncontroller.index',
                    'description' => '',
                ),
            52 =>
                array (
                    'name' => 'subscriptioncontroller.create',
                    'description' => '',
                ),
            53 =>
                array (
                    'name' => 'subscriptioncontroller.store',
                    'description' => '',
                ),
            54 =>
                array (
                    'name' => 'subscriptioncontroller.show',
                    'description' => '',
                ),
            55 =>
                array (
                    'name' => 'subscriptioncontroller.edit',
                    'description' => '',
                ),
            56 =>
                array (
                    'name' => 'subscriptioncontroller.update',
                    'description' => '',
                ),
            57 =>
                array (
                    'name' => 'subscriptioncontroller.destroy',
                    'description' => '',
                ),
            58 =>
                array (
                    'name' => 'expensecategorycontroller.index',
                    'description' => '',
                ),
            59 =>
                array (
                    'name' => 'expensecategorycontroller.create',
                    'description' => '',
                ),
            60 =>
                array (
                    'name' => 'expensecategorycontroller.store',
                    'description' => '',
                ),
            61 =>
                array (
                    'name' => 'expensecategorycontroller.show',
                    'description' => '',
                ),
            62 =>
                array (
                    'name' => 'expensecategorycontroller.edit',
                    'description' => '',
                ),
            63 =>
                array (
                    'name' => 'expensecategorycontroller.update',
                    'description' => '',
                ),
            64 =>
                array (
                    'name' => 'expensecategorycontroller.destroy',
                    'description' => '',
                ),
            65 =>
                array (
                    'name' => 'brandcontroller.index',
                    'description' => '',
                ),
            66 =>
                array (
                    'name' => 'brandcontroller.create',
                    'description' => '',
                ),
            67 =>
                array (
                    'name' => 'brandcontroller.store',
                    'description' => '',
                ),
            68 =>
                array (
                    'name' => 'brandcontroller.show',
                    'description' => '',
                ),
            69 =>
                array (
                    'name' => 'brandcontroller.edit',
                    'description' => '',
                ),
            70 =>
                array (
                    'name' => 'brandcontroller.update',
                    'description' => '',
                ),
            71 =>
                array (
                    'name' => 'brandcontroller.destroy',
                    'description' => '',
                ),
            72 =>
                array (
                    'name' => 'bankaccountcontroller.preview',
                    'description' => '',
                ),
            73 =>
                array (
                    'name' => 'bankaccountcontroller.import',
                    'description' => '',
                ),
            74 =>
                array (
                    'name' => 'bankaccountcontroller.index',
                    'description' => '',
                ),
            75 =>
                array (
                    'name' => 'bankaccountcontroller.create',
                    'description' => '',
                ),
            76 =>
                array (
                    'name' => 'bankaccountcontroller.store',
                    'description' => '',
                ),
            77 =>
                array (
                    'name' => 'bankaccountcontroller.show',
                    'description' => '',
                ),
            78 =>
                array (
                    'name' => 'bankaccountcontroller.edit',
                    'description' => '',
                ),
            79 =>
                array (
                    'name' => 'bankaccountcontroller.update',
                    'description' => '',
                ),
            80 =>
                array (
                    'name' => 'bankaccountcontroller.destroy',
                    'description' => '',
                ),
            81 =>
                array (
                    'name' => 'importcontroller.import',
                    'description' => '',
                ),
            82 =>
                array (
                    'name' => 'importcontroller.importpreview',
                    'description' => '',
                ),
            83 =>
                array (
                    'name' => 'importcontroller.export',
                    'description' => '',
                ),
            84 =>
                array (
                    'name' => 'bankcontroller.index',
                    'description' => '',
                ),
            85 =>
                array (
                    'name' => 'bankcontroller.create',
                    'description' => '',
                ),
            86 =>
                array (
                    'name' => 'bankcontroller.store',
                    'description' => '',
                ),
            87 =>
                array (
                    'name' => 'bankcontroller.show',
                    'description' => '',
                ),
            88 =>
                array (
                    'name' => 'bankcontroller.edit',
                    'description' => '',
                ),
            89 =>
                array (
                    'name' => 'bankcontroller.update',
                    'description' => '',
                ),
            90 =>
                array (
                    'name' => 'bankcontroller.destroy',
                    'description' => '',
                ),
            91 =>
                array (
                    'name' => 'designcontroller.index',
                    'description' => '',
                ),
            92 =>
                array (
                    'name' => 'designcontroller.create',
                    'description' => '',
                ),
            93 =>
                array (
                    'name' => 'designcontroller.store',
                    'description' => '',
                ),
            94 =>
                array (
                    'name' => 'designcontroller.show',
                    'description' => '',
                ),
            95 =>
                array (
                    'name' => 'designcontroller.edit',
                    'description' => '',
                ),
            96 =>
                array (
                    'name' => 'designcontroller.update',
                    'description' => '',
                ),
            97 =>
                array (
                    'name' => 'designcontroller.destroy',
                    'description' => '',
                ),
            98 =>
                array (
                    'name' => 'messagecontroller.getcustomers',
                    'description' => '',
                ),
            99 =>
                array (
                    'name' => 'messagecontroller.index',
                    'description' => '',
                ),
            100 =>
                array (
                    'name' => 'messagecontroller.store',
                    'description' => '',
                ),
            101 =>
                array (
                    'name' => 'companycontroller.index',
                    'description' => '',
                ),
            102 =>
                array (
                    'name' => 'companycontroller.restore',
                    'description' => '',
                ),
            103 =>
                array (
                    'name' => 'companycontroller.store',
                    'description' => '',
                ),
            104 =>
                array (
                    'name' => 'companycontroller.archive',
                    'description' => '',
                ),
            105 =>
                array (
                    'name' => 'companycontroller.destroy',
                    'description' => '',
                ),
            106 =>
                array (
                    'name' => 'companycontroller.show',
                    'description' => '',
                ),
            107 =>
                array (
                    'name' => 'companycontroller.update',
                    'description' => '',
                ),
            108 =>
                array (
                    'name' => 'companycontroller.getindustries',
                    'description' => '',
                ),
            109 =>
                array (
                    'name' => 'categorycontroller.index',
                    'description' => '',
                ),
            110 =>
                array (
                    'name' => 'categorycontroller.store',
                    'description' => '',
                ),
            111 =>
                array (
                    'name' => 'categorycontroller.destroy',
                    'description' => '',
                ),
            112 =>
                array (
                    'name' => 'categorycontroller.edit',
                    'description' => '',
                ),
            113 =>
                array (
                    'name' => 'categorycontroller.update',
                    'description' => '',
                ),
            114 =>
                array (
                    'name' => 'commentcontroller.index',
                    'description' => '',
                ),
            115 =>
                array (
                    'name' => 'commentcontroller.destroy',
                    'description' => '',
                ),
            116 =>
                array (
                    'name' => 'commentcontroller.update',
                    'description' => '',
                ),
            117 =>
                array (
                    'name' => 'commentcontroller.store',
                    'description' => '',
                ),
            118 =>
                array (
                    'name' => 'eventcontroller.index',
                    'description' => '',
                ),
            119 =>
                array (
                    'name' => 'eventcontroller.archive',
                    'description' => '',
                ),
            120 =>
                array (
                    'name' => 'eventcontroller.destroy',
                    'description' => '',
                ),
            121 =>
                array (
                    'name' => 'eventcontroller.update',
                    'description' => '',
                ),
            122 =>
                array (
                    'name' => 'eventcontroller.show',
                    'description' => '',
                ),
            123 =>
                array (
                    'name' => 'eventcontroller.store',
                    'description' => '',
                ),
            124 =>
                array (
                    'name' => 'eventcontroller.geteventsfortask',
                    'description' => '',
                ),
            125 =>
                array (
                    'name' => 'eventcontroller.geteventsforuser',
                    'description' => '',
                ),
            126 =>
                array (
                    'name' => 'eventcontroller.geteventtypes',
                    'description' => '',
                ),
            127 =>
                array (
                    'name' => 'eventcontroller.filterevents',
                    'description' => '',
                ),
            128 =>
                array (
                    'name' => 'eventcontroller.updateeventstatus',
                    'description' => '',
                ),
            129 =>
                array (
                    'name' => 'eventcontroller.restore',
                    'description' => '',
                ),
            130 =>
                array (
                    'name' => 'productcontroller.index',
                    'description' => '',
                ),
            131 =>
                array (
                    'name' => 'productcontroller.store',
                    'description' => '',
                ),
            132 =>
                array (
                    'name' => 'productcontroller.bulk',
                    'description' => '',
                ),
            133 =>
                array (
                    'name' => 'productcontroller.archive',
                    'description' => '',
                ),
            134 =>
                array (
                    'name' => 'productcontroller.destroy',
                    'description' => '',
                ),
            135 =>
                array (
                    'name' => 'productcontroller.removethumbnail',
                    'description' => '',
                ),
            136 =>
                array (
                    'name' => 'productcontroller.update',
                    'description' => '',
                ),
            137 =>
                array (
                    'name' => 'ordercontroller.getorderfortask',
                    'description' => '',
                ),
            138 =>
                array (
                    'name' => 'productcontroller.filterproducts',
                    'description' => '',
                ),
            139 =>
                array (
                    'name' => 'productcontroller.getproduct',
                    'description' => '',
                ),
            140 =>
                array (
                    'name' => 'productcontroller.restore',
                    'description' => '',
                ),
            141 =>
                array (
                    'name' => 'projectcontroller.index',
                    'description' => '',
                ),
            142 =>
                array (
                    'name' => 'projectcontroller.store',
                    'description' => '',
                ),
            143 =>
                array (
                    'name' => 'projectcontroller.show',
                    'description' => '',
                ),
            144 =>
                array (
                    'name' => 'projectcontroller.update',
                    'description' => '',
                ),
            145 =>
                array (
                    'name' => 'projectcontroller.archive',
                    'description' => '',
                ),
            146 =>
                array (
                    'name' => 'projectcontroller.destroy',
                    'description' => '',
                ),
            147 =>
                array (
                    'name' => 'projectcontroller.restore',
                    'description' => '',
                ),
            148 =>
                array (
                    'name' => 'ordercontroller.index',
                    'description' => '',
                ),
            149 =>
                array (
                    'name' => 'ordercontroller.update',
                    'description' => '',
                ),
            150 =>
                array (
                    'name' => 'ordercontroller.store',
                    'description' => '',
                ),
            151 =>
                array (
                    'name' => 'ordercontroller.action',
                    'description' => '',
                ),
            152 =>
                array (
                    'name' => 'ordercontroller.archive',
                    'description' => '',
                ),
            153 =>
                array (
                    'name' => 'ordercontroller.destroy',
                    'description' => '',
                ),
            154 =>
                array (
                    'name' => 'ordercontroller.restore',
                    'description' => '',
                ),
            155 =>
                array (
                    'name' => 'uploadcontroller.index',
                    'description' => '',
                ),
            156 =>
                array (
                    'name' => 'uploadcontroller.destroy',
                    'description' => '',
                ),
            157 =>
                array (
                    'name' => 'taskstatuscontroller.search',
                    'description' => '',
                ),
            158 =>
                array (
                    'name' => 'taskstatuscontroller.store',
                    'description' => '',
                ),
            159 =>
                array (
                    'name' => 'taskstatuscontroller.update',
                    'description' => '',
                ),
            160 =>
                array (
                    'name' => 'taskstatuscontroller.destroy',
                    'description' => '',
                ),
            161 =>
                array (
                    'name' => 'invoicecontroller.store',
                    'description' => '',
                ),
            162 =>
                array (
                    'name' => 'invoicecontroller.archive',
                    'description' => '',
                ),
            163 =>
                array (
                    'name' => 'invoicecontroller.destroy',
                    'description' => '',
                ),
            164 =>
                array (
                    'name' => 'invoicecontroller.restore',
                    'description' => '',
                ),
            165 =>
                array (
                    'name' => 'invoicecontroller.bulk',
                    'description' => '',
                ),
            166 =>
                array (
                    'name' => 'invoicecontroller.index',
                    'description' => '',
                ),
            167 =>
                array (
                    'name' => 'invoicecontroller.getinvoicelinesfortask',
                    'description' => '',
                ),
            168 =>
                array (
                    'name' => 'invoicecontroller.show',
                    'description' => '',
                ),
            169 =>
                array (
                    'name' => 'invoicecontroller.update',
                    'description' => '',
                ),
            170 =>
                array (
                    'name' => 'invoicecontroller.getinvoicesbystatus',
                    'description' => '',
                ),
            171 =>
                array (
                    'name' => 'invoicecontroller.action',
                    'description' => '',
                ),
            172 =>
                array (
                    'name' => 'recurringinvoicecontroller.store',
                    'description' => '',
                ),
            173 =>
                array (
                    'name' => 'recurringinvoicecontroller.bulk',
                    'description' => '',
                ),
            174 =>
                array (
                    'name' => 'recurringinvoicecontroller.update',
                    'description' => '',
                ),
            175 =>
                array (
                    'name' => 'recurringinvoicecontroller.archive',
                    'description' => '',
                ),
            176 =>
                array (
                    'name' => 'recurringinvoicecontroller.destroy',
                    'description' => '',
                ),
            177 =>
                array (
                    'name' => 'recurringinvoicecontroller.index',
                    'description' => '',
                ),
            178 =>
                array (
                    'name' => 'recurringinvoicecontroller.restore',
                    'description' => '',
                ),
            179 =>
                array (
                    'name' => 'recurringinvoicecontroller.action',
                    'description' => '',
                ),
            180 =>
                array (
                    'name' => 'recurringquotecontroller.update',
                    'description' => '',
                ),
            181 =>
                array (
                    'name' => 'recurringquotecontroller.index',
                    'description' => '',
                ),
            182 =>
                array (
                    'name' => 'recurringquotecontroller.store',
                    'description' => '',
                ),
            183 =>
                array (
                    'name' => 'recurringquotecontroller.bulk',
                    'description' => '',
                ),
            184 =>
                array (
                    'name' => 'recurringquotecontroller.archive',
                    'description' => '',
                ),
            185 =>
                array (
                    'name' => 'recurringquotecontroller.destroy',
                    'description' => '',
                ),
            186 =>
                array (
                    'name' => 'recurringquotecontroller.restore',
                    'description' => '',
                ),
            187 =>
                array (
                    'name' => 'recurringquotecontroller.action',
                    'description' => '',
                ),
            188 =>
                array (
                    'name' => 'creditcontroller.store',
                    'description' => '',
                ),
            189 =>
                array (
                    'name' => 'creditcontroller.archive',
                    'description' => '',
                ),
            190 =>
                array (
                    'name' => 'creditcontroller.destroy',
                    'description' => '',
                ),
            191 =>
                array (
                    'name' => 'creditcontroller.index',
                    'description' => '',
                ),
            192 =>
                array (
                    'name' => 'creditcontroller.update',
                    'description' => '',
                ),
            193 =>
                array (
                    'name' => 'creditcontroller.restore',
                    'description' => '',
                ),
            194 =>
                array (
                    'name' => 'creditcontroller.action',
                    'description' => '',
                ),
            195 =>
                array (
                    'name' => 'creditcontroller.getcreditsbystatus',
                    'description' => '',
                ),
            196 =>
                array (
                    'name' => 'expensecontroller.store',
                    'description' => '',
                ),
            197 =>
                array (
                    'name' => 'expensecontroller.archive',
                    'description' => '',
                ),
            198 =>
                array (
                    'name' => 'expensecontroller.destroy',
                    'description' => '',
                ),
            199 =>
                array (
                    'name' => 'expensecontroller.index',
                    'description' => '',
                ),
            200 =>
                array (
                    'name' => 'expensecontroller.show',
                    'description' => '',
                ),
            201 =>
                array (
                    'name' => 'expensecontroller.update',
                    'description' => '',
                ),
            202 =>
                array (
                    'name' => 'expensecontroller.restore',
                    'description' => '',
                ),
            203 =>
                array (
                    'name' => 'expensecontroller.bulk',
                    'description' => '',
                ),
            204 =>
                array (
                    'name' => 'quotecontroller.convert',
                    'description' => '',
                ),
            205 =>
                array (
                    'name' => 'quotecontroller.archive',
                    'description' => '',
                ),
            206 =>
                array (
                    'name' => 'quotecontroller.destroy',
                    'description' => '',
                ),
            207 =>
                array (
                    'name' => 'quotecontroller.approve',
                    'description' => '',
                ),
            208 =>
                array (
                    'name' => 'quotecontroller.store',
                    'description' => '',
                ),
            209 =>
                array (
                    'name' => 'quotecontroller.update',
                    'description' => '',
                ),
            210 =>
                array (
                    'name' => 'quotecontroller.index',
                    'description' => '',
                ),
            211 =>
                array (
                    'name' => 'quotecontroller.show',
                    'description' => '',
                ),
            212 =>
                array (
                    'name' => 'quotecontroller.action',
                    'description' => '',
                ),
            213 =>
                array (
                    'name' => 'quotecontroller.getquotelinesfortask',
                    'description' => '',
                ),
            214 =>
                array (
                    'name' => 'quotecontroller.restore',
                    'description' => '',
                ),
            215 =>
                array (
                    'name' => 'purchaseordercontroller.archive',
                    'description' => '',
                ),
            216 =>
                array (
                    'name' => 'purchaseordercontroller.destroy',
                    'description' => '',
                ),
            217 =>
                array (
                    'name' => 'purchaseordercontroller.store',
                    'description' => '',
                ),
            218 =>
                array (
                    'name' => 'purchaseordercontroller.update',
                    'description' => '',
                ),
            219 =>
                array (
                    'name' => 'purchaseordercontroller.index',
                    'description' => '',
                ),
            220 =>
                array (
                    'name' => 'purchaseordercontroller.show',
                    'description' => '',
                ),
            221 =>
                array (
                    'name' => 'purchaseordercontroller.action',
                    'description' => '',
                ),
            222 =>
                array (
                    'name' => 'purchaseordercontroller.restore',
                    'description' => '',
                ),
            223 =>
                array (
                    'name' => 'accountcontroller.store',
                    'description' => '',
                ),
            224 =>
                array (
                    'name' => 'accountcontroller.savecustomfields',
                    'description' => '',
                ),
            225 =>
                array (
                    'name' => 'accountcontroller.update',
                    'description' => '',
                ),
            226 =>
                array (
                    'name' => 'accountcontroller.refresh',
                    'description' => '',
                ),
            227 =>
                array (
                    'name' => 'accountcontroller.getallcustomfields',
                    'description' => '',
                ),
            228 =>
                array (
                    'name' => 'accountcontroller.getcustomfields',
                    'description' => '',
                ),
            229 =>
                array (
                    'name' => 'accountcontroller.index',
                    'description' => '',
                ),
            230 =>
                array (
                    'name' => 'accountcontroller.show',
                    'description' => '',
                ),
            231 =>
                array (
                    'name' => 'accountcontroller.getdateformats',
                    'description' => '',
                ),
            232 =>
                array (
                    'name' => 'accountcontroller.destroy',
                    'description' => '',
                ),
            233 =>
                array (
                    'name' => 'emailcontroller.send',
                    'description' => '',
                ),
            234 =>
                array (
                    'name' => 'accountcontroller.changeaccount',
                    'description' => '',
                ),
            235 =>
                array (
                    'name' => 'groupcontroller.index',
                    'description' => '',
                ),
            236 =>
                array (
                    'name' => 'groupcontroller.show',
                    'description' => '',
                ),
            237 =>
                array (
                    'name' => 'groupcontroller.archive',
                    'description' => '',
                ),
            238 =>
                array (
                    'name' => 'groupcontroller.destroy',
                    'description' => '',
                ),
            239 =>
                array (
                    'name' => 'groupcontroller.update',
                    'description' => '',
                ),
            240 =>
                array (
                    'name' => 'groupcontroller.store',
                    'description' => '',
                ),
            241 =>
                array (
                    'name' => 'groupcontroller.restore',
                    'description' => '',
                ),
            242 =>
                array (
                    'name' => 'templatecontroller.show',
                    'description' => '',
                ),
            243 =>
                array (
                    'name' => 'companygatewaycontroller.index',
                    'description' => '',
                ),
            244 =>
                array (
                    'name' => 'companygatewaycontroller.show',
                    'description' => '',
                ),
            245 =>
                array (
                    'name' => 'companygatewaycontroller.update',
                    'description' => '',
                ),
            246 =>
                array (
                    'name' => 'companygatewaycontroller.store',
                    'description' => '',
                ),
            247 =>
                array (
                    'name' => 'taxratecontroller.index',
                    'description' => '',
                ),
            248 =>
                array (
                    'name' => 'taxratecontroller.store',
                    'description' => '',
                ),
            249 =>
                array (
                    'name' => 'taxratecontroller.archive',
                    'description' => '',
                ),
            250 =>
                array (
                    'name' => 'taxratecontroller.destroy',
                    'description' => '',
                ),
            251 =>
                array (
                    'name' => 'taxratecontroller.edit',
                    'description' => '',
                ),
            252 =>
                array (
                    'name' => 'taxratecontroller.update',
                    'description' => '',
                ),
            253 =>
                array (
                    'name' => 'taxratecontroller.restore',
                    'description' => '',
                ),
            254 =>
                array (
                    'name' => 'paymentcontroller.index',
                    'description' => '',
                ),
            255 =>
                array (
                    'name' => 'paymentcontroller.show',
                    'description' => '',
                ),
            256 =>
                array (
                    'name' => 'paymentcontroller.store',
                    'description' => '',
                ),
            257 =>
                array (
                    'name' => 'paymentcontroller.bulk',
                    'description' => '',
                ),
            258 =>
                array (
                    'name' => 'paymentcontroller.archive',
                    'description' => '',
                ),
            259 =>
                array (
                    'name' => 'paymentcontroller.destroy',
                    'description' => '',
                ),
            260 =>
                array (
                    'name' => 'paymentcontroller.update',
                    'description' => '',
                ),
            261 =>
                array (
                    'name' => 'paymentcontroller.restore',
                    'description' => '',
                ),
            262 =>
                array (
                    'name' => 'paymentcontroller.action',
                    'description' => '',
                ),
            263 =>
                array (
                    'name' => 'paymenttermscontroller.index',
                    'description' => '',
                ),
            264 =>
                array (
                    'name' => 'paymenttermscontroller.create',
                    'description' => '',
                ),
            265 =>
                array (
                    'name' => 'paymenttermscontroller.store',
                    'description' => '',
                ),
            266 =>
                array (
                    'name' => 'paymenttermscontroller.show',
                    'description' => '',
                ),
            267 =>
                array (
                    'name' => 'paymenttermscontroller.edit',
                    'description' => '',
                ),
            268 =>
                array (
                    'name' => 'paymenttermscontroller.update',
                    'description' => '',
                ),
            269 =>
                array (
                    'name' => 'paymenttermscontroller.destroy',
                    'description' => '',
                ),
            270 =>
                array (
                    'name' => 'paymenttypecontroller.index',
                    'description' => '',
                ),
            271 =>
                array (
                    'name' => 'setupcontroller.healthcheck',
                    'description' => '',
                ),
            272 =>
                array (
                    'name' => 'customercontroller.index',
                    'description' => '',
                ),
            273 =>
                array (
                    'name' => 'customercontroller.dashboard',
                    'description' => '',
                ),
            274 =>
                array (
                    'name' => 'customercontroller.show',
                    'description' => '',
                ),
            275 =>
                array (
                    'name' => 'customercontroller.update',
                    'description' => '',
                ),
            276 =>
                array (
                    'name' => 'customercontroller.store',
                    'description' => '',
                ),
            277 =>
                array (
                    'name' => 'customercontroller.bulk',
                    'description' => '',
                ),
            278 =>
                array (
                    'name' => 'customercontroller.archive',
                    'description' => '',
                ),
            279 =>
                array (
                    'name' => 'customercontroller.destroy',
                    'description' => '',
                ),
            280 =>
                array (
                    'name' => 'customercontroller.getcustomertypes',
                    'description' => '',
                ),
            281 =>
                array (
                    'name' => 'customercontroller.restore',
                    'description' => '',
                ),
            282 =>
                array (
                    'name' => 'taskcontroller.restore',
                    'description' => '',
                ),
            283 =>
                array (
                    'name' => 'taskcontroller.update',
                    'description' => '',
                ),
            284 =>
                array (
                    'name' => 'taskcontroller.store',
                    'description' => '',
                ),
            285 =>
                array (
                    'name' => 'taskcontroller.gettasksforproject',
                    'description' => '',
                ),
            286 =>
                array (
                    'name' => 'taskcontroller.markascompleted',
                    'description' => '',
                ),
            287 =>
                array (
                    'name' => 'taskcontroller.destroy',
                    'description' => '',
                ),
            288 =>
                array (
                    'name' => 'taskcontroller.updatestatus',
                    'description' => '',
                ),
            289 =>
                array (
                    'name' => 'taskcontroller.index',
                    'description' => '',
                ),
            290 =>
                array (
                    'name' => 'taskcontroller.getsubtasks',
                    'description' => '',
                ),
            291 =>
                array (
                    'name' => 'taskcontroller.getproducts',
                    'description' => '',
                ),
            292 =>
                array (
                    'name' => 'taskcontroller.gettaskswithproducts',
                    'description' => '',
                ),
            293 =>
                array (
                    'name' => 'taskcontroller.getsourcetypes',
                    'description' => '',
                ),
            294 =>
                array (
                    'name' => 'taskcontroller.gettasktypes',
                    'description' => '',
                ),
            295 =>
                array (
                    'name' => 'taskcontroller.converttodeal',
                    'description' => '',
                ),
            296 =>
                array (
                    'name' => 'taskcontroller.updatetimer',
                    'description' => '',
                ),
            297 =>
                array (
                    'name' => 'taskcontroller.archive',
                    'description' => '',
                ),
            298 =>
                array (
                    'name' => 'taskcontroller.action',
                    'description' => '',
                ),
            299 =>
                array (
                    'name' => 'taskcontroller.bulk',
                    'description' => '',
                ),
            300 =>
                array (
                    'name' => 'taskcontroller.show',
                    'description' => '',
                ),
            301 =>
                array (
                    'name' => 'leadcontroller.index',
                    'description' => '',
                ),
            302 =>
                array (
                    'name' => 'leadcontroller.update',
                    'description' => '',
                ),
            303 =>
                array (
                    'name' => 'leadcontroller.show',
                    'description' => '',
                ),
            304 =>
                array (
                    'name' => 'leadcontroller.archive',
                    'description' => '',
                ),
            305 =>
                array (
                    'name' => 'leadcontroller.destroy',
                    'description' => '',
                ),
            306 =>
                array (
                    'name' => 'leadcontroller.restore',
                    'description' => '',
                ),
            307 =>
                array (
                    'name' => 'leadcontroller.action',
                    'description' => '',
                ),
            308 =>
                array (
                    'name' => 'leadcontroller.sorttasks',
                    'description' => '',
                ),
            309 =>
                array (
                    'name' => 'usercontroller.archive',
                    'description' => '',
                ),
            310 =>
                array (
                    'name' => 'usercontroller.destroy',
                    'description' => '',
                ),
            311 =>
                array (
                    'name' => 'usercontroller.store',
                    'description' => '',
                ),
            312 =>
                array (
                    'name' => 'usercontroller.dashboard',
                    'description' => '',
                ),
            313 =>
                array (
                    'name' => 'usercontroller.edit',
                    'description' => '',
                ),
            314 =>
                array (
                    'name' => 'usercontroller.update',
                    'description' => '',
                ),
            315 =>
                array (
                    'name' => 'usercontroller.index',
                    'description' => '',
                ),
            316 =>
                array (
                    'name' => 'usercontroller.upload',
                    'description' => '',
                ),
            317 =>
                array (
                    'name' => 'usercontroller.bulk',
                    'description' => '',
                ),
            318 =>
                array (
                    'name' => 'usercontroller.profile',
                    'description' => '',
                ),
            319 =>
                array (
                    'name' => 'usercontroller.filterusersbydepartment',
                    'description' => '',
                ),
            320 =>
                array (
                    'name' => 'usercontroller.restore',
                    'description' => '',
                ),
            321 =>
                array (
                    'name' => 'permissioncontroller.index',
                    'description' => '',
                ),
            322 =>
                array (
                    'name' => 'permissioncontroller.store',
                    'description' => '',
                ),
            323 =>
                array (
                    'name' => 'permissioncontroller.destroy',
                    'description' => '',
                ),
            324 =>
                array (
                    'name' => 'permissioncontroller.edit',
                    'description' => '',
                ),
            325 =>
                array (
                    'name' => 'permissioncontroller.update',
                    'description' => '',
                ),
            326 =>
                array (
                    'name' => 'rolecontroller.index',
                    'description' => '',
                ),
            327 =>
                array (
                    'name' => 'rolecontroller.store',
                    'description' => '',
                ),
            328 =>
                array (
                    'name' => 'rolecontroller.destroy',
                    'description' => '',
                ),
            329 =>
                array (
                    'name' => 'rolecontroller.edit',
                    'description' => '',
                ),
            330 =>
                array (
                    'name' => 'rolecontroller.update',
                    'description' => '',
                ),
            331 =>
                array (
                    'name' => 'departmentcontroller.index',
                    'description' => '',
                ),
            332 =>
                array (
                    'name' => 'departmentcontroller.store',
                    'description' => '',
                ),
            333 =>
                array (
                    'name' => 'departmentcontroller.destroy',
                    'description' => '',
                ),
            334 =>
                array (
                    'name' => 'departmentcontroller.edit',
                    'description' => '',
                ),
            335 =>
                array (
                    'name' => 'departmentcontroller.update',
                    'description' => '',
                ),
            336 =>
                array (
                    'name' => 'countrycontroller.index',
                    'description' => '',
                ),
            337 =>
                array (
                    'name' => 'currencycontroller.index',
                    'description' => '',
                ),
            338 =>
                array (
                    'name' => 'timercontroller.index',
                    'description' => '',
                ),
            339 =>
                array (
                    'name' => 'timercontroller.create',
                    'description' => '',
                ),
            340 =>
                array (
                    'name' => 'timercontroller.store',
                    'description' => '',
                ),
            341 =>
                array (
                    'name' => 'timercontroller.show',
                    'description' => '',
                ),
            342 =>
                array (
                    'name' => 'timercontroller.edit',
                    'description' => '',
                ),
            343 =>
                array (
                    'name' => 'timercontroller.update',
                    'description' => '',
                ),
            344 =>
                array (
                    'name' => 'timercontroller.destroy',
                    'description' => '',
                ),
            345 =>
                array (
                    'name' => 'attributecontroller.index',
                    'description' => '',
                ),
            346 =>
                array (
                    'name' => 'attributecontroller.create',
                    'description' => '',
                ),
            347 =>
                array (
                    'name' => 'attributecontroller.store',
                    'description' => '',
                ),
            348 =>
                array (
                    'name' => 'attributecontroller.show',
                    'description' => '',
                ),
            349 =>
                array (
                    'name' => 'attributecontroller.edit',
                    'description' => '',
                ),
            350 =>
                array (
                    'name' => 'attributecontroller.update',
                    'description' => '',
                ),
            351 =>
                array (
                    'name' => 'attributecontroller.destroy',
                    'description' => '',
                ),
            352 =>
                array (
                    'name' => 'attributevaluecontroller.index',
                    'description' => '',
                ),
            353 =>
                array (
                    'name' => 'attributevaluecontroller.create',
                    'description' => '',
                ),
            354 =>
                array (
                    'name' => 'attributevaluecontroller.store',
                    'description' => '',
                ),
            355 =>
                array (
                    'name' => 'attributevaluecontroller.show',
                    'description' => '',
                ),
            356 =>
                array (
                    'name' => 'attributevaluecontroller.edit',
                    'description' => '',
                ),
            357 =>
                array (
                    'name' => 'attributevaluecontroller.update',
                    'description' => '',
                ),
            358 =>
                array (
                    'name' => 'attributevaluecontroller.destroy',
                    'description' => '',
                ),
            359 =>
                array (
                    'name' => 'logincontroller.showlogin',
                    'description' => '',
                ),
            360 =>
                array (
                    'name' => 'logincontroller.dologin',
                    'description' => '',
                ),
            361 =>
                array (
                    'name' => 'logincontroller.dologout',
                    'description' => '',
                ),
            362 =>
                array (
                    'name' => 'auth\\forgotpasswordcontroller.sendresetlinkemail',
                    'description' => '',
                ),
            363 =>
                array (
                    'name' => 'auth\\verificationcontroller.resend',
                    'description' => '',
                ),
            364 =>
                array (
                    'name' => 'categorycontroller.getrootcategories',
                    'description' => '',
                ),
            365 =>
                array (
                    'name' => 'categorycontroller.getchildcategories',
                    'description' => '',
                ),
            366 =>
                array (
                    'name' => 'categorycontroller.getform',
                    'description' => '',
                ),
            367 =>
                array (
                    'name' => 'categorycontroller.getcategory',
                    'description' => '',
                ),
            368 =>
                array (
                    'name' => 'taskcontroller.addproducts',
                    'description' => '',
                ),
            369 =>
                array (
                    'name' => 'taskcontroller.createdeal',
                    'description' => '',
                ),
            370 =>
                array (
                    'name' => 'leadcontroller.store',
                    'description' => '',
                ),
            371 =>
                array (
                    'name' => 'leadcontroller.convert',
                    'description' => '',
                ),
            372 =>
                array (
                    'name' => 'paymentcontroller.refund',
                    'description' => '',
                ),
            373 =>
                array (
                    'name' => 'invoicecontroller.markviewed',
                    'description' => '',
                ),
            374 =>
                array (
                    'name' => 'quotecontroller.markviewed',
                    'description' => '',
                ),
            375 =>
                array (
                    'name' => 'creditcontroller.markviewed',
                    'description' => '',
                ),
            376 =>
                array (
                    'name' => 'ordercontroller.markviewed',
                    'description' => '',
                ),
            377 =>
                array (
                    'name' => 'invoicecontroller.downloadpdf',
                    'description' => '',
                ),
            378 =>
                array (
                    'name' => 'quotecontroller.downloadpdf',
                    'description' => '',
                ),
            379 =>
                array (
                    'name' => 'ordercontroller.downloadpdf',
                    'description' => '',
                ),
            380 =>
                array (
                    'name' => 'creditcontroller.downloadpdf',
                    'description' => '',
                ),
            381 =>
                array (
                    'name' => 'paymentcontroller.completepayment',
                    'description' => '',
                ),
            382 =>
                array (
                    'name' => 'recurringinvoicecontroller.requestcancellation',
                    'description' => '',
                ),
            383 =>
                array (
                    'name' => 'quotecontroller.bulk',
                    'description' => '',
                ),
            384 =>
                array (
                    'name' => 'ordercontroller.bulk',
                    'description' => '',
                ),
            385 =>
                array (
                    'name' => 'productcontroller.show',
                    'description' => '',
                ),
            386 =>
                array (
                    'name' => 'productcontroller.find',
                    'description' => '',
                ),
            387 =>
                array (
                    'name' => 'productcontroller.getproductsforcategory',
                    'description' => '',
                ),
            388 =>
                array (
                    'name' => 'promocodecontroller.apply',
                    'description' => '',
                ),
            389 =>
                array (
                    'name' => 'promocodecontroller.validatecode',
                    'description' => '',
                ),
            390 =>
                array (
                    'name' => 'shippingcontroller.getrates',
                    'description' => '',
                ),
            391 =>
                array (
                    'name' => 'previewcontroller.show',
                    'description' => '',
                ),
            392 =>
                array (
                    'name' => 'customercontroller.register',
                    'description' => '',
                ),
            393 =>
                array (
                    'name' => 'casecontroller.index',
                    'description' => '',
                ),
            394 =>
                array (
                    'name' => 'casecontroller.create',
                    'description' => '',
                ),
            395 =>
                array (
                    'name' => 'casecontroller.store',
                    'description' => '',
                ),
            396 =>
                array (
                    'name' => 'casecontroller.show',
                    'description' => '',
                ),
            397 =>
                array (
                    'name' => 'casecontroller.edit',
                    'description' => '',
                ),
            398 =>
                array (
                    'name' => 'casecontroller.update',
                    'description' => '',
                ),
            399 =>
                array (
                    'name' => 'casecontroller.destroy',
                    'description' => '',
                ),
            400 =>
                array (
                    'name' => 'casecontroller.action',
                    'description' => '',
                ),
            401 =>
                array (
                    'name' => 'casecategorycontroller.index',
                    'description' => '',
                ),
            402 =>
                array (
                    'name' => 'casecategorycontroller.create',
                    'description' => '',
                ),
            403 =>
                array (
                    'name' => 'casecategorycontroller.store',
                    'description' => '',
                ),
            404 =>
                array (
                    'name' => 'casecategorycontroller.show',
                    'description' => '',
                ),
            405 =>
                array (
                    'name' => 'casecategorycontroller.edit',
                    'description' => '',
                ),
            406 =>
                array (
                    'name' => 'casecategorycontroller.update',
                    'description' => '',
                ),
            407 =>
                array (
                    'name' => 'casecategorycontroller.destroy',
                    'description' => '',
                ),
            408 =>
                array (
                    'name' => 'casetemplatecontroller.index',
                    'description' => '',
                ),
            409 =>
                array (
                    'name' => 'casetemplatecontroller.create',
                    'description' => '',
                ),
            410 =>
                array (
                    'name' => 'casetemplatecontroller.store',
                    'description' => '',
                ),
            411 =>
                array (
                    'name' => 'casetemplatecontroller.show',
                    'description' => '',
                ),
            412 =>
                array (
                    'name' => 'casetemplatecontroller.edit',
                    'description' => '',
                ),
            413 =>
                array (
                    'name' => 'casetemplatecontroller.update',
                    'description' => '',
                ),
            414 =>
                array (
                    'name' => 'casetemplatecontroller.destroy',
                    'description' => '',
                ),
            415 =>
                array (
                    'name' => 'uploadcontroller.store',
                    'description' => '',
                ),
            416 =>
                array (
                    'name' => 'statementcontroller.download',
                    'description' => '',
                ),
            417 =>
                array (
                    'name' => 'setupcontroller.finish',
                    'description' => '',
                ),
            418 =>
                array (
                    'name' => 'auth\\resetpasswordcontroller.getpassword',
                    'description' => '',
                ),
            419 =>
                array (
                    'name' => 'auth\\resetpasswordcontroller.updatepassword',
                    'description' => '',
                ),
            420 =>
                array (
                    'name' => 'twofactorcontroller.enabletwofactorauthenticationforuser',
                    'description' => '',
                ),
            421 =>
                array (
                    'name' => 'twofactorcontroller.show2faform',
                    'description' => '',
                ),
            422 =>
                array (
                    'name' => 'twofactorcontroller.verifytoken',
                    'description' => '',
                ),
            423 =>
                array (
                    'name' => 'setupcontroller.welcome',
                    'description' => '',
                ),
            424 =>
                array (
                    'name' => 'setupcontroller.requirements',
                    'description' => '',
                ),
            425 =>
                array (
                    'name' => 'setupcontroller.permissions',
                    'description' => '',
                ),
            426 =>
                array (
                    'name' => 'setupcontroller.environmentmenu',
                    'description' => '',
                ),
            427 =>
                array (
                    'name' => 'setupcontroller.database',
                    'description' => '',
                ),
            428 =>
                array (
                    'name' => 'setupcontroller.user',
                    'description' => '',
                ),
            429 =>
                array (
                    'name' => 'setupcontroller.twofactorsetup',
                    'description' => '',
                ),
            430 =>
                array (
                    'name' => 'setupcontroller.environmentwizard',
                    'description' => '',
                ),
            431 =>
                array (
                    'name' => 'setupcontroller.saveuser',
                    'description' => '',
                ),
            432 =>
                array (
                    'name' => 'setupcontroller.savewizard',
                    'description' => '',
                ),
            433 =>
                array (
                    'name' => 'setupcontroller.environmentclassic',
                    'description' => '',
                ),
            434 =>
                array (
                    'name' => 'buynowcontroller.buynowtrigger',
                    'description' => '',
                ),
            435 =>
                array (
                    'name' => 'paymentcontroller.buynow',
                    'description' => '',
                ),
            436 =>
                array (
                    'name' => 'paymentcontroller.buynowsuccess',
                    'description' => '',
                ),
            437 =>
                array (
                    'name' => 'illuminate\\routing\\viewcontroller.\\illuminate\\routing\\viewcontroller',
                    'description' => '',
                ),
            438 =>
                array (
                    'name' => 'logincontroller.redirecttogoogle',
                    'description' => '',
                ),
            439 =>
                array (
                    'name' => 'logincontroller.handlegooglecallback',
                    'description' => '',
                ),
        );

         foreach ($permissions as $permission) {

             $permissionName = $permission['name'];

             $exists = Permission::where('name', '=', $permissionName)->first();

             if (!$exists) {
                 $flight = Permission::create(['name' => $permissionName]);
                 $arrPermissions[] = $flight->id;
             }
         }

        foreach(Permission::all() as $permission) {
           $role->permissions()->attach($permission->id);
        }

        //
    }

}
