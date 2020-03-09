<?php
$head = array(
    'title' => "Countries",
);
echo head($head);
?>

<?php
$countries = get_all_countries();
?>

<section class="container-fluid" id="countries-list">


    <div class="row mb-3">
        <div class="col d-flex justify-content-end">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-secondary active" id="gridButton">Grid View</button>
                <button type="button" class="btn btn-secondary" id="listButton">List View</button>
            </div>
        </div>
    </div>


    <div class="row" id="grid">
        <div class="col">
            <h2>Countries</h2>
            <div class="card-deck d-flex justify-content-center align-items-center text-center">
                <?php foreach ($countries as $country): ?>
                    <?php
                    $banner = get_country_banner($country->id);
                    ?>
                    <div class="card mb-4" style="min-width: 280px; max-width: 240px;">
                        <div class="embed-responsive embed-responsive-16by9">
                            <img alt="Card image cap" class="card-img-top embed-responsive-item" src="<?= $banner != null ? $banner->thumbnail : "https://placehold.it/280x140/abc" ?>">
                        </div>
                        <div class="card-body">
                            <h3 class="card-title text-capitalize"><?= $country->name; ?></h3>
                            <p class="card-text"><small class="text-muted">(0) Festivals</small></p>
                            <a href="<?= $this->url('countries/' . str_replace(" ", "-", strtolower($country->name))); ?>" class="stretched-link"></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="row d-none" id="list">
        <div class="col">
            <h2>Festival Countries</h2>
            <ul class="countries-list">
                <?php foreach ($countries as $country): ?>
                    <li class="text-capitalize">
                        <a href="<?= $this->url('countries/' . str_replace(" ", "-", strtolower($country->name))); ?>">
                            <?php echo $country->name ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <script>
        $(document).ready(() => {
            const listButton = $('#listButton');
            const gridButton = $('#gridButton');
            const listElem = $('#list');
            const gridElem = $('#grid');

            gridButton.click(() => grid());
            listButton.click(() => list());

            const grid = () => {
                gridButton.addClass('active');
                listButton.removeClass('active');
                /// if grid hidden, unhide
                if (gridElem.hasClass('d-none')) gridElem.removeClass('d-none');
                // if list not hidden, hide
                if (!listElem.hasClass('d-none')) listElem.addClass('d-none');
            };

            const list = () => {
                gridButton.removeClass('active');
                listButton.addClass('active');
                /// if grid hidden, unhide
                if (listElem.hasClass('d-none')) listElem.removeClass('d-none');
                // if list not hidden, hide
                if (!gridElem.hasClass('d-none')) gridElem.addClass('d-none');
            };
        });
    </script>

</section>

<?php echo foot(); ?>