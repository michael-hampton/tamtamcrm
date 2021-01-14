<?php


namespace App\Traits;


use ReflectionClass;

trait Archiveable
{
    public function archive()
    {
        $this->delete();
        $this->triggerEvent('archived');

        return true;
    }

    private function triggerEvent($type)
    {
        $entity_class = (new ReflectionClass($this))->getShortName();
        $event_class = "App\Events\\" . $entity_class . "\\" . $entity_class . "Was" . ucfirst($type);

        if (class_exists($event_class)) {
            event(new $event_class($this));
        }

        return true;
    }

    /**
     * @return bool
     */
    public function restoreEntity()
    {
        $this->restore();
        $this->is_deleted = false;
        $this->save();
        $this->triggerEvent('restored');

        return true;
    }

    /**
     * @return bool
     */
    public function deleteEntity()
    {
        $this->is_deleted = true;
        $this->save();
        $this->delete();
        $this->triggerEvent('deleted');

        return true;
    }
}
