
<?php
$this->loadJs(array(
	'admin/bootstrap.min.js"',
	'admin/plugins/metismenu.min.js',
	'admin/plugins/pace.min.js',
	'admin/plugins/gritter.min.js',
	'admin/plugins/bootstrap-tabdrop.min.js',
	'admin/plugins/bootstrap-switch.min.js',
	'admin/plugins/bootstrap-spinbox.min.js',
	'admin/plugins/bootstrap-confirmation.min.js',
	'admin/plugins/bootstrap-editable.min.js',
	'admin/plugins/bootstrap-fileinput.min.js',
	'admin/plugins/icheck.min.js',
	'admin/plugins/bootstrap-datepicker.min.js',
	'admin/plugins/bootstrap-select2.min.js',
	'admin/plugins/typeahead.min.js',
	'admin/plugins/jquery-nestable.min.js',
	'admin/th.js',
));
$this->loadJs($this->Js);

echo $this->onDomready();
?>
</body>