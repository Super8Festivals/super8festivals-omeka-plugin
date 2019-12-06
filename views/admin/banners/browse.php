<?php
echo head(array(
    'title' => 'Browse Banners',
));
?>

<!--Omeka 'flash' message partial -->
<?php echo flash(); ?>

<!-- 'Add City' Button -->
<?php echo $this->partial('__components/button.php', array('url' => 'add', 'text' => 'Add Banner')); ?>

<table class="full">
    <thead>
    <tr>
        <?php echo browse_sort_links(
            array(
                "Internal ID" => 'id',
                "Country" => 'country_id',
                "Path" => 'path',
                "Thumbnail" => 'thumbnail',
            ),
            array('link_tag' => 'th scope="col"', 'list_tag' => ''));
        ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach (loop('super_eight_festivals_country_banner') as $banner): ?>
        <tr style="text-transform: capitalize;">
            <td>
                <span class="title">
                    <a href="<?php echo html_escape(record_url('super_eight_festivals_country_banner')); ?>" style="text-transform: capitalize;">
                       <?= $banner->id; ?>
                    </a>
                </span>
                <ul class="action-links group">
                    <!-- Edit Item-->
                    <li>
                        <a class="edit" href="<?php echo html_escape(record_url('super_eight_festivals_country_banner', 'edit')); ?>">
                            Edit
                        </a>
                    </li>
                    <!-- Delete Item-->
                    <li>
                        <a class="edit" href="<?php echo html_escape(record_url('super_eight_festivals_country_banner', 'delete-confirm')); ?>">
                            Delete
                        </a>
                    </li>
                </ul>
            </td>
            <td><?= $banner->getCountry()->name; ?></td>
            <td style="text-transform: lowercase;"><?= $banner->path; ?></td>
            <td style="text-transform: lowercase;"><?= $banner->thumbnail; ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>


<?php echo foot(); ?>

