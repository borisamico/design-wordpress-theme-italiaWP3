<?php
/*
 * ### LINK SOCIAL ###
 *
 */

$args = wp_parse_args($args, array(
    'ul_class'    => 'list-inline social',
    'li_class'    => 'list-inline-item',
    'always_wrap' => false,
    'label'       => false,
));

$sprite_ponmetro = esc_url(get_template_directory_uri()) . '/static/img/ponmetroca.svg';
$sprite_bi = esc_url(get_template_directory_uri()) . '/static/img/bootstrap-italia.svg';

$telegram = get_option('dettagli-telegram');
$whatsapp = get_option('dettagli-whatsapp');

$socials = array(
    array('Facebook',  get_option('dettagli-facebook'),  $sprite_ponmetro . '#ca-facebook'),
    array('Twitter',   get_option('dettagli-twitter'),   $sprite_ponmetro . '#ca-twitter'),
    array('YouTube',   get_option('dettagli-youtube'),   $sprite_ponmetro . '#ca-youtube'),
    array('Instagram', get_option('dettagli-instagram'), $sprite_ponmetro . '#ca-instagram'),
    array('Telegram',  $telegram != "" ? 'https://telegram.me/' . $telegram : '', $sprite_ponmetro . '#ca-telegram'),
    array('Whatsapp',  $whatsapp != "" ? 'https://wa.me/39' . preg_replace('/\D/', '', $whatsapp) : '', $sprite_ponmetro . '#ca-whatsapp'),
    array('LinkedIn',  get_option('dettagli-linkedin'),  $sprite_bi . '#it-linkedin-square'),
);

$socials = array_filter($socials, function ($social) {
    return $social[1] != "";
});

if (empty($socials) && !$args['always_wrap']) {
    return;
}

$li_class = $args['li_class'] != "" ? ' class="' . esc_attr($args['li_class']) . '"' : '';
?>
<ul class="<?php echo esc_attr($args['ul_class']); ?>">
<?php if (!empty($socials)): ?>
    <?php if ($args['label']): ?>
    <li class="small"><?php echo __('Follow us on','italiawp3'); ?></li>
    <?php endif; ?>
    <?php foreach ($socials as $social) :
        list($name, $url, $icon) = $social; ?>
    <li<?php echo $li_class; ?>>
        <a  target="_blank" class="social-icon"
            aria-label="<?php echo __('Link to external site','italiawp3'); ?> - <?php echo $name; ?>"
            href="<?php echo esc_url($url); ?>">
            <svg class="icon" aria-hidden="true" focusable="false">
                <use  xlink:href="<?php echo $icon; ?>"></use>
            </svg>
            <span class="hidden"><?php echo __('Follow us on','italiawp3'); ?> <?php echo $name; ?></span>
        </a>
    </li>
    <?php endforeach; ?>
<?php endif; ?>
</ul>
