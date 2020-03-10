<?php

class SuperEightFestivalsFestivalFilmmaker extends SuperEightFestivalsPerson
{
    // ======================================================================================================================== \\

    /**
     * @var int
     */
    public $festival_id = -1;

    // ======================================================================================================================== \\

    public function __construct()
    {
        parent::__construct();
    }

    protected function _validate()
    {
        parent::_validate();
        if ($this->festival_id <= 0) {
            $this->addError('festival_id', 'You must select a valid festival!');
        }
    }

    protected function afterSave($args)
    {
        $this->create_files();
    }

    protected function afterDelete()
    {
        parent::afterDelete();
        $this->delete_files();
    }

    public function getRecordUrl($action = 'show')
    {
        return array(
            'module' => 'super-eight-festivals',
            'controller' => 'filmmakers',
            'action' => $action,
            'id' => $this->id,
        );
    }

    public function getResourceId()
    {
        return 'SuperEightFestivals_Festival_Filmmaker';
    }

    // ======================================================================================================================== \\

    public function get_festival()
    {
        return get_festival_by_id($this->festival_id);
    }

    public function get_city()
    {
        return $this->get_festival()->get_city();
    }

    public function get_country()
    {
        return $this->get_festival()->get_country();
    }

    public function get_dir(): string
    {
        return $this->get_city()->get_filmmakers_dir() . "/" . $this->id;
    }

    private function create_files()
    {
        if (!file_exists($this->get_dir())) {
            mkdir($this->get_dir());
        }
    }

    public function delete_files()
    {
        if (file_exists($this->get_dir())) {
            rrmdir($this->get_dir());
        }
    }


    // ======================================================================================================================== \\
}