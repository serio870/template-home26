<?php
/**
 * Plugin Name: Lumina Home
 * Description: Home page Lumina Tech pronta per WordPress e Divi. Usa lo shortcode [lumina_home].
 * Version: 1.2.0
 * Author: Codex
 */

if (!defined('ABSPATH')) {
    exit;
}

define('LUMINA_HOME_VERSION', '1.2.0');
define('LUMINA_HOME_URL', plugin_dir_url(__FILE__));
define('LUMINA_HOME_STAGE_IMAGE', 'http://www.blackstarservice.it/wp-content/uploads/2026/05/stage.jpg');

function lumina_home_defaults() {
    return array(
        'brand_name' => 'LUMINA TECH',
        'brand_blue' => '#1677FF',
        'hero_title' => 'Tecnologia per ogni evento, supporto per ogni esigenza.',
        'hero_highlight' => 'ogni evento',
        'hero_text' => 'Dal semplice noleggio al service completo: audio, video, luci, LED wall ed effetti speciali per eventi di ogni dimensione, con soluzioni professionali e flessibili per ogni budget.',
        'button_text' => 'Configura noleggio',
        'phone_1_label' => 'Preventivi / Noleggi',
        'phone_1' => '+39 393 2885376',
        'phone_2_label' => 'Ufficio / Amministrazione',
        'phone_2' => '+39 392 1135447',
        'phone_3_label' => 'Tecnico / Noleggi',
        'phone_3' => '+39 320 1169791',
        'whatsapp' => '3201169791',
        'background_image' => LUMINA_HOME_STAGE_IMAGE,
    );
}

function lumina_home_options() {
    return wp_parse_args(get_option('lumina_home_options', array()), lumina_home_defaults());
}

function lumina_home_admin_menu() {
    add_menu_page(
        'Lumina Home',
        'Lumina Home',
        'manage_options',
        'lumina-home',
        'lumina_home_settings_page',
        'dashicons-welcome-widgets-menus',
        61
    );
}
add_action('admin_menu', 'lumina_home_admin_menu');

function lumina_home_register_settings() {
    register_setting('lumina_home_group', 'lumina_home_options', 'lumina_home_sanitize_options');
}
add_action('admin_init', 'lumina_home_register_settings');

function lumina_home_maybe_update_stage_image() {
    $options = get_option('lumina_home_options', array());
    $old_image = 'https://images.unsplash.com/photo-1514525253361-b83f85f327c6?q=80&w=2574&auto=format&fit=crop';

    if (empty($options) || (($options['background_image'] ?? '') === $old_image)) {
        $options = wp_parse_args($options, lumina_home_defaults());
        $options['background_image'] = LUMINA_HOME_STAGE_IMAGE;
        update_option('lumina_home_options', $options);
    }
}
add_action('init', 'lumina_home_maybe_update_stage_image');

function lumina_home_sanitize_options($input) {
    $defaults = lumina_home_defaults();
    $clean = array();

    foreach ($defaults as $key => $value) {
        if ($key === 'hero_text' || $key === 'background_image') {
            $clean[$key] = esc_url_raw($input[$key] ?? $value);
            if ($key === 'hero_text') {
                $clean[$key] = sanitize_textarea_field($input[$key] ?? $value);
            }
        } elseif ($key === 'brand_blue') {
            $clean[$key] = sanitize_hex_color($input[$key] ?? $value) ?: $value;
        } else {
            $clean[$key] = sanitize_text_field($input[$key] ?? $value);
        }
    }

    return $clean;
}

