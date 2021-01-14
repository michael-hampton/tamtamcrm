<?php

namespace App\Traits;

use App\Designs\PdfColumns;
use Illuminate\Support\Facades\App;
use ReflectionClass;
use ReflectionException;

/**
 * Class MakesInvoiceHtml.
 */
trait MakesInvoiceHtml
{
    /**
     * @param $objPdf
     * @param PdfColumns $designer
     * @param $entity
     * @param null $contact
     * @param string $entity_string
     * @return string
     * @throws ReflectionException
     */
    public function generateEntityHtml(
        $objPdf,
        PdfColumns $designer,
        $entity,
        $contact = null,
        string $entity_string = ''
    ): string {
        switch (get_class($entity)) {
            case 'App\Models\Lead':
            case 'App\Models\Customer':
                $lang = $entity->preferredLocale();
                App::setLocale($lang);
                break;
            case 'App\Models\PurchaseOrder':
                $lang = $entity->company->preferredLocale();
                App::setLocale($lang);
                break;
            default:
                $lang = $entity->customer->preferredLocale();
                App::setLocale($lang);
                break;
        }

        $objPdf->build($contact);
        $labels = $objPdf->getLabels();
        $values = $objPdf->getValues();

        $designer->buildDesign();

        $table = (get_class($entity) === 'App\Models\Customer')
            ? $designer->buildStatementTable()
            : ((in_array(
                get_class($entity),
                ['App\Models\Task', 'App\Models\Cases', 'App\Models\Deal', 'App\Models\Lead']
            )) ? $designer->getSection('task_table') : $designer->buildInvoiceTable());

        $settings = $entity->account->settings;

        $client_signature = $this->getClientSignature($entity, $contact);

        if (in_array(get_class($entity), ['App\Models\Lead', 'App\Models\PurchaseOrder', 'App\Models\Customer'])) {
            $signature = !empty($settings->email_signature) && $entity->account->settings->show_signature_on_pdf === true ? '<span style="margin-bottom: 20px; margin-top:20px">Your Signature</span> <br><br><br><img style="display:block; width:100px;height:100px;" id="base64image" src="' . $settings->email_signature . '"/>' : '';

            $client_signature = !empty($client_signature) && $entity->account->settings->show_signature_on_pdf === true ? '<span style="margin-bottom: 20px">Client Signature</span> <br><br><br><img style="display:block; width:100px;height:100px;" id="base64image" src="' . $client_signature . '"/>' : '';
        } else {
            $signature = !empty($settings->email_signature) && $entity->customer->getSetting(
                'show_signature_on_pdf'
            ) === true ? '<span style="margin-bottom: 20px; margin-top:20px">Your Signature</span> <br><br><br><img style="display:block; width:100px;height:100px;" id="base64image" src="' . $settings->email_signature . '"/>' : '';

            $client_signature = !empty($client_signature) && $entity->customer->getSetting(
                'show_signature_on_pdf'
            ) === true ? '<span style="margin-bottom: 20px">Client Signature</span> <br><br><br><img style="display:block; width:100px;height:100px;" id="base64image" src="' . $client_signature . '"/>' : '';
        }

        if ($entity_string === 'dispatch_note') {
            $signature = '';
            $client_signature = '';
        }

        $footer = $designer->getSection('footer');
        $footer = str_replace('$signature_here', $signature, $footer);
        $footer = str_replace('$client_signature_here', $client_signature, $footer);

        if (get_class($entity) === 'App\Models\Invoice' && $entity->customer->getSetting(
                'buy_now_links_enabled'
            ) === true) {
            $footer = str_replace(
                '$pay_now_link',
                '<a target="_blank" class="btn btn-primary" href="http://' . config(
                    'taskmanager.app_domain'
                ) . '/pay_now/' . $entity->number . '">Pay Now</a>',
                $footer
            );
        } else {
            $footer = str_replace('$pay_now_link', '', $footer);
        }

        $data = [
            'entity'   => $entity,
            'lang'     => $lang,
            'settings' => $settings,
            'header'   => $designer->getSection('header'),
            'body'     => str_replace('$table_here', $table, $designer->getSection('body')),
            'footer'   => $footer
        ];

        $html = view('pdf.stub', $data)->render();
        $html = $this->generateCustomCSS($settings, $html);

        if (in_array(
                get_class($entity),
                ['App\Models\Task', 'App\Models\Cases', 'App\Models\Deal', 'App\Models\Lead']
            ) || $entity_string === 'dispatch_note') {
            $html = str_replace('$costs', '', $html);
        } else {
            $html = str_replace('$costs', $designer->getSection('totals'), $html);
        }

        if ($entity_string === 'dispatch_note') {
            $html = str_replace(['$entity.public_notes', '$terms_label', '$terms', '$footer'], '', $html);
        }

        $entity_class = (new ReflectionClass($entity))->getShortName();

        $html = $objPdf->parseLabels($labels, $html);
        $html = $objPdf->parseValues($values, $html);
        $html = $objPdf->removeEmptyValues(
            [
                '$customer.paid_to_date_label:',
                '$customer.balance_label:',
                '$customer.paid_to_date',
                '$customer.balance'
            ],
            $html
        );

        $html = str_replace(['<span> </span>', '&nbsp;<br>'], '', $html);

        $html = str_replace(
            '$pdf_type',
            $entity_string === 'dispatch_note' ? 'Dispatch Note' : ucfirst($entity_class),
            $html
        );

        $html = str_replace('$entity_number', $entity->number, $html);

        if (empty($entity->voucher_code)) {
            $html = str_replace(['$voucher_label', '$voucher'], '', $html);
        }

        return $html;
    }

    /**
     * @param $entity
     * @param $contact
     * @return string|null
     */
    private function getClientSignature($entity, $contact = null): ?string
    {
        if (!in_array(get_class($entity), ['App\Models\Invoice', 'App\Models\Quote'])) {
            return null;
        }

        $invitations = $entity->invitations;

        $selected_invitation = null;

        if (!empty($contact)) {
            $selected_invitation = $entity->invitations->where('contact_id', '=', $contact->id);
        } else {
            foreach ($invitations as $invitation) {
                if (!empty($invitation->client_signature)) {
                    $selected_invitation = $invitation;
                    break;
                }
            }
        }

        if (!empty($selected_invitation->client_signature)) {
            return $selected_invitation->client_signature;
        }

        return null;
    }

    private
    function generateCustomCSS(
        $settings,
        $html
    ) {
        if ($settings->all_pages_header && $settings->all_pages_footer) {
            $html = str_replace('header_class', 'header', $html);
            $html = str_replace('footer_class', 'footer', $html);
            $html = str_replace('header-space', 'header-margin', $html);
        } elseif ($settings->all_pages_header && !$settings->all_pages_footer) {
            $html = str_replace('header_class', 'header', $html);
            $html = str_replace('header-space', 'header-margin', $html);
        } elseif (!$settings->all_pages_header && $settings->all_pages_footer) {
            $html = str_replace('footer_class', 'footer', $html);
        }

        return $html;
    }
}
