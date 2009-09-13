<link rel="stylesheet" type="text/css" href="external/jtageditor/jojobbcode/jojobbcode-skin.css.php" />
<script type="text/javascript" src="external/jtageditor/jquery.jtageditor.js"></script>

<script type="text/javascript">
{literal}
$(document).ready(function(){
$("#body_code").jTagEditor({
		tagSet:"external/jtageditor/jojobbcode/jojobbcode-tags.js.php",
		tagMask:"\\[(.*?)\\]",
		insertOnShiftEnter:"",
		insertOnCtrlEnter:""
	});
});
{/literal}
</script>