function lumina_home_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $options = lumina_home_options();
    $fields = array(
        'brand_name' => 'Nome logo',
        'brand_blue' => 'Colore blu',
        'hero_title' => 'Titolo principale',
        'hero_highlight' => 'Parola blu nel titolo',
        'hero_text' => 'Testo sotto al titolo',
        'button_text' => 'Testo bottone blu',
        'phone_1_label' => 'Etichetta telefono 1',
        'phone_1' => 'Telefono 1',
        'phone_2_label' => 'Etichetta telefono 2',
        'phone_2' => 'Telefono 2',
        'phone_3_label' => 'Etichetta telefono 3',
        'phone_3' => 'Telefono 3',
        'whatsapp' => 'Numero WhatsApp',
        'background_image' => 'Immagine sfondo',
    );
    ?>
    <div class="wrap">
        <h1>Lumina Home</h1>
        <p>Qui cambi i testi principali della home. Poi metti lo shortcode <strong>[lumina_home]</strong> nella pagina WordPress o Divi.</p>
        <form method="post" action="options.php">
            <?php settings_fields('lumina_home_group'); ?>
            <table class="form-table" role="presentation">
                <?php foreach ($fields as $key => $label) : ?>
                    <tr>
                        <th scope="row"><label for="lumina_<?php echo esc_attr($key); ?>"><?php echo esc_html($label); ?></label></th>
                        <td>
                            <?php if ($key === 'hero_text') : ?>
                                <textarea id="lumina_<?php echo esc_attr($key); ?>" name="lumina_home_options[<?php echo esc_attr($key); ?>]" rows="4" class="large-text"><?php echo esc_textarea($options[$key]); ?></textarea>
                            <?php else : ?>
                                <input id="lumina_<?php echo esc_attr($key); ?>" name="lumina_home_options[<?php echo esc_attr($key); ?>]" type="text" class="regular-text" value="<?php echo esc_attr($options[$key]); ?>">
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <?php submit_button('Salva modifiche'); ?>
        </form>
    </div>
    <?php
}

function lumina_home_enqueue_assets() {
    wp_enqueue_style('lumina-home-style', LUMINA_HOME_URL . 'assets/lumina-home.css', array(), LUMINA_HOME_VERSION);
    wp_enqueue_script('lumina-home-script', LUMINA_HOME_URL . 'assets/lumina-home.js', array(), LUMINA_HOME_VERSION, true);
}

function lumina_home_tel_link($phone) {
    return 'tel:' . preg_replace('/[^0-9+]/', '', $phone);
}

