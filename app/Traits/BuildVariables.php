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
}
