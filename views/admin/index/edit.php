<?php
queue_js_file('vendor/tiny_mce/tiny_mce');
$head = array('bodyclass' => 'auto-gloss primary', 
              'title' => __('Auto Gloss | Edit "%s"', metadata('term', 'id')));
echo head($head);
?>

<script type="text/javascript">
jQuery(window).load(function() {
    // Initialize and configure TinyMCE.
    tinyMCE.init({
        // Assign TinyMCE a textarea:
        mode : 'exact',
        elements: '<?php echo 'auto-gloss-description'; ?>',
        // Add plugins:
        plugins: 'media,paste,inlinepopups',
        // Configure theme:
        theme: 'advanced',
        theme_advanced_toolbar_location: 'top',
        theme_advanced_toolbar_align: 'left',
        theme_advanced_buttons3_add : 'pastetext,pasteword,selectall',
        // Allow object embed. Used by media plugin
        // See http://www.tinymce.com/forum/viewtopic.php?id=24539
        media_strict: false,
        // General configuration:
        convert_urls: false,
    });
});
</script>

<?php echo flash(); ?>
<?php echo $form; ?>
<?php echo foot(); ?>
