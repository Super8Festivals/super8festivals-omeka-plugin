<?php
$head = array(
    'title' => ucwords($country->name),
);
echo head($head);

$banner = get_active_country_banner($country->id);
$cities = get_all_cities_in_country($country->id);
?>

<style>
    .city {
        list-style-type: none;
    }

    .city .content {
        margin-left: 3em;
    }
</style>

<section class="container-fluid" id="countries-list">

    <div class="container">
        <div class="row mb-4">
            <div class="col-4">
                <img src="<?= $banner != null ? get_relative_path($banner->get_thumbnail_path()) : "https://placehold.it/280x140/abc" ?>" class="img-fluid img-thumbnail" alt="Responsive image" width="300"/>
            </div>
            <div class="col-8 d-flex justify-content-start align-items-center" style="vertical-align: middle">
                <h2 class="mb-0 title"><?= $country->name; ?></h2>
            </div>
        </div>
        <div class="row">
            <div class="col d-flex flex-column align-items-center">
                <div class="row">
                    <div class="col">
                        <h3>Cities</h3>
                    </div>
                </div>
                <div class="row">
                    <ul>
                        <?php foreach ($cities as $city): ?>
                            <?php
                            $festivals = get_all_festivals_in_city($city->id);
                            ?>
                            <li class="city pb-0">
                                <a class="title" href="/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>"><?= $city->name; ?></a>
                                <div class="content">
                                    <p><?= count($festivals); ?> Festival(s)</p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</section>

<?php echo foot(); ?>
