<?php
/**
 * Plugin Name: WP Code Highlight.js
 * Plugin URI: https://github.com/owt5008137/WP-Code-Highlight.js
 * Description: This is simple wordpress plugin for <a href="http://highlightjs.org/">highlight.js</a> library. Highlight.js highlights syntax in code examples on blogs, forums and in fact on any web pages. It&acute;s very easy to use because it works automatically: finds blocks of code, detects a language, highlights it.
 * Version: 0.6.3
 * Author: OWenT
 * Author URI: https://owent.net/
 * License: 3-clause BSD
*/

$PLUGIN_DIR =  plugins_url() . '/' . dirname(plugin_basename(__FILE__));
/**
 * Get version of this plugins
 */
function hljs_get_version() {
    return '0.6.3';
}
/**
 * Get version of Highlight.js
 */
function hljs_get_lib_version() {
    return '9.15.8';
}
/**
 * list cdn list
 */
function hljs_cdn_list() {
    return array(
        'local' => array(
            'cdn' => 'local',
            'desc' => __('local', 'wp-code-highlight.js'),
            'css' => '',
            'js' => '',
            'readme' => '',
        ),
        'CdnJs' => array(
            'cdn' => '//cdnjs.cloudflare.com/ajax/libs/highlight.js/' . hljs_get_lib_version(),
            'desc' => __('Public CDN', 'wp-code-highlight.js') . ': cdnjs (' . __('highlightjs.org recommend', 'wp-code-highlight.js') . ')',
            'css' => '.min',
            'js' => '.min',
            'readme' => 'https://cdnjs.com/libraries/highlight.js'
        ),
        'BootCSS' => array(
            'cdn' => '//cdn.bootcss.com/highlight.js/' . hljs_get_lib_version(),
            // 'cdn' => '//cdn.bootcss.com/highlight.js/9.2.0',
            'desc' => __('Public CDN', 'wp-code-highlight.js') . ': BootCSS',
            'css' => '.min',
            'js' => '.min',
            'readme' => 'http://www.bootcdn.cn/highlight.js/'
        ),
        'jsDelivr' => array(
            'cdn' => '//cdn.jsdelivr.net/gh/highlightjs/cdn-release@' . hljs_get_lib_version() . '/build',
            //'cdn' => '//cdn.jsdelivr.net/highlight.js/9.11.0',
            'desc' => __('Public CDN', 'wp-code-highlight.js') . ': jsDelivr (' . __('highlightjs.org recommend', 'wp-code-highlight.js') . ') ' . __('lastest version', 'wp-code-highlight.js') . ': 9.11.0)',
            'css' => '.min',
            'js' => '.min',
            'readme' => 'http://www.jsdelivr.com/#!highlight.js'
        )
   );
}
/**
 * Plugin Installation:
 *   - create configuration keys
 */
function hljs_install() {
    add_option('hljs_code_option', array(
        'location' => 'local',
        'package' => 'common',
        'theme' => 'default',
        'hljs_option' => array(
            'tabReplace' => '    ',
            'classPrefix' => '',
            'useBR' => false,
            'languages' => ''
        ),
        'additional_css' => "code.hljs { /*margin: 5px;*/ }",
        'syntaxhighlighter_compatible' => false,
        'crayonsyntaxhighlighter_compatible' => false,
        'prettify_compatible' => false,
        'shortcode' => false,
        'custom_lang' => array()
    ));
}
register_activation_hook(__FILE__, 'hljs_install');

/**
 * Plugin Deinstallation
 *   - delete configuration keys
 */
function hljs_deinstall() {
    delete_option('hljs_code_option');
}
register_deactivation_hook(__FILE__, 'hljs_deinstall');

/**
 * Get option of this plugin
 */
function hljs_get_option($item) {
    $res = get_option('hljs_code_option');
    if (empty($res) || !isset($res[$item]))
        return null;
    return $res[$item];
}
/**
 * Set option of this plugin
 */
function hljs_set_option($item, $val) {
    $res = get_option('hljs_code_option');
    if (empty($res))
        $res = array();
    $res[$item] = $val;
    update_option('hljs_code_option', $res);
    return $val;
}
/**
 * Get option of highlight.js
 */
function hljs_get_lib_option($item) {
    $res = hljs_get_option('hljs_option');
    if (empty($res) || !isset($res[$item]))
        return '';
    return $res[$item];
}
function hljs_remove_ex_mode() {
    $upload_options = get_option('hljs_code_option');
    if (!empty($upload_options)) {
        $opt_packs = $upload_options['package'];
        // ex mode already deleted, so convert to custom mode
        if ('ex' == $opt_packs) {
            $upload_options['package'] = 'custom';
            $upload_options['custom_lang'] = array('actionscript', 'applescript', 'cmake', 'capnproto', 'd', 'dos', 'erlang', 'fsharp', 'go', 'less', 'lisp', 'lua', 'matlab', 'protobuf', 'profile', 'scala', 'tex', 'typescript');
            update_option('hljs_code_option', $upload_options);
        }
        hljs_generate_custom_pack();
    }
}
/**
 * Attach Highlight.js to the current page
 *   - attach highlight.pack.js
 *   - attach colorscheme stylesheet
 */
function hljs_include() {
    global $PLUGIN_DIR;
    $hljs_code_option = get_option('hljs_code_option');
    $hljs_location = $hljs_code_option['location'];
    $hljs_package = hljs_get_option('package');
    $hljs_cdn_list = hljs_cdn_list();
    $hljs_cdn_info = $hljs_cdn_list['local'];
    if (!empty($hljs_cdn_list[$hljs_location]))
        $hljs_cdn_info = $hljs_cdn_list[$hljs_location];
    // inject js & css file
    if ( 'local' == $hljs_cdn_info['cdn'] ) {
        if ('ex' == $hljs_package) {
            hljs_remove_ex_mode();
        }
        $dep_libs = array('jquery');
        if ('custom' == $hljs_package) {
            wp_enqueue_script( 'hljs_preload', $PLUGIN_DIR . '/highlight.common.pack.js', $dep_libs, hljs_get_version(), true );
            $dep_libs = array('hljs_preload');
        }
        wp_enqueue_script( 'hljs', $PLUGIN_DIR . '/highlight.' . $hljs_package .'.pack.js', $dep_libs, hljs_get_version(), true );
        wp_enqueue_style( 'hljstheme', $PLUGIN_DIR . '/styles/' . $hljs_code_option['theme'] . '.css', array(), hljs_get_version() );
    } else {
        wp_enqueue_script( 'hljs', $hljs_cdn_info['cdn'] . '/highlight' . $hljs_cdn_info['js'] . '.js', array('jquery'), hljs_get_version(), true );
        wp_enqueue_style( 'hljstheme', $hljs_cdn_info['cdn'] . '/styles/' . $hljs_code_option['theme'] . $hljs_cdn_info['css'] . '.css', array(), hljs_get_version() );
        // additional languages
        $custom_addition_langs = hljs_get_option('custom_lang');
        if(!empty($custom_addition_langs)) {
            foreach(hljs_get_option('custom_lang') as $lang) {
                 wp_enqueue_script( 'hljs_lang_' . $lang, $hljs_cdn_info['cdn'] . '/languages/' . $lang . $hljs_cdn_info['js'] . '.js', array('hljs'), hljs_get_version(), true );
            }
        }
    }
}
add_action('wp_head', 'hljs_include');
/**
 * Attach init code to the current page
 */
