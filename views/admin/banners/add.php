<?php
echo head(array(
    'title' => 'Add Banner',
));
?>

<?php echo flash(); ?>

<?= $this->partial("__components/breadcrumbs.php"); ?>

<?php echo $form; ?>

<?php echo foot(); ?>