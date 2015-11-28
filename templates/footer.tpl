<!-- footer.tpl -->

{foreach from=$debug_messages item=message}
 {if $message != ''}
  <div class="debug_message">{$message}</div>
 {/if}
{/foreach}

</div> <!-- /container -->
</body>
</html>
