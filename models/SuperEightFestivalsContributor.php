<?php

class SuperEightFestivalsContributor extends Omeka_Record_AbstractRecord implements Zend_Acl_Resource_Interface
{
    public $first_name;
    public $last_name;
    public $organization_name;
    public $email;

    public function getResourceId()
    {
        return 'SuperEightFestivals_Contributor';
    }
}