<?php
/*
 * ### SIDEBAR per le PAGINE ###
 *
 */
?>

<?php if (is_active_sidebar('sidebar-pagine')) { ?>
<div class="italiawp3-sidebar">
    <?php dynamic_sidebar('sidebar-pagine'); ?>
</div>
<?php } ?>
