<?php


namespace App\Components\Pdf;


use App\Models\Project;
use App\Models\Timer;
use App\Repositories\TimerRepository;
use ReflectionClass;
use ReflectionException;
use stdClass;

class TaskPdf extends PdfBuilder
{
    protected $entity;

    /**
     * TaskPdf constructor.
     * @param $entity
     * @throws ReflectionException
     */
    public function __construct($entity)
    {
        parent::__construct($entity);
        $this->entity = $entity;
        $this->class = strtolower((new ReflectionClass($this->entity))->getShortName());
    }

    public function build($contact = null)
    {
        $contact = $contact === null ? $this->entity->customer->contacts->first() : $contact;
        $customer = $this->entity->customer;

        $this->setDefaults($customer)
             ->buildContact($contact)
             ->buildCustomer($customer)
             ->buildCustomerAddress($customer)
             ->buildAccount($this->entity->account)
             ->setTerms($this->entity->terms)
             ->setFooter($this->entity->footer)
             ->setNotes($this->entity->public_notes)
             ->buildTask();

        foreach ($this->data as $key => $value) {
            if (isset($value['label'])) {
                $this->labels[$key . '_label'] = $value['label'];
            }

            if (isset($value['value'])) {
                $this->values[$key] = $value['value'];
            }
        }

        return $this;
    }

    public function buildTable($columns)
    {
        $labels = $this->getLabels();
        $values = $this->getValues();

        $table = new stdClass();

        $table->header = '<tr>';
        $table->body = '';
        $table_row = '<tr>';

        foreach ($columns as $key => $column) {
            $table->header .= '<td class="table_header_td_class">' . $column . '_label</td>';
            $table_row .= '<td class="table_header_td_class">' . $column . '</td>';
        }

        $table_row .= '</tr>';

        $item['$task.name'] = $this->entity->name;
        $item['$task.description'] = $this->entity->description;
        $item['$task.hours'] = 0;
        $item['$task.rate'] = 0;
        $item['$task.cost'] = 0;

        switch ($this->class) {
            case 'task':
                $budgeted_hours = $this->calculateBudgetedHours();

                $task_rate = $this->entity->getTaskRate();

                $cost = !empty($task_rate) && !empty($budgeted_hours) ? $task_rate * $budgeted_hours : 0;

                $item['$task.hours'] = !empty($budgeted_hours) ? $budgeted_hours : 0;
                $item['$task.rate'] = !empty($task_rate) ? $task_rate : 0;
                $item['$task.cost'] = !empty($cost) ? $cost : 0;
                break;
            case 'cases':
                $item['$task.name'] = $this->entity->subject;
                $item['$task.description'] = $this->entity->message;
                break;
            case 'deal':
                $item['$task.cost'] = $this->entity->valued_at;
                break;
            default:

                break;
        }

        $tmp = strtr($table_row, $item);
        $tmp = strtr($tmp, $values);
        $table->body .= $tmp;

        $table->header .= '</tr>';

        $table->header = strtr($table->header, $labels);

        return $table;
    }

    private function calculateBudgetedHours()
    {
        $budgeted_hours = 1;

        $project = !empty($this->entity->project_id) ? Project::where(
            'id',
            '=',
            $this->entity->project_id
        )->first() : false;

        $duration = (new TimerRepository(new Timer()))->getTotalDuration($this->entity);
        $budgeted_hours = 0;

        if (!empty($duration) && $duration > 0) {
            $budgeted_hours = $duration;
        }

        if (!empty($project)) {
            $budgeted_hours = $budgeted_hours === 0 ? $project->budgeted_hours : $budgeted_hours;
        }

        return $budgeted_hours;
    }
}