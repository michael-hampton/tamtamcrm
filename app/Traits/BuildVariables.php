<?php


namespace App\Traits;


use App\Components\Pdf\PdfFactory;
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
        $objPdf = (new PdfFactory())->create($entity);

        $objPdf->build();
        $labels = $objPdf->getLabels();
        $values = $objPdf->getValues();

        $content = $objPdf->parseLabels($labels, $content);
        $content = $objPdf->parseValues($values, $content);

        return $content;
    }

    protected function populateDefaults($entity)
    {
        $class = strtolower((new \ReflectionClass($entity))->getShortName());

        if (empty($entity->terms) && !empty($entity->customer->getSetting($class . '_terms'))) {
            $entity->terms = $entity->customer->getSetting($class . '_terms');
        }
        if (empty($entity->footer) && !empty($entity->customer->getSetting($class . '_footer'))) {
            $entity->footer = $entity->customer->getSetting($class . '_footer');
        }
        if (empty($entity->public_notes) && !empty($entity->customer->public_notes)) {
            $entity->public_notes = $entity->customer->public_notes;
        }

        return $entity;
    }

    /**
     * @param string $content
     * @param $entity
     * @return array|string|string[]
     */
    protected function parseCaseVariables(string $content, $entity)
    {
        $variables = [];

        $variables['$status'] = $entity->status_name;

        if (!empty($entity->description)) {
            $variables['$description'] = $entity->description;
        }

        if (!empty($entity->number)) {
            $variables['$number'] = $entity->number;
        }

        if (!empty($entity->due_date)) {
            $variables['$due_date'] = $entity->due_date;
        }

        if (!empty($entity->priority_id) && method_exists($entity, 'getPriorityName')) {
            $variables['$priority'] = $entity->priority_name;
        }

        if (!empty($entity->customer_id)) {
            $variables['$customer'] = $entity->customer->name;
        }

        if (!empty($entity->assigned_to)) {
            $variables['$agent'] = $entity->assignee->first_name . ' ' . $entity->assignee->last_name;
        }

        return str_replace(array_keys($variables), array_values($variables), $content);
    }
}
