[WP Code Highlight.js]	Project
====================


Project Information
------
#### Project Name: 
[**WP Code Highlight.js**][7]
#### Github Home: 
https://github.com/owt5008137/WP-Code-Highlight.js
#### Plugin Home:
http://wordpress.org/plugins/wp-code-highlightjs/
#### Description: 
This is a plugin of *[Wordpress][3]* using [**Highlight.js**][4] to make codes on posts, articles or any web pages more beautiful and easier to read.

This plugin allow you to load [Highlight.js][4] from local web server or some of public CDN we collected.

To make you easier to migrate from [SyntaxHilighter][5] and [Google Prettify][6] to [Highlight.js][4], this plugin will provide some compatible mode so that you need not to change any code on your old web pages. They will be converted automaticly.

At the same time, [**WP Code Highlight.js**][7] also allow you to set options of [Highlight.js][4] in the setting page of your wordpress. And we also provide some other useful options.For example, you can use only common language package 

#### Usage:
Install from wordpress 

1. Open plugin installing page
2. Search  WP Code Highlight.js
3. Install it

Install custom

1. Download release package
2. Unzip and rename folder name into wp-code-highlight.js
3. Move this folder into *[your wordpress path]/wp-content/plugins/* folder

Have fun.

About:
-------
#### Why [Highlight.js][4] ?
Recently, I determine to turn to use **Markdown** to write blog. But here is a problem, I use [SyntaxHighlighter][5] before and I'm  failed to find a tool to support [SyntaxHighlighter][5] and markdown very well. But with [Highlight.js][4] it's very easy.

There is a web markdown editor named **StackEdit** you can use to write markdown and publish to Github, Wordpress, Blog and etc. or export it to html. It allow you to write code like what you do in github but it's more powerful. Especially , it has [Highlight.js][4] and [Prettify][6] plugin to highlight codes. Or you can disable code highlight and it will use **&lt;pre&gt;&lt;code&gt;** to wrap codes. This also can be used by [Highlight.js][4].

If you would like to just use vim, emacs or other text editor. There is also a tool named [**Pandoc**][8] that you can use to convert markdown to many formats. with option --no-highlight, it will also use **&lt;pre&gt;&lt;code&gt;** to wrap codes.

#### Start to write a plugin
There is already a plugin named [wp-highlight.js][2] which can be used in [Wordpress][3]. But it load the full version of [Highlight.js][4]. It cost too much data traffic(*about 180+KB*). It will slow down loading time and I really don't need so many languages(especially some of them I have never heard of). I don't want to pay for it. So I need a plugin to load just the languages I need, or download [Highlight.js][4] from public CDN.

At the same time, I wrote many blogs before and using [SyntaxHighlighter][5] for years, I do not want to fix my codes that already puhlished. So I need a plugin to convert those code automaticly.

Then this plugin starts. It allow user only load common package of [Highlight.js][4] (*only 31KB*) or extended package(*about 54KB*). It also can analysis doms on web page, find codes in [SyntaxHighlighter][5] format or [Prettify][6] format, and then turn them into [Highlight.js][4] format, and finally , highlight them.

Thanks to
------
This plugin fork from [wp-highlight.js][2] and rewrote all the codes. So we must thanks to [wp-highlight.js][2]'s author [Igor Kalnitsky](http://kalnitsky.org).

Also thanks to [Highlight.js][4]'s author [Ivan Sagalaev](http://softwaremaniacs.org/)

And thanks to all  contributors and users. You make this plugin better.

FAQ
------
Any questions please mailto [owent@owent.net](mailto:owent@owent.net) or [owt5008137@live.com](mailto:owt5008137@live.com)

Report Problems: https://github.com/owt5008137/WP-Code-Highlight.js/issues


Notes
------

  [stackedit]: [StackEdit](https://stackedit.io/) is a full-featured, open-source Markdown editor based on PageDown, the Markdown library used by Stack Overflow and the other Stack Exchange sites.

  [highlight.js]: [Highlight.js][4]  is a syntax highlighter written in JavaScript. It works in the browser as well as on the server. It works with pretty much any markup, doesn't depend on any framework and has automatic language detection. 

  [wp-code-highlight.js]: [WP Code Highlight.js][7] is a syntax highlight plugin for [Wordpress][3], which using [highlight.js][4] to highlight codes.

  [pandoc]: [Pandoc][8] is a tool to  convert files from one markup format into another, it support more than ten format as input and even more format as output.

  [1]: http://wordpress.org/plugins/wp-code-highlightjs/
  [2]: http://wordpress.org/plugins/wp-highlightjs/
  [3]: http://wordpress.org
  [4]: http://highlightjs.org/
  [5]: http://alexgorbatchev.com/SyntaxHighlighter/
  [6]: https://code.google.com/p/google-code-prettify/
  [7]: https://github.com/owt5008137/WP-Code-Highlight.js
  [8]: http://johnmacfarlane.net/pandoc/
