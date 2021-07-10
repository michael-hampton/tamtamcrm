<?php


namespace App\Components\Pdf;


use App\Models\Lead;
use App\Models\Project;
use App\ViewModels\LeadViewModel;
use Laracasts\Presenter\Exceptions\PresenterException;
use ReflectionClass;
use ReflectionException;

class LeadPdf extends PdfBuilder
{
    protected $entity;

    /**
     * @var LeadViewModel
     */
    private LeadViewModel $view_model;

    /**
     * InvoicePdf constructor.
     * @param $entity
     * @throws ReflectionException
     */
    public function __construct($entity)
    {
        parent::__construct($entity);
        $this->entity = $entity;
        $this->class = strtolower((new ReflectionClass($this->entity))->getShortName());
        $this->view_model = new LeadViewModel($entity);
    }

    public function getEntityString()
    {
        return 'lead';
    }

    public function build($contact = null)
    {
        $this->buildClientForLead($this->entity)
             ->buildAddress($this->entity, $this->entity, $this->view_model)
             ->buildAccount($this->entity->account)
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

    /**
     * @param Lead $lead
     * @return $this
     * @throws PresenterException
     */
    private function buildClientForLead(Lead $lead): self
    {
        $this->data['$customer.website'] = [
            'value' => $this->view_model->website() ?: '&nbsp;',
            'label' => trans('texts.website')
        ];
        $this->data['$customer.phone'] = [
            'value' => $this->view_model->phone() ?: '&nbsp;',
            'label' => trans('texts.phone_number')
        ];
        $this->data['$customer.email'] = ['value' => $lead->email, 'label' => trans('texts.email_address')];
        $this->data['$customer.name'] = [
            'value' => $this->view_model->name() ?: '&nbsp;',
            'label' => trans('texts.customer_name')
        ];
        $this->data['$customer1'] = [
            'value' => $lead->custom_value1 ?: '&nbsp;',
            'label' => $this->makeCustomField('Lead', 'custom_value1')
        ];
        $this->data['$customer2'] = [
            'value' => $lead->custom_value2 ?: '&nbsp;',
            'label' => $this->makeCustomField('Lead', 'custom_value2')
        ];
        $this->data['$customer3'] = [
            'value' => $lead->custom_value3 ?: '&nbsp;',
            'label' => $this->makeCustomField('Lead', 'custom_value3')
        ];
        $this->data['$customer4'] = [
            'value' => $lead->custom_value4 ?: '&nbsp;',
            'label' => $this->makeCustomField('Lead', 'custom_value4')
        ];

        return $this;
    }

    public function getTable($design, $entity_string, $entity)
    {
        $html = $design->task_table;

        $entity_string = empty($entity_string) ? strtolower(
            (new ReflectionClass($entity))->getShortName()
        ) : $entity_string;

        $task_columns = $this->getTableColumns($entity_string);

        $table = $this->buildTable(
            $task_columns
        );

        if (empty($table)) {
            return true;
        }

        $table_html = str_replace(
            ['$task_table_header', '$task_table_body'],
            [$table['header'], $table['body']],
            $html
        );

        return $table_html;
    }

    private function getTableColumns($entity_string)
    {
        $input_variables = json_decode(json_encode($this->entity->account->settings->pdf_variables), true);
        return $input_variables['task_columns'];
    }

    public function buildTable($columns)
    {
        $labels = $this->getLabels();
        $values = $this->getValues();

        $table = [];

        $table['header'] = '<tr>';
        $table['body'] = '';
        $table_row = '<tr>';

        foreach ($columns as $key => $column) {
            $table['header'] .= '<td class="table_header_td_class">' . $column . '_label</td>';
            $table_row .= '<td class="table_header_td_class">' . $column . '</td>';
        }

        $table_row .= '</tr>';

        $item['$task.name'] = $this->entity->name;
        $item['$task.description'] = $this->entity->description;
        $item['$task.hours'] = 0;
        $item['$task.rate'] = 0;
        $item['$task.cost'] = 0;

        $budgeted_hours = $this->calculateBudgetedHours();

        $task_rate = $this->entity->calculated_task_rate;

        $cost = !empty($task_rate) && !empty($budgeted_hours) ? $task_rate * $budgeted_hours : 0;

        $item['$task.hours'] = !empty($budgeted_hours) ? $budgeted_hours : 0;
        $item['$task.rate'] = !empty($task_rate) ? $task_rate : 0;
        $item['$task.cost'] = !empty($cost) ? $cost : 0;

        $tmp = strtr($table_row, $item);
        $tmp = strtr($tmp, $values);
        $table['body'] .= $tmp;

        $table['header'] .= '</tr>';

        $table['header'] = strtr($table['header'], $labels);

        return $table;
    }

    private function calculateBudgetedHours()
    {
        $project = !empty($this->entity->project_id) ? Project::where(
            'id',
            '=',
            $this->entity->project_id
        )->first() : false;

        $budgeted_hours = 0;

        if (!empty($project)) {
            $budgeted_hours = $budgeted_hours === 0 ? $project->budgeted_hours : $budgeted_hours;
        }

        return $budgeted_hours;
    }
}
