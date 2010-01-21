{include file='dojo.tpl' assign=scripts}
{include file='header.tpl' scripts=$scripts title="Redirect"}
<!-- begin manual_redirect.tpl -->

<div class="pagemessage" style="color: #aa0000;">{$smarty.request.pagemessage}&nbsp;</div>

{if $capture_redirects}
  <ul>
  {foreach from=$smarty.post key=k item=v}
    <li>{$k} =&gt; {$v}</li>
  {/foreach}
  </ul>
{/if}

<br />
<a href="{$url}">Continue on to {$url}</a>
<br />
<br />
<br />


<!-- end manual_redirect.tpl -->
{include file='footer.tpl'}
