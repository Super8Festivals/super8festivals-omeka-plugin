<style>
    #cities {
        margin-bottom: 1em;
        padding: 0;
    }

    .title {
        text-transform: capitalize;
    }

    thead td {
        font-weight: bold;
    }

    td {
        vertical-align: middle;
    }

    td .button {
        margin: 0;
    }

    tr td:first-child {
        /*width: 100%;*/
    }

    table {
        margin-bottom: 5em;
    }

</style>
<table id="cities">
    <thead>
    <tr>
        <td>ID</td>
        <td>Year</td>
        <td>Title</td>
        <td>Description</td>
        <td></td>
        <td></td>
    </tr>
    </thead>
    <?php foreach ($festivals as $festival): ?>
        <tr>
            <td><a class="title" href="/admin/super-eight-festivals/countries/<?= urlencode($festival->get_country()->name); ?>/cities/<?= urlencode($festival->get_city()->name); ?>/festivals/<?= $festival->id; ?>"><?= $festival->id; ?></a></td>
            <td><a class="title" href="/admin/super-eight-festivals/countries/<?= urlencode($festival->get_country()->name); ?>/cities/<?= urlencode($festival->get_city()->name); ?>/festivals/<?= $festival->id; ?>"><?= $festival->year; ?></a></td>
            <td><a class="title" href="/admin/super-eight-festivals/countries/<?= urlencode($festival->get_country()->name); ?>/cities/<?= urlencode($festival->get_city()->name); ?>/festivals/<?= $festival->id; ?>"><?= $festival->title; ?></a></td>
            <td><a class="title" href="/admin/super-eight-festivals/countries/<?= urlencode($festival->get_country()->name); ?>/cities/<?= urlencode($festival->get_city()->name); ?>/festivals/<?= $festival->id; ?>"><?= $festival->description; ?></a></td>
            <td><a class="button blue" href="/admin/super-eight-festivals/countries/<?= urlencode($festival->get_country()->name); ?>/cities/<?= urlencode($festival->get_city()->name); ?>/festivals/<?= $festival->id; ?>/edit">Edit</a></td>
            <td><a class="button red" href="/admin/super-eight-festivals/countries/<?= urlencode($festival->get_country()->name); ?>/cities/<?= urlencode($festival->get_city()->name); ?>/festivals/<?= $festival->id; ?>/delete">Delete</a></td>
        </tr>
    <?php endforeach; ?>
</table>