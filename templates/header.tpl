<!DOCTYPE html 
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{$title|default:"Project"}</title>
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.0r4/build/reset/reset-min.css" />
<link rel="stylesheet" type="text/css" href="http://yui.yahooapis.com/2.8.0r4/build/fonts/fonts-min.css" />
<link rel="stylesheet" href="styles/standard.css" />
<!--[if IE 6]>
  <link rel="stylesheet" href="styles/ie6.css">
<![endif]-->
<!--[if IE 7]>
  <link rel="stylesheet" href="styles/ie7.css">
<![endif]-->
<!--[if IE 8]>
  <link rel="stylesheet" href="styles/ie8.css">
<![endif]-->
{$scripts|smarty:nodefaults}
{$styles|smarty:nodefaults}
</head>
<body class="{$bodyclass|default:"tundra"}">

{if $smarty.request.action != 'login_page'}
 <div style="float:right; padding: 3px;">
  <a href="?action=logout">Sign out</a>
 </div>
{/if}

<br />

<!-- header.tpl -->