function lumina_home_icon($name) {
    $icons = array(
        'zap' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 4 14h7l-1 8 10-13h-7l0-7Z"/></svg>',
        'sliders' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3"/><path d="M8 8v8"/><path d="M12 8v8"/><path d="M16 8v8"/><path d="M7 11h2"/><path d="M11 14h2"/><path d="M15 10h2"/></svg>',
        'chevron' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>',
        'phone' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7.5 4.5h2.2l1.7 4-2 1.4a11 11 0 0 0 4.7 4.7l1.4-2 4 1.7v2.2a2.5 2.5 0 0 1-2.8 2.5A14.5 14.5 0 0 1 5 7.3a2.5 2.5 0 0 1 2.5-2.8Z"/></svg>',
        'whatsapp' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7.5 4.5h2.2l1.7 4-2 1.4a11 11 0 0 0 4.7 4.7l1.4-2 4 1.7v2.2a2.5 2.5 0 0 1-2.8 2.5A14.5 14.5 0 0 1 5 7.3a2.5 2.5 0 0 1 2.5-2.8Z"/><path d="M16.8 6.8c1.1.6 1.9 1.5 2.4 2.6"/><path d="M15.6 3.8a8.7 8.7 0 0 1 6 6.2"/></svg>',
        'headset' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 13v-1a7 7 0 0 1 14 0v1"/><path d="M4.5 13.5h3v4.5h-3a1 1 0 0 1-1-1v-2.5a1 1 0 0 1 1-1Z"/><path d="M16.5 13.5h3a1 1 0 0 1 1 1V17a1 1 0 0 1-1 1h-3v-4.5Z"/><path d="M16 18.5c-.6 1.4-1.8 2-3.5 2H11"/></svg>',
        'audio' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4h8a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"/><circle cx="11" cy="14" r="3.4"/><circle cx="11" cy="8" r="1.2"/><path d="M17 10h2"/><path d="M17 14h2"/></svg>',
        'video' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3.5" y="6" width="13" height="10" rx="2"/><path d="m16.5 9.5 4-2.2v7.4l-4-2.2"/><path d="M8 19h8"/><path d="M12 16v3"/></svg>',
        'luci' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5h8l1.5 5-3 2.5h-5L6.5 10 8 5Z"/><path d="M12 12.5V20"/><path d="M8.5 20h7"/><path d="M9.5 15.5 6 18"/><path d="M14.5 15.5 18 18"/></svg>',
        'led' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="13" rx="2"/><path d="M8 5v13"/><path d="M13 5v13"/><path d="M18 5v13"/><path d="M3 9.5h18"/><path d="M3 14h18"/><path d="M9 21h6"/></svg>',
        'strutture' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 18h16"/><path d="M6 18V7h12v11"/><path d="M6 7l12 11"/><path d="M18 7 6 18"/><path d="M10 7v11"/><path d="M14 7v11"/></svg>',
        'effetti' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 20h10"/><path d="M9 20l1-7h4l1 7"/><path d="M12 13V5"/><path d="M8 8l4-3 4 3"/><path d="M5 6l1.5 1.5"/><path d="M19 6l-1.5 1.5"/><path d="M4 12h2"/><path d="M18 12h2"/></svg>',
        'dj' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="5" width="16" height="14" rx="2"/><path d="M8 9h8"/><path d="M8 13h2"/><path d="M14 13h2"/><path d="M8 16h8"/><path d="M10 11v4"/><path d="M14 11v4"/></svg>',
        'accessori' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 7h10v6a5 5 0 0 1-10 0V7Z"/><path d="M9 7V4"/><path d="M15 7V4"/><path d="M12 18v3"/><path d="M9 21h6"/><path d="M17 11h3"/><path d="M4 11h3"/></svg>',
        'arredo' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 11V8a3 3 0 0 1 6 0v3"/><path d="M13 11V8a3 3 0 0 1 6 0v3"/><path d="M4 11h16v7H4z"/><path d="M6 18v2"/><path d="M18 18v2"/><path d="M11 11v7"/></svg>',
        'tecnici' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.5 6.5 17 4l3 3-2.5 2.5"/><path d="M16 8 8 16"/><path d="M6 18l-2 2"/><path d="M9 4h-4v4"/><path d="M5 4l6 6"/><path d="M15 14l5 5"/></svg>',
        'briefcase' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 12h18"/></svg>',
        'headphones' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 14v-2a8 8 0 0 1 16 0v2"/><rect x="3" y="14" width="4" height="6" rx="1"/><rect x="17" y="14" width="4" height="6" rx="1"/></svg>',
        'wrench' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14.7 6.3a4 4 0 0 0-5 5L3 18v3h3l6.7-6.7a4 4 0 0 0 5-5L15 12l-3-3 2.7-2.7Z"/></svg>',
        'message' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 12a8 8 0 0 1-8 8H5l-3 3v-8a8 8 0 1 1 19-3Z"/></svg>',
        'edit' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2 2 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5Z"/></svg>',
        'check' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>',
    );

    return $icons[$name] ?? $icons['zap'];
}

