<?php


class SuperEightFestivalsFile extends Super8FestivalsRecord
{
    // ======================================================================================================================== \\

    public string $file_name = "";
    public string $thumbnail_file_name = "";
    public string $title = "";
    public string $description = "";

    public int $contributor_id = 0;

    // ======================================================================================================================== \\

    public function get_db_columns()
    {
        return array_merge(
            array(
                "`file_name`                    VARCHAR(255)",
                "`thumbnail_file_name`          VARCHAR(255)",
                "`title`                        VARCHAR(255)",
                "`description`                  TEXT(65535)",

                "`contributor_id`               INT(10) UNSIGNED NOT NULL",
            ),
            parent::get_db_columns()
        );
    }

    public static function create($arr = [])
    {
//        $file = new SuperEightFestivalsFile();
//        $file->update($arr);
//        return $file;
    }

    public function update($arr, $save = true)
    {
        parent::update($arr, $save);
    }

    public function to_array()
    {
        return array_merge([
            "file_path" => get_relative_path($this->get_path()),
            "thumbnail_file_path" => get_relative_path($this->get_thumbnail_path()),
        ], parent::to_array());
    }

    // ======================================================================================================================== \\

    protected function afterDelete()
    {
        parent::afterDelete();
        $this->delete_files();
    }

    // ======================================================================================================================== \\

    /**
     * @return string The absolute path to the resource
     */
    public function get_path(): ?string
    {
        if (empty($this->file_name)) return null;
        return get_uploads_dir() . "/" . $this->file_name;
    }

    /**
     * @return string The absolute path to the resource's thumbnail
     */
    public function get_thumbnail_path(): ?string
    {
        if (empty($this->thumbnail_file_name)) return null;
        return get_uploads_dir() . "/" . $this->thumbnail_file_name;
    }

    /**
     * @return bool Returns true if the thumbnail file exists and is not a directory
     */
    public function has_thumbnail(): bool
    {
        return $this->get_thumbnail_path() != null &&
            file_exists($this->get_thumbnail_path()) &&
            !is_dir($this->get_thumbnail_path());
    }

    public function create_thumbnail()
    {
        // no file to create a thumbnail from
        if ($this->file_name === null || $this->file_name === "" || $this->get_path() === "" || is_dir($this->get_path()) || !file_exists($this->get_path())) {
            throw new Error("Failed to create thumbnail: file does not exist or is a directory!");
        }
        if ($this->has_thumbnail()) {
            throw new Error("Failed to create thumbnail: a thumbnail already exists!");
        }

        try {
            $name = pathinfo($this->file_name, PATHINFO_FILENAME) . "_thumb.jpg";
            $this->thumbnail_file_name = $name;
            $imagick = new Imagick($this->get_path());
//            $imagick = $imagick->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
            $imagick->setFirstIterator();
//            $imagick->setBackgroundColor("white");
            $imagick->scaleImage(300, 0);
            $imagick->setImageFormat("jpg");
            $imagick->writeImage($this->get_thumbnail_path());
            $this->save();
            return true;
        } catch (ImagickException $e) {
            throw new Error("Failed to create thumbnail (original: $this->file_name)");
        }
    }

    public function delete_files()
    {
        if (file_exists($this->get_path()))
            delete_file($this->get_path());
        if (file_exists($this->get_thumbnail_path()))
            delete_file($this->get_thumbnail_path());
    }

    // ======================================================================================================================== \\

    /**
     * @return SuperEightFestivalsFile[]
     */
    public static function get_all()
    {
        return parent::get_all();
    }

    // ======================================================================================================================== \\
}