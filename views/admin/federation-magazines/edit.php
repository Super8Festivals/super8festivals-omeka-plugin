<?php
queue_css_file("admin");
queue_js_file("jquery.min");
echo head(array(
    'title' => 'Edit Federation Magazine: ' . (strlen($federation_magazine->title) > 0 ? ucwords($federation_magazine->title) : "Untitled"),
));
?>

<?php echo flash(); ?>

<?= $this->partial("__components/breadcrumbs.php"); ?>

<?php echo $form; ?>

<?php echo foot(); ?>