function hljs_append_init_codes() {
    $hljs_code_option = get_option('hljs_code_option');
    // inject init script
    foreach ($hljs_code_option['hljs_option'] as $key => $val) {
        if (!empty($val)) {
            $hljs_lib_config[$key] = $val;
        }
    }
?>
    <style><?php echo $hljs_code_option['additional_css']; ?></style>
    <script type="text/javascript">
    (function($, window) {
        var init_fn_flag = false;
        var init_fn = (function() {
            if (init_fn_flag)
                return;
            init_fn_flag = true;
 <?php if (count($hljs_lib_config) > 0) { ?>
            hljs.configure(<?php echo json_encode($hljs_lib_config); ?>);
<?php } ?>
            $('pre code').each(function(i, block) {
                hljs.highlightBlock(block);
            });
<?php
    // inject compatible support
    if (hljs_get_option('syntaxhighlighter_compatible')){ ?>
            $('pre:not(:has(code))').each(function(i, block){
                var class_desc = $(block).attr("class") || "";
                var reg_mat = class_desc.match(/brush\s*:\s*([\w\d]+)/i);
                if(!reg_mat || reg_mat.length < 2)
                    return;
                var code_content = $(block).removeClass("brush:").removeClass("ruler:").removeClass("first-line:").removeClass("highlight:")
                    .removeClass("brush:" + reg_mat[1] + ";").removeClass(reg_mat[1] + ";").removeClass("true;").removeClass("false;").html();
                $(block).empty().append($("<code class='hljs'></code>").html(code_content)).addClass(reg_mat[1]);
                hljs.highlightBlock(block);
            });
<?php }
    if (hljs_get_option('prettify_compatible')) { ?>
        $('pre.prettyprint:not(.prettyprinted), code.prettyprint:not(.prettyprinted), xmp.prettyprint:not(.prettyprinted)').each(function(i, block){
            var jblock = $(block).removeClass("prettyprint");
            if (jblock.prop("tagName").toLowerCase() == "code" && jblock.parent().prop("tagName").toLowerCase() == "pre") {
                hljs.highlightBlock(jblock.parent().get(0));
                return;
            }

            var class_desc = $(block).attr("class") || "";
            var reg_mat = class_desc.match(/lang[^-]*-([\w\d]+)/i);
            var code_content = $("<code class='hljs'></code>");
            if (reg_mat && reg_mat.length >= 2) {
                code_content.addClass(reg_mat[1]);
            }
            if (jblock.prop("tagName") === "PRE") {
              jblock.wrapInner(code_content);
              hljs.highlightBlock(jblock.children().get(0));
            } else {
              var code_content = jblock.html();
              jblock.replaceWith($("<pre></pre>").append(code_content.html(code_content)));
              hljs.highlightBlock(jblock.get(0));
            }
        });
<?php }
    //crayon compatible
    if(hljs_get_option('crayonsyntaxhighlighter_compatible')){ ?>
        $('pre:not(:has(code))').each(function(i, block){
            var class_desc = $(block).attr("class") || "";
            var reg_mat = class_desc.match(/lang\s*:\s*([\w\d]+)/i);
            var code_content = $("<code class='hljs'></code>").html($(block).removeAttr('class').html());
            if (reg_mat && reg_mat.length >= 2) {
                code_content.addClass(reg_mat[1]);
            }
            $(block).empty().append(code_content);
            hljs.highlightBlock(block);
        });
<?php } ?>
        });
        $(document).ready(init_fn);
        $(window).on("load", init_fn);
    })(jQuery, window);
    </script>
<?php
}
add_action('wp_print_footer_scripts', 'hljs_append_init_codes');
/**
 * Initialize Localization Functions
 */
function init_hljs_textdomain() {
    if (function_exists('load_plugin_textdomain')) {
        load_plugin_textdomain('wp-code-highlight.js', false, dirname(plugin_basename( __FILE__ )) . '/' . 'l10n');
    }
}
add_action('init', 'init_hljs_textdomain');
/**
 * Print CDN location addrs
 */
function hljs_get_location_list($current_location) {
    $cdn_list  = hljs_cdn_list();
    foreach($cdn_list as $key => $val) {
         ?><option value="<?php echo $key; ?>" <?php
        if($key == $current_location)
            echo ' selected="selected"';
        ?> readme_url="<?php echo $val['readme']; ?>" ><?php echo empty($val['desc'])? $key: $val['desc']; ?></option><?php
    }
}
/**
 * Print package version
 */
function hljs_get_package_list($current_package) {
    $pkgs = array(
        'common' => 'Common(about 40+KB)',
        'all' => 'All(about 400+KB)',
        'custom' => 'Custom',
    );
    foreach($pkgs as $key => $val) {
        ?><option value="<?php echo $key; ?>" <?php
        if($key == $current_package)
            echo ' selected="selected"';
        ?>><?php echo $val; ?></option><?php
    }
}
/**
 * Print Combobox With Styles
 */
function hljs_get_style_list($current_theme) {
    $styleDir = plugin_dir_path( __FILE__ ) . 'styles';
    if ($dir = scandir($styleDir)) {
        foreach($dir as $file) {
            if (($file == '.') or ($file == '..'))
                continue;
            if ('.css' != substr($file, strlen($file) - 4))
                continue;
            $theme_name = substr($file, 0, strlen($file) - 4);
            ?><option value="<?php echo $theme_name; ?>" <?php
            if($theme_name == $current_theme)
                echo ' selected="selected"';
            ?>><?php echo $theme_name; ?></option><?php
        }
    }
}
/**
 * Add Settings Page to Admin Menu
 */
