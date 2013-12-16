<?php
$head = array('bodyclass' => 'auto-gloss primary',
              'title' => html_escape(__('Auto Gloss | Browse')),
              'content_class' => 'horizontal-nav');
echo head($head);
?>

<?php echo flash(); ?>

<a class="add-page button small green" href="<?php echo html_escape(url('auto-gloss/index/add')); ?>"><?php echo __('Add a Term'); ?></a>
<?php if (!has_loop_records('terms')): ?>
    <p><?php echo __('There are no terms.'); ?> <a href="<?php echo html_escape(url('auto-gloss/index/add')); ?>"><?php echo __('Add a Term.'); ?></a></p>
<?php else: ?>
    <?php echo $this->partial('index/browse-list.php', array('terms' => $terms)); ?>
<?php endif; ?>

<?php echo foot(); ?>
