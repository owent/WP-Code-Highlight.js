<?php
/**
 * Plugin Name: WP Code Highlight.js
 * Plugin URI: https://github.com/owt5008137/WP-Code-Highlight.js 
 * Description: This is simple wordpress plugin for <a href="http://highlightjs.org/">highlight.js</a> library. Highlight.js highlights syntax in code examples on blogs, forums and in fact on any web pages. It&acute;s very easy to use because it works automatically: finds blocks of code, detects a language, highlights it.
 * Version: 0.2.0
 * Author: OWenT
 * Author URI: http://owent.net/
 * License: 3-clause BSD
*/


$PLUGIN_DIR =  plugins_url() . '/' . dirname(plugin_basename(__FILE__));


/**
 * Get version of Highlight.js 
 */
function hljs_get_lib_version() {
    return '8.4';
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
            'js' => ''
        ),
        'CdnJs' => array(
            'cdn' => '//cdnjs.cloudflare.com/ajax/libs/highlight.js/' . hljs_get_lib_version(),
            'desc' => 'Public CDN: cdnjs',
            'css' => '.min', 
            'js' => '.min'
        ), 
        'jsDelivr' => array(
            'cdn' => '//cdn.jsdelivr.net/highlight.js/' . hljs_get_lib_version(), 
            'desc' => 'Public CDN: jsDelivr (highlightjs.org recommend)',
            'css' => '', 
            'js' => '.min'
        ),
        'Yandex' => array(
            'cdn' => '//yandex.st/highlightjs/' . hljs_get_lib_version(), 
            'desc' => 'Public CDN: Yandex(lastest version: 8.2)',
            'css' => '.min', 
            'js' => '.min'
        ), 
        'BootCSS' => array(
            //'cdn' => 'http://cdn.bootcss.com/highlight.js/' . hljs_get_lib_version(), 
            'cdn' => 'http://cdn.bootcss.com/highlight.js/8.3', 
            'desc' => 'Public CDN: BootCSS(http only, lastest version: 8.3)',
            'css' => '.min', 
            'js' => '.min'
        ),
        'Baidu' => array(
            //'cdn' => 'http://apps.bdimg.com/libs/highlight.js/' . hljs_get_lib_version(),
            'cdn' => 'http://apps.bdimg.com/libs/highlight.js/8.2',
            'desc' => 'Public CDN: Baidui(http only)',
            'css' => '.min', 
            'js' => '.min'
        ), 
        'Qihoo360' => array(
            'cdn' => 'http://libs.useso.com/js/highlight.js/8.0',// . hljs_get_lib_version(), 
            'desc' => 'Public CDN: QiHoo 360(http only, lastest version: 8.0)',
            'css' => '.min', 
            'js' => '.min'
        ), 
        'Qiniu' => array(
            'cdn' => 'http://cdn.staticfile.org/highlight.js/8.3',// . hljs_get_lib_version(), 
            'desc' => 'Public CDN: Qiniu(http only, lastest version: 8.3)',
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
        'additional_css' => "pre.hljs {padding: 5px;}\npre.hljs code {}",
        'syntaxhighlighter_compatible' => false,
        'prettify_compatible' => false,
        'shortcode' => false
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

    // inject jquery
    wp_enqueue_script('jquery');

    // inject js & css file
    if ('local' == $hljs_cdn_info['cdn']) { ?>
    <script type="text/javascript" src="<?php echo ($PLUGIN_DIR . '/highlight.' . $hljs_package .'.pack.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo ($PLUGIN_DIR . '/styles/' . $hljs_code_option['theme'] . '.css'); ?>" />
<?php } else { ?>
    <script type="text/javascript" src="<?php echo ($hljs_cdn_info['cdn'] . '/highlight' . $hljs_cdn_info['js'] . '.js'); ?>"></script>
    <link rel="stylesheet" href="<?php echo ($hljs_cdn_info['cdn'] . '/styles/' . $hljs_code_option['theme'] . $hljs_cdn_info['css'] . '.css'); ?>" />
<?php } 

    // inject init script
    $hljs_lib_configure = false;
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
                $(block).empty().append($("<code></code>").html(code_content)).addClass('language-' + reg_mat[1]);
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

            var code_content = jblock.html();
            jblock.replaceWith($("<pre></pre>").append($("<code></code>").html(code_content)));
            hljs.highlightBlock(jblock.get(0));
        });
<?php } ?>
           
        });

        $(document).ready(init_fn);
        $(window).on("load", init_fn);
    })(jQuery, window);
    </script>
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
        ?>><?php echo empty($val['desc'])? $key: $val['desc']; ?></option><?php
    }
}

/**
 * Print package version
 */
