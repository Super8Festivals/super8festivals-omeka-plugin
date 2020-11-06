<?php
echo head(array(
    'title' => 'Add Federation By-Law',
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
            <h3>Add Federation By-law</h3>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <?php echo $form; ?>
        </div>
    </div>

</section>


<script type='module' src='/plugins/SuperEightFestivals/views/shared/javascripts/preview-file.js'></script>
<script type='module' src='/plugins/SuperEightFestivals/views/shared/javascripts/sort-selects.js'></script>

<?php echo foot(); ?>
