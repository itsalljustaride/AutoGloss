<table class="full">
    <thead>
        <tr>
            <?php echo browse_sort_links(array(
                __('Replace') => 'replace_set',
                __('Ignore') => 'ignore_set',
                __('Description') => 'description'), array('link_tag' => 'th scope="col"', 'list_tag' => ''));
            ?>
        </tr>
    </thead>
    <tbody>
    <?php foreach (loop('terms') as $terms): ?>
        <tr>
            <td>
	            <span class="title">
                  <a href="<?php echo html_escape(record_url('term')); ?>">
                      <?php echo metadata('term', 'replace_set'); ?>
                  </a>
              </span>
              <ul class="action-links group">
                  <li><a class="edit" href="<?php echo html_escape(record_url('term', 'edit')); ?>">
                      <?php echo __('Edit'); ?>
                  </a></li>
                  <li><a class="delete-confirm" href="<?php echo html_escape(record_url('term', 'delete-confirm')); ?>">
                      <?php echo __('Delete'); ?>
                  </a></li>
              </ul>
            </td>
            <td><?php echo metadata('term', 'ignore_set'); ?></td>
            <td><?php echo metadata('term', 'description'); ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
