=== WP Code Highlight.js ===
Donate link: http://owent.net/
Tags: source, code, highlight, sourcecode, highlighter, plugin, syntax, SyntaxHighlighter
Requires at least: 3.0
Tested up to: 4.1.1
Stable tag: 0.3.1

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
* Work well with [pandoc](http://johnmacfarlane.net/pandoc/) and [stackeditor](https://stackedit.io/editor) and other similar markdown editors

Author: [OWenT](http://owent.net/)

== Installation ==

Install from wordpress 

1. Open plugin installing page
2. Search  WP Code Highlight.js
3. Install it
4. Use `<pre><code>` and `</code></pre>` to wrapper your code.(see [highlightjs](https://github.com/isagalaev/highlight.js) for detail)
5. Enable SyntaxHighlighter compatible mode and Prettify compatible mode if you need it.

Install custom

1. Download release package
2. Unzip and rename folder name into wp-code-highlight.js
3. Move this folder into *[your wordpress path]/wp-content/plugins/* folder
4. Use `<pre><code>` and `</code></pre>` to wrapper your code.(see [highlightjs](https://github.com/isagalaev/highlight.js) for detail)
5. Enable SyntaxHighlighter compatible mode and Prettify compatible mode if you need it.

Just have fun.

== Changelog ==

= 0.3.1 =
1. fix inject script order problem

= 0.3.0 =
1. turn wp-highlight.js's codes into footer
2. update highlight.js to 8.5
3. you can select which languages to include when not using CDN

= 0.2.3 =
1. merge [Request 5](https://github.com/owt5008137/WP-Code-Highlight.js/pull/5)

= 0.2.2 =
1. fix bug in no default plugin directory, merge [Request 4](https://github.com/owt5008137/WP-Code-Highlight.js/pull/4) [Issues 3](https://github.com/owt5008137/WP-Code-Highlight.js/issues/3)
2. add maxcdn
3. set jdfilvr .min css files

= 0.2.1 =
1. open ver 8.4 in baidu cdn,(lib is merged [Request 48](https://github.com/Clouda-team/baiducdnstatic/pull/48)

= 0.2.0 =
1. close BBCode of [code] for default([BUG #1](https://github.com/owt5008137/WP-Code-Highlight.js/issues/1))
2. update highlight.js to 8.4

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
