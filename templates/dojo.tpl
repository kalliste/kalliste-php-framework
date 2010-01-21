{literal}
<script type="text/javascript">
 var djConfig = {
  isDebug:false,
  parseOnLoad:true
 };
</script>
<script type="text/javascript" src="scripts/dojo/dojo/dojo.js"></script>
<script type="text/javascript">
  dojo.require("dijit.form.DateTextBox");
  dojo.require("dijit.form.TextBox");
  dojo.require("dijit.form.NumberSpinner");
  dojo.addOnLoad(function(){
    dojo.query(".pagemessage").fadeOut({ delay:2500 }).play();
  });
  function ajax_form(formName) {
   dojo.xhrGet({ form: formName, content: {ajax: "yes"} });
  }
</script>
<style type="text/css">
  @import "scripts/dojo/dijit/themes/tundra/tundra.css";
</style>
{/literal}
