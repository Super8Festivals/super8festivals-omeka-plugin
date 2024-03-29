<?= $this->partial("__partials/header.php", ["title" => "Debug"]); ?>

    <div class="row">
        <div class="col">
            <div class="alert alert-danger" role="alert">
                <h5 class="text-danger text-center">
                    Warning: This section is intended for the developer of the website only.
                </h5>
            </div>
        </div>
    </div>


    <section class="mb-5">
        <div class="row">
            <div class="col">
                <h3 class="mb-3">Database Tools</h3>
            </div>
        </div>
        <?php
        $migration_files = array_filter(glob(__DIR__ . "/../../../migrations/*"), 'is_file');
        $migration_files = array_filter($migration_files, function ($file) {
            return !strpos($file, S8FDatabaseMigration::class);
        });
        ?>
        <div class="row">
            <div class="col">
                <h4 class="ms-2">Migrations</h4>
                <?php if (count($migration_files) > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <td>Migration File Name</td>
                            <td>Apply Migration</td>
                            <td>Undo Migration</td>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($migration_files as $migration_file): ?>
                            <?php
                            $file_name = basename($migration_file);
                            ?>
                            <tr>
                                <td>
                                    <p><?= $file_name; ?></p>
                                </td>
                                <td>
                                    <form action="/rest-api/migrations/" method="GET">
                                        <input class="d-none" type="text" name="migration-name" value="<?= $file_name; ?>">
                                        <input class="d-none" type="text" name="migration-direction" value="forward">
                                        <button type="submit" class="btn btn-link m-0 p-0 text-success fs-4"><span class="bi bi-check"></span></button>
                                    </form>
                                </td>
                                <td>
                                    <form action="/rest-api/migrations/" method="GET">
                                        <input class="d-none" type="text" name="migration-name" value="<?= $file_name; ?>">
                                        <input class="d-none" type="text" name="migration-direction" value="backward">
                                        <button type="submit" class="btn btn-link m-0 p-0 text-danger fs-4"><span class="bi bi-x"></span></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No migrations found.</p>
                <?php endif; ?>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h4 class="ms-2">Records</h4>
                <div>
                    <a href="/admin/super-eight-festivals/debug/purge/unused" class="btn btn-primary" style="margin: 0; padding: 0.5em 1.5em;">Purge All Unused records</a>
                    <a href="/admin/super-eight-festivals/debug/fix-festivals" class="btn btn-primary" style="margin: 0; padding: 0.5em 1.5em;">Fix Festival IDs</a>
                    <a href="/admin/super-eight-festivals/debug/create-tables" class="btn btn-primary" style="margin: 0; padding: 0.5em 1.5em;">Create DB Tables</a>
                    <a href="/admin/super-eight-festivals/debug/drop-tables" class="btn btn-primary" style="margin: 0; padding: 0.5em 1.5em;">Drop DB Tables</a>
                    <a href="/admin/super-eight-festivals/debug/create-missing-columns" class="btn btn-primary" style="margin: 0; padding: 0.5em 1.5em;">Create DB Columns</a>
                </div>
            </div>
        </div>
    </section>

    <section class="mb-5">
        <div class="row">
            <div class="col">
                <h3 class="mb-3">File Tools</h3>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h4 class="ms-2">Thumbnails</h4>
                <div>
                    <a href="/admin/super-eight-festivals/debug/generate-missing-thumbnails" class="btn btn-primary" style="margin: 0; padding: 0.5em 1.5em;">Generate Missing Thumbnails</a>
                    <a href="/admin/super-eight-festivals/debug/regenerate-all-thumbnails" class="btn btn-secondary" style="margin: 0; padding: 0.5em 1.5em;">Regenerate All Thumbnails</a>
                    <a href="/admin/super-eight-festivals/debug/delete-all-thumbnails" class="btn btn-danger" style="margin: 0; padding: 0.5em 1.5em;">Delete All Thumbnails</a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <h4 class="mb-3">Other</h4>
                <div>
                    <a href="/admin/super-eight-festivals/debug/create-directories" class="btn btn-primary" style="margin: 0; padding: 0.5em 1.5em;">Create Directories</a>
                    <a href="/admin/super-eight-festivals/debug/relocate-files" class="btn btn-danger" style="margin: 0; padding: 0.5em 1.5em;">Relocate Files</a>
                </div>
            </div>
        </div>
    </section>


<?= $this->partial("__partials/footer.php") ?>