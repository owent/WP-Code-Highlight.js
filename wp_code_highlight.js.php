<?php
/**
 * Plugin Name: WP Code Highlight.js
 * Plugin URI: https://github.com/owt5008137/WP-Code-Highlight.js
 * Description: This is simple wordpress plugin for <a href="http://highlightjs.org/">highlight.js</a> library. Highlight.js highlights syntax in code examples on blogs, forums and in fact on any web pages. It&acute;s very easy to use because it works automatically: finds blocks of code, detects a language, highlights it.
 * Version: 0.5.18
 * Author: OWenT
 * Author URI: https://owent.net/
 * License: 3-clause BSD
*/

$PLUGIN_DIR =  plugins_url() . '/' . dirname(plugin_basename(__FILE__));
/**
 * Get version of this plugins
 */
function hljs_get_version() {
    return '0.5.18';
}
/**
 * Get version of Highlight.js
 */
function hljs_get_lib_version() {
    return '9.10.0';
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
            'cdn' => '//cdn.jsdelivr.net/highlight.js/' . hljs_get_lib_version(),
            //'cdn' => '//cdn.jsdelivr.net/highlight.js/9.6.0',
            'desc' => __('Public CDN', 'wp-code-highlight.js') . ': jsDelivr (' . __('highlightjs.org recommend', 'wp-code-highlight.js') . ') ' . __('lastest version', 'wp-code-highlight.js') . ': 9.6.0)',
            'css' => '.min',
            'js' => '.min',
            'readme' => 'http://www.jsdelivr.com/#!highlight.js'
        ),
        'MaxCDN' => array(
            'cdn' => '//oss.maxcdn.com/highlight.js/' . hljs_get_lib_version(),
            //'cdn' => '//oss.maxcdn.com/highlight.js/9.6.0',
            'desc' => __('Public CDN', 'wp-code-highlight.js') . ': MaxCDN ' . __('lastest version', 'wp-code-highlight.js') . ': 9.6.0)',
            'css' => '.min',
            'js' => '.min',
            'readme' => 'http://osscdn.com/#/highlight.js'
        ),
        'Baidu' => array(
            //'cdn' => 'http://apps.bdimg.com/libs/highlight.js/' . hljs_get_lib_version(),
            //'cdn' => '//openapi.baidu.com/libs/highlight.js/' . hljs_get_lib_version(),
            'cdn' => '//openapi.baidu.com/libs/highlight.js/9.1.0',
            'desc' => __('Public CDN', 'wp-code-highlight.js') . ': ' . __('Baidu', 'wp-code-highlight.js') . __('lastest version', 'wp-code-highlight.js') . ': 9.1.0)',
            'css' => '.min',
            'js' => '.min',
            'readme' => 'http://cdn.code.baidu.com/#highlight.js'
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
        'additional_css' => "pre.hljs {padding: 5px;}\npre.hljs code {}",
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
        return "<pre class=\"hljs\"><code $language>" . do_shortcode(ltrim($content, '\n')) . '</code></pre>';
    } else {
        return "<pre class=\"hljs\"><code $language>" . ltrim($content, '\n') . '</code></pre>';
    }
}
if (hljs_get_option('shortcode')) {
    add_shortcode('code', 'hljs_code_handler');
}
function hljs_generate_custom_pack() {
    // generate custom language pack
    $opt_loc = hljs_get_option('location');
    $opt_packs = hljs_get_option('package');
    $opt_langs = hljs_get_option('custom_lang');
    $plugin_root_dir = plugin_dir_path( __FILE__ );
    if ('local' == $opt_loc && 'custom' == $opt_packs) {
        $custom_pack_file = $plugin_root_dir . DIRECTORY_SEPARATOR . 'highlight.custom.pack.js';
        file_put_contents($custom_pack_file, '');
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
/**
 * Highlight.js Settings Page
 */
function hljs_settings_page() {
    global $PLUGIN_DIR;
    if (isset( $_POST['cmd'] ) && $_POST['cmd'] == 'hljs_save')
    {
        $upload_options = array(
            'location' => $_POST['hljs_location'],
            'package' => $_POST['hljs_package'],
            'theme' => $_POST['hljs_theme'],
            'hljs_option' => array(
                'tabReplace' => $_POST['hljs_option_tab_replace'],
                'classPrefix' => $_POST['hljs_option_class_prefix'],
                'useBR' => (isset($_POST['hljs_option_use_br']) && $_POST['hljs_option_use_br'])? true: false,
                'languages' => $_POST['hljs_option_languages']
            ),
            'additional_css' => $_POST['hljs_additional_css'],
            'syntaxhighlighter_compatible' => (isset($_POST['hljs_syntaxhighlighter_compatible']) && $_POST['hljs_syntaxhighlighter_compatible'])? true: false,
            'prettify_compatible' => (isset($_POST['hljs_prettify_compatible']) && $_POST['hljs_prettify_compatible'])? true: false,
            'crayonsyntaxhighlighter_compatible' => (isset($_POST['hljs_crayonsyntaxhighlighter_compatible']) && $_POST['hljs_crayonsyntaxhighlighter_compatible'])? true: false,
            'shortcode' => (isset($_POST['hljs_enable_shortcode']) && $_POST['hljs_enable_shortcode'])? true: false,
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
        echo '<p class="info">' . __('All configurations successfully saved...', 'wp-code-highlight.js') . '</p>';
        hljs_generate_custom_pack();
    }
    ?>
    <!-- html code of settings page -->
    <div class="wrap">
      <form id="hljs" method="post" action="<?php echo $_SERVER['REQUEST_URI'];?>">
        <script type="text/javascript" src="<?php echo ($PLUGIN_DIR . '/highlight.common.pack.js'); ?>"></script>
        <script type="text/javascript">hljs.initHighlightingOnLoad();</script>
        <link rel="stylesheet" href="<?php echo ($PLUGIN_DIR . '/styles/default.css'); ?>" />
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
                    <li><label><input name="apache.js" checked="" type="checkbox"> Apache</label>
                    </li><li><label><input name="bash.js" checked="" type="checkbox"> Bash</label>
                    </li><li><label><input name="cs.js" checked="" type="checkbox"> C#</label>
                    </li><li><label><input name="cpp.js" checked="" type="checkbox"> C++</label>
                    </li><li><label><input name="css.js" checked="" type="checkbox"> CSS</label>
                    </li><li><label><input name="coffeescript.js" checked="" type="checkbox"> CoffeeScript</label>
                    </li><li><label><input name="diff.js" checked="" type="checkbox"> Diff</label>
                    </li><li><label><input name="xml.js" checked="" type="checkbox"> HTML, XML</label>
                    </li><li><label><input name="http.js" checked="" type="checkbox"> HTTP</label>
                    </li><li><label><input name="ini.js" checked="" type="checkbox"> Ini</label>
                    </li><li><label><input name="json.js" checked="" type="checkbox"> JSON</label>
                    </li><li><label><input name="java.js" checked="" type="checkbox"> Java</label>
                    </li><li><label><input name="javascript.js" checked="" type="checkbox"> JavaScript</label>
                    </li><li><label><input name="makefile.js" checked="" type="checkbox"> Makefile</label>
                    </li><li><label><input name="markdown.js" checked="" type="checkbox"> Markdown</label>
                    </li><li><label><input name="nginx.js" checked="" type="checkbox"> Nginx</label>
                    </li><li><label><input name="objectivec.js" checked="" type="checkbox"> Objective-C</label>
                    </li><li><label><input name="php.js" checked="" type="checkbox"> PHP</label>
                    </li><li><label><input name="perl.js" checked="" type="checkbox"> Perl</label>
                    </li><li><label><input name="python.js" checked="" type="checkbox"> Python</label>
                    </li><li><label><input name="ruby.js" checked="" type="checkbox"> Ruby</label>
                    </li><li><label><input name="sql.js" checked="" type="checkbox"> SQL</label>
                    </li>
                </ul>
                <p><b>Other</b></p>
                <ul id="language_support_list_other">
                    <!-- copy from view-source:https://highlightjs.org/download/ -->
                    <li><label><input name="1c.js" type="checkbox"> 1C</label>
                    </li><li><label><input name="armasm.js" type="checkbox"> ARM Assembly</label>
                    </li><li><label><input name="avrasm.js" type="checkbox"> AVR Assembler</label>
                    </li><li><label><input name="accesslog.js" type="checkbox"> Access log</label>
                    </li><li><label><input name="actionscript.js" type="checkbox"> ActionScript</label>
                    </li><li><label><input name="ada.js" type="checkbox"> Ada</label>
                    </li><li><label><input name="applescript.js" type="checkbox"> AppleScript</label>
                    </li><li><label><input name="arduino.js" type="checkbox"> Arduino</label>
                    </li><li><label><input name="asciidoc.js" type="checkbox"> AsciiDoc</label>
                    </li><li><label><input name="aspectj.js" type="checkbox"> AspectJ</label>
                    </li><li><label><input name="abnf.js" type="checkbox"> Augmented Backus-Naur Form</label>
                    </li><li><label><input name="autohotkey.js" type="checkbox"> AutoHotkey</label>
                    </li><li><label><input name="autoit.js" type="checkbox"> AutoIt</label>
                    </li><li><label><input name="awk.js" type="checkbox"> Awk</label>
                    </li><li><label><input name="axapta.js" type="checkbox"> Axapta</label>
                    </li><li><label><input name="bnf.js" type="checkbox"> Backus–Naur Form</label>
                    </li><li><label><input name="basic.js" type="checkbox"> Basic</label>
                    </li><li><label><input name="brainfuck.js" type="checkbox"> Brainfuck</label>
                    </li><li><label><input name="cal.js" type="checkbox"> C/AL</label>
                    </li><li><label><input name="cmake.js" type="checkbox"> CMake</label>
                    </li><li><label><input name="csp.js" type="checkbox"> CSP</label>
                    </li><li><label><input name="cos.js" type="checkbox"> Caché Object Script</label>
                    </li><li><label><input name="capnproto.js" type="checkbox"> Cap’n Proto</label>
                    </li><li><label><input name="ceylon.js" type="checkbox"> Ceylon</label>
                    </li><li><label><input name="clean.js" type="checkbox"> Clean</label>
                    </li><li><label><input name="clojure.js" type="checkbox"> Clojure</label>
                    </li><li><label><input name="clojure-repl.js" type="checkbox"> Clojure REPL</label>
                    </li><li><label><input name="coq.js" type="checkbox"> Coq</label>
                    </li><li><label><input name="crystal.js" type="checkbox"> Crystal</label>
                    </li><li><label><input name="d.js" type="checkbox"> D</label>
                    </li><li><label><input name="dns.js" type="checkbox"> DNS Zone file</label>
                    </li><li><label><input name="dos.js" type="checkbox"> DOS .bat</label>
                    </li><li><label><input name="dart.js" type="checkbox"> Dart</label>
                    </li><li><label><input name="delphi.js" type="checkbox"> Delphi</label>
                    </li><li><label><input name="dts.js" type="checkbox"> Device Tree</label>
                    </li><li><label><input name="django.js" type="checkbox"> Django</label>
                    </li><li><label><input name="dockerfile.js" type="checkbox"> Dockerfile</label>
                    </li><li><label><input name="dust.js" type="checkbox"> Dust</label>
                    </li><li><label><input name="erb.js" type="checkbox"> ERB (Embedded Ruby)</label>
                    </li><li><label><input name="elixir.js" type="checkbox"> Elixir</label>
                    </li><li><label><input name="elm.js" type="checkbox"> Elm</label>
                    </li><li><label><input name="erlang.js" type="checkbox"> Erlang</label>
                    </li><li><label><input name="erlang-repl.js" type="checkbox"> Erlang REPL</label>
                    </li><li><label><input name="excel.js" type="checkbox"> Excel</label>
                    </li><li><label><input name="ebnf.js" type="checkbox"> Extended Backus-Naur Form</label>
                    </li><li><label><input name="fsharp.js" type="checkbox"> F#</label>
                    </li><li><label><input name="fix.js" type="checkbox"> FIX</label>
                    </li><li><label><input name="flix.js" type="checkbox"> Flix</label>
                    </li><li><label><input name="fortran.js" type="checkbox"> Fortran</label>
                    </li><li><label><input name="gcode.js" type="checkbox"> G-code (ISO 6983)</label>
                    </li><li><label><input name="gams.js" type="checkbox"> GAMS</label>
                    </li><li><label><input name="gauss.js" type="checkbox"> GAUSS</label>
                    </li><li><label><input name="glsl.js" type="checkbox"> GLSL</label>
                    </li><li><label><input name="gherkin.js" type="checkbox"> Gherkin</label>
                    </li><li><label><input name="go.js" type="checkbox"> Go</label>
                    </li><li><label><input name="golo.js" type="checkbox"> Golo</label>
                    </li><li><label><input name="gradle.js" type="checkbox"> Gradle</label>
                    </li><li><label><input name="groovy.js" type="checkbox"> Groovy</label>
                    </li><li><label><input name="hsp.js" type="checkbox"> HSP</label>
                    </li><li><label><input name="htmlbars.js" type="checkbox"> HTMLBars</label>
                    </li><li><label><input name="haml.js" type="checkbox"> Haml</label>
                    </li><li><label><input name="handlebars.js" type="checkbox"> Handlebars</label>
                    </li><li><label><input name="haskell.js" type="checkbox"> Haskell</label>
                    </li><li><label><input name="haxe.js" type="checkbox"> Haxe</label>
                    </li><li><label><input name="irpf90.js" type="checkbox"> IRPF90</label>
                    </li><li><label><input name="inform7.js" type="checkbox"> Inform 7</label>
                    </li><li><label><input name="x86asm.js" type="checkbox"> Intel x86 Assembly</label>
                    </li><li><label><input name="julia.js" type="checkbox"> Julia</label>
                    </li><li><label><input name="kotlin.js" type="checkbox"> Kotlin</label>
                    </li><li><label><input name="ldif.js" type="checkbox"> LDIF</label>
                    </li><li><label><input name="llvm.js" type="checkbox"> LLVM IR</label>
                    </li><li><label><input name="lasso.js" type="checkbox"> Lasso</label>
                    </li><li><label><input name="less.js" type="checkbox"> Less</label>
                    </li><li><label><input name="lsl.js" type="checkbox"> Linden Scripting Language</label>
                    </li><li><label><input name="lisp.js" type="checkbox"> Lisp</label>
                    </li><li><label><input name="livecodeserver.js" type="checkbox"> LiveCode</label>
                    </li><li><label><input name="livescript.js" type="checkbox"> LiveScript</label>
                    </li><li><label><input name="lua.js" type="checkbox"> Lua</label>
                    </li><li><label><input name="mel.js" type="checkbox"> MEL</label>
                    </li><li><label><input name="mipsasm.js" type="checkbox"> MIPS Assembly</label>
                    </li><li><label><input name="mathematica.js" type="checkbox"> Mathematica</label>
                    </li><li><label><input name="matlab.js" type="checkbox"> Matlab</label>
                    </li><li><label><input name="maxima.js" type="checkbox"> Maxima</label>
                    </li><li><label><input name="mercury.js" type="checkbox"> Mercury</label>
                    </li><li><label><input name="mizar.js" type="checkbox"> Mizar</label>
                    </li><li><label><input name="mojolicious.js" type="checkbox"> Mojolicious</label>
                    </li><li><label><input name="monkey.js" type="checkbox"> Monkey</label>
                    </li><li><label><input name="moonscript.js" type="checkbox"> MoonScript</label>
                    </li><li><label><input name="nsis.js" type="checkbox"> NSIS</label>
                    </li><li><label><input name="nimrod.js" type="checkbox"> Nimrod</label>
                    </li><li><label><input name="nix.js" type="checkbox"> Nix</label>
                    </li><li><label><input name="ocaml.js" type="checkbox"> OCaml</label>
                    </li><li><label><input name="openscad.js" type="checkbox"> OpenSCAD</label>
                    </li><li><label><input name="ruleslanguage.js" type="checkbox"> Oracle Rules Language</label>
                    </li><li><label><input name="oxygene.js" type="checkbox"> Oxygene</label>
                    </li><li><label><input name="parser3.js" type="checkbox"> Parser3</label>
                    </li><li><label><input name="pony.js" type="checkbox"> Pony</label>
                    </li><li><label><input name="powershell.js" type="checkbox"> PowerShell</label>
                    </li><li><label><input name="processing.js" type="checkbox"> Processing</label>
                    </li><li><label><input name="prolog.js" type="checkbox"> Prolog</label>
                    </li><li><label><input name="protobuf.js" type="checkbox"> Protocol Buffers</label>
                    </li><li><label><input name="puppet.js" type="checkbox"> Puppet</label>
                    </li><li><label><input name="purebasic.js" type="checkbox"> PureBASIC</label>
                    </li><li><label><input name="profile.js" type="checkbox"> Python profile</label>
                    </li><li><label><input name="q.js" type="checkbox"> Q</label>
                    </li><li><label><input name="qml.js" type="checkbox"> QML</label>
                    </li><li><label><input name="r.js" type="checkbox"> R</label>
                    </li><li><label><input name="rib.js" type="checkbox"> RenderMan RIB</label>
                    </li><li><label><input name="rsl.js" type="checkbox"> RenderMan RSL</label>
                    </li><li><label><input name="roboconf.js" type="checkbox"> Roboconf</label>
                    </li><li><label><input name="rust.js" type="checkbox"> Rust</label>
                    </li><li><label><input name="scss.js" type="checkbox"> SCSS</label>
                    </li><li><label><input name="sml.js" type="checkbox"> SML</label>
                    </li><li><label><input name="sqf.js" type="checkbox"> SQF</label>
                    </li><li><label><input name="step21.js" type="checkbox"> STEP Part 21</label>
                    </li><li><label><input name="scala.js" type="checkbox"> Scala</label>
                    </li><li><label><input name="scheme.js" type="checkbox"> Scheme</label>
                    </li><li><label><input name="scilab.js" type="checkbox"> Scilab</label>
                    </li><li><label><input name="smali.js" type="checkbox"> Smali</label>
                    </li><li><label><input name="smalltalk.js" type="checkbox"> Smalltalk</label>
                    </li><li><label><input name="stan.js" type="checkbox"> Stan</label>
                    </li><li><label><input name="stata.js" type="checkbox"> Stata</label>
                    </li><li><label><input name="stylus.js" type="checkbox"> Stylus</label>
                    </li><li><label><input name="subunit.js" type="checkbox"> SubUnit</label>
                    </li><li><label><input name="swift.js" type="checkbox"> Swift</label>
                    </li><li><label><input name="tp.js" type="checkbox"> TP</label>
                    </li><li><label><input name="taggerscript.js" type="checkbox"> Tagger Script</label>
                    </li><li><label><input name="tcl.js" type="checkbox"> Tcl</label>
                    </li><li><label><input name="tex.js" type="checkbox"> TeX</label>
                    </li><li><label><input name="tap.js" type="checkbox"> Test Anything Protocol</label>
                    </li><li><label><input name="thrift.js" type="checkbox"> Thrift</label>
                    </li><li><label><input name="twig.js" type="checkbox"> Twig</label>
                    </li><li><label><input name="typescript.js" type="checkbox"> TypeScript</label>
                    </li><li><label><input name="vbnet.js" type="checkbox"> VB.NET</label>
                    </li><li><label><input name="vbscript.js" type="checkbox"> VBScript</label>
                    </li><li><label><input name="vbscript-html.js" type="checkbox"> VBScript in HTML</label>
                    </li><li><label><input name="vhdl.js" type="checkbox"> VHDL</label>
                    </li><li><label><input name="vala.js" type="checkbox"> Vala</label>
                    </li><li><label><input name="verilog.js" type="checkbox"> Verilog</label>
                    </li><li><label><input name="vim.js" type="checkbox"> Vim Script</label>
                    </li><li><label><input name="xl.js" type="checkbox"> XL</label>
                    </li><li><label><input name="xquery.js" type="checkbox"> XQuery</label>
                    </li><li><label><input name="yaml.js" type="checkbox"> YAML</label>
                    </li><li><label><input name="zephir.js" type="checkbox"> Zephir</label>
                    </li><li><label><input name="crmsh.js" type="checkbox"> crmsh</label>
                    </li><li><label><input name="dsconfig.js" type="checkbox"> dsconfig</label>
                    </li><li><label><input name="pf.js" type="checkbox"> pf</label>
                    </li>
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
                    <td><?php echo __('<p>For code highlighting you should use one of the following ways.</p>
                            <p><strong>The first way</strong> is to use bb-codes:</p>
                            <p><pre><code>[code] this language will be automatically determined [/code]</code></pre></p>
                        <p><pre><code>[code lang="cpp"] highlight the code with certain language [/code]</code></pre></p>
                            <p><strong>The second way</strong> is to use html-tags:</p>
                            <p><pre><code class="html">&lt;pre&gt;&lt;code&gt; this language will be automatically determined &lt;/code&gt;&lt;/pre&gt;</code></pre></p>
                            <p><pre><code class="html">&lt;pre&gt;&lt;code bbcode=enable&gt; this language will be [b]automatically determined[\b] and inner bbcode is available &lt;/code&gt;&lt;/pre&gt;</code></pre></p>
                        <p><pre><code class="html">&lt;pre&gt;&lt;code class="html"&gt; highlight the code with certain language &lt;/code&gt;&lt;/pre&gt;</code></pre></p>', 'wp-code-highlight.js'); ?></td>
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
                    </ul></td>
                </tr>
           </table>
    </div>
    <!-- /html code of settings page -->
<?php
}
