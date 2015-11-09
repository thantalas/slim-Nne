
<?php
$this->loadJs(Config::read('fe.asset.js'));
$this->loadJs($this->Js);

echo $this->onDomready();
?>
</body>