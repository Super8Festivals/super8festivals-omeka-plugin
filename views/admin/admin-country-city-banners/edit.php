<?= $this->partial("__partials/header.php", ["title" => "Edit City Banner"]); ?>

<section class="container">

    <?= $this->partial("__partials/flash.php"); ?>

    <div class="row">
        <div class="col">
            <?= $this->partial("__components/breadcrumbs.php"); ?>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <h2>Edit City Banner</h2>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <?php echo $form; ?>
        </div>
    </div>

</section>

<script type='module' src='/plugins/SuperEightFestivals/views/shared/javascripts/preview-file.js'></script>

<?= $this->partial("__partials/footer.php") ?>