function hljs_get_package_list($current_package) {
    $pkgs = array(
        'common' => 'Common(about 35KB)',
        'ex' => 'Ext.(about 62KB)',
        'all' => 'All(about 240KB)'
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
 *   Usage: [CODE lang=cpp]...[/CODE]
 */
function hljs_code_handler($atts, $content) {
    $language = '';
    if (!empty($atts['lang']))
        $language = "class=\"${atts['lang']}\"";
    return "<pre class=\"hljs\"><code $language>" . ltrim($content, '\n') . '</code></pre>';
}
if (hljs_get_lib_option('shortcode')) {
    add_shortcode('code', 'hljs_code_handler');
}


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
            'shortcode' => (isset($_POST['hljs_enable_shortcode']) && $_POST['hljs_enable_shortcode'])? true: false
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
        <div class="section">
          <label for="hljs_location"><?php echo __('CDN', 'wp-code-highlight.js'); ?></label><br/>
          <select name="hljs_location" id="hljs_location">
             <?php hljs_get_location_list(hljs_get_option('location')); ?>
          </select>
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
              <h3>Support List:</h3>
              <div class="language_support_list" id="language_support_list_common">
                  <h4>Common</h4>
                  <div>Apache  Bash  C#  C++  CSS  CoffeeScript  Diff  HTML, XML  HTTP  Ini  JSON
        Java  JavaScript  Makefile  Markdown  Nginx  Objective C  PHP  Perl  Python
        Ruby  SQL</div>
              </div>
              <div class="language_support_list" id="language_support_list_ex">
                  <h4>Ext.</h4>
                  <div>Apache  Bash  C#  C++  CSS  CoffeeScript  Diff  HTML, XML  HTTP  Ini  JSON
        Java  JavaScript  Makefile  Markdown  Nginx  Objective C  PHP  Perl  Python
        Ruby  SQL
        ActionScript  AppleScript  Cap'n Proto  CMake  D  DOS.bat Erlang  F#  Go Lisp Lua Matlab
        Protocol Buffers Python profile Scala SCSS Swift Tex Typescript</div>
              </div>
              <div class="language_support_list" id="language_support_list_all">
                  <h4>All</h4>
                  <div>Apache  Bash  C#  C++  CSS  CoffeeScript  Diff  HTML, XML  HTTP  Ini  JSON
        Java  JavaScript  Makefile  Markdown  Nginx  Objective C  PHP  Perl  Python
        Ruby  SQL
        1C  AVR Assembler  ActionScript  AppleScript  AsciiDoc  AutoHotkey  Axapta
        Brainfuck  Cap'n Proto  CMake  Clojure  D  Dart  DOS .bat  Delphi  Django  Dust  Erlang  Erlang Elixir REPL
        F#  FIX  G-Code  Gherkin  GLSL  Go  Gradle  Groovy  Haml  Handlebars  Haskell  Haxe  Lasso  Lisp  LiveCode server and
        revIgniter  Lua  MEL  Mathematica  Matlab  Mizar  Monkey  Nimrod  Nix  NSIS  OCaml  Oracle Rules Language
        Oxygene  Parser3  Protocol Buffers  Python profile  Q  R  RenderMan RIB  RenderMan RSL  Rust  SCSS
        Scala  Scheme  Scilab  Smalltalk  Swift  TeX  Thrift  Typescript  VB.NET  VBScript  VHDL  Vim script  Vala  x86asm</div>
              </div>
          </div>
        </div>
        <script type="text/javascript">
        (function($, window){
            $(document).ready(function(){
                var show_package_language = (function(){
                    $(".language_support_list").hide();
                    $("#language_support_list_" + $("#hljs_package").val()).show();
               });

                var show_package_fn = (function(){
                    if ($("#hljs_location").val() != "local") {
                        $("#hljs_local_package").hide();
                        return;
                    }

                    $("#hljs_local_package").show();
                    show_package_language();
                });

                show_package_fn();
                $("#hljs_location").change(function(){ show_package_fn(); });
                $("#hljs_package").change(function(){ show_package_language(); });
            });
        })(jQuery, window);

        </script>

        <!-- combo box with styles -->
        <div class="section">
          <label for="hljs_theme"><?php echo __('Color Scheme:', 'wp-code-highlight.js'); ?></label><br/>

          <select name="hljs_theme" id="hljs_theme">
             <?php hljs_get_style_list(hljs_get_option('theme')); ?>
          </select>
          <div>You can get a quick look of all style and all language at <a href="https://highlightjs.org/static/test.html">https://highlightjs.org/static/test.html</a>
          </div>
          <div>
            Notice: some cdn support only highligh.js v8.0 and some style is unusable, see <a href="https://highlightjs.org/" target="_blank">https://highlightjs.org/</a> for detail
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
          <label for="hljs_syntaxhighlighter_compatible"><?php echo __('SyntaxHighlighter Compatiable:', 'wp-code-highlight.js') ?></label>
          <input type="checkbox" name="hljs_syntaxhighlighter_compatible" id="hljs_syntaxhighlighter_compatible" value="1" <?php if(hljs_get_option('syntaxhighlighter_compatible')) echo ' checked="checked"'; ?> />
          <label for="hljs_prettify_compatible"><?php echo __('Prettify Compatible:', 'wp-code-highlight.js') ?></label>
          <input type="checkbox" name="hljs_prettify_compatible" id="hljs_prettify_compatible" value="1" <?php if(hljs_get_option('prettify_compatible')) echo ' checked="checked"'; ?> /><br />
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
                        <a href="http://softwaremaniacs.org/">Ivan Sagalaev</a> (for his <a href="http://highlightjs.org/">highlight.js</a>)
                    </p></td>
                </tr>
                <tr>
                    <td width="120px" align="center"><?php echo __('Thanks To', 'wp-code-highlight.js'); ?></td>
                    <td><p>
                        <a href="http://kalnitsky.org">Igor Kalnitsky</a> (for his <a href="https://wordpress.org/plugins/wp-highlightjs/">wp-highlight.js</a> plugin)
                    </p></td>
                </tr>
           </table>
    </div>

    <!-- /html code of settings page -->

<?php
}