function lumina_home_render_shortcode() {
    lumina_home_enqueue_assets();
    $o = lumina_home_options();
    $title = $o['hero_title'];
    $highlight = $o['hero_highlight'];
    $highlighted_title = esc_html($title);

    if ($highlight && stripos($title, $highlight) !== false) {
        $highlighted_title = preg_replace(
            '/' . preg_quote($highlight, '/') . '/i',
            '<span>' . esc_html($highlight) . '</span>',
            esc_html($title),
            1
        );
    }

    $services = array(
        array('Audio', 'audio'),
        array('Video', 'video'),
        array('Luci', 'luci'),
        array('LED Wall', 'led'),
        array('Strutture', 'strutture'),
        array('Effetti Speciali', 'effetti'),
        array('DJ / Strumenti', 'dj'),
        array('Accessori', 'accessori'),
        array('Arredamento', 'arredo'),
        array('Servizi Tecnici', 'tecnici'),
    );
    $pillars = array(
        array('Noleggio attrezzature', 'briefcase', 'Ampio parco macchine delle migliori marche con ritiro in sede o consegna dedicata per professionisti e privati.'),
        array('Service tecnico eventi', 'headphones', 'Personale qualificato e soluzioni tecniche chiavi in mano per concerti, congressi, matrimoni e sfilate di moda.'),
        array('Vendita e installazione', 'wrench', 'Soluzioni professionali su misura per locali, uffici e spazi pubblici con assistenza post-vendita dedicata.'),
    );
    $steps = array(
        array('01', 'message', 'Brief', 'Raccogliamo esigenze, location, tempi e obiettivi dell\'evento.'),
        array('02', 'sliders', 'Progetto tecnico', 'Definiamo configurazione, materiali, logistica e squadra operativa.'),
        array('3A', 'briefcase', 'Noleggio attrezzature', 'Prepariamo l\'attrezzatura richiesta con ritiro, consegna o supporto dedicato.'),
        array('3B', 'headset', 'Service completo', 'Gestiamo audio, video, luci, strutture e regia tecnica in modo integrato.'),
        array('04', 'check', 'Evento e assistenza', 'Installazione, assistenza live, presidio tecnico e rientro materiali.'),
    );

    ob_start();
    ?>
    <div class="lumina-home" style="--lumina-blue: <?php echo esc_attr($o['brand_blue']); ?>;">
        <section class="lumina-hero">
            <div class="lumina-hero-bg" style="background-image: url('<?php echo esc_url($o['background_image']); ?>');"></div>
            <div class="lumina-hero-inner">
                <h1><?php echo wp_kses($highlighted_title, array('span' => array())); ?></h1>
                <p><?php echo esc_html($o['hero_text']); ?></p>
                <div class="lumina-actions">
                    <a class="lumina-button lumina-button-main" href="#lumina-noleggio"><span class="lumina-button-icon"><?php echo lumina_home_icon('sliders'); ?></span><?php echo esc_html($o['button_text']); ?><span class="lumina-button-arrow"><?php echo lumina_home_icon('chevron'); ?></span></a>
                    <button class="lumina-button lumina-contact-toggle" type="button" aria-expanded="false"><span class="lumina-button-icon"><?php echo lumina_home_icon('headset'); ?></span>Contattaci</button>
                    <a class="lumina-button lumina-button-dark lumina-button-whatsapp" href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', $o['whatsapp'])); ?>"><span class="lumina-button-icon"><?php echo lumina_home_icon('whatsapp'); ?></span>WhatsApp</a>
                </div>
                <div id="lumina-contatti" class="lumina-contact-box" hidden>
                    <a href="<?php echo esc_url(lumina_home_tel_link($o['phone_1'])); ?>"><small><?php echo esc_html($o['phone_1_label']); ?></small><?php echo esc_html($o['phone_1']); ?></a>
                    <a href="<?php echo esc_url(lumina_home_tel_link($o['phone_2'])); ?>"><small><?php echo esc_html($o['phone_2_label']); ?></small><?php echo esc_html($o['phone_2']); ?></a>
                    <a href="<?php echo esc_url(lumina_home_tel_link($o['phone_3'])); ?>"><small><?php echo esc_html($o['phone_3_label']); ?></small><?php echo esc_html($o['phone_3']); ?></a>
                </div>
            </div>
        </section>

        <section id="lumina-servizi" class="lumina-services">
            <?php foreach ($services as $service) : ?>
                <article class="lumina-service-card">
                    <div class="lumina-service-icon"><?php echo lumina_home_icon($service[1]); ?></div>
                    <h2><?php echo esc_html($service[0]); ?></h2>
                </article>
            <?php endforeach; ?>
        </section>

        <section id="lumina-noleggio" class="lumina-pillars">
            <?php foreach ($pillars as $pillar) : ?>
                <article class="lumina-pillar">
                    <div class="lumina-pillar-icon"><?php echo lumina_home_icon($pillar[1]); ?></div>
                    <h2><?php echo esc_html($pillar[0]); ?></h2>
                    <p><?php echo esc_html($pillar[2]); ?></p>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="lumina-workflow">
            <h2>Come lavoriamo</h2>
            <div class="lumina-step-grid">
                <?php foreach ($steps as $index => $step) : ?>
                    <?php if ($index === 2) : ?>
                        <div class="lumina-step-split">
                    <?php endif; ?>
                    <article class="lumina-step<?php echo ($index === 2 || $index === 3) ? ' lumina-step-small' : ''; ?>">
                        <div class="lumina-step-top"><strong><?php echo esc_html($step[0]); ?></strong><?php echo lumina_home_icon($step[1]); ?></div>
                        <h3><?php echo esc_html($step[2]); ?></h3>
                        <p><?php echo esc_html($step[3]); ?></p>
                    </article>
                    <?php if ($index === 3) : ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </section>

    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('lumina_home', 'lumina_home_render_shortcode');
