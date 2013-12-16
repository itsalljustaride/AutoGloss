<?php echo head(array(
    'title' => metadata('term', 'replace_set'),
    'bodyclass' => 'term auto-gloss',
    'bodyid' => metadata('term', 'id')
)); ?>
<div id="primary">
    <p id="auto-gloss-breadcrumbs"><?php //echo simple_pages_display_breadcrumbs(); ?></p>
    <h1><?php echo metadata('term', 'replace_set'); ?></h1>
</div>

<?php echo foot(); ?>
