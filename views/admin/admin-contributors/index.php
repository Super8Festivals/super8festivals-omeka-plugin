<?= $this->partial("__partials/header.php", ["title" => "Contributors"]); ?>

    <section class="container">

        <div class="row">
            <div class="col">
                <?= $this->partial("__components/breadcrumbs.php"); ?>
            </div>
        </div>

        <!-- Omeka Alerts -->
        <div class="row">
            <div class="col">
                <?= $this->partial("__partials/flash.php"); ?>
            </div>
        </div>

        <!-- S8F Alerts -->
        <div class="row">
            <div class="col">
                <s8f-alerts-area id="alerts"></s8f-alerts-area>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <s8f-contributors-table></s8f-contributors-table>
            </div>
        </div>

    </section>

    <script type='module' src='/plugins/SuperEightFestivals/views/admin/javascripts/components/s8f-contributors-table.js'></script>

<?= $this->partial("__partials/footer.php") ?>