function hljs_admin_page() {
    if (function_exists('add_submenu_page'))
        add_options_page(__('WP Code Highlight.js Settings'), __('WP Code Highlight.js'), 'manage_options', 'wp-code-highlight-js', 'hljs_settings_page');
}
add_action('admin_menu', 'hljs_admin_page');

/**
 * Add Settings link to plugin page
 */
function hljs_add_settings_link($links, $file) {
    if ($file == plugin_basename(__FILE__)) {
      $links[] = '<a href="options-general.php?page=wp-code-highlight-js">' . __('Settings') . '</a>';
    }
    return $links;
}
add_filter('plugin_action_links', 'hljs_add_settings_link', 10, 2);

/**
 * Add BB-Tag for highlighting.
 *
 *   Usage: [CODE lang=cpp bbcode=enable]...[/CODE]
 */
function hljs_code_handler($attrs, $content) {
    $language = '';
    if (!empty($attrs['lang']))
        $language = "class=\"${attrs['lang']}\"";
    $enable_inner_bbcode = false;
    if(isset($attrs['bbcode'])) {
        $enable_inner_bbcode = true;
        if('disable' === $attrs['bbcode'] || 0 === $attrs['bbcode']) {
            $enable_inner_bbcode = false;
        }
    }
    if($enable_inner_bbcode) {
        return "<pre><code $language>" . do_shortcode(ltrim($content, "\n")) . '</code></pre>';
    } else {
        return "<pre><code $language>" . ltrim($content, "\n") . '</code></pre>';
    }
}
if (hljs_get_option('shortcode')) {
    add_shortcode('code', 'hljs_code_handler');
}
function hljs_generate_custom_pack() {
    // generate custom language pack
    $ret = TRUE;
    $opt_loc = hljs_get_option('location');
    $opt_packs = hljs_get_option('package');
    $opt_langs = hljs_get_option('custom_lang');
    $plugin_root_dir = plugin_dir_path( __FILE__ );
    if ('local' == $opt_loc && 'custom' == $opt_packs) {
        $custom_pack_file = $plugin_root_dir . DIRECTORY_SEPARATOR . 'highlight.custom.pack.js';
        if ( FALSE  === file_put_contents($custom_pack_file, '') ) {
            echo '<p class="error">' . __('Write custom highlight language package to', 'wp-code-highlight.js') . ' ' . $custom_pack_file . __('failed, please grant write permission to it.', 'wp-code-highlight.js') . '</p>';
            $ret = FALSE;
        } else {
            foreach($opt_langs as $language_name) {
                $file_name = $language_name . '.min.js';
                $full_path = $plugin_root_dir . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . $file_name;
                if (file_exists($full_path)) {
                    $fc = file_get_contents($full_path);
                    file_put_contents($custom_pack_file, $fc . PHP_EOL, FILE_APPEND);
                } else {
                    echo '<p class="warn">' .
                        __('Language file', 'wp-code-highlight.js') .
                        ' ' . $file_name . ' ' .
                        __('not found', 'wp-code-highlight.js') . ', ' .
                        __('ignored', 'wp-code-highlight.js') . '</p>';
                }
            }
            echo '<p class="info">' . __('Generate custom highlight language package done.', 'wp-code-highlight.js') . '</p>';
        }
    } else {
        // check if CDN available
        $all_available_cdns = hljs_cdn_list();
        if(empty($all_available_cdns[$opt_loc])) {
            $default_cdn = 'CdnJs';
            foreach($all_available_cdns as $key => $val) {
                if ('local' != $key) {
                    $default_cdn = $key;
                    break;
                }
            }
            hljs_set_option('location', $default_cdn);
            echo '<p class="info">' . __('CDN ', 'wp-code-highlight.js') . $opt_loc . __(' not available now', 'wp-code-highlight.js') . '</p>';
            echo '<p class="info">' . __('Use default CDN ', 'wp-code-highlight.js') . $default_cdn . '</p>';
        }
    }
    // rename style to default when filename is changed by highlight.js
    $style_name = hljs_get_option('theme');
    if (false == file_exists($plugin_root_dir . DIRECTORY_SEPARATOR . 'styles' . DIRECTORY_SEPARATOR . $style_name . '.css')) {
        hljs_set_option('theme', 'default');
        echo '<p class="warn">' . __('Style named ' . $style_name . ' is unavailable, maybe highlight.js has changed the name. Theme changed to default now', 'wp-code-highlight.js') . '</p>';
    }

    return $ret;
}

function hljs_on_update_complete($plugin, $data) {
    if (!empty($data) && !empty($data['type']) && 'plugin' == $data['type'] && 'update' == $data['action']) {
        $this_file_name = basename(__FILE__);
        $rebuild_flag = false;
        foreach($data['plugins'] as $updated_file) {
            if ($this_file_name == basename($updated_file)) {
                $rebuild_flag = true;
            }
        }
        if ($rebuild_flag) {
            hljs_generate_custom_pack();
        }
    }
}
add_action('upgrader_process_complete', 'hljs_on_update_complete', 10, 2);

function hljs_check_post_bool($name) {
    if (!isset($_POST[$name])) {
        return false;
    }

    $val = sanitize_text_field($_POST[$name]);
    return $val != '' && $val != '0';
}

/**
 * Highlight.js Settings Page
 */
