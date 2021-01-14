<?php


namespace App\Components\OFX;


use App\Models\BankAccount;

class OFXHandler
{

    public function build(BankAccount $bank_account)
    {
        $finance = new Finance();
        $finance->banks['amex'] = new Bank(
            $finance,
            '3101',
            'https://online.americanexpress.com/myca/ofxdl/desktop/desktopDownload.do?request_type=nl_ofxdownload',
            'AMEX'
        );
        $finance->banks['amex']->logins[] = new Login($finance->banks['amex'], 'username', 'password');

        foreach ($finance->banks as $bank) {
            foreach ($bank->logins as $login) {
                $login->setup();
                foreach ($login->accounts as $account) {
                    $account->setup();
                }
            }
        }
    }
}