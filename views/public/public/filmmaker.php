<?php
$head = array(
    'title' => "Filmmaker",
);
echo head($head);


function replace_links_with_href($input) {
    $pattern = '@(http(s)?://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
    return preg_replace($pattern, '<a href="http$2://$3">$0</a>', $input);
}
?>

<section class="container my-5" id="filmmaker">

    <div class="row">
        <div class="col">
            <h2 class="my-4 text-capitalize"><?= html_escape($filmmaker->get_person()->get_name()); ?></h2>
        </div>
    </div>

    <div class="row my-5" id="filmmaker-films">
        <div class="col">
            <h3 class="">Biography</h3>
            <?php if ($filmmaker->bio): ?>
                <p><?= replace_links_with_href($filmmaker->bio); ?></p>
            <?php else: ?>
                <p>There is no biography available.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="row my-5" id="filmmaker-films">
        <div class="col">
            <h3 class="ms-2">Films</h3>
            <div id="films-container"></div>
        </div>
    </div>

    <div class="row my-5" id="filmmaker-photos">
        <div class="col">
            <h3 class="ms-2">Photos</h3>
            <div id="photos-container"></div>
        </div>
    </div>

</section>

<script type="module" src="/plugins/SuperEightFestivals/views/public/javascripts/components/s8f-card.js"></script>
<script type="module" src="/plugins/SuperEightFestivals/views/public/javascripts/components/s8f-filmmaker-records.js"></script>
<script type="module">
    import API, { HTTPRequestMethod } from "/plugins/SuperEightFestivals/views/shared/javascripts/api.js";
    import { html, render } from "/plugins/SuperEightFestivals/views/shared/javascripts/vendor/lit-html.js";

    const fetchFilms = () => API.performRequest(API.constructURL(["filmmakers", <?= $filmmaker->id ?>, "films"]), HTTPRequestMethod.GET);
    const fetchPhotos = () => API.performRequest(API.constructURL(["filmmakers", <?= $filmmaker->id ?>, "photos"]), HTTPRequestMethod.GET);

    $(() => {
        render(html`<p>Loading...</p>`, document.getElementById("films-container"));
        render(html`<p>Loading...</p>`, document.getElementById("photos-container"));

        const promises = [];

        promises.push(fetchFilms().then((films) => {
            render(
                html`<s8f-filmmaker-records .sectionId=${"films"} .records=${films}></s8f-filmmaker-records>`,
                document.getElementById("films-container"),
            );
        }).catch((e) => {
            render(html`<p>Error: ${e.toString()}</p>`, document.getElementById("films"));
        }));

        promises.push(fetchPhotos().then((photos) => {
            render(
                html`<s8f-filmmaker-records .sectionId=${"photos"} .records=${photos}></s8f-filmmaker-records>`,
                document.getElementById("photos-container"),
            );
        }).catch((e) => {
            render(html`<p>Error: ${e.toString()}</p>`, document.getElementById("photos"));
        }));

        Promise.all(promises).then(() => {
            if (window.location.hash) {
                document.getElementById(window.location.hash.substring(1)).scrollIntoView();
            }
        });
    });
</script>

<?php echo foot(); ?>
