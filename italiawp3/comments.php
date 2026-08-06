<?php
/*
 * ### COMMENTI ###
 *
 * Fino alla 1.6.1 questo file non usava wp_list_comments(): eseguiva una
 * WP_Comment_Query propria e stampava i risultati a mano. Da quella scelta
 * discendevano quattro difetti, di cui uno di sicurezza:
 *
 *  - autore e contenuto erano letti direttamente dall'oggetto commento
 *    (echo $comment->comment_author / ->comment_content). WordPress NON sanifica i
 *    commenti in scrittura: li conserva grezzi e applica kses in LETTURA, tramite i
 *    filtri get_comment_author e comment_text. Leggendo la proprieta' si saltava
 *    l'intera pipeline, e il markup di un commento approvato finiva eseguito nel
 *    browser del visitatore. Verificato: uno <script> dentro un commento partiva.
 *  - non veniva stampato alcun link "Rispondi" e la gerarchia non era resa, quindi
 *    le risposte annidate non comparivano mai. E' la meta' mancante della correzione
 *    dell'is_singular() top-level, che aveva rimesso in carico lo script comment-reply
 *    lasciandolo pero' senza markup su cui agire.
 *  - la query con status=approve raccoglieva anche pingback e trackback, stampati
 *    come se fossero commenti di persone.
 *  - i commenti erano <div> in sequenza: nessuna semantica di lista per gli screen
 *    reader (WCAG 1.3.1).
 *
 * comments_template() prepara gia' have_comments() e la query globale: bastava usarle.
 */

if (!function_exists('italiawp3_comment_callback')) :
    /**
     * Rende un singolo commento con il callout del tema.
     *
     * Riproduce le classi che il file stampava prima (callout callout-highlight
     * pt16 pb16 / callout-title mb0), cosi' l'aspetto resta quello di sempre.
     * Il tag di chiusura non va emesso: ci pensa Walker_Comment::end_el().
     *
     * @param WP_Comment $comment Il commento.
     * @param array      $args    Argomenti di wp_list_comments().
     * @param int        $depth   Livello di annidamento.
     */
    function italiawp3_comment_callback($comment, $args, $depth) {
        $tag = ('div' === $args['style']) ? 'div' : 'li';
        ?>
        <<?php echo $tag; ?> id="comment-<?php echo esc_attr(get_comment_ID()); ?>" <?php comment_class('callout callout-highlight pt16 pb16'); ?>>
            <?php
            /*
             * Il wrapper interno non e' decorativo: comment-reply.js sposta #respond
             * SUBITO DOPO l'elemento indicato da add_below. Puntando al <li> stesso il
             * form finirebbe come fratello del <li>, cioe' un <div> figlio diretto di
             * <ol>: markup non valido. Puntando a questo div ci finisce dentro al <li>,
             * dove deve stare. E' il motivo per cui i temi del core hanno div-comment-N.
             */
            ?>
            <div id="div-comment-<?php echo esc_attr(get_comment_ID()); ?>">
            <article>
                <div class="callout-title mb0"><?php echo esc_html(get_comment_author($comment)); ?></div>

                <p class="comment-meta">
                    <time datetime="<?php echo esc_attr(get_comment_time('c')); ?>">
                        <?php echo esc_html(get_comment_date('', $comment)); ?>
                    </time>
                </p>

                <?php
                // comment_text() riporta il contenuto dentro la pipeline di WordPress:
                // kses, wpautop, make_clickable, convert_smilies, wptexturize, piu' i
                // filtri dei plugin. Genera da se' i propri <p>.
                comment_text($comment);
                ?>

                <?php if ('0' === $comment->comment_approved) : ?>
                    <p class="comment-awaiting-moderation">
                        <?php echo esc_html__('Your comment is awaiting moderation.', 'italiawp3'); ?>
                    </p>
                <?php endif; ?>

                <?php
                comment_reply_link(array_merge($args, array(
                    'add_below'  => 'div-comment',
                    'depth'      => $depth,
                    'max_depth'  => $args['max_depth'],
                    'reply_text' => __('Reply', 'italiawp3'),
                )), $comment);
                ?>
            </article>
            </div>
        <?php
    }
endif;
?>

