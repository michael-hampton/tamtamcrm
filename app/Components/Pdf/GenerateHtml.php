<?php

namespace App\Components\Pdf;

use Illuminate\Support\Facades\App;
use ReflectionClass;
use ReflectionException;

/**
 * Class GenerateHtml
 * @package App\Components\Pdf
 */
class GenerateHtml
{
    private array $exported_variables = [];

    /**
     * @param $objPdf
     * @param $design
     * @param $entity
     * @param null $contact
     * @param string $entity_string
     * @return string
     * @throws ReflectionException
     */
    public function generateEntityHtml(
        $objPdf,
        $design,
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

        $this->process($entity_string, $entity);

        $table = $objPdf->getTable($design->design, $entity_string, $entity);

        $settings = $entity->account->settings;

        $footer = $this->getSection('footer', $design->design);
        $footer = $this->addSignaturesToPdf($entity, $entity_string, $contact, $settings, $footer);
        $footer = $this->addBuyButtonsToPdf($entity, $entity_string, $footer);

        $data = [
            'entity'   => $entity,
            'lang'     => $lang,
            'settings' => $settings,
            'header'   => $this->getSection('header', $design->design),
            'body'     => str_replace('$table_here', $table, $this->getSection('body', $design->design)),
            'footer'   => $footer
        ];

        $html = view('pdf.stub', $data)->render();
        $html = $this->generateCustomCSS($settings, $html);
        $html = $this->addCostsToPdf($entity, $entity_string, $design, $html);

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
     * @param $entity_string
     * @param $entity
     * @return bool
     * @throws ReflectionException
     */
    private function process($entity_string, $entity)
    {
        $entity_string = empty($entity_string) ? strtolower(
            (new ReflectionClass($entity))->getShortName()
        ) : $entity_string;

        $pdf_data = json_decode(file_get_contents(public_path('columns.json')), true);

        $default_columns = $pdf_data['default'];
        $entity_columns = $pdf_data['entity'];
        $input_variables = json_decode(json_encode($entity->account->settings->pdf_variables), true);

        foreach ($default_columns as $key => $default) {
            $this->exported_variables['$' . $key] = $this->formatVariables(
                array_values($input_variables[$key]),
                $default
            );
        }

        $this->exported_variables['$entity_details'] = $this->formatVariables(
            array_values($input_variables[$entity_string]),
            $entity_columns[$entity_string],
            '<br>'
        );

        $this->exported_variables['$entity_labels'] = $this->formatVariables(
            array_keys($input_variables[$entity_string]),
            $entity_columns[$entity_string],
            'label'
        );

        return true;
    }

    /**
     * @param $values
     * @param $variables
     * @param string $appends
     * @param string $type
     * @return string
     */
    private function formatVariables($values, $variables, $appends = '', $type = 'values'): string
    {
        $output = '';

        foreach ($values as $key => $value) {
            if (isset($variables[$value])) {
                $tmp = str_replace("</span>", "_label</span>", $variables[$value]);
                $output .= $type === 'label' ? $tmp : $variables[$value] . $appends;
                continue;
            }
        }

        return $output;
    }

    /**
     * @param $section
     * @param $design
     * @return string
     */
    private function getSection($section, $design): string
    {
        return str_replace(
            array_keys($this->exported_variables),
            array_values($this->exported_variables),
            $design->{$section}
        );
    }

    /**
     * @param $entity
     * @param $entity_string
     * @param $contact
     * @param $settings
     * @param $html
     * @return string|string[]
     */
    private function addSignaturesToPdf($entity, $entity_string, $contact, $settings, $html)
    {
        if ($entity_string === 'dispatch_note') {
            $html = str_replace('$signature_here', '', $html);
            $html = str_replace('$client_signature_here', '', $html);

            return $html;
        }

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

        $html = str_replace('$signature_here', $signature, $html);
        $html = str_replace('$client_signature_here', $client_signature, $html);

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

    /**
     * @param $entity
     * @param $entity_string
     * @param $html
     * @return string|string[]
     */
    private function addBuyButtonsToPdf($entity, $entity_string, $html)
    {
        if (get_class($entity) === 'App\Models\Invoice' && $entity->customer->getSetting(
                'buy_now_links_enabled'
            ) === true && $entity_string !== 'dispatch_note') {
            $footer = str_replace(
                '$pay_now_link',
                '<a target="_blank" class="btn btn-primary" href="http://' . config(
                    'taskmanager.app_domain'
                ) . '/pay_now/' . $entity->number . '">Pay Now</a>',
                $html
            );
        } else {
            $footer = str_replace('$pay_now_link', '', $html);
        }

        return $footer;
    }

    /**
     * @param $settings
     * @param $html
     * @return string|string[]
     */
    private function generateCustomCSS($settings, $html)
    {
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

    /**
     * @param $entity
     * @param $entity_string
     * @param $design
     * @param $html
     * @return string|string[]
     */
    private function addCostsToPdf($entity, $entity_string, $design, $html)
    {
        if (in_array(
                get_class($entity),
                ['App\Models\Customer', 'App\Models\Task', 'App\Models\Cases', 'App\Models\Deal', 'App\Models\Lead']
            ) || $entity_string === 'dispatch_note') {
            $html = str_replace('$costs', '', $html);
        } else {
            $html = str_replace('$costs', $this->getSection('totals', $design->design), $html);
        }

        return $html;
    }
}
