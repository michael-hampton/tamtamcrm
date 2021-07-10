<?php


namespace App\Components\Export;


interface ExportsAccountData
{
    public function selectAccountData(AccountDataSelection $accountDataSelection): void;

    public function accountDataExportName(): string;

    public function getKey();
}