<div class="row mt64">
    <div class="col-12">
        <h2><?php echo __('Comments','italiawp3'); ?></h2>

        <?php if (have_comments()) : ?>

            <ol class="comment-list">
                <?php
                wp_list_comments(array(
                    'style'    => 'ol',
                    'callback' => 'italiawp3_comment_callback',
                    // Pingback e trackback non sono commenti di persone e non vanno
                    // mescolati a quelli: se ce ne sono, si elencano a parte.
                    'type'     => 'comment',
                ));
                ?>
            </ol>

            <?php
            $italiawp3_nav_commenti = paginate_comments_links(array('echo' => false));
            if ($italiawp3_nav_commenti) :
                ?>
                <nav class="comment-navigation" aria-label="<?php echo esc_attr__('Comments navigation', 'italiawp3'); ?>">
                    <?php echo $italiawp3_nav_commenti; ?>
                </nav>
            <?php endif; ?>

        <?php else : ?>

            <div class="callout callout-highlight pt16 pb16">
                <p><?php echo __('No comment','italiawp3'); ?></p>
            </div>

        <?php endif; ?>
    </div>
    
    <div class="col-12">
        <div class="callout callout-highlight pt16 pb16">
            <?php
            $commenter = wp_get_current_commenter();
            $req = get_option('require_name_email');
            $aria_req = ( $req ? " aria-required='true'" : '' );
            $consent = empty($commenter['comment_author_email']) ? '' : ' checked="checked"';

            /*
             * Accessibilita' del form commenti:
             *
             * - Le <label> di author ed email avevano il markup corrotto: la stringa era
             *   '<label ' . ( $req ? 'is-required' : '' ) . '" for="author">', cioe' senza
             *   class=" di apertura. Ne usciva <label is-required" for="author">, HTML non
             *   valido con l'associazione al campo a rischio (WCAG 1.3.1, 4.1.1).
             * - L'asterisco da solo non spiega nulla: serve una legenda, referenziata dai
             *   campi con aria-describedby (WCAG 3.3.2).
             * - aria-required non va sul campo url, che obbligatorio non e' mai.
             */
            $required_note_id = 'comment-form-required-note';
            $aria_desc = ( $req ? ' aria-describedby="' . $required_note_id . '"' : '' );
            $label_class = ( $req ? ' class="is-required"' : '' );

            $fields = array(
                'author' =>
                '<div class="form-row">' .
                    '<div class="form-group col-12">' .
                        '<input class="form-control" id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" ' . $aria_req . $aria_desc . ($req ? ' size="30" required' : '') . '>' .
                        '<label' . $label_class . ' for="author">' . esc_html__('Name','italiawp3') . ($req ? '*' : '') . '</label>' .
                    '</div>' .
                '</div>',

                'email' =>
                '<div class="form-row">' .
                    '<div class="form-group col-12">' .
                        '<input class="form-control" id="email" name="email" type="email" value="' . esc_attr($commenter['comment_author_email']) . '" ' . $aria_req . $aria_desc . ($req ? ' size="30" required' : '') . '>' .
                        '<label' . $label_class . ' for="email">' . esc_html__('Email','italiawp3') . ($req ? '*' : '') . '</label>' .
                    '</div>' .
                '</div>',

                'url' =>
                '<div class="form-row">' .
                    '<div class="form-group col-12">' .
                        '<input class="form-control" id="url" name="url" type="text" value="' . esc_attr($commenter['comment_author_url']) . '" size="30">' .
                        '<label for="url">'.__('Site','italiawp3').'</label>' .
                    '</div>' .
                '</div>',

                'cookies' =>
                '<div class="form-row">' .
                    '<p class="is-required">'.__('Saving a cookie with my data (name, email, website) for the next comment','italiawp3').'</p>' .
                '</div>' .
                '<div class="form-row">' .
                    '<div class="form-check">' .
                        '<input class="form-check-input" type="checkbox" value="yes" id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" aria-required="true" aria-describedby="' . $required_note_id . '"' . $consent . ' required>' .
                        '<label class="form-check-label is-required" for="wp-comment-cookies-consent">*' . esc_html__('I give my consent','italiawp3') .'</label>' .
                    '</div>' .
                '</div>'
            );

            $args = array(
                'fields' => $fields,
                
                'comment_field' =>
                '<div class="form-row">' .
                    '<div class="form-group col-12">' .
                        '<textarea id="comment" name="comment" maxlength="65525" ' . $aria_req . $aria_desc . ($req ? ' required' : '') . ' rows="3"></textarea>' .
                        '<label' . $label_class . ' for="comment">' . esc_html__('Comment','italiawp3') . ($req ? '*' : '') . '</label>' .
                    '</div>' .
                '</div>',

                /*
                 * Era un <p>: la sezione del form non compariva nella navigazione per
                 * titoli. Diventa <h3>, cioe' una sottosezione del blocco "Commenti"
                 * (<h2> poche righe sopra), senza salti di livello (WCAG 1.3.1).
                 * Subito sotto, la legenda che spiega l'asterisco.
                 */
                'title_reply' =>
                '<div class="form-row">' .
                    '<h3>'.__('Leave a comment','italiawp3').'</h3>' .
                    // Sempre presente: la spunta di consenso e' obbligatoria a prescindere
                    // da require_name_email, e un aria-describedby non deve mai puntare a
                    // un id inesistente.
                    '<p id="' . $required_note_id . '" class="comment-form-required-note">' . esc_html__('Fields marked with an asterisk are required','italiawp3') . '</p>' .
                '</div>',
                
                'submit_button' =>
                '<div class="form-row mt32">' .
                    '<div class="form-group col-12 text-center mb0">' .
                        '<input name="%1$s" type="submit" id="%2$s" class="%3$s btn btn-primary" value="%4$s" />' .
                    '</div>' .
                '</div>'
            );
            
            comment_form($args); ?>
        </div>
    </div>
    
</div>
