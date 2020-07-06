<?php
echo head(array(
    'title' => 'Contributors',
));
$rootURL = "/admin/super-eight-festivals/contributors";
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
            <h2>
                Contributors
                <a class="btn btn-success" href="<?= $rootURL; ?>/add">Add Contributor</a>
            </h2>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <?php
            $contributors = get_all_contributors();
            usort($contributors, function ($a, $b) {
                return $a['first_name'] > $b['first_name'];
            });
            usort($contributors, function ($a, $b) {
                return $a['last_name'] > $b['last_name'];
            });
            ?>
            <?php if (count($contributors) == 0): ?>
                <p>There are no contributors available.</p>
            <?php else: ?>
                <table id="contributors" class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <td style="width: 1px;">ID</td>
                        <td>First Name</td>
                        <td>Last Name</td>
                        <td>Organization</td>
                        <td>Email</td>
                        <td style="width: 1px;"></td>
                        <td style="width: 1px;"></td>
                    </tr>
                    </thead>
                    <?php foreach ($contributors as $contributor): ?>
                        <?php
                        $recordRootURL = "$rootURL/" . $contributor->id;
                        ?>
                        <tr>
                            <td onclick="window.location.href = '<?= $recordRootURL; ?>';" style="cursor: pointer;"><span class="title"><?= $contributor->id; ?></span></td>
                            <td onclick="window.location.href = '<?= $recordRootURL; ?>';" style="cursor: pointer;"><span class="title"><?= $contributor->first_name; ?></span></td>
                            <td onclick="window.location.href = '<?= $recordRootURL; ?>';" style="cursor: pointer;"><span class="title"><?= $contributor->last_name; ?></span></td>
                            <td onclick="window.location.href = '<?= $recordRootURL; ?>';" style="cursor: pointer;"><span class="title"><?= $contributor->organization_name; ?></span></td>
                            <td onclick="window.location.href = '<?= $recordRootURL; ?>';" style="cursor: pointer;"><span class="title"><?= $contributor->email; ?></span></td>
                            <td><a class="btn btn-primary btn-sm" href="<?= $rootURL; ?>/<?= $contributor->id; ?>/edit">Edit</a></td>
                            <td><a class="btn btn-danger btn-sm" href="<?= $rootURL; ?>/<?= $contributor->id; ?>/delete">Delete</a></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>

</section>

<?php echo foot(); ?>
