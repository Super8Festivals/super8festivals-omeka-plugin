<?php
queue_css_file("admin");
queue_js_file("jquery.min");
echo head(array(
    'title' => 'Delete Filmmaker: ' . ucwords($filmmaker->get_display_name()),
));
?>

<?php echo flash(); ?>

<?= $this->partial("__components/breadcrumbs.php"); ?>

<h2>Are you sure?</h2>
<?php echo $form; ?>

<?php echo foot(); ?>