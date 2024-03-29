<?php

class SuperEightFestivalsFestival extends Super8FestivalsRecord
{
    // ======================================================================================================================== \\

    public ?int $city_id = null;
    public int $year = 0;

    // ======================================================================================================================== \\

    public function get_db_columns()
    {
        return array_merge(
            array(
                "`city_id`      INT UNSIGNED NOT NULL",
                "`year`         INT(4) NOT NULL",
            ),
            parent::get_db_columns()
        );
    }

    public function get_db_foreign_keys()
    {
        return array_merge(
            array(
                "FOREIGN KEY (`city_id`) REFERENCES {db_prefix}{table_prefix}cities(`id`) ON DELETE CASCADE",
            ),
            parent::get_db_foreign_keys()
        );
    }

    public function to_array()
    {
        $res = parent::to_array();
        if ($this->get_city()) $res = array_merge($res, ["city" => $this->get_city()->to_array()]);
        return $res;
    }

    protected function beforeDelete()
    {
        parent::beforeDelete();
        $this->delete_children();
    }

    public function delete_children()
    {
        foreach (SuperEightFestivalsFestivalFilm::get_by_param('festival_id', $this->id) as $record) $record->beforeDelete();
        foreach (SuperEightFestivalsFestivalFilmCatalog::get_by_param('festival_id', $this->id) as $record) $record->beforeDelete();
        foreach (SuperEightFestivalsFestivalPhoto::get_by_param('festival_id', $this->id) as $record) $record->beforeDelete();
        foreach (SuperEightFestivalsFestivalPoster::get_by_param('festival_id', $this->id) as $record) $record->beforeDelete();
        foreach (SuperEightFestivalsFestivalPrintMedia::get_by_param('festival_id', $this->id) as $record) $record->beforeDelete();
    }

    public static function create($arr = [])
    {
        if (SuperEightFestivalsFestival::get_by_params([
            "year" => $arr["year"],
            "city_id" => $arr["city_id"],
        ])) {
            throw new Exception("A festival with that year for this city already exists!");
        }

        $festival = new SuperEightFestivalsFestival();
        $festival->year = $arr['year'];
        $city_id = $arr['city_id'];
        $festival->city_id = $city_id;

        try {
            $festival->save();
            return $festival;
        } catch (Exception $e) {
            return null;
        }
    }

    public function update($arr, $save = true)
    {
        parent::update($arr, $save);
    }

    // ======================================================================================================================== \\

    /**
     * @return SuperEightFestivalsFestival[]
     */
    public static function get_all()
    {
        return parent::get_all();
    }

    /**
     * @return SuperEightFestivalsCity|null
     */
    public function get_city()
    {
        return SuperEightFestivalsCity::get_by_id($this->city_id);
    }

    public function get_country()
    {
        return $this->get_city()->get_country() ?? null;
    }

    public function get_posters()
    {
        return SuperEightFestivalsFestivalPoster::get_by_param('festival_id', $this->id);
    }

    public function get_photos()
    {
        return SuperEightFestivalsFestivalPhoto::get_by_param('festival_id', $this->id);
    }

    public function get_print_media()
    {
        return SuperEightFestivalsFestivalPrintMedia::get_by_param('festival_id', $this->id);
    }

    public function get_films()
    {
        return SuperEightFestivalsFestivalFilm::get_by_param('festival_id', $this->id);
    }

    public function get_filmmakers()
    {
        $all_films = $this->get_films();
        $res = [];
        foreach ($all_films as $film) {
            array_push($res, $film->get_filmmaker_film()->get_filmmaker());
        }
        return $res;
    }

    public function get_film_catalogs()
    {
        return SuperEightFestivalsFestivalFilmCatalog::get_by_param('festival_id', $this->id);
    }

    public function get_title()
    {
        $year = $this->year != 0 ? $this->year : "uncategorized";
        return "{$this->get_city()->get_location()->name} {$year} festival";
    }

    // ======================================================================================================================== \\
}