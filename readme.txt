=== WP Code Highlight.js ===
Donate link: https://github.com/owt5008137/WP-Code-Highlight.js/#donate
Tags: source, code, highlight, sourcecode, highlighter, plugin, syntax, SyntaxHighlighter, prettify, pandoc, stackedit, typora, source, markdown
Requires at least: 4.7.0
Tested up to: 5.2.2
Stable tag: 0.6.3

This plugin allow you to use [highlight.js](https://highlightjs.org) to highlight codes on any wordpress page.
And you can easily migrate from SynaxHighlight, Google Prettify or CrayonSyntaxHighlighter without change
any code

== Description ==

[WP-Code-Highlight.js](https://github.com/owt5008137/WP-Code-Highlight.js) is a wordpress plugin for [highlight.js](https://highlightjs.org) library.
[Highlight.js](https://highlightjs.org) highlights codes on any web pages very fastly.

This plugin allow you load highlight.js from local storage or from CDN.
If you use local highlight.js, you can also choose only some languages
to make javascript file smaller(the smallest size is about 40+KB).

You can also migrate from some other syntax highlighter easily. By now, we support SyntaxHighlighter, Prettify and CrayonSyntaxHighlighter.

= Features: =
* Works with comments
* High performance
* Nice colorshemes
* Load from cdn or local
* Custom language packages
* SyntaxHighlighter compatible mode
* Prettify compatible mode
* Crayon Syntax Highlighter compatible mode
* Work well with [pandoc](http://johnmacfarlane.net/pandoc/), [stackedit](https://stackedit.io/editor), [typora](https://typora.io) or other markdown editors

Author: [OWenT](http://owent.net/)
Open Source: [https://github.com/owt5008137/WP-Code-Highlight.js](https://github.com/owt5008137/WP-Code-Highlight.js)

== Installation ==

Install from wordpress

1. Open plugin installing page
2. Search  WP Code Highlight.js
3. Install it
4. Use &lt;pre&gt;&lt;code&gt; and &lt;/code&gt;&lt;/pre&gt; to wrapper your code.(see [highlightjs](https://github.com/isagalaev/highlight.js) for detail)
5. Enable SyntaxHighlighter compatible mode and Prettify compatible mode if you need it.

Install custom

1. Download release package
2. Unzip and rename folder name into wp-code-highlight.js
3. Move this folder into *[your wordpress path]/wp-content/plugins/* folder
4. Use &lt;pre&gt;&lt;code&gt; and &lt;/code&gt;&lt;/pre&gt; to wrapper your code.(see [highlightjs](https://github.com/isagalaev/highlight.js) for detail)
5. Enable SyntaxHighlighter compatible mode and Prettify compatible mode if you need it.

Just have fun.

== Screenshots ==

1. Highlight.js Demo
2. Setting1: Select CDN or local
3. Setting2: Custom Languages
4. Setting3: Highlighting styles
5. Setting4: Compatiable Mode
6. Sample: Simple
7. Sample: Syntax Highlighter Compatiable Mode

== Changelog ==

= 0.6.3
1. Fix CSRF vulnerability that can lead to blog-wide XSS [RP #27](https://github.com/owt5008137/WP-Code-Highlight.js/pull/27)
2. add highlight.js 9.15.8
3. remove useless CDNs

= 0.6.2
1. merge [PR #24](https://github.com/owt5008137/WP-Code-Highlight.js/pull/24), thanks to [Ivan Akulov](http://iamakulov.com)

= 0.6.1
1. fix missing languages for highlight.js 9.12.0

= 0.6.0
1. report warning when we has no writing access to the plugin directory
2. fix [issue #22](https://github.com/owt5008137/WP-Code-Highlight.js/issues/22). thanks to [Daredzik](https://github.com/Daredzik)
3. update highlight.js to 9.12.0

= 0.5.18
1. update highlight.js to 9.10.0

= 0.5.17
1. fix some software treat .js suffix as static file.(https://github.com/owt5008137/WP-Code-Highlight.js/pull/18)
2. fix language miss when in prettify compatible

= 0.5.16
1. update highlight.js version to 9.9.0
2. jsDelivr and MaxCDN use latest version (9.9.0) now

= 0.5.15
1. update highlight.js version to 9.7.0

= 0.5.14
1. update highlight.js version to 9.5.0

= 0.5.13
1. translate

= 0.5.12
1. tested in wordpress 4.5.2
2. update highlight.js version to 9.4.0
3. clear some old CDN

= 0.5.11
1. add bbcode option to enable or disable bbcode inside [code][/code]
2. update highlight.js version to 9.3.0

= 0.5.10
1. update highlight.js version to 9.2.0

= 0.5.9
1. update Baidu CDN highlight.js version to 9.1.0
3. merge [PR#15](https://github.com/owt5008137/WP-Code-Highlight.js/pull/15)


= 0.5.8
1. update highlight.js version to 9.1.0
2. update Baidu CDN highlight.js version to 9.0.0
3. update PR (9.1.0) is pushed to baiducdnstatic repository and wait for merge

= 0.5.7
1. fix auto rename bug([Issue #12](https://github.com/owt5008137/WP-Code-Highlight.js/issues/12)). thanks to [shiya](http://www.shiyaluo.com)

= 0.5.5 =
1. update highlight.js to 9.0.0
2. using default style file when highlight.js change the name rule

= 0.5.4 =
1. update baidu cdn to 8.9.1

= 0.5.3 =
1. update highlight.js to 8.9.1

= 0.5.2 =
1. update highlight.js to 8.8.0

= 0.5.1 =
1. fix bbcode bug([Issue #10](https://github.com/owt5008137/WP-Code-Highlight.js/issues/10))

= 0.5.0 =
1. update highlight 8.7
2. remove ext mode

= 0.5.0 =
1. update highlight 8.7
2. remove ext mode

= 0.4.2 =
1. style list is sorted.

= 0.4.1 =
1. add hljs css class when generate code label in compatiable mode
2. fix html symbol in readme

= 0.4.0 =
1. rebuild custom language packages only upgrade this plugin.

= 0.3.8 =
1. fix installation readme
2. optimize upgrade hook

= 0.3.7 =
1. update highlight.js in Baidu CDN into 8.6
2. hook upgrade action and auto update custom package when upgraded

= 0.3.6 =
1. update highlight.js into 8.6

= 0.3.5 =
1. fix baidu cdn urls
2. allow to load custom language scripts from CDN
3. add CDN reference url of highlight.js to readme

= 0.3.4 =
1. replace baidu cdn with url support https
2. update baidu cdn because it accept highlight.js 8.5 now

= 0.3.3 =
1. add [Crayon Syntax Highlighter Compatible] option
2. thanks to [David](https://github.com/David-CodingSerf)

= 0.3.2 =
1. tested under wp 4.2

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
