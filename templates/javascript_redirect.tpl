{include file='dojo.tpl' assign=scripts}
{include file='header.tpl' scripts=$scripts title="Redirect"}
<!-- begin javascript_redirect.tpl -->

<div class="pagemessage" style="color: #aa0000;">{$smarty.request.pagemessage}&nbsp;</div>

{literal}
<script>
  dojo.addOnLoad(function(){
    setTimeout('redirect_after_delay()', 5000);
  });
  function redirect_after_delay() {
    window.location = "{/literal}{$url}{literal}";
  }
</script>
{/literal}

<!-- end javascript_redirect.tpl -->
{include file='footer.tpl'}
