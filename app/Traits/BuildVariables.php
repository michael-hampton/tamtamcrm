<?php


namespace App\Traits;


use App\Components\Pdf\InvoicePdf;
use App\Components\Pdf\LeadPdf;
use App\Components\Pdf\PurchaseOrderPdf;
use App\Components\Pdf\TaskPdf;
use ReflectionException;

trait BuildVariables
{
    public function formatNotes($entity)
    {
        if (isset($entity->public_notes) && strlen($entity->public_notes) > 0) {
            $entity->public_notes = $this->parseVariables($entity->public_notes, $entity);
        }

        if (isset($entity->private_notes) && strlen($entity->private_notes) > 0) {
            $entity->private_notes = $this->parseVariables($entity->private_notes, $entity);
        }

        return $entity;
    }

    /**
     * @param $content
     * @param $entity
     * @return string
     * @throws ReflectionException
     */
    public function parseVariables($content, $entity)
    {
        switch (get_class($entity)) {
            case in_array(get_class($entity), ['App\Models\Cases', 'App\Models\Task', 'App\Models\Deal']):
                $objPdf = new TaskPdf($entity);
                break;
            case 'App\Models\Lead':
                $objPdf = new LeadPdf($entity);
                break;
            case 'App\Models\PurchaseOrder':
                $objPdf = new PurchaseOrderPdf($entity);
                break;
            default:
                $objPdf = new InvoicePdf($entity);
                break;
        }

        $objPdf->build();
        $labels = $objPdf->getLabels();
        $values = $objPdf->getValues();

        $content = $objPdf->parseLabels($labels, $content);
        $content = $objPdf->parseValues($values, $content);

        return $content;
    }
}
