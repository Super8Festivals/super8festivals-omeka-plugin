<?php

trait S8FFederationImage
{
    // ======================================================================================================================== \\

    use S8FRecord;
    use S8FImage;
    use S8FContributable;

    public static function get_db_columns()
    {
        return array_merge(
            S8FImage::get_db_columns(),
            S8FContributable::get_db_columns()
        );
    }

    // ======================================================================================================================== \\
}