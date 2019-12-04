<?php
$head = array(
    'title' => $country->name,
);
echo head($head);
?>

<?php
$posters = get_all_posters_for_country($country->id);
$photos = get_all_photos_for_country($country->id);
$printMedia = get_all_print_media_for_country($country->id);
$memorabilias = get_all_memorabilia_for_country($country->id);
$films = get_all_films_for_country($country->id);
?>

<?php echo flash(); ?>

<section class="container-fluid px-0 overflow-hidden">

    <!--Header & Buttons-->
    <section id="top2" class="pl-4 pr-4" style="height: 690px;">

        <div class="row">
            <div class="col d-flex justify-content-center">
                <h2 class="text-capitalize text-center size-2"><?= $country->name; ?></h2>
            </div>
        </div>

        <div class="row">
            <div class="col-6 order-2 col-lg-3 order-1">
                <div class="row">
                    <div class="col">
                        <a class="btn btn-block btn-lg btn-dark pt-4 pb-4 mb-3 d-flex align-items-center justify-content-center" href="#posters" role="button" style="height: 100px;">Posters</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <a class="btn btn-block btn-lg btn-dark pt-4 pb-4 mb-3 d-flex align-items-center justify-content-center" href="#photos" role="button" style="height: 100px;">Photos</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <a class="btn btn-block btn-lg btn-dark pt-4 pb-4 mb-3 d-flex align-items-center justify-content-center" href="#print-media" role="button" style="height: 100px;">Print
                            Media</a>
                    </div>
                </div>
            </div>

            <div class="col-12 order-1 col-lg order-lg-2 mb-lg-0 mb-4 d-flex flex-column justify-content-center align-items-center ">
                <div class="d-flex flex-column justify-content-center align-items-center bg-dark" style="background-color:#2e2e2e; color: #FFFFFF;">
                    <img class="img-fluid d-none d-lg-block" src="<?= get_banner_for_country($country->id)->path; ?>" alt="Banner Image"/>
                </div>
            </div>

            <div class="col-6 order-3 col-lg-3 order-lg-3">
                <div class="row">
                    <div class="col">
                        <a class="btn btn-block btn-lg btn-dark pt-4 pb-4 mb-3 d-flex align-items-center justify-content-center" href="#memorabilia" role="button"
                           style="height: 100px;">Memorabilia</a>
                    </div>
                </div>
                <div class="row button-row">
                    <div class="col">
                        <a class="btn btn-block btn-lg btn-dark pt-4 pb-4 mb-3 d-flex align-items-center justify-content-center" href="#films" role="button" style="height: 100px;">Films</a>
                    </div>
                </div>
                <div class="row button-row">
                    <div class="col">
                        <a class="btn btn-block btn-lg btn-dark pt-4 pb-4 mb-3 d-flex align-items-center justify-content-center" href="#filmmakers" role="button" style="height: 100px;">Filmmakers</a>
                    </div>
                </div>
                <div class="row button-row">
                    <div class="col">
                        <a class="btn btn-block btn-lg btn-dark pt-4 pb-4 mb-3 d-flex align-items-center justify-content-center" href="#film-catalogs" role="button" style="height: 100px;">Film
                            Catalogs</a>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <!--Posters-->
    <section id="posters" class="d-flex flex-column justify-content-center mt-5 p-4 bg-light">
        <div class="row">
            <div class="col">
                <h3 class="pt-4 pb-4">Posters</h3>
            </div>
        </div>
        <div class="row">
            <?php foreach ($posters as $poster): ?>
                <div class="col-md-4 ">
                    <div class="card mb-4 shadow-sm">
                        <img class="img-fluid" src="<?= $poster->path; ?>" alt="<?= $poster->title; ?>"/>
                        <div class="card-body">
                            <h5 class="card-title"><?= $poster->title; ?></h5>
                            <p class="card-text"><?= $poster->description; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!--Photos-->
    <section id="photos" class="container d-flex flex-column justify-content-center mt-5">
        <div class="row">
            <div class="col">
                <h3>Photos</h3>
            </div>
        </div>
        <div class="row">
            <?php foreach ($photos as $photo): ?>
                <div class="col-md-4 ">
                    <div class="card mb-4 shadow-sm">
                        <img class="img-fluid" src="<?= $photo->path; ?>" alt="<?= $photo->title; ?>"/>
                        <div class="card-body">
                            <h5 class="card-title"><?= $photo->title; ?></h5>
                            <p class="card-text"><?= $photo->description; ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!--Print Media -->
    <section id="print-media" class="d-flex flex-column justify-content-center mt-5 p-4 bg-light ">
        <div class="row">
            <div class="col">
                <h3 class="pt-4 pb-4">Print Media</h3>
            </div>
        </div>
        <div class="row">
            <?php foreach ($printMedia as $media): ?>
                <div class="col-md-4 ">
                    <div class="card mb-4 shadow-sm">
                        <img class="img-fluid" src="<?= $media->path; ?>" alt=""/>
                        <a href="" class="stretched-link"></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!--Memorabilia-->
    <section id="photos" class="container d-flex flex-column justify-content-center mt-5">
        <div class="row">
            <div class="col">
                <h3>Memorabilia</h3>
            </div>
        </div>
        <div class="row">
            <?php foreach ($memorabilias as $memorabilia): ?>
                <div class="col-md-4 ">
                    <div class="card mb-4 shadow-sm">
                        <img class="img-fluid" src="<?= $memorabilia->path; ?>" alt=""/>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!--Films-->
    <section id="photos" class="container d-flex flex-column justify-content-center mt-5">
        <div class="row">
            <div class="col">
                <h3>Films</h3>
            </div>
        </div>
        <div class="row">
            <?php foreach ($films as $film): ?>
                <div class="col-md-4 ">
                    <div class="card mb-4 shadow-sm">
                        <div class="embed-responsive embed-responsive-16by9">
                            <?= $film->embed; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>


</section>


<?php echo foot(); ?>
