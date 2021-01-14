<?php

namespace App\Events\Project;

use App\Models\Project;
use App\Traits\SendSubscription;
use Illuminate\Queue\SerializesModels;

/**
 * Class InvoiceWasMarkedSent.
 */
class ProjectWasUpdated
{
    use SerializesModels;
    use SendSubscription;

    /**
     * @var Project
     */
    public Project $project;

    /**
     * ProjectWasUpdated constructor.
     * @param Project $project
     */
    public function __construct(Project $project)
    {
        $this->project = $project;
        $this->send($project, get_class($this));
    }
}
