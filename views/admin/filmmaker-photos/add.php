<?php
queue_js_file("preview-file");
queue_js_file("sort-selects");
echo head(array(
    'title' => 'Add photo for ' . $filmmaker->get_display_name(),
));
?>

<section class="container">

    <?= $this->partial("__partials/flash.php"); ?>

    <div class="row">
        <div class="col">
            <?= $this->partial("__components/breadcrumbs.php"); ?>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <h3>Add Filmmaker Photo</h3>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <?php echo $form; ?>
        </div>
    </div>

</section>

<?php echo foot(); ?>