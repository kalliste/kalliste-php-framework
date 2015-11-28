{include file='header.tpl' scripts=$scripts title='Login'}
<!-- begin login_page.tpl -->

<div class="pagemessage" style="color: #aa0000;">{$smarty.request.pagemessage}&nbsp;</div>

   {appbox}
      <form method="post" action="?">
      <input type="hidden" name="action" value="login" />
       <table>
        <tr>
         <td><label>Username:</label></td>
         <td><input type="text" name="username" value="" /></td>
        </tr>
        <tr>
         <td><label>Password:</label></td>
         <td><input type="password" name="password" value="" /></td>
        </tr>
       </table>
        <br style="clear:both;" />
        <input type="submit" class="boxbutton" value="Login" />

      </form>
    {/appbox}


<!-- end login_page.tpl -->
{include file='footer.tpl'}
