<?php

class SuperEightFestivalsStaff extends Super8FestivalsRecord
{
    // ======================================================================================================================== \\

    public int $person_id = 0;

    // ======================================================================================================================== \\

    public function get_db_columns()
    {
        return array_merge(
            array(
                "`person_id`   INT(10) UNSIGNED NOT NULL",
            ),
            parent::get_db_columns()
        );
    }

    public function to_array()
    {
        return array_merge(
            parent::to_array(),
            ["person" => $this->get_person()],
        );
    }

    public static function create($arr = [])
    {
    }

    public function update($arr, $save = true)
    {
        if (!SuperEightFestivalsStaff::get_by_id($person_id = $arr['person_id'])) throw new Exception("No person exists with id {$person_id}");
        parent::update($arr, $save);
    }

    // ======================================================================================================================== \\

    /**
     * @return SuperEightFestivalsStaff[]
     */
    public static function get_all()
    {
        return parent::get_all();
    }

    /**
     * @return SuperEightFestivalsPerson|null
     */
    public function get_person()
    {
        return SuperEightFestivalsPerson::get_by_id($this->person_id);
    }

    // ======================================================================================================================== \\
}