{include file='header.tpl' scripts=$scripts title="Redirect"}
<!-- begin manual_redirect.tpl -->

<div class="pagemessage" style="color: #aa0000;">{$smarty.request.pagemessage}&nbsp;</div>

{if $capture_redirects}
<pre>
  {$smarty.post|@var_export}
</pre>
{/if}

<br />
<a href="{$url}">Continue on to {$url}</a>
<br />
<br />
<br />


<!-- end manual_redirect.tpl -->
{include file='footer.tpl'}
