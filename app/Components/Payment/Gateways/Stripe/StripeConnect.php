<?php
//https://stripe.com/docs/api/accounts/list?lang=php

namespace App\Components\Payment\Gateways\Stripe;


use Stripe\BaseStripeClient;
use Stripe\StripeClient;

class StripeConnect extends BaseStripeClient
{

    private function setupConfig()
    {
        $this->stripe = new StripeClient(
            config('taskmanager.stripe_api_key')
        );

        return true;
    }

    public function connectAccount($account)
    {
        $this->setupConfig();

        $response = $this->stripe->accountLinks->create([
            'account'     => $account,
            'refresh_url' => url('company_gateways/stripe/refresh'),
            'return_url'  => url('company_gateways/stripe/complete'),
            'type'        => 'account_onboarding',
        ]);

        return $response['url'];

    }

    public function retrieveBankAccount($account_number, $bank_account_number)
    {
        $this->setupConfig();

        $response = $this->stripe->accounts->retrieveExternalAccount(
            $account_number,
            $bank_account_number,
            []
        );

        echo '<pre>';
        print_r($response);
        die;
    }

    public function attachBankAccount($account)
    {
        $this->setupConfig();

        $response = $this->stripe->accounts->createExternalAccount(
            $account,
            [
                'external_account' => [
                    'object'              => 'bank_account',
                    'country'             => 'GB',
                    'currency'            => 'GBP',
                    'account_holder_name' => 'Michael Hampton',
                    'account_holder_type' => 'individual',
                    'routing_number'      => '108800',
                    'account_number'      => '00012345'
                ],
            ]
        );

        echo $response['id'];

        echo '<pre>';
        print_r($response);
        die;
    }

    public function retrieveAccount($account)
    {
        $this->setupConfig();

        $stripe_account = $this->stripe->accounts->retrieve(
            $account,
            []
        );

        echo '<pre>';
        print_r($stripe_account);
        die;
    }

    public function createAccount(array $data)
    {
        $this->setupConfig();

        $response = $this->stripe->accounts->create([
            'type'         => 'standard',
            'country'      => $data['country'],
            'email'        => $data['email'],
            //            'capabilities' => [
            //                'card_payments' => ['requested' => true],
            //                'transfers'     => ['requested' => true],
            //            ],
        ]);

        return $response['id'];
    }

    public function listAllConnectedAccounts()
    {
        $this->setupConfig();

        return $this->stripe->accounts->all();
    }
}