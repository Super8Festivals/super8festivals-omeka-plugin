<?php

abstract class SuperEightFestivalsImage extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public int $contributor_id = -1;
    public string $title = "";
    public string $description = "";
    public string $thumbnail_path_file = "";
    public string $thumbnail_path_web = "";
    public string $path_file = "";
    public string $path_web = "";
    public int $width = -1;
    public int $height = -1;

    public function __construct()
    {
        parent::__construct();
    }

    protected function beforeSave($args)
    {
        $this->title = trim($this->title);
        $this->description = trim($this->description);
    }
}