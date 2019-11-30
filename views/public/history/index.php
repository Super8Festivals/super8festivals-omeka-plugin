<?php
$head = array(
    'title' => "About",
);
echo head($head);
?>

<?php echo flash(); ?>

<section class="container">

    <div class="row">
        <div class="col text-center">
            <h2>History</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-2">
            <img src="<?= img("placeholder-200x200.svg", "images"); ?>" class="img-fluid img-thumbnail" width="300px" height="300px" alt="Responsive image">
        </div>
        <div class="col">
            <p>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.
                Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
                Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
            </p>
        </div>
    </div>


</section>

<?php echo foot(); ?>