<?php
queue_css_file("admin");
queue_js_file("jquery.min");
echo head(array(
    'title' => $festival->title,
));
?>

<!--Omeka 'flash' message partial -->
<?php echo flash(); ?>

<a class="button blue" href='/admin/super-eight-festivals/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>/edit'>Edit</a>
<a class="button red" href='/admin/super-eight-festivals/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>/delete'>Delete</a>

<?= $this->partial("__components/breadcrumbs.php"); ?>

<style>
</style>

<section id="country-single">

    <div class="records-section">
        <h2>Film Catalogs</h2>
        <?php $film_catalogs = get_all_film_catalogs_for_festival($festival->id); ?>
        <?php if (count($film_catalogs) == 0): ?>
            <p>There are no film catalogs available for this festival.</p>
        <?php else: ?>
            <?= $this->partial("__components/records/film-catalogs.php", array('film_catalogs' => $film_catalogs)); ?>
        <?php endif; ?>
        <a class="button" href="/admin/super-eight-festivals/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>/festivals/<?= $festival->id; ?>/film-catalogs/add">Add Film Catalog</a>
    </div>

    <div class="records-section">
        <h2>Filmmakers</h2>
        <?php $filmmakers = get_all_filmmakers_for_festival($festival->id); ?>
        <?php if (count($filmmakers) == 0): ?>
            <p>There are no filmmakers available for this festival.</p>
        <?php else: ?>
            <?= $this->partial("__components/records/filmmakers.php", array('filmmakers' => $filmmakers)); ?>
        <?php endif; ?>
        <a class="button" href="/admin/super-eight-festivals/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>/festivals/<?= $festival->id; ?>/filmmakers/add">Add Filmmaker</a>
    </div>

    <div class="records-section">
        <h2>Films</h2>
        <?php $films = get_all_films_for_festival($festival->id); ?>
        <?php if (count($films) == 0): ?>
            <p>There are no films available for this festival.</p>
        <?php else: ?>
            <ul id="films">
                <?php foreach ($films as $film): ?>
                    <?php
                    $contributor = $film->get_contributor();
                    ?>
                    <li>
                        <p><?= $film->title; ?></p>
                        <p><?= $film->description; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a class="button" href="/admin/super-eight-festivals/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>/festivals/<?= $festival->id; ?>/films/add">Add Film</a>
    </div>

    <div class="records-section">
        <h2>Memorabilia</h2>
        <?php $memorabilia = get_all_memorabilia_for_festival($festival->id); ?>
        <?php if (count($memorabilia) == 0): ?>
            <p>There are no memorabilia available for this festival.</p>
        <?php else: ?>
            <ul id="memorabilia">
                <?php foreach ($memorabilia as $mem): ?>
                    <?php
                    $contributor = $mem->get_contributor();
                    ?>
                    <li>
                        <p><?= $mem->title; ?></p>
                        <p><?= $mem->description; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a class="button" href="/admin/super-eight-festivals/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>/festivals/<?= $festival->id; ?>/memorabilia/add">Add Memorabilia</a>
    </div>

    <div class="records-section">
        <h2>Print Media</h2>
        <?php $printMedia = get_all_print_media_for_festival($festival->id); ?>
        <?php if (count($printMedia) == 0): ?>
            <p>There are no print media available for this festival.</p>
        <?php else: ?>
            <ul id="memorabilia">
                <?php foreach ($printMedia as $media): ?>
                    <?php
                    $contributor = $media->get_contributor();
                    ?>
                    <li>
                        <p><?= $media->title; ?></p>
                        <p><?= $media->description; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a class="button" href="/admin/super-eight-festivals/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>/festivals/<?= $festival->id; ?>/print-media/add">Add Print Media</a>
    </div>

    <div class="records-section">
        <h2>Photos</h2>
        <?php $photos = get_all_photos_for_festival($festival->id); ?>
        <?php if (count($photos) == 0): ?>
            <p>There are no photos available for this festival.</p>
        <?php else: ?>
            <ul id="memorabilia">
                <?php foreach ($photos as $photo): ?>
                    <?php
                    $contributor = $photo->get_contributor();
                    ?>
                    <li>
                        <p><?= $photo->title; ?></p>
                        <p><?= $photo->description; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a class="button" href="/admin/super-eight-festivals/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>/festivals/<?= $festival->id; ?>/photos/add">Add Photo</a>
    </div>

    <div class="records-section">
        <h2>Posters</h2>
        <?php $posters = get_all_posters_for_festival($festival->id); ?>
        <?php if (count($posters) == 0): ?>
            <p>There are no posters available for this festival.</p>
        <?php else: ?>
            <ul id="memorabilia">
                <?php foreach ($posters as $poster): ?>
                    <?php
                    $contributor = $poster->get_contributor();
                    ?>
                    <li>
                        <p><?= $poster->title; ?></p>
                        <p><?= $poster->description; ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a class="button" href="/admin/super-eight-festivals/countries/<?= urlencode($country->name); ?>/cities/<?= urlencode($city->name); ?>/festivals/<?= $festival->id; ?>/posters/add">Add Poster</a>
    </div>

</section>

<?php echo foot(); ?>
