<?php $view = get_view(); ?>
<div id="auto-gloss-settings">
<h2><?php echo __('Auto Gloss Settings'); ?></h2>
    <div class="field">
        <div class="two columns alpha">
            <?php echo $view->formLabel('auto_gloss_settings_first_only', __('Gloss First Match Only')); ?>
        </div>
        <div class="inputs five columns omega">
		        <?php echo get_view()->formCheckbox('auto_gloss_settings_first_only', true, 
		         array('checked'=>(boolean)get_option('auto_gloss_settings_first_only'))); ?>
		        <p class="explanation"><?php echo __(
		            'If checked, this plugin will only replace the first instance ' 
		          . 'of a matched term. Otherwise, it will replace every instance ' 
		          . 'of any of the term\'s replace set.'
		        ); ?></p>
        </div>
    </div>
	
	<div class="field">
        <div class="two columns alpha">
            <?php echo $view->formLabel('auto_gloss_settings_gloss_fields', __('Fields to Gloss')); ?>
        </div>
	    <div class="inputs five columns omega">
		    <?php echo get_view()->formSelect(
	                    'auto_gloss_settings_gloss_fields',
	                    unserialize(get_option('auto_gloss_settings_gloss_fields')),
	                    array(),
	                    get_table_options('Element', null, array(
	                        'record_types' => array('Item', 'All'),
	                        'sort' => 'alphaBySet')
	                    )
	                ); ?>

		    <p class="explanation">
		    <?php
		        echo __('The fields to gloss. Default is DC:Description.'); ?>
		    </p>
	    </div>
	</div>
</div>