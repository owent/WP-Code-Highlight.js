=== WP Code Highlight.js ===
Donate link: http://owent.net/
Tags: source, code, highlight, sourcecode, highlighter, plugin, syntax, SyntaxHighlighter
Requires at least: 3.0
Tested up to: 4.1.0
Stable tag: 0.1.8

This is a wordpress plugin for highlight.js library.
And you can easily migrate from SynaxHighlight or Google Prettify without change
any code

== Description ==

This is a wordpress plugin for highlight.js library.
Highlight.js highlights syntax in code examples on any web pages. 
It's very easy to use because it works automatically: finds
blocks of code, detects a language, highlights it.

This plugin allow you load highlight.js from local storage or from CDN.
If you use local highlight.js, you can also choose only partly languages  
to make javascript file smaller(the smallest size is about 31KB).

On the other hand.This plugin allow you to switch syntax highlighter from
other libraries. For now, we support SyntaxHighlighter and Prettify. 

= Features: =
* works with comments
* high performance
* nice colorshemes
* load from cdn or local
* choose language packages
* SyntaxHighlighter compatible mode
* Prettify compatible mode

Author: [OWenT](http://owent.net/)

== Installation ==

1. Upload `WP-Code-Highlight.js` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Use `[code lang="some_lang"]some code[/code]` construction for highlighting
   or `[code]some code[/code]` for highlighting with language autodetection.
   You also can use `<pre><code>` tags instead `[code]` bb-tag.
4. Or enable SyntaxHighlighter compatible and Prettify compatible and then
   just 

== Changelog ==

= 0.1.8 =
1. tested under wordpress 4.1.0
2. update highlight.js to 8.4

= 0.1.7 =
1. commit files to publish cdn and update cdn conf

= 0.1.6 =
1. upgrade highlight.js to 8.2
2. add some cdn mirrors

= 0.1.5 =
1. tested under wordpress 4.0.0

= 0.1.4 =
1. fix $(document).ready() not actived in some mobile browser

= 0.1.3 =
1. add Baidu CDN
2. fix jsDelivr CDN address

= 0.1.2 =
1. add support for syntaxhighlighter
2. add support for prettify
3. fix some translation problems

= 0.1.0 =
create