function hljs_settings_page() {
    global $PLUGIN_DIR;
    if (isset( $_POST['cmd'] ) && $_POST['cmd'] == 'hljs_save')
    {
        check_admin_referer('hljs_save');
        $upload_options = array(
            'location' => sanitize_text_field($_POST['hljs_location']),
            'package' => sanitize_text_field($_POST['hljs_package']),
            'theme' => sanitize_text_field($_POST['hljs_theme']),
            'hljs_option' => array(
                'tabReplace' => sanitize_text_field($_POST['hljs_option_tab_replace']),
                'classPrefix' => sanitize_text_field($_POST['hljs_option_class_prefix']),
                'useBR' => hljs_check_post_bool('hljs_option_use_br'),
                'languages' => sanitize_textarea_field($_POST['hljs_option_languages'])
            ),
            'additional_css' => sanitize_textarea_field($_POST['hljs_additional_css']),
            'syntaxhighlighter_compatible' => hljs_check_post_bool('hljs_syntaxhighlighter_compatible'),
            'prettify_compatible' => hljs_check_post_bool('hljs_prettify_compatible'),
            'crayonsyntaxhighlighter_compatible' => hljs_check_post_bool('hljs_crayonsyntaxhighlighter_compatible'),
            'shortcode' => hljs_check_post_bool('hljs_enable_shortcode'),
            'custom_lang' => hljs_get_option('custom_lang')
        );
        // generate custom language pack
        if ('local' == $upload_options['location'] && 'custom' == $upload_options['package']) {
            $upload_options['custom_lang'] = array();
            $plugin_root_dir = plugin_dir_path( __FILE__ );
            foreach($_POST as $key => $val) {
                $suffix = substr($key, -3);
                if (('.js' == $suffix || '_js' == $suffix )&& intval($val) == 1) {
                    $language_name =  substr($key, 0, strlen($key) - 3);
                    $file_name = $language_name . '.min.js';
                    $full_path = $plugin_root_dir . DIRECTORY_SEPARATOR . 'languages' . DIRECTORY_SEPARATOR . $file_name;
                    if (file_exists($full_path)) {
                        array_push($upload_options['custom_lang'], $language_name);
                    } else {
                        echo '<p class="warn">' .
                            __('Language file', 'wp-code-highlight.js') .
                            ' ' . $file_name . ' ' .
                            __('not found', 'wp-code-highlight.js') . ', ' .
                            __('ignored', 'wp-code-highlight.js') . '</p>';
                    }
                }
            }
        }
        update_option('hljs_code_option', $upload_options);
        $res = hljs_generate_custom_pack();
        if ($res === FALSE) {
            echo '<p class="error">' . __('Save configurations failed.', 'wp-code-highlight.js') . '</p>'; 
        } else {
            echo '<p class="info">' . __('All configurations successfully saved.', 'wp-code-highlight.js') . '</p>'; 
        }
    }
    ?>
    <!-- html code of settings page -->
    <div class="wrap">
      <form id="hljs" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
        <?php 
            wp_nonce_field('hljs_save'); 
            wp_enqueue_script('hljs_preload', $PLUGIN_DIR . '/highlight.common.pack.js', array('jquery'), hljs_get_version(), false );
            wp_add_inline_script('hljs_preload', 'hljs.initHighlightingOnLoad();', 'after');
            wp_enqueue_style('hljstheme', $PLUGIN_DIR . '/styles/default.css', array(), hljs_get_version());
        ?>
        <style>
            .info { padding: 15px; background: #EDEDED; border: 1px solid #333333; font: 14px #333333 Verdana; margin: 30px 10px 0px 0px; }
            .section { padding: 10px; margin: 30px 0 0px; background: #FAFAFA; border: 1px solid #DDDDDD; display: block; }
            input[type="text"] { width: 400px; margin: 10px 0px 0px;}
            textarea {width: 400px; height: 100px; }
            #hljs_theme { width: 200px;  margin: 10px 0px 0px;}
            #submit { min-width: 40px; margin-top: 20px; }
            #hljs_location_readme { color: Gray; font-style: italic; }
            table.hljs_copyright { font-size: 8px; margin-top: 50px;}
            table.hljs_copyright tr {margin-bottom: 10px;}
            table.hljs_copyright tr td {padding: 5px; font: 12px Sans-Serif; border: 1px solid #DDDDDD;}
        </style>
        <!-- combo box with location -->
        <div class="section">
          <label for="hljs_location"><?php echo __('CDN', 'wp-code-highlight.js'); ?></label><br/>
          <select name="hljs_location" id="hljs_location">
             <?php hljs_get_location_list(hljs_get_option('location')); ?>
          </select> <span id="hljs_location_readme"></span>
          <div>
            Current Highlight.js Version: <?php echo hljs_get_lib_version(); ?>
          </div>
        </div>
        <!-- combo box with local source package -->
        <div class="section" id="hljs_local_package">
          <label for="hljs_package"><?php echo __('Package', 'wp-code-highlight.js'); ?></label><br/>
          <select name="hljs_package" id="hljs_package">
             <?php hljs_get_package_list(hljs_get_option('package')); ?>
          </select>
          <div>
              <h3>Support List:</h3> <a href="javascript:void();" id="hljs_support_list_btn"><?php echo __('[Show/Hide]', 'wp-code-highlight.js'); ?></a>
              <div class="language_support_list" id="language_support_list">
                <p><b>Common</b></p>
                <ul id="language_support_list_common">
                  <!-- copy from view-source:https://highlightjs.org/download/ -->
                    <li><label><input type="checkbox" name="apache.js" checked> Apache</label></li>
                    <li><label><input type="checkbox" name="bash.js" checked> Bash</label></li>
                    <li><label><input type="checkbox" name="cs.js" checked> C#</label></li>
                    <li><label><input type="checkbox" name="cpp.js" checked> C++</label></li>
                    <li><label><input type="checkbox" name="css.js" checked> CSS</label></li>
                    <li><label><input type="checkbox" name="coffeescript.js" checked> CoffeeScript</label></li>
                    <li><label><input type="checkbox" name="diff.js" checked> Diff</label></li>
                    <li><label><input type="checkbox" name="xml.js" checked> HTML, XML</label></li>
                    <li><label><input type="checkbox" name="http.js" checked> HTTP</label></li>
                    <li><label><input type="checkbox" name="ini.js" checked> Ini, TOML</label></li>
                    <li><label><input type="checkbox" name="json.js" checked> JSON</label></li>
                    <li><label><input type="checkbox" name="java.js" checked> Java</label></li>
                    <li><label><input type="checkbox" name="javascript.js" checked> JavaScript</label></li>
                    <li><label><input type="checkbox" name="makefile.js" checked> Makefile</label></li>
                    <li><label><input type="checkbox" name="markdown.js" checked> Markdown</label></li>
                    <li><label><input type="checkbox" name="nginx.js" checked> Nginx</label></li>
                    <li><label><input type="checkbox" name="objectivec.js" checked> Objective-C</label></li>
                    <li><label><input type="checkbox" name="php.js" checked> PHP</label></li>
                    <li><label><input type="checkbox" name="perl.js" checked> Perl</label></li>
                    <li><label><input type="checkbox" name="properties.js" checked> Properties</label></li>
                    <li><label><input type="checkbox" name="python.js" checked> Python</label></li>
                    <li><label><input type="checkbox" name="ruby.js" checked> Ruby</label></li>
                    <li><label><input type="checkbox" name="sql.js" checked> SQL</label></li>
                    <li><label><input type="checkbox" name="shell.js" checked> Shell Session</label></li>
                </ul>
                <p><b>Other</b></p>
                <ul id="language_support_list_other">
                    <!-- copy from view-source:https://highlightjs.org/download/ -->
                    <li><label><input type="checkbox" name="1c.js"> 1C:Enterprise (v7, v8)</label></li>
                    <li><label><input type="checkbox" name="armasm.js"> ARM Assembly</label></li>
                    <li><label><input type="checkbox" name="avrasm.js"> AVR Assembler</label></li>
                    <li><label><input type="checkbox" name="accesslog.js"> Access log</label></li>
                    <li><label><input type="checkbox" name="actionscript.js"> ActionScript</label></li>
                    <li><label><input type="checkbox" name="ada.js"> Ada</label></li>
                    <li><label><input type="checkbox" name="angelscript.js"> AngelScript</label></li>
                    <li><label><input type="checkbox" name="applescript.js"> AppleScript</label></li>
                    <li><label><input type="checkbox" name="arcade.js"> ArcGIS Arcade</label></li>
                    <li><label><input type="checkbox" name="arduino.js"> Arduino</label></li>
                    <li><label><input type="checkbox" name="asciidoc.js"> AsciiDoc</label></li>
                    <li><label><input type="checkbox" name="aspectj.js"> AspectJ</label></li>
                    <li><label><input type="checkbox" name="abnf.js"> Augmented Backus-Naur Form</label></li>
                    <li><label><input type="checkbox" name="autohotkey.js"> AutoHotkey</label></li>
                    <li><label><input type="checkbox" name="autoit.js"> AutoIt</label></li>
                    <li><label><input type="checkbox" name="awk.js"> Awk</label></li>
                    <li><label><input type="checkbox" name="axapta.js"> Axapta</label></li>
                    <li><label><input type="checkbox" name="bnf.js"> Backus?Naur Form</label></li>
                    <li><label><input type="checkbox" name="basic.js"> Basic</label></li>
                    <li><label><input type="checkbox" name="brainfuck.js"> Brainfuck</label></li>
                    <li><label><input type="checkbox" name="cal.js"> C/AL</label></li>
                    <li><label><input type="checkbox" name="cmake.js"> CMake</label></li>
                    <li><label><input type="checkbox" name="csp.js"> CSP</label></li>
                    <li><label><input type="checkbox" name="cos.js"> Cach? Object Script</label></li>
                    <li><label><input type="checkbox" name="capnproto.js"> Cap?n Proto</label></li>
                    <li><label><input type="checkbox" name="ceylon.js"> Ceylon</label></li>
                    <li><label><input type="checkbox" name="clean.js"> Clean</label></li>
                    <li><label><input type="checkbox" name="clojure.js"> Clojure</label></li>
                    <li><label><input type="checkbox" name="clojure-repl.js"> Clojure REPL</label></li>
                    <li><label><input type="checkbox" name="coq.js"> Coq</label></li>
                    <li><label><input type="checkbox" name="crystal.js"> Crystal</label></li>
                    <li><label><input type="checkbox" name="d.js"> D</label></li>
                    <li><label><input type="checkbox" name="dns.js"> DNS Zone file</label></li>
                    <li><label><input type="checkbox" name="dos.js"> DOS .bat</label></li>
                    <li><label><input type="checkbox" name="dart.js"> Dart</label></li>
                    <li><label><input type="checkbox" name="delphi.js"> Delphi</label></li>
                    <li><label><input type="checkbox" name="dts.js"> Device Tree</label></li>
                    <li><label><input type="checkbox" name="django.js"> Django</label></li>
                    <li><label><input type="checkbox" name="dockerfile.js"> Dockerfile</label></li>
                    <li><label><input type="checkbox" name="dust.js"> Dust</label></li>
                    <li><label><input type="checkbox" name="erb.js"> ERB (Embedded Ruby)</label></li>
                    <li><label><input type="checkbox" name="elixir.js"> Elixir</label></li>
                    <li><label><input type="checkbox" name="elm.js"> Elm</label></li>
                    <li><label><input type="checkbox" name="erlang.js"> Erlang</label></li>
                    <li><label><input type="checkbox" name="erlang-repl.js"> Erlang REPL</label></li>
                    <li><label><input type="checkbox" name="excel.js"> Excel</label></li>
                    <li><label><input type="checkbox" name="ebnf.js"> Extended Backus-Naur Form</label></li>
                    <li><label><input type="checkbox" name="fsharp.js"> F#</label></li>
                    <li><label><input type="checkbox" name="fix.js"> FIX</label></li>
                    <li><label><input type="checkbox" name="flix.js"> Flix</label></li>
                    <li><label><input type="checkbox" name="fortran.js"> Fortran</label></li>
                    <li><label><input type="checkbox" name="gcode.js"> G-code (ISO 6983)</label></li>
                    <li><label><input type="checkbox" name="gams.js"> GAMS</label></li>
                    <li><label><input type="checkbox" name="gauss.js"> GAUSS</label></li>
                    <li><label><input type="checkbox" name="glsl.js"> GLSL</label></li>
                    <li><label><input type="checkbox" name="gml.js"> GML</label></li>
                    <li><label><input type="checkbox" name="gherkin.js"> Gherkin</label></li>
                    <li><label><input type="checkbox" name="go.js"> Go</label></li>
                    <li><label><input type="checkbox" name="golo.js"> Golo</label></li>
                    <li><label><input type="checkbox" name="gradle.js"> Gradle</label></li>
                    <li><label><input type="checkbox" name="groovy.js"> Groovy</label></li>
                    <li><label><input type="checkbox" name="hsp.js"> HSP</label></li>
                    <li><label><input type="checkbox" name="htmlbars.js"> HTMLBars</label></li>
                    <li><label><input type="checkbox" name="haml.js"> Haml</label></li>
                    <li><label><input type="checkbox" name="handlebars.js"> Handlebars</label></li>
                    <li><label><input type="checkbox" name="haskell.js"> Haskell</label></li>
                    <li><label><input type="checkbox" name="haxe.js"> Haxe</label></li>
                    <li><label><input type="checkbox" name="hy.js"> Hy</label></li>
                    <li><label><input type="checkbox" name="irpf90.js"> IRPF90</label></li>
                    <li><label><input type="checkbox" name="isbl.js"> ISBL</label></li>
                    <li><label><input type="checkbox" name="inform7.js"> Inform 7</label></li>
                    <li><label><input type="checkbox" name="x86asm.js"> Intel x86 Assembly</label></li>
                    <li><label><input type="checkbox" name="julia.js"> Julia</label></li>
                    <li><label><input type="checkbox" name="julia-repl.js"> Julia REPL</label></li>
                    <li><label><input type="checkbox" name="kotlin.js"> Kotlin</label></li>
                    <li><label><input type="checkbox" name="ldif.js"> LDIF</label></li>
                    <li><label><input type="checkbox" name="llvm.js"> LLVM IR</label></li>
                    <li><label><input type="checkbox" name="lasso.js"> Lasso</label></li>
                    <li><label><input type="checkbox" name="leaf.js"> Leaf</label></li>
                    <li><label><input type="checkbox" name="less.js"> Less</label></li>
                    <li><label><input type="checkbox" name="lsl.js"> Linden Scripting Language</label></li>
                    <li><label><input type="checkbox" name="lisp.js"> Lisp</label></li>
                    <li><label><input type="checkbox" name="livecodeserver.js"> LiveCode</label></li>
                    <li><label><input type="checkbox" name="livescript.js"> LiveScript</label></li>
                    <li><label><input type="checkbox" name="lua.js"> Lua</label></li>
                    <li><label><input type="checkbox" name="mel.js"> MEL</label></li>
                    <li><label><input type="checkbox" name="mipsasm.js"> MIPS Assembly</label></li>
                    <li><label><input type="checkbox" name="mathematica.js"> Mathematica</label></li>
                    <li><label><input type="checkbox" name="matlab.js"> Matlab</label></li>
                    <li><label><input type="checkbox" name="maxima.js"> Maxima</label></li>
                    <li><label><input type="checkbox" name="mercury.js"> Mercury</label></li>
                    <li><label><input type="checkbox" name="routeros.js"> Microtik RouterOS script</label></li>
                    <li><label><input type="checkbox" name="mizar.js"> Mizar</label></li>
                    <li><label><input type="checkbox" name="mojolicious.js"> Mojolicious</label></li>
                    <li><label><input type="checkbox" name="monkey.js"> Monkey</label></li>
                    <li><label><input type="checkbox" name="moonscript.js"> MoonScript</label></li>
                    <li><label><input type="checkbox" name="n1ql.js"> N1QL</label></li>
                    <li><label><input type="checkbox" name="nsis.js"> NSIS</label></li>
                    <li><label><input type="checkbox" name="nimrod.js"> Nimrod</label></li>
                    <li><label><input type="checkbox" name="nix.js"> Nix</label></li>
                    <li><label><input type="checkbox" name="ocaml.js"> OCaml</label></li>
                    <li><label><input type="checkbox" name="openscad.js"> OpenSCAD</label></li>
                    <li><label><input type="checkbox" name="ruleslanguage.js"> Oracle Rules Language</label></li>
                    <li><label><input type="checkbox" name="oxygene.js"> Oxygene</label></li>
                    <li><label><input type="checkbox" name="parser3.js"> Parser3</label></li>
                    <li><label><input type="checkbox" name="pony.js"> Pony</label></li>
                    <li><label><input type="checkbox" name="pgsql.js"> PostgreSQL SQL dialect and PL/pgSQL</label></li>
                    <li><label><input type="checkbox" name="powershell.js"> PowerShell</label></li>
                    <li><label><input type="checkbox" name="processing.js"> Processing</label></li>
                    <li><label><input type="checkbox" name="prolog.js"> Prolog</label></li>
                    <li><label><input type="checkbox" name="protobuf.js"> Protocol Buffers</label></li>
                    <li><label><input type="checkbox" name="puppet.js"> Puppet</label></li>
                    <li><label><input type="checkbox" name="purebasic.js"> PureBASIC</label></li>
                    <li><label><input type="checkbox" name="profile.js"> Python profile</label></li>
                    <li><label><input type="checkbox" name="q.js"> Q</label></li>
                    <li><label><input type="checkbox" name="qml.js"> QML</label></li>
                    <li><label><input type="checkbox" name="r.js"> R</label></li>
                    <li><label><input type="checkbox" name="reasonml.js"> ReasonML</label></li>
                    <li><label><input type="checkbox" name="rib.js"> RenderMan RIB</label></li>
                    <li><label><input type="checkbox" name="rsl.js"> RenderMan RSL</label></li>
                    <li><label><input type="checkbox" name="roboconf.js"> Roboconf</label></li>
                    <li><label><input type="checkbox" name="rust.js"> Rust</label></li>
                    <li><label><input type="checkbox" name="sas.js"> SAS</label></li>
                    <li><label><input type="checkbox" name="scss.js"> SCSS</label></li>
                    <li><label><input type="checkbox" name="sml.js"> SML</label></li>
                    <li><label><input type="checkbox" name="sqf.js"> SQF</label></li>
                    <li><label><input type="checkbox" name="step21.js"> STEP Part 21</label></li>
                    <li><label><input type="checkbox" name="scala.js"> Scala</label></li>
                    <li><label><input type="checkbox" name="scheme.js"> Scheme</label></li>
                    <li><label><input type="checkbox" name="scilab.js"> Scilab</label></li>
                    <li><label><input type="checkbox" name="smali.js"> Smali</label></li>
                    <li><label><input type="checkbox" name="smalltalk.js"> Smalltalk</label></li>
                    <li><label><input type="checkbox" name="stan.js"> Stan</label></li>
                    <li><label><input type="checkbox" name="stata.js"> Stata</label></li>
                    <li><label><input type="checkbox" name="stylus.js"> Stylus</label></li>
                    <li><label><input type="checkbox" name="subunit.js"> SubUnit</label></li>
                    <li><label><input type="checkbox" name="swift.js"> Swift</label></li>
                    <li><label><input type="checkbox" name="tp.js"> TP</label></li>
                    <li><label><input type="checkbox" name="taggerscript.js"> Tagger Script</label></li>
                    <li><label><input type="checkbox" name="tcl.js"> Tcl</label></li>
                    <li><label><input type="checkbox" name="tex.js"> TeX</label></li>
                    <li><label><input type="checkbox" name="tap.js"> Test Anything Protocol</label></li>
                    <li><label><input type="checkbox" name="thrift.js"> Thrift</label></li>
                    <li><label><input type="checkbox" name="twig.js"> Twig</label></li>
                    <li><label><input type="checkbox" name="typescript.js"> TypeScript</label></li>
                    <li><label><input type="checkbox" name="vbnet.js"> VB.NET</label></li>
                    <li><label><input type="checkbox" name="vbscript.js"> VBScript</label></li>
                    <li><label><input type="checkbox" name="vbscript-html.js"> VBScript in HTML</label></li>
                    <li><label><input type="checkbox" name="vhdl.js"> VHDL</label></li>
                    <li><label><input type="checkbox" name="vala.js"> Vala</label></li>
                    <li><label><input type="checkbox" name="verilog.js"> Verilog</label></li>
                    <li><label><input type="checkbox" name="vim.js"> Vim Script</label></li>
                    <li><label><input type="checkbox" name="xl.js"> XL</label></li>
                    <li><label><input type="checkbox" name="xquery.js"> XQuery</label></li>
                    <li><label><input type="checkbox" name="yaml.js"> YAML</label></li>
                    <li><label><input type="checkbox" name="zephir.js"> Zephir</label></li>
                    <li><label><input type="checkbox" name="crmsh.js"> crmsh</label></li>
                    <li><label><input type="checkbox" name="dsconfig.js"> dsconfig</label></li>
                    <li><label><input type="checkbox" name="jboss-cli.js"> jboss-cli</label></li>
                    <li><label><input type="checkbox" name="pf.js"> pf</label></li>
                    <li><label><input type="checkbox" name="plaintext.js"> plaintext</label></li>
                </ul>
                <div style="clear: both;"></div>
              </div>
            </div>
        </div>
        <script type="text/javascript">
        (function($, window){
            $(document).ready(function(){
                $("#language_support_list ul").css({
                    overflow: 'auto',
                    margin: '1em 0px',
                    padding: '0px'
                });
                $("#language_support_list ul li").css({
                    margin: '0.2em 0px',
                    padding: '0px',
                    'list-style': 'outside none none',
                    float: 'left',
                    width: '24.5%'
                });
                $("#language_support_list_common input").prop("disabled", true);
                $("#language_support_list_common input").prop("checked", true);
                $("#language_support_list_common input").val(0);
                $("#language_support_list_common input").addClass("hljs_lang common");
                $("#language_support_list_other input").val(1);
                $("#language_support_list_other input").addClass("hljs_lang");
                var show_package_language = (function(){
                    var hljs_package_name = $("#hljs_package").val();
                    if ("custom" == hljs_package_name || $("#hljs_location").val() != "local") {
                        $("#language_support_list_other input").prop("disabled", false);
                        $("#language_support_list_other input").prop("checked", false);
                        // custom languages
                        var selected_langs = "<?php
                            $custom_lang = hljs_get_option('custom_lang');
                            if(!empty($custom_lang)) {
                                echo implode(' ', $custom_lang);
                            }
                        ?>".split(/[ \t\r\n]/);
                        $.each(selected_langs, function(k, v) {
                            if (v) {
                                $('#language_support_list_other input[name="' + v.replace(/_js$/, "")　+ '.js"]').prop("checked", true);
                            }
                        });
                    } else {
                        // select default languages
                        $("#language_support_list_other input").prop("disabled", true);
                        if ("all" == hljs_package_name) {
                            $("#language_support_list_other input").prop("checked", true);
                        } else {
                            $("#language_support_list_other input").prop("checked", false);
                        }
                    }
               });
                var show_package_fn = (function(){
                    if ($("#hljs_location").val() != "local") {
                        $("#hljs_package").prop('disabled', true);
                    } else {
                        $("#hljs_package").prop('disabled', false);
                    }
                    show_package_language();
                });
                show_package_fn();
                $("#hljs_location").change(function(){
                    $("#hljs_location_readme").empty();
                    $.each($("option", this), function(k, v) {
                        if ($(v).prop('selected') && $(v).attr("readme_url")) {
                            var text = "<?php echo __('click', 'wp-code-highlight.js'); ?>" +
                                " <a href=\"" + $(v).attr("readme_url") + "\" target=\"_blank\">" +$(v).attr("readme_url") + "</a> " +
                                "<?php echo __('for detail', 'wp-code-highlight.js'); ?>";
                            $("#hljs_location_readme").html(text);
                        }
                    });
                    show_package_fn();
                });
                $("#hljs_package").change(function(){ show_package_language(); });
                $("#hljs_support_list_btn").click(function() {
                    $("#language_support_list").slideToggle();
                });
            });
        })(jQuery, window);
        </script>
        <!-- combo box with styles -->
        <div class="section">
          <label for="hljs_theme"><?php echo __('Color Scheme:', 'wp-code-highlight.js'); ?></label><br/>
          <select name="hljs_theme" id="hljs_theme">
             <?php hljs_get_style_list(hljs_get_option('theme')); ?>
          </select>
            <div><?php echo __('You can get a quick look of all style and all language at <a href="https://highlightjs.org/static/demo/" target="_blank">https://highlightjs.org/static/demo/</a>', 'wp-code-highlight.js'); ?>
          </div>
          <div>
            <strong><?php echo __('Notice', 'wp-code-highlight.js'); ?><strong/>: <?php echo __('some cdn support only older version of highligh.js, and some language or style is unusable, see <a href="https://highlightjs.org/" target="_blank">https://highlightjs.org/</a> for detail', 'wp-code-highlight.js'); ?>
          </div>
        </div>
        <!-- text edit : tab replace -->
        <p class="section">
          <label for="hljs_option_tab_replace"><?php echo __('Highlight.js Option - Tab replace:', 'wp-code-highlight.js'); ?></label><br/>
          <input type="text" name="hljs_option_tab_replace" id="hljs_option_tab_replace" value="<?php echo hljs_get_lib_option('tabReplace'); ?>" /><br />
          <label for="hljs_option_class_prefix"><?php echo __('Highlight.js Option - Class prefix:', 'wp-code-highlight.js') ?></label><br/>
          <input type="text" name="hljs_option_class_prefix" id="hljs_option_class_prefix" value="<?php echo hljs_get_lib_option('classPrefix'); ?>" /><br />
          <label for="hljs_option_use_br"><?php echo __('Highlight.js Option - Use BR:', 'wp-code-highlight.js') ?></label>
          <input type="checkbox" name="hljs_option_use_br" id="hljs_option_use_br" value="1" <?php if(hljs_get_lib_option('useBR')) echo ' checked="checked"'; ?> /><br />
          <label for="hljs_option_languages"><?php echo __('Highlight.js Option - Languages:', 'wp-code-highlight.js'); ?></label><br/>
          <textarea type="text" name="hljs_option_languages" id="hljs_option_languages" value="<?php echo hljs_get_lib_option('languages'); ?>"><?php echo hljs_get_lib_option('languages'); ?></textarea><br />
       </p>
        <!-- text edit : additional css -->
        <p class="section">
          <label for="hljs_additional_css"><?php echo __('You can add some additional CSS rules for better display:', 'wp-code-highlight.js'); ?></label><br/>
          <textarea type="text" name="hljs_additional_css" id="hljs_additional_css"><?php echo hljs_get_option('additional_css'); ?></textarea>
        </p>
        <!-- check box : compatible options -->
        <p class="section">
          <input type="checkbox" name="hljs_syntaxhighlighter_compatible" id="hljs_syntaxhighlighter_compatible" value="1" <?php if(hljs_get_option('syntaxhighlighter_compatible')) echo ' checked="checked"'; ?> />
          <label for="hljs_syntaxhighlighter_compatible"><?php echo __('Syntax Highlighter Compatiable', 'wp-code-highlight.js') ?></label><br />
          <input type="checkbox" name="hljs_prettify_compatible" id="hljs_prettify_compatible" value="1" <?php if(hljs_get_option('prettify_compatible')) echo ' checked="checked"'; ?> />
          <label for="hljs_prettify_compatible"><?php echo __('Prettify Compatible', 'wp-code-highlight.js') ?></label><br />
          <input type="checkbox" name="hljs_crayonsyntaxhighlighter_compatible" id="hljs_crayonsyntaxhighlighter_compatible" value="1" <?php if(hljs_get_option('crayonsyntaxhighlighter_compatible')) echo ' checked="checked"'; ?> />
          <label for="hljs_crayonsyntaxhighlighter_compatible"><?php echo __('Crayon Syntax Highlighter Compatiable', 'wp-code-highlight.js') ?></label><br />
        </p>
        <!-- check box : shortcode options -->
        <p class="section">
          <label for="hljs_enable_shortcode"><?php echo __('Enable [code]code content ...[/code] support:', 'wp-code-highlight.js') ?></label>
          <input type="checkbox" name="hljs_enable_shortcode" id="hljs_enable_shortcode" value="1" <?php if(hljs_get_option('shortcode')) echo ' checked="checked"'; ?> />
        </p>
        <input type="hidden" name="cmd" value="hljs_save" />
        <input type="submit" name="submit" value="<?php echo __('Save', 'wp-code-highlight.js'); ?>" id="submit" />
      </form>
        <!-- copyright information -->
            <table border="0" class="hljs_copyright">
                <tr>
                    <td width="120px" align="center"><?php echo __('Author', 'wp-code-highlight.js'); ?></td>
                    <td><p><a href="http://owent.net"><?php echo __('OWenT', 'wp-code-highlight.js'); ?></a> &lt;<a href="mailto:admin@owent.net">admin@owent.net</a>&gt;</p></td>
                </tr>
                <tr>
                    <td width="120px" align="center"><?php echo __('Plugin Info', 'wp-code-highlight.js'); ?></td>
                    <td><?php echo __('<p>This is a wordpress plugin for <a href="http://highlightjs.org/">highlight.js</a> library.
                        <a href="http://highlightjs.org/">Highlight.js</a> highlights syntax in code examples on web pages.
                        It&acute;s very easy to use because it works automatically: finds blocks of code, detects a language, highlights it.</p>
                        <p>And it&acute;s very easy to work with <a href="https://stackedit.io/" target="_blank">stackedit</a> or other markdown editors</p>
                        ', 'wp-code-highlight.js'); ?></td>
                </tr>
                <tr>
                    <td width="120px" align="center"><?php echo __('Plugin Usage', 'wp-code-highlight.js'); ?></td>
                    <td>
                        <p><?php echo __('For code highlighting you should use one of the following ways.', 'wp-code-highlight.js'); ?></p>
                        <p><strong><?php echo __('The first way', 'wp-code-highlight.js'); ?></strong><?php echo __(' is to use bb-codes', 'wp-code-highlight.js'); ?>:</p>
                            <p><pre><code>[code] this language will be automatically determined [/code]</code></pre></p>
                        <p><pre><code>[code lang="cpp"] highlight the code with certain language [/code]</code></pre></p>
                            <p><strong><?php echo __('The second way', 'wp-code-highlight.js'); ?></strong><?php echo __(' is to use html-tags', 'wp-code-highlight.js'); ?>:</p>
                            <p><pre><code class="html">&lt;pre&gt;&lt;code&gt; this language will be automatically determined &lt;/code&gt;&lt;/pre&gt;</code></pre></p>
                            <p><pre><code class="html">&lt;pre&gt;&lt;code bbcode=enable&gt; this language will be [b]automatically determined[\b] and inner bbcode is available &lt;/code&gt;&lt;/pre&gt;</code></pre></p>
                        <p><pre><code class="html">&lt;pre&gt;&lt;code class="html"&gt; highlight the code with certain language &lt;/code&gt;&lt;/pre&gt;</code></pre></p>
                    </td>
                </tr>
                <tr>
                    <td width="120px" align="center"><?php echo __('Donate', 'wp-code-highlight.js'); ?></td>
                    <td><?php echo __('If you interested my work, welcome to visit <a href="https://github.com/owt5008137/WP-Code-Highlight.js/#donate" target="_blank">https://github.com/owt5008137/WP-Code-Highlight.js/#donate</a> to donate me for a cup of coffee.', 'wp-code-highlight.js'); ?></td>
                </tr>
                <tr>
                    <td width="120px" align="center"><?php echo __('Thanks To', 'wp-code-highlight.js'); ?></td>
                    <td><ul>
                            <li><a href="http://softwaremaniacs.org/">Ivan Sagalaev</a> (for his <a href="http://highlightjs.org/">highlight.js</a>)</li>
                            <li><a href="http://kalnitsky.org">Igor Kalnitsky</a> (for his <a href="https://wordpress.org/plugins/wp-highlightjs/">wp-highlight.js</a> plugin)</li>
                    </ul></td>
                </tr>
                <tr>
                    <td width="120px" align="center"><?php echo __('Thanks To', 'wp-code-highlight.js'); ?></td>
                    <td><h3><?php echo __('Contributor List', 'wp-code-highlight.js'); ?>:</h3><ul>
                            <li><a href="http://geraint.co">Geraint Palmer</a></li>
                            <li><a href="http://www.codingserf.com">David</a></li>
                            <li><a href="http://www.shiyaluo.com">shiya</a></li>
                            <li><a href="https://github.com/Beej126">Beej126</a></li>
                            <li><a href="https://github.com/kylegundersen">kylegundersen</a></li>
                            <li><a href="http://iamakulov.com">Ivan Akulov</a></li>
                            <li><a href="https://github.com/jasonswan/">jasonswan</a></li>
                    </ul></td>
                </tr>
           </table>
    </div>
    <!-- /html code of settings page -->
<?php
}
