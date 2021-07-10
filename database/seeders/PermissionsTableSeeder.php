<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        DB::table('permissions')->delete();
        
        DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => 1406,
                'name' => 'dashboardstatscontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            1 => 
            array (
                'id' => 1407,
                'name' => 'workloadcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            2 => 
            array (
                'id' => 1408,
                'name' => 'mastersupervisorcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            3 => 
            array (
                'id' => 1409,
                'name' => 'monitoringcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            4 => 
            array (
                'id' => 1410,
                'name' => 'monitoringcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            5 => 
            array (
                'id' => 1411,
                'name' => 'monitoringcontroller.paginate',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            6 => 
            array (
                'id' => 1412,
                'name' => 'monitoringcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            7 => 
            array (
                'id' => 1413,
                'name' => 'jobmetricscontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            8 => 
            array (
                'id' => 1414,
                'name' => 'jobmetricscontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            9 => 
            array (
                'id' => 1415,
                'name' => 'queuemetricscontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            10 => 
            array (
                'id' => 1416,
                'name' => 'queuemetricscontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:16',
                'updated_at' => '2021-03-03 19:05:16',
            ),
            11 => 
            array (
                'id' => 1417,
                'name' => 'batchescontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            12 => 
            array (
                'id' => 1418,
                'name' => 'batchescontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            13 => 
            array (
                'id' => 1419,
                'name' => 'batchescontroller.retry',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            14 => 
            array (
                'id' => 1420,
                'name' => 'pendingjobscontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            15 => 
            array (
                'id' => 1421,
                'name' => 'completedjobscontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            16 => 
            array (
                'id' => 1422,
                'name' => 'failedjobscontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            17 => 
            array (
                'id' => 1423,
                'name' => 'failedjobscontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            18 => 
            array (
                'id' => 1424,
                'name' => 'retrycontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            19 => 
            array (
                'id' => 1425,
                'name' => 'jobscontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            20 => 
            array (
                'id' => 1426,
                'name' => 'homecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            21 => 
            array (
                'id' => 1427,
                'name' => 'losure.closure',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            22 => 
            array (
                'id' => 1428,
                'name' => 'taskstatuscontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            23 => 
            array (
                'id' => 1429,
                'name' => 'dashboardcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            24 => 
            array (
                'id' => 1430,
                'name' => 'reportcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:17',
                'updated_at' => '2021-03-03 19:05:17',
            ),
            25 => 
            array (
                'id' => 1431,
                'name' => 'activitycontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            26 => 
            array (
                'id' => 1432,
                'name' => 'supportcontroller.app\\http\\controllers\\supportcontroller',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            27 => 
            array (
                'id' => 1433,
                'name' => 'companyledgercontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            28 => 
            array (
                'id' => 1434,
                'name' => 'tokencontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            29 => 
            array (
                'id' => 1435,
                'name' => 'tokencontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            30 => 
            array (
                'id' => 1436,
                'name' => 'tokencontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            31 => 
            array (
                'id' => 1437,
                'name' => 'tokencontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            32 => 
            array (
                'id' => 1438,
                'name' => 'tokencontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            33 => 
            array (
                'id' => 1439,
                'name' => 'tokencontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            34 => 
            array (
                'id' => 1440,
                'name' => 'tokencontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            35 => 
            array (
                'id' => 1441,
                'name' => 'promocodecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:18',
                'updated_at' => '2021-03-03 19:05:18',
            ),
            36 => 
            array (
                'id' => 1442,
                'name' => 'promocodecontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            37 => 
            array (
                'id' => 1443,
                'name' => 'promocodecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            38 => 
            array (
                'id' => 1444,
                'name' => 'promocodecontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            39 => 
            array (
                'id' => 1445,
                'name' => 'promocodecontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            40 => 
            array (
                'id' => 1446,
                'name' => 'promocodecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            41 => 
            array (
                'id' => 1447,
                'name' => 'promocodecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            42 => 
            array (
                'id' => 1448,
                'name' => 'dealcontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            43 => 
            array (
                'id' => 1449,
                'name' => 'taskcontroller.sorttasks',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            44 => 
            array (
                'id' => 1450,
                'name' => 'dealcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            45 => 
            array (
                'id' => 1451,
                'name' => 'dealcontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            46 => 
            array (
                'id' => 1452,
                'name' => 'dealcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            47 => 
            array (
                'id' => 1453,
                'name' => 'dealcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            48 => 
            array (
                'id' => 1454,
                'name' => 'dealcontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            49 => 
            array (
                'id' => 1455,
                'name' => 'dealcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:19',
                'updated_at' => '2021-03-03 19:05:19',
            ),
            50 => 
            array (
                'id' => 1456,
                'name' => 'dealcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            51 => 
            array (
                'id' => 1457,
                'name' => 'subscriptioncontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            52 => 
            array (
                'id' => 1458,
                'name' => 'subscriptioncontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            53 => 
            array (
                'id' => 1459,
                'name' => 'subscriptioncontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            54 => 
            array (
                'id' => 1460,
                'name' => 'subscriptioncontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            55 => 
            array (
                'id' => 1461,
                'name' => 'subscriptioncontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            56 => 
            array (
                'id' => 1462,
                'name' => 'subscriptioncontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            57 => 
            array (
                'id' => 1463,
                'name' => 'subscriptioncontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            58 => 
            array (
                'id' => 1464,
                'name' => 'expensecategorycontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            59 => 
            array (
                'id' => 1465,
                'name' => 'expensecategorycontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            60 => 
            array (
                'id' => 1466,
                'name' => 'expensecategorycontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            61 => 
            array (
                'id' => 1467,
                'name' => 'expensecategorycontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            62 => 
            array (
                'id' => 1468,
                'name' => 'expensecategorycontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            63 => 
            array (
                'id' => 1469,
                'name' => 'expensecategorycontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            64 => 
            array (
                'id' => 1470,
                'name' => 'expensecategorycontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            65 => 
            array (
                'id' => 1471,
                'name' => 'brandcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:20',
                'updated_at' => '2021-03-03 19:05:20',
            ),
            66 => 
            array (
                'id' => 1472,
                'name' => 'brandcontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            67 => 
            array (
                'id' => 1473,
                'name' => 'brandcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            68 => 
            array (
                'id' => 1474,
                'name' => 'brandcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            69 => 
            array (
                'id' => 1475,
                'name' => 'brandcontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            70 => 
            array (
                'id' => 1476,
                'name' => 'brandcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            71 => 
            array (
                'id' => 1477,
                'name' => 'brandcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            72 => 
            array (
                'id' => 1478,
                'name' => 'bankaccountcontroller.preview',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            73 => 
            array (
                'id' => 1479,
                'name' => 'bankaccountcontroller.import',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            74 => 
            array (
                'id' => 1480,
                'name' => 'bankaccountcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            75 => 
            array (
                'id' => 1481,
                'name' => 'bankaccountcontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            76 => 
            array (
                'id' => 1482,
                'name' => 'bankaccountcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            77 => 
            array (
                'id' => 1483,
                'name' => 'bankaccountcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            78 => 
            array (
                'id' => 1484,
                'name' => 'bankaccountcontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            79 => 
            array (
                'id' => 1485,
                'name' => 'bankaccountcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:21',
                'updated_at' => '2021-03-03 19:05:21',
            ),
            80 => 
            array (
                'id' => 1486,
                'name' => 'bankaccountcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            81 => 
            array (
                'id' => 1487,
                'name' => 'importcontroller.import',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            82 => 
            array (
                'id' => 1488,
                'name' => 'importcontroller.importpreview',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            83 => 
            array (
                'id' => 1489,
                'name' => 'importcontroller.export',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            84 => 
            array (
                'id' => 1490,
                'name' => 'bankcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            85 => 
            array (
                'id' => 1491,
                'name' => 'bankcontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            86 => 
            array (
                'id' => 1492,
                'name' => 'bankcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            87 => 
            array (
                'id' => 1493,
                'name' => 'bankcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            88 => 
            array (
                'id' => 1494,
                'name' => 'bankcontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            89 => 
            array (
                'id' => 1495,
                'name' => 'bankcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            90 => 
            array (
                'id' => 1496,
                'name' => 'bankcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            91 => 
            array (
                'id' => 1497,
                'name' => 'designcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            92 => 
            array (
                'id' => 1498,
                'name' => 'designcontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            93 => 
            array (
                'id' => 1499,
                'name' => 'designcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            94 => 
            array (
                'id' => 1500,
                'name' => 'designcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            95 => 
            array (
                'id' => 1501,
                'name' => 'designcontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            96 => 
            array (
                'id' => 1502,
                'name' => 'designcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:22',
                'updated_at' => '2021-03-03 19:05:22',
            ),
            97 => 
            array (
                'id' => 1503,
                'name' => 'designcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            98 => 
            array (
                'id' => 1504,
                'name' => 'messagecontroller.getcustomers',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            99 => 
            array (
                'id' => 1505,
                'name' => 'messagecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            100 => 
            array (
                'id' => 1506,
                'name' => 'messagecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            101 => 
            array (
                'id' => 1507,
                'name' => 'companycontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            102 => 
            array (
                'id' => 1508,
                'name' => 'companycontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            103 => 
            array (
                'id' => 1509,
                'name' => 'companycontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            104 => 
            array (
                'id' => 1510,
                'name' => 'companycontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            105 => 
            array (
                'id' => 1511,
                'name' => 'companycontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            106 => 
            array (
                'id' => 1512,
                'name' => 'companycontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            107 => 
            array (
                'id' => 1513,
                'name' => 'companycontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            108 => 
            array (
                'id' => 1514,
                'name' => 'companycontroller.getindustries',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            109 => 
            array (
                'id' => 1515,
                'name' => 'categorycontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            110 => 
            array (
                'id' => 1516,
                'name' => 'categorycontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            111 => 
            array (
                'id' => 1517,
                'name' => 'categorycontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:23',
                'updated_at' => '2021-03-03 19:05:23',
            ),
            112 => 
            array (
                'id' => 1518,
                'name' => 'categorycontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            113 => 
            array (
                'id' => 1519,
                'name' => 'categorycontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            114 => 
            array (
                'id' => 1520,
                'name' => 'commentcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            115 => 
            array (
                'id' => 1521,
                'name' => 'commentcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            116 => 
            array (
                'id' => 1522,
                'name' => 'commentcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            117 => 
            array (
                'id' => 1523,
                'name' => 'commentcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            118 => 
            array (
                'id' => 1524,
                'name' => 'eventcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            119 => 
            array (
                'id' => 1525,
                'name' => 'eventcontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            120 => 
            array (
                'id' => 1526,
                'name' => 'eventcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            121 => 
            array (
                'id' => 1527,
                'name' => 'eventcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            122 => 
            array (
                'id' => 1528,
                'name' => 'eventcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            123 => 
            array (
                'id' => 1529,
                'name' => 'eventcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:24',
                'updated_at' => '2021-03-03 19:05:24',
            ),
            124 => 
            array (
                'id' => 1530,
                'name' => 'eventcontroller.geteventsfortask',
                'description' => '',
                'created_at' => '2021-03-03 19:05:25',
                'updated_at' => '2021-03-03 19:05:25',
            ),
            125 => 
            array (
                'id' => 1531,
                'name' => 'eventcontroller.geteventsforuser',
                'description' => '',
                'created_at' => '2021-03-03 19:05:25',
                'updated_at' => '2021-03-03 19:05:25',
            ),
            126 => 
            array (
                'id' => 1532,
                'name' => 'eventcontroller.geteventtypes',
                'description' => '',
                'created_at' => '2021-03-03 19:05:25',
                'updated_at' => '2021-03-03 19:05:25',
            ),
            127 => 
            array (
                'id' => 1533,
                'name' => 'eventcontroller.filterevents',
                'description' => '',
                'created_at' => '2021-03-03 19:05:25',
                'updated_at' => '2021-03-03 19:05:25',
            ),
            128 => 
            array (
                'id' => 1534,
                'name' => 'eventcontroller.updateeventstatus',
                'description' => '',
                'created_at' => '2021-03-03 19:05:25',
                'updated_at' => '2021-03-03 19:05:25',
            ),
            129 => 
            array (
                'id' => 1535,
                'name' => 'eventcontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:25',
                'updated_at' => '2021-03-03 19:05:25',
            ),
            130 => 
            array (
                'id' => 1536,
                'name' => 'productcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:25',
                'updated_at' => '2021-03-03 19:05:25',
            ),
            131 => 
            array (
                'id' => 1537,
                'name' => 'productcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:25',
                'updated_at' => '2021-03-03 19:05:25',
            ),
            132 => 
            array (
                'id' => 1538,
                'name' => 'productcontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:25',
                'updated_at' => '2021-03-03 19:05:25',
            ),
            133 => 
            array (
                'id' => 1539,
                'name' => 'productcontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:26',
                'updated_at' => '2021-03-03 19:05:26',
            ),
            134 => 
            array (
                'id' => 1540,
                'name' => 'productcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:26',
                'updated_at' => '2021-03-03 19:05:26',
            ),
            135 => 
            array (
                'id' => 1541,
                'name' => 'productcontroller.removethumbnail',
                'description' => '',
                'created_at' => '2021-03-03 19:05:26',
                'updated_at' => '2021-03-03 19:05:26',
            ),
            136 => 
            array (
                'id' => 1542,
                'name' => 'productcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:26',
                'updated_at' => '2021-03-03 19:05:26',
            ),
            137 => 
            array (
                'id' => 1543,
                'name' => 'ordercontroller.getorderfortask',
                'description' => '',
                'created_at' => '2021-03-03 19:05:26',
                'updated_at' => '2021-03-03 19:05:26',
            ),
            138 => 
            array (
                'id' => 1544,
                'name' => 'productcontroller.filterproducts',
                'description' => '',
                'created_at' => '2021-03-03 19:05:27',
                'updated_at' => '2021-03-03 19:05:27',
            ),
            139 => 
            array (
                'id' => 1545,
                'name' => 'productcontroller.getproduct',
                'description' => '',
                'created_at' => '2021-03-03 19:05:27',
                'updated_at' => '2021-03-03 19:05:27',
            ),
            140 => 
            array (
                'id' => 1546,
                'name' => 'productcontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:27',
                'updated_at' => '2021-03-03 19:05:27',
            ),
            141 => 
            array (
                'id' => 1547,
                'name' => 'projectcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:27',
                'updated_at' => '2021-03-03 19:05:27',
            ),
            142 => 
            array (
                'id' => 1548,
                'name' => 'projectcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:27',
                'updated_at' => '2021-03-03 19:05:27',
            ),
            143 => 
            array (
                'id' => 1549,
                'name' => 'projectcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:27',
                'updated_at' => '2021-03-03 19:05:27',
            ),
            144 => 
            array (
                'id' => 1550,
                'name' => 'projectcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:27',
                'updated_at' => '2021-03-03 19:05:27',
            ),
            145 => 
            array (
                'id' => 1551,
                'name' => 'projectcontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:28',
                'updated_at' => '2021-03-03 19:05:28',
            ),
            146 => 
            array (
                'id' => 1552,
                'name' => 'projectcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:28',
                'updated_at' => '2021-03-03 19:05:28',
            ),
            147 => 
            array (
                'id' => 1553,
                'name' => 'projectcontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:28',
                'updated_at' => '2021-03-03 19:05:28',
            ),
            148 => 
            array (
                'id' => 1554,
                'name' => 'ordercontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:28',
                'updated_at' => '2021-03-03 19:05:28',
            ),
            149 => 
            array (
                'id' => 1555,
                'name' => 'ordercontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:28',
                'updated_at' => '2021-03-03 19:05:28',
            ),
            150 => 
            array (
                'id' => 1556,
                'name' => 'ordercontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:28',
                'updated_at' => '2021-03-03 19:05:28',
            ),
            151 => 
            array (
                'id' => 1557,
                'name' => 'ordercontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:28',
                'updated_at' => '2021-03-03 19:05:28',
            ),
            152 => 
            array (
                'id' => 1558,
                'name' => 'ordercontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:28',
                'updated_at' => '2021-03-03 19:05:28',
            ),
            153 => 
            array (
                'id' => 1559,
                'name' => 'ordercontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:29',
                'updated_at' => '2021-03-03 19:05:29',
            ),
            154 => 
            array (
                'id' => 1560,
                'name' => 'ordercontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:29',
                'updated_at' => '2021-03-03 19:05:29',
            ),
            155 => 
            array (
                'id' => 1561,
                'name' => 'uploadcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:29',
                'updated_at' => '2021-03-03 19:05:29',
            ),
            156 => 
            array (
                'id' => 1562,
                'name' => 'uploadcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:29',
                'updated_at' => '2021-03-03 19:05:29',
            ),
            157 => 
            array (
                'id' => 1563,
                'name' => 'taskstatuscontroller.search',
                'description' => '',
                'created_at' => '2021-03-03 19:05:29',
                'updated_at' => '2021-03-03 19:05:29',
            ),
            158 => 
            array (
                'id' => 1564,
                'name' => 'taskstatuscontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:29',
                'updated_at' => '2021-03-03 19:05:29',
            ),
            159 => 
            array (
                'id' => 1565,
                'name' => 'taskstatuscontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:29',
                'updated_at' => '2021-03-03 19:05:29',
            ),
            160 => 
            array (
                'id' => 1566,
                'name' => 'taskstatuscontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            161 => 
            array (
                'id' => 1567,
                'name' => 'invoicecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            162 => 
            array (
                'id' => 1568,
                'name' => 'invoicecontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            163 => 
            array (
                'id' => 1569,
                'name' => 'invoicecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            164 => 
            array (
                'id' => 1570,
                'name' => 'invoicecontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            165 => 
            array (
                'id' => 1571,
                'name' => 'invoicecontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            166 => 
            array (
                'id' => 1572,
                'name' => 'invoicecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            167 => 
            array (
                'id' => 1573,
                'name' => 'invoicecontroller.getinvoicelinesfortask',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            168 => 
            array (
                'id' => 1574,
                'name' => 'invoicecontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            169 => 
            array (
                'id' => 1575,
                'name' => 'invoicecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            170 => 
            array (
                'id' => 1576,
                'name' => 'invoicecontroller.getinvoicesbystatus',
                'description' => '',
                'created_at' => '2021-03-03 19:05:30',
                'updated_at' => '2021-03-03 19:05:30',
            ),
            171 => 
            array (
                'id' => 1577,
                'name' => 'invoicecontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            172 => 
            array (
                'id' => 1578,
                'name' => 'recurringinvoicecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            173 => 
            array (
                'id' => 1579,
                'name' => 'recurringinvoicecontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            174 => 
            array (
                'id' => 1580,
                'name' => 'recurringinvoicecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            175 => 
            array (
                'id' => 1581,
                'name' => 'recurringinvoicecontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            176 => 
            array (
                'id' => 1582,
                'name' => 'recurringinvoicecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            177 => 
            array (
                'id' => 1583,
                'name' => 'recurringinvoicecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            178 => 
            array (
                'id' => 1584,
                'name' => 'recurringinvoicecontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            179 => 
            array (
                'id' => 1585,
                'name' => 'recurringinvoicecontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            180 => 
            array (
                'id' => 1586,
                'name' => 'recurringquotecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            181 => 
            array (
                'id' => 1587,
                'name' => 'recurringquotecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            182 => 
            array (
                'id' => 1588,
                'name' => 'recurringquotecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:31',
                'updated_at' => '2021-03-03 19:05:31',
            ),
            183 => 
            array (
                'id' => 1589,
                'name' => 'recurringquotecontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            184 => 
            array (
                'id' => 1590,
                'name' => 'recurringquotecontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            185 => 
            array (
                'id' => 1591,
                'name' => 'recurringquotecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            186 => 
            array (
                'id' => 1592,
                'name' => 'recurringquotecontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            187 => 
            array (
                'id' => 1593,
                'name' => 'recurringquotecontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            188 => 
            array (
                'id' => 1594,
                'name' => 'creditcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            189 => 
            array (
                'id' => 1595,
                'name' => 'creditcontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            190 => 
            array (
                'id' => 1596,
                'name' => 'creditcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            191 => 
            array (
                'id' => 1597,
                'name' => 'creditcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            192 => 
            array (
                'id' => 1598,
                'name' => 'creditcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            193 => 
            array (
                'id' => 1599,
                'name' => 'creditcontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            194 => 
            array (
                'id' => 1600,
                'name' => 'creditcontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            195 => 
            array (
                'id' => 1601,
                'name' => 'creditcontroller.getcreditsbystatus',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            196 => 
            array (
                'id' => 1602,
                'name' => 'expensecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:32',
                'updated_at' => '2021-03-03 19:05:32',
            ),
            197 => 
            array (
                'id' => 1603,
                'name' => 'expensecontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            198 => 
            array (
                'id' => 1604,
                'name' => 'expensecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            199 => 
            array (
                'id' => 1605,
                'name' => 'expensecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            200 => 
            array (
                'id' => 1606,
                'name' => 'expensecontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            201 => 
            array (
                'id' => 1607,
                'name' => 'expensecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            202 => 
            array (
                'id' => 1608,
                'name' => 'expensecontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            203 => 
            array (
                'id' => 1609,
                'name' => 'expensecontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            204 => 
            array (
                'id' => 1610,
                'name' => 'quotecontroller.convert',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            205 => 
            array (
                'id' => 1611,
                'name' => 'quotecontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            206 => 
            array (
                'id' => 1612,
                'name' => 'quotecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            207 => 
            array (
                'id' => 1613,
                'name' => 'quotecontroller.approve',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            208 => 
            array (
                'id' => 1614,
                'name' => 'quotecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            209 => 
            array (
                'id' => 1615,
                'name' => 'quotecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            210 => 
            array (
                'id' => 1616,
                'name' => 'quotecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            211 => 
            array (
                'id' => 1617,
                'name' => 'quotecontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            212 => 
            array (
                'id' => 1618,
                'name' => 'quotecontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:33',
                'updated_at' => '2021-03-03 19:05:33',
            ),
            213 => 
            array (
                'id' => 1619,
                'name' => 'quotecontroller.getquotelinesfortask',
                'description' => '',
                'created_at' => '2021-03-03 19:05:34',
                'updated_at' => '2021-03-03 19:05:34',
            ),
            214 => 
            array (
                'id' => 1620,
                'name' => 'quotecontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:34',
                'updated_at' => '2021-03-03 19:05:34',
            ),
            215 => 
            array (
                'id' => 1621,
                'name' => 'purchaseordercontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:34',
                'updated_at' => '2021-03-03 19:05:34',
            ),
            216 => 
            array (
                'id' => 1622,
                'name' => 'purchaseordercontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:34',
                'updated_at' => '2021-03-03 19:05:34',
            ),
            217 => 
            array (
                'id' => 1623,
                'name' => 'purchaseordercontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:34',
                'updated_at' => '2021-03-03 19:05:34',
            ),
            218 => 
            array (
                'id' => 1624,
                'name' => 'purchaseordercontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:34',
                'updated_at' => '2021-03-03 19:05:34',
            ),
            219 => 
            array (
                'id' => 1625,
                'name' => 'purchaseordercontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:34',
                'updated_at' => '2021-03-03 19:05:34',
            ),
            220 => 
            array (
                'id' => 1626,
                'name' => 'purchaseordercontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:35',
                'updated_at' => '2021-03-03 19:05:35',
            ),
            221 => 
            array (
                'id' => 1627,
                'name' => 'purchaseordercontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:35',
                'updated_at' => '2021-03-03 19:05:35',
            ),
            222 => 
            array (
                'id' => 1628,
                'name' => 'purchaseordercontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:35',
                'updated_at' => '2021-03-03 19:05:35',
            ),
            223 => 
            array (
                'id' => 1629,
                'name' => 'accountcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:35',
                'updated_at' => '2021-03-03 19:05:35',
            ),
            224 => 
            array (
                'id' => 1630,
                'name' => 'accountcontroller.savecustomfields',
                'description' => '',
                'created_at' => '2021-03-03 19:05:35',
                'updated_at' => '2021-03-03 19:05:35',
            ),
            225 => 
            array (
                'id' => 1631,
                'name' => 'accountcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:35',
                'updated_at' => '2021-03-03 19:05:35',
            ),
            226 => 
            array (
                'id' => 1632,
                'name' => 'accountcontroller.refresh',
                'description' => '',
                'created_at' => '2021-03-03 19:05:35',
                'updated_at' => '2021-03-03 19:05:35',
            ),
            227 => 
            array (
                'id' => 1633,
                'name' => 'accountcontroller.getallcustomfields',
                'description' => '',
                'created_at' => '2021-03-03 19:05:35',
                'updated_at' => '2021-03-03 19:05:35',
            ),
            228 => 
            array (
                'id' => 1634,
                'name' => 'accountcontroller.getcustomfields',
                'description' => '',
                'created_at' => '2021-03-03 19:05:35',
                'updated_at' => '2021-03-03 19:05:35',
            ),
            229 => 
            array (
                'id' => 1635,
                'name' => 'accountcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            230 => 
            array (
                'id' => 1636,
                'name' => 'accountcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            231 => 
            array (
                'id' => 1637,
                'name' => 'accountcontroller.getdateformats',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            232 => 
            array (
                'id' => 1638,
                'name' => 'accountcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            233 => 
            array (
                'id' => 1639,
                'name' => 'emailcontroller.send',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            234 => 
            array (
                'id' => 1640,
                'name' => 'accountcontroller.changeaccount',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            235 => 
            array (
                'id' => 1641,
                'name' => 'groupcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            236 => 
            array (
                'id' => 1642,
                'name' => 'groupcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            237 => 
            array (
                'id' => 1643,
                'name' => 'groupcontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            238 => 
            array (
                'id' => 1644,
                'name' => 'groupcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            239 => 
            array (
                'id' => 1645,
                'name' => 'groupcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            240 => 
            array (
                'id' => 1646,
                'name' => 'groupcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            241 => 
            array (
                'id' => 1647,
                'name' => 'groupcontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            242 => 
            array (
                'id' => 1648,
                'name' => 'templatecontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            243 => 
            array (
                'id' => 1649,
                'name' => 'companygatewaycontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            244 => 
            array (
                'id' => 1650,
                'name' => 'companygatewaycontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            245 => 
            array (
                'id' => 1651,
                'name' => 'companygatewaycontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:36',
                'updated_at' => '2021-03-03 19:05:36',
            ),
            246 => 
            array (
                'id' => 1652,
                'name' => 'companygatewaycontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            247 => 
            array (
                'id' => 1653,
                'name' => 'taxratecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            248 => 
            array (
                'id' => 1654,
                'name' => 'taxratecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            249 => 
            array (
                'id' => 1655,
                'name' => 'taxratecontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            250 => 
            array (
                'id' => 1656,
                'name' => 'taxratecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            251 => 
            array (
                'id' => 1657,
                'name' => 'taxratecontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            252 => 
            array (
                'id' => 1658,
                'name' => 'taxratecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            253 => 
            array (
                'id' => 1659,
                'name' => 'taxratecontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            254 => 
            array (
                'id' => 1660,
                'name' => 'paymentcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            255 => 
            array (
                'id' => 1661,
                'name' => 'paymentcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            256 => 
            array (
                'id' => 1662,
                'name' => 'paymentcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            257 => 
            array (
                'id' => 1663,
                'name' => 'paymentcontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:37',
                'updated_at' => '2021-03-03 19:05:37',
            ),
            258 => 
            array (
                'id' => 1664,
                'name' => 'paymentcontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            259 => 
            array (
                'id' => 1665,
                'name' => 'paymentcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            260 => 
            array (
                'id' => 1666,
                'name' => 'paymentcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            261 => 
            array (
                'id' => 1667,
                'name' => 'paymentcontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            262 => 
            array (
                'id' => 1668,
                'name' => 'paymentcontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            263 => 
            array (
                'id' => 1669,
                'name' => 'paymenttermscontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            264 => 
            array (
                'id' => 1670,
                'name' => 'paymenttermscontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            265 => 
            array (
                'id' => 1671,
                'name' => 'paymenttermscontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            266 => 
            array (
                'id' => 1672,
                'name' => 'paymenttermscontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            267 => 
            array (
                'id' => 1673,
                'name' => 'paymenttermscontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            268 => 
            array (
                'id' => 1674,
                'name' => 'paymenttermscontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            269 => 
            array (
                'id' => 1675,
                'name' => 'paymenttermscontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:38',
                'updated_at' => '2021-03-03 19:05:38',
            ),
            270 => 
            array (
                'id' => 1676,
                'name' => 'paymenttypecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:39',
                'updated_at' => '2021-03-03 19:05:39',
            ),
            271 => 
            array (
                'id' => 1677,
                'name' => 'setupcontroller.healthcheck',
                'description' => '',
                'created_at' => '2021-03-03 19:05:39',
                'updated_at' => '2021-03-03 19:05:39',
            ),
            272 => 
            array (
                'id' => 1678,
                'name' => 'customercontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:39',
                'updated_at' => '2021-03-03 19:05:39',
            ),
            273 => 
            array (
                'id' => 1679,
                'name' => 'customercontroller.dashboard',
                'description' => '',
                'created_at' => '2021-03-03 19:05:39',
                'updated_at' => '2021-03-03 19:05:39',
            ),
            274 => 
            array (
                'id' => 1680,
                'name' => 'customercontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:40',
                'updated_at' => '2021-03-03 19:05:40',
            ),
            275 => 
            array (
                'id' => 1681,
                'name' => 'customercontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:40',
                'updated_at' => '2021-03-03 19:05:40',
            ),
            276 => 
            array (
                'id' => 1682,
                'name' => 'customercontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:40',
                'updated_at' => '2021-03-03 19:05:40',
            ),
            277 => 
            array (
                'id' => 1683,
                'name' => 'customercontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:40',
                'updated_at' => '2021-03-03 19:05:40',
            ),
            278 => 
            array (
                'id' => 1684,
                'name' => 'customercontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:40',
                'updated_at' => '2021-03-03 19:05:40',
            ),
            279 => 
            array (
                'id' => 1685,
                'name' => 'customercontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:40',
                'updated_at' => '2021-03-03 19:05:40',
            ),
            280 => 
            array (
                'id' => 1686,
                'name' => 'customercontroller.getcustomertypes',
                'description' => '',
                'created_at' => '2021-03-03 19:05:40',
                'updated_at' => '2021-03-03 19:05:40',
            ),
            281 => 
            array (
                'id' => 1687,
                'name' => 'customercontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:40',
                'updated_at' => '2021-03-03 19:05:40',
            ),
            282 => 
            array (
                'id' => 1688,
                'name' => 'taskcontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:40',
                'updated_at' => '2021-03-03 19:05:40',
            ),
            283 => 
            array (
                'id' => 1689,
                'name' => 'taskcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:41',
                'updated_at' => '2021-03-03 19:05:41',
            ),
            284 => 
            array (
                'id' => 1690,
                'name' => 'taskcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:41',
                'updated_at' => '2021-03-03 19:05:41',
            ),
            285 => 
            array (
                'id' => 1691,
                'name' => 'taskcontroller.gettasksforproject',
                'description' => '',
                'created_at' => '2021-03-03 19:05:41',
                'updated_at' => '2021-03-03 19:05:41',
            ),
            286 => 
            array (
                'id' => 1692,
                'name' => 'taskcontroller.markascompleted',
                'description' => '',
                'created_at' => '2021-03-03 19:05:41',
                'updated_at' => '2021-03-03 19:05:41',
            ),
            287 => 
            array (
                'id' => 1693,
                'name' => 'taskcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:41',
                'updated_at' => '2021-03-03 19:05:41',
            ),
            288 => 
            array (
                'id' => 1694,
                'name' => 'taskcontroller.updatestatus',
                'description' => '',
                'created_at' => '2021-03-03 19:05:41',
                'updated_at' => '2021-03-03 19:05:41',
            ),
            289 => 
            array (
                'id' => 1695,
                'name' => 'taskcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:42',
                'updated_at' => '2021-03-03 19:05:42',
            ),
            290 => 
            array (
                'id' => 1696,
                'name' => 'taskcontroller.getsubtasks',
                'description' => '',
                'created_at' => '2021-03-03 19:05:43',
                'updated_at' => '2021-03-03 19:05:43',
            ),
            291 => 
            array (
                'id' => 1697,
                'name' => 'taskcontroller.getproducts',
                'description' => '',
                'created_at' => '2021-03-03 19:05:43',
                'updated_at' => '2021-03-03 19:05:43',
            ),
            292 => 
            array (
                'id' => 1698,
                'name' => 'taskcontroller.gettaskswithproducts',
                'description' => '',
                'created_at' => '2021-03-03 19:05:43',
                'updated_at' => '2021-03-03 19:05:43',
            ),
            293 => 
            array (
                'id' => 1699,
                'name' => 'taskcontroller.getsourcetypes',
                'description' => '',
                'created_at' => '2021-03-03 19:05:43',
                'updated_at' => '2021-03-03 19:05:43',
            ),
            294 => 
            array (
                'id' => 1700,
                'name' => 'taskcontroller.gettasktypes',
                'description' => '',
                'created_at' => '2021-03-03 19:05:43',
                'updated_at' => '2021-03-03 19:05:43',
            ),
            295 => 
            array (
                'id' => 1701,
                'name' => 'taskcontroller.converttodeal',
                'description' => '',
                'created_at' => '2021-03-03 19:05:43',
                'updated_at' => '2021-03-03 19:05:43',
            ),
            296 => 
            array (
                'id' => 1702,
                'name' => 'taskcontroller.updatetimer',
                'description' => '',
                'created_at' => '2021-03-03 19:05:43',
                'updated_at' => '2021-03-03 19:05:43',
            ),
            297 => 
            array (
                'id' => 1703,
                'name' => 'taskcontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:43',
                'updated_at' => '2021-03-03 19:05:43',
            ),
            298 => 
            array (
                'id' => 1704,
                'name' => 'taskcontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:44',
                'updated_at' => '2021-03-03 19:05:44',
            ),
            299 => 
            array (
                'id' => 1705,
                'name' => 'taskcontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:44',
                'updated_at' => '2021-03-03 19:05:44',
            ),
            300 => 
            array (
                'id' => 1706,
                'name' => 'taskcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:44',
                'updated_at' => '2021-03-03 19:05:44',
            ),
            301 => 
            array (
                'id' => 1707,
                'name' => 'leadcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:44',
                'updated_at' => '2021-03-03 19:05:44',
            ),
            302 => 
            array (
                'id' => 1708,
                'name' => 'leadcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:44',
                'updated_at' => '2021-03-03 19:05:44',
            ),
            303 => 
            array (
                'id' => 1709,
                'name' => 'leadcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:44',
                'updated_at' => '2021-03-03 19:05:44',
            ),
            304 => 
            array (
                'id' => 1710,
                'name' => 'leadcontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:44',
                'updated_at' => '2021-03-03 19:05:44',
            ),
            305 => 
            array (
                'id' => 1711,
                'name' => 'leadcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            306 => 
            array (
                'id' => 1712,
                'name' => 'leadcontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            307 => 
            array (
                'id' => 1713,
                'name' => 'leadcontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            308 => 
            array (
                'id' => 1714,
                'name' => 'leadcontroller.sorttasks',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            309 => 
            array (
                'id' => 1715,
                'name' => 'usercontroller.archive',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            310 => 
            array (
                'id' => 1716,
                'name' => 'usercontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            311 => 
            array (
                'id' => 1717,
                'name' => 'usercontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            312 => 
            array (
                'id' => 1718,
                'name' => 'usercontroller.dashboard',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            313 => 
            array (
                'id' => 1719,
                'name' => 'usercontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            314 => 
            array (
                'id' => 1720,
                'name' => 'usercontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            315 => 
            array (
                'id' => 1721,
                'name' => 'usercontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            316 => 
            array (
                'id' => 1722,
                'name' => 'usercontroller.upload',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            317 => 
            array (
                'id' => 1723,
                'name' => 'usercontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            318 => 
            array (
                'id' => 1724,
                'name' => 'usercontroller.profile',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            319 => 
            array (
                'id' => 1725,
                'name' => 'usercontroller.filterusersbydepartment',
                'description' => '',
                'created_at' => '2021-03-03 19:05:45',
                'updated_at' => '2021-03-03 19:05:45',
            ),
            320 => 
            array (
                'id' => 1726,
                'name' => 'usercontroller.restore',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            321 => 
            array (
                'id' => 1727,
                'name' => 'permissioncontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            322 => 
            array (
                'id' => 1728,
                'name' => 'permissioncontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            323 => 
            array (
                'id' => 1729,
                'name' => 'permissioncontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            324 => 
            array (
                'id' => 1730,
                'name' => 'permissioncontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            325 => 
            array (
                'id' => 1731,
                'name' => 'permissioncontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            326 => 
            array (
                'id' => 1732,
                'name' => 'rolecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            327 => 
            array (
                'id' => 1733,
                'name' => 'rolecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            328 => 
            array (
                'id' => 1734,
                'name' => 'rolecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            329 => 
            array (
                'id' => 1735,
                'name' => 'rolecontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:46',
                'updated_at' => '2021-03-03 19:05:46',
            ),
            330 => 
            array (
                'id' => 1736,
                'name' => 'rolecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:47',
                'updated_at' => '2021-03-03 19:05:47',
            ),
            331 => 
            array (
                'id' => 1737,
                'name' => 'departmentcontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:47',
                'updated_at' => '2021-03-03 19:05:47',
            ),
            332 => 
            array (
                'id' => 1738,
                'name' => 'departmentcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:47',
                'updated_at' => '2021-03-03 19:05:47',
            ),
            333 => 
            array (
                'id' => 1739,
                'name' => 'departmentcontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:47',
                'updated_at' => '2021-03-03 19:05:47',
            ),
            334 => 
            array (
                'id' => 1740,
                'name' => 'departmentcontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:47',
                'updated_at' => '2021-03-03 19:05:47',
            ),
            335 => 
            array (
                'id' => 1741,
                'name' => 'departmentcontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:47',
                'updated_at' => '2021-03-03 19:05:47',
            ),
            336 => 
            array (
                'id' => 1742,
                'name' => 'countrycontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            337 => 
            array (
                'id' => 1743,
                'name' => 'currencycontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            338 => 
            array (
                'id' => 1744,
                'name' => 'timercontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            339 => 
            array (
                'id' => 1745,
                'name' => 'timercontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            340 => 
            array (
                'id' => 1746,
                'name' => 'timercontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            341 => 
            array (
                'id' => 1747,
                'name' => 'timercontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            342 => 
            array (
                'id' => 1748,
                'name' => 'timercontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            343 => 
            array (
                'id' => 1749,
                'name' => 'timercontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            344 => 
            array (
                'id' => 1750,
                'name' => 'timercontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            345 => 
            array (
                'id' => 1751,
                'name' => 'attributecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            346 => 
            array (
                'id' => 1752,
                'name' => 'attributecontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            347 => 
            array (
                'id' => 1753,
                'name' => 'attributecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            348 => 
            array (
                'id' => 1754,
                'name' => 'attributecontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            349 => 
            array (
                'id' => 1755,
                'name' => 'attributecontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            350 => 
            array (
                'id' => 1756,
                'name' => 'attributecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:48',
                'updated_at' => '2021-03-03 19:05:48',
            ),
            351 => 
            array (
                'id' => 1757,
                'name' => 'attributecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            352 => 
            array (
                'id' => 1758,
                'name' => 'attributevaluecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            353 => 
            array (
                'id' => 1759,
                'name' => 'attributevaluecontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            354 => 
            array (
                'id' => 1760,
                'name' => 'attributevaluecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            355 => 
            array (
                'id' => 1761,
                'name' => 'attributevaluecontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            356 => 
            array (
                'id' => 1762,
                'name' => 'attributevaluecontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            357 => 
            array (
                'id' => 1763,
                'name' => 'attributevaluecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            358 => 
            array (
                'id' => 1764,
                'name' => 'attributevaluecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            359 => 
            array (
                'id' => 1765,
                'name' => 'logincontroller.showlogin',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            360 => 
            array (
                'id' => 1766,
                'name' => 'logincontroller.dologin',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            361 => 
            array (
                'id' => 1767,
                'name' => 'logincontroller.dologout',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            362 => 
            array (
                'id' => 1768,
                'name' => 'auth\\forgotpasswordcontroller.sendresetlinkemail',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            363 => 
            array (
                'id' => 1769,
                'name' => 'auth\\verificationcontroller.resend',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            364 => 
            array (
                'id' => 1770,
                'name' => 'categorycontroller.getrootcategories',
                'description' => '',
                'created_at' => '2021-03-03 19:05:49',
                'updated_at' => '2021-03-03 19:05:49',
            ),
            365 => 
            array (
                'id' => 1771,
                'name' => 'categorycontroller.getchildcategories',
                'description' => '',
                'created_at' => '2021-03-03 19:05:50',
                'updated_at' => '2021-03-03 19:05:50',
            ),
            366 => 
            array (
                'id' => 1772,
                'name' => 'categorycontroller.getform',
                'description' => '',
                'created_at' => '2021-03-03 19:05:50',
                'updated_at' => '2021-03-03 19:05:50',
            ),
            367 => 
            array (
                'id' => 1773,
                'name' => 'categorycontroller.getcategory',
                'description' => '',
                'created_at' => '2021-03-03 19:05:50',
                'updated_at' => '2021-03-03 19:05:50',
            ),
            368 => 
            array (
                'id' => 1774,
                'name' => 'taskcontroller.addproducts',
                'description' => '',
                'created_at' => '2021-03-03 19:05:50',
                'updated_at' => '2021-03-03 19:05:50',
            ),
            369 => 
            array (
                'id' => 1775,
                'name' => 'taskcontroller.createdeal',
                'description' => '',
                'created_at' => '2021-03-03 19:05:50',
                'updated_at' => '2021-03-03 19:05:50',
            ),
            370 => 
            array (
                'id' => 1776,
                'name' => 'leadcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:50',
                'updated_at' => '2021-03-03 19:05:50',
            ),
            371 => 
            array (
                'id' => 1777,
                'name' => 'leadcontroller.convert',
                'description' => '',
                'created_at' => '2021-03-03 19:05:50',
                'updated_at' => '2021-03-03 19:05:50',
            ),
            372 => 
            array (
                'id' => 1778,
                'name' => 'paymentcontroller.refund',
                'description' => '',
                'created_at' => '2021-03-03 19:05:50',
                'updated_at' => '2021-03-03 19:05:50',
            ),
            373 => 
            array (
                'id' => 1779,
                'name' => 'invoicecontroller.markviewed',
                'description' => '',
                'created_at' => '2021-03-03 19:05:50',
                'updated_at' => '2021-03-03 19:05:50',
            ),
            374 => 
            array (
                'id' => 1780,
                'name' => 'quotecontroller.markviewed',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            375 => 
            array (
                'id' => 1781,
                'name' => 'creditcontroller.markviewed',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            376 => 
            array (
                'id' => 1782,
                'name' => 'ordercontroller.markviewed',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            377 => 
            array (
                'id' => 1783,
                'name' => 'invoicecontroller.downloadpdf',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            378 => 
            array (
                'id' => 1784,
                'name' => 'quotecontroller.downloadpdf',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            379 => 
            array (
                'id' => 1785,
                'name' => 'ordercontroller.downloadpdf',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            380 => 
            array (
                'id' => 1786,
                'name' => 'creditcontroller.downloadpdf',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            381 => 
            array (
                'id' => 1787,
                'name' => 'paymentcontroller.completepayment',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            382 => 
            array (
                'id' => 1788,
                'name' => 'recurringinvoicecontroller.requestcancellation',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            383 => 
            array (
                'id' => 1789,
                'name' => 'quotecontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            384 => 
            array (
                'id' => 1790,
                'name' => 'ordercontroller.bulk',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            385 => 
            array (
                'id' => 1791,
                'name' => 'productcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            386 => 
            array (
                'id' => 1792,
                'name' => 'productcontroller.find',
                'description' => '',
                'created_at' => '2021-03-03 19:05:51',
                'updated_at' => '2021-03-03 19:05:51',
            ),
            387 => 
            array (
                'id' => 1793,
                'name' => 'productcontroller.getproductsforcategory',
                'description' => '',
                'created_at' => '2021-03-03 19:05:52',
                'updated_at' => '2021-03-03 19:05:52',
            ),
            388 => 
            array (
                'id' => 1794,
                'name' => 'promocodecontroller.apply',
                'description' => '',
                'created_at' => '2021-03-03 19:05:52',
                'updated_at' => '2021-03-03 19:05:52',
            ),
            389 => 
            array (
                'id' => 1795,
                'name' => 'promocodecontroller.validatecode',
                'description' => '',
                'created_at' => '2021-03-03 19:05:52',
                'updated_at' => '2021-03-03 19:05:52',
            ),
            390 => 
            array (
                'id' => 1796,
                'name' => 'shippingcontroller.getrates',
                'description' => '',
                'created_at' => '2021-03-03 19:05:52',
                'updated_at' => '2021-03-03 19:05:52',
            ),
            391 => 
            array (
                'id' => 1797,
                'name' => 'previewcontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:52',
                'updated_at' => '2021-03-03 19:05:52',
            ),
            392 => 
            array (
                'id' => 1798,
                'name' => 'customercontroller.register',
                'description' => '',
                'created_at' => '2021-03-03 19:05:52',
                'updated_at' => '2021-03-03 19:05:52',
            ),
            393 => 
            array (
                'id' => 1799,
                'name' => 'casecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:52',
                'updated_at' => '2021-03-03 19:05:52',
            ),
            394 => 
            array (
                'id' => 1800,
                'name' => 'casecontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:52',
                'updated_at' => '2021-03-03 19:05:52',
            ),
            395 => 
            array (
                'id' => 1801,
                'name' => 'casecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:52',
                'updated_at' => '2021-03-03 19:05:52',
            ),
            396 => 
            array (
                'id' => 1802,
                'name' => 'casecontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            397 => 
            array (
                'id' => 1803,
                'name' => 'casecontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            398 => 
            array (
                'id' => 1804,
                'name' => 'casecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            399 => 
            array (
                'id' => 1805,
                'name' => 'casecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            400 => 
            array (
                'id' => 1806,
                'name' => 'casecontroller.action',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            401 => 
            array (
                'id' => 1807,
                'name' => 'casecategorycontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            402 => 
            array (
                'id' => 1808,
                'name' => 'casecategorycontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            403 => 
            array (
                'id' => 1809,
                'name' => 'casecategorycontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            404 => 
            array (
                'id' => 1810,
                'name' => 'casecategorycontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            405 => 
            array (
                'id' => 1811,
                'name' => 'casecategorycontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            406 => 
            array (
                'id' => 1812,
                'name' => 'casecategorycontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            407 => 
            array (
                'id' => 1813,
                'name' => 'casecategorycontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:53',
                'updated_at' => '2021-03-03 19:05:53',
            ),
            408 => 
            array (
                'id' => 1814,
                'name' => 'casetemplatecontroller.index',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            409 => 
            array (
                'id' => 1815,
                'name' => 'casetemplatecontroller.create',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            410 => 
            array (
                'id' => 1816,
                'name' => 'casetemplatecontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            411 => 
            array (
                'id' => 1817,
                'name' => 'casetemplatecontroller.show',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            412 => 
            array (
                'id' => 1818,
                'name' => 'casetemplatecontroller.edit',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            413 => 
            array (
                'id' => 1819,
                'name' => 'casetemplatecontroller.update',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            414 => 
            array (
                'id' => 1820,
                'name' => 'casetemplatecontroller.destroy',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            415 => 
            array (
                'id' => 1821,
                'name' => 'uploadcontroller.store',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            416 => 
            array (
                'id' => 1822,
                'name' => 'statementcontroller.download',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            417 => 
            array (
                'id' => 1823,
                'name' => 'setupcontroller.finish',
                'description' => '',
                'created_at' => '2021-03-03 19:05:54',
                'updated_at' => '2021-03-03 19:05:54',
            ),
            418 => 
            array (
                'id' => 1824,
                'name' => 'auth\\resetpasswordcontroller.getpassword',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            419 => 
            array (
                'id' => 1825,
                'name' => 'auth\\resetpasswordcontroller.updatepassword',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            420 => 
            array (
                'id' => 1826,
                'name' => 'twofactorcontroller.enabletwofactorauthenticationforuser',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            421 => 
            array (
                'id' => 1827,
                'name' => 'twofactorcontroller.show2faform',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            422 => 
            array (
                'id' => 1828,
                'name' => 'twofactorcontroller.verifytoken',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            423 => 
            array (
                'id' => 1829,
                'name' => 'setupcontroller.welcome',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            424 => 
            array (
                'id' => 1830,
                'name' => 'setupcontroller.requirements',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            425 => 
            array (
                'id' => 1831,
                'name' => 'setupcontroller.permissions',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            426 => 
            array (
                'id' => 1832,
                'name' => 'setupcontroller.environmentmenu',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            427 => 
            array (
                'id' => 1833,
                'name' => 'setupcontroller.database',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            428 => 
            array (
                'id' => 1834,
                'name' => 'setupcontroller.user',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            429 => 
            array (
                'id' => 1835,
                'name' => 'setupcontroller.twofactorsetup',
                'description' => '',
                'created_at' => '2021-03-03 19:05:55',
                'updated_at' => '2021-03-03 19:05:55',
            ),
            430 => 
            array (
                'id' => 1836,
                'name' => 'setupcontroller.environmentwizard',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
            431 => 
            array (
                'id' => 1837,
                'name' => 'setupcontroller.saveuser',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
            432 => 
            array (
                'id' => 1838,
                'name' => 'setupcontroller.savewizard',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
            433 => 
            array (
                'id' => 1839,
                'name' => 'setupcontroller.environmentclassic',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
            434 => 
            array (
                'id' => 1840,
                'name' => 'buynowcontroller.buynowtrigger',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
            435 => 
            array (
                'id' => 1841,
                'name' => 'paymentcontroller.buynow',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
            436 => 
            array (
                'id' => 1842,
                'name' => 'paymentcontroller.buynowsuccess',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
            437 => 
            array (
                'id' => 1843,
                'name' => 'illuminate\\routing\\viewcontroller.\\illuminate\\routing\\viewcontroller',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
            438 => 
            array (
                'id' => 1844,
                'name' => 'logincontroller.redirecttogoogle',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
            439 => 
            array (
                'id' => 1845,
                'name' => 'logincontroller.handlegooglecallback',
                'description' => '',
                'created_at' => '2021-03-03 19:05:56',
                'updated_at' => '2021-03-03 19:05:56',
            ),
        ));
        
        
    }
}