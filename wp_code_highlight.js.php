<?php
/**
 * Plugin Name: WP Code Highlight.js
 * Plugin URI: https://github.com/owt5008137/WP-Code-Highlight.js 
 * Description: This is simple wordpress plugin for <a href="http://highlightjs.org/">highlight.js</a> library. Highlight.js highlights syntax in code examples on blogs, forums and in fact on any web pages. It&acute;s very easy to use because it works automatically: finds blocks of code, detects a language, highlights it.
 * Version: 0.1.0
 * Author: OWenT
 * Author URI: http://owent.net/
 * License: 3-clause BSD
*/


$PLUGIN_DIR =  plugins_url() . '/' . dirname(plugin_basename(__FILE__));


init_hljs_textdomain();  # initialize localization functions: _e(), __()

/**
 * Get version of Highlight.js 
 */
function hljs_get_lib_version() {
    return '8.0';
}

/**
 * list cdn list
 */
function hljs_cdn_list() {
    return array(
        'local' => array(
            'cdn' => 'local', 
            'css' => '', 
            'js' => ''
        ),
        'CdnJs' => array(
            'cdn' => '//cdnjs.cloudflare.com/ajax/libs/highlight.js/' . hljs_get_lib_version(), 
            'css' => '.min', 
            'js' => '.min'
        ), 
        'jsDelivr' => array(
            'cdn' => '//cdn.jsdelivr.net/highlight.js/8.0/highlight.min.js/' . hljs_get_lib_version(), 
            'css' => '', 
            'js' => '.min'
        ),
        'Yandex' => array(
            'cdn' => 'https://yandex.st/highlightjs/' . hljs_get_lib_version(), 
            'css' => '.min', 
            'js' => '.min'
        ),
        'BootCSS' => array(
            'cdn' => 'http://cdn.bootcss.com/highlight.js/' . hljs_get_lib_version(), 
            'css' => '.min', 
            'js' => '.min'
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
        'additional_css' => "pre.hljs {padding: 0px; overflow: scroll;}\npre.hljs code {border: 1px solid #ccc; padding: 5px;}"
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
 * Get option of highlight.js
 */
function hljs_get_lib_option($item) {
    $res = hljs_get_option('hljs_option');
    if (empty($res) || !isset($res[$item]))
        return '';
    return $res[$item];
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

    if ('local' == $hljs_cdn_info['cdn']) { ?>
    <script type="text/javascript" src="<?php echo ($PLUGIN_DIR . '/highlight.' . $hljs_package .'.pack.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo ($PLUGIN_DIR . '/styles/' . $hljs_code_option['theme'] . '.css'); ?>" />
<?php } else { ?>
    <script type="text/javascript" src="<?php echo ($hljs_cdn_info['cdn'] . '/highlight' . $hljs_cdn_info['js'] . '.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo ($hljs_cdn_info['cdn'] . '/styles/' . $hljs_code_option['theme'] . $hljs_cdn_info['css'] . '.css'); ?>" />
<?php } 

    $hljs_lib_configure = false;
    foreach ($hljs_code_option['hljs_option'] as $key => $val) {
        if (!empty($val)) {
            $hljs_lib_config[$key] = $val;
        }
    }
?>
    <script type="text/javascript">
<?php if (count($hljs_lib_config) > 0) { ?>
    hljs.configure(<?php echo json_encode($hljs_lib_config); ?>);
<?php } ?>
    hljs.initHighlightingOnLoad();
    </script>
    <style><?php echo $hljs_code_option['additional_css']; ?></style>
<?php
}
add_action('wp_head', 'hljs_include');


/**
 * Initialize Localization Functions
 */
function init_hljs_textdomain() {
    if (function_exists('load_plugin_textdomain')) {
        load_plugin_textdomain('wp-code-highlight.js', false, dirname(plugin_basename( __FILE__ )) . '/' . 'l10n');
    }
}

/**
 * Print CDN location addrs
 */
function hljs_get_location_list($current_location) {
    $cdn_list  = hljs_cdn_list();
    foreach($cdn_list as $key => $val) {
         ?><option value="<?php echo $key; ?>" <?php
        if($key == $current_location)
            echo ' selected="selected"';
        ?>><?php echo $key; ?></option><?php
    }
}

/**
 * Print package version
 */
function hljs_get_package_list($current_package) {
    $pkgs = array(
        'common' => 'Common(about 31KB)',
        'ex' => 'Ext.(about 54KB)',
        'all' => 'All(about 184KB)'
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
    $styleDir = '..' . '/' . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/' . 'styles'; # dirty hack

    if ($dir = opendir($styleDir))
    {
        while($file = readdir($dir))
        {
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
    closedir($dir);
}

/**
 * Add Settings Page to Admin Menu
 */
function hljs_admin_page() {
    if (function_exists('add_submenu_page'))
        add_options_page(__('WP Code Highlight.js Settings'), __('WP Code Highlight.js'), 'manage_options', 'wp-code-highlight.js', 'hljs_settings_page');
}
add_action('admin_menu', 'hljs_admin_page');


/**
 * Add Settings link to plugin page
 */
function hljs_add_settings_link($links, $file) {
    if ($file == plugin_basename(__FILE__)) {
      $links[] = '<a href="options-general.php?page=wp-code-highlight.js">' . __('Settings') . '</a>';
    }
    return $links;
}
add_filter('plugin_action_links', 'hljs_add_settings_link', 10, 2);


/**
 * Add BB-Tag for highlighting.
 *
 *   Usage: [CODE lang=C++]...[/CODE]
 */
function hljs_code_handler($atts, $content) {
    $language = $atts['lang'];
    return "<pre class=\"hljs\"><code class=\"$language\">" . ltrim($content, '\n') . '</code></pre>';
}
add_shortcode('code', 'hljs_code_handler');


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
                'useBR' => (isset($_POST['hljs_option_use_br']) && $_POST['hljs_option_use_br'] == 1)? true: false,
                'languages' => $_POST['hljs_option_languages']
            ),
            'additional_css' => $_POST['hljs_additional_css']
        );
        update_option('hljs_code_option', $upload_options);
        echo '<p class="info">' . __('All configurations successfully saved...', 'wp-code-highlight.js') . '</p>';
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

            table.hljs_copyright { font-size: 8px; margin-top: 50px;}
            table.hljs_copyright tr {margin-bottom: 10px;}
            table.hljs_copyright tr td {padding: 5px; font: 12px Sans-Serif; border: 1px solid #DDDDDD;}

        </style>

        <!-- combo box with location -->
        <p class="section">
          <label for="hljs_location"><?php echo __('CDN', 'wp-code-highlight.js'); ?></label><br/>
          <select name="hljs_location" id="hljs_location">
             <?php hljs_get_location_list(hljs_get_option('location')); ?>
          </select>
        </p>

        <!-- combo box with local source package -->
        <div class="section" id="hljs_local_package">
          <label for="hljs_package"><?php echo __('Package', 'wp-code-highlight.js'); ?></label><br/>
          <select name="hljs_package" id="hljs_package">
             <?php hljs_get_package_list(hljs_get_option('package')); ?>
          </select>
          <div>
            <h3>Support List:</h3>
            <h4>Common</h4>
            <div>Apache  Bash  C#  C++  CSS  CoffeeScript  Diff  HTML, XML  HTTP  Ini  JSON
Java  JavaScript  Makefile  Markdown  Nginx  Objective C  PHP  Perl  Python
Ruby  SQL</div>
            <h4>Ext.</h4>
            <div>Apache  Bash  C#  C++  CSS  CoffeeScript  Diff  HTML, XML  HTTP  Ini  JSON
Java  JavaScript  Makefile  Markdown  Nginx  Objective C  PHP  Perl  Python
Ruby  SQL
ActionScript  AppleScript CMake D  DOS.bat Erlang  F#  Go Lisp Lua Matlab
Python profile SCSS Tes VB.Net VBScript</div>
            <h4>All</h4>
            <div>Apache  Bash  C#  C++  CSS  CoffeeScript  Diff  HTML, XML  HTTP  Ini  JSON
Java  JavaScript  Makefile  Markdown  Nginx  Objective C  PHP  Perl  Python
Ruby  SQL
1C  AVR Assembler  ActionScript  AppleScript  AsciiDoc  AutoHotkey  Axapta
Brainfuck  CMake  Clojure  D  DOS .bat  Delphi  Django  Erlang  Erlang REPL
F#  FIX  GLSL  Go  Haml  Handlebars  Haskell  Lasso  Lisp  LiveCode server and
revIgniter  Lua  MEL  Mathematica  Matlab  Mizar  OCaml  Oracle Rules Language
Oxygene  Parser3  Python profile  R  RenderMan RIB  RenderMan RSL  Rust  SCSS
Scala  Scilab  Smalltalk  TeX  VB.NET  VBScript  VHDL  Vala</div>
        </div>
        </div>
        <script type="text/javascript">
        (function($, window){
            $(document).ready(function(){
                var show_package_fn = (function(){
                    if ($("#hljs_location").val() == "local") {
                        $("#hljs_local_package").show();
                    } else {
                        $("#hljs_local_package").hide();
                    }
                });

                show_package_fn();
                $("#hljs_location").click(function(){ show_package_fn(); });
            });
        })(jQuery, window);

        </script>

        <!-- combo box with styles -->
        <p class="section">
          <label for="hljs_theme"><?php echo __('Color Scheme:', 'wp-code-highlight.js'); ?></label><br/>

          <select name="hljs_theme" id="hljs_theme">
             <?php hljs_get_style_list(hljs_get_option('theme')); ?>
          </select>
        </p>

        <!-- text edit : tab replace -->
        <p class="section">
          <label for="hljs_option_tab_replace"><?php echo __('Highlight.js Option - Tab replace:', 'wp-code-highlight.js'); ?></label><br/>
          <input type="text" name="hljs_option_tab_replace" id="hljs_option_tab_replace" value="<?php echo hljs_get_lib_option('tabReplace'); ?>" /><br />

          <label for="hljs_option_class_prefix"><?php echo __('Highlight.js Option - Class prefix:', 'wp-code-highlight.js') ?></label><br/>
          <input type="text" name="hljs_option_class_prefix" id="hljs_option_class_prefix" value="<?php echo hljs_get_lib_option('classPrefix'); ?>" /><br />

          <label for="hljs_option_use_br"><?php echo __('Highlight.js Option - Use BR:', 'wp-code-highlight.js') ?></label><br/>
          <input type="checkbox" name="hljs_option_use_br" id="hljs_option_use_br" value="1" <?php if(hljs_get_lib_option('useBR')) echo ' checked="checked"'; ?>" /><br />

          <label for="hljs_option_languages"><?php echo __('Highlight.js Option - Languages:', 'wp-code-highlight.js'); ?></label><br/>
          <textarea type="text" name="hljs_option_languages" id="hljs_option_languages" value="<?php echo hljs_get_lib_option('languages'); ?>"><?php echo hljs_get_lib_option('languages'); ?></textarea><br />
       </p>

        <!-- text edit : additional css -->
        <p class="section">
          <label for="hljs_additional_css"><?php echo __('You can add some additional CSS rules for better display:', 'wp-code-highlight.js'); ?></label><br/>
          <textarea type="text" name="hljs_additional_css" id="hljs_additional_css"><?php echo hljs_get_option('additional_css'); ?></textarea>
        </p>

        <input type="hidden" name="cmd" value="hljs_save" />
        <input type="submit" name="submit" value="<?php echo __('Save', 'wp-code-highlight.js'); ?>" id="submit" />

      </form>

        <!-- copyright information -->
            <table border="0" class="hljs_copyright">
                <tr>
                    <td width="120px" align="center"><?php echo __('Author', 'wp-code-highlight.js'); ?></td>
                    <td><p><a href="http://owent.net"><?php echo __('OWenT', 'wp-code-highlight.js'); ?></a> &lt;<a href="mailto:owent@owent.net">owent@owent.net</a>&gt;</p></td>
                </tr>

                <tr>
                    <td width="120px" align="center"><?php echo __('Plugin Info', 'wp-code-highlight.js'); ?></td>
                    <td><p><?php echo __('This is a wordpress plugin for <a href="http://highlightjs.org/">highlight.js</a> library. <a href="http://highlightjs.org/">Highlight.js</a> highlights syntax in code examples on web pages. It&acute;s very easy to use because it works automatically: finds blocks of code, detects a language, highlights it.', 'wp-code-highlight.js'); ?></p></td>
                </tr>

                <tr>
                    <td width="120px" align="center"><?php echo __('Plugin Usage', 'wp-code-highlight.js'); ?></td>
                    <td><?php echo __('<p>For code highlighting you should use one of the following ways.</p>
                        
                        <p><strong>The first way</strong> is to use bb-codes:</p>
                        
                        <p><pre><code>[code] this language will be automatically determined [/code]</code></pre></p>
                        <p><pre><code>[code lang="cpp"] highlight the code with certain language [/code]</code></pre></p>
                        
                        <p><strong>The second way</strong> is to use html-tags:</p>
                        
                        <p><pre><code class="html">&lt;pre&gt;&lt;code&gt; this language will be automatically determined &lt;/code&gt;&lt;/pre&gt;</code></pre></p>
                        <p><pre><code class="html">&lt;pre&gt;&lt;code class="html"&gt; highlight the code with certain language &lt;/code&gt;&lt;/pre&gt;</code></pre></p>', 'wp-code-highlight.js'); ?></td>
                </tr>
                 <tr>
                    <td width="120px" align="center"><?php echo __('Thanks To', 'wp-code-highlight.js'); ?></td>
                    <td><p>
                        <a href="http://kalnitsky.org">kalnitsky</a> (for his <a href="https://wordpress.org/plugins/wp-highlightjs/">wp-highlight.js</a> plugin)
                    </p></td>
                </tr>

           </table>
    </div>

    <!-- /html code of settings page -->

<?php
}
