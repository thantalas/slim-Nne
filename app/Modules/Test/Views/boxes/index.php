<?php
echo "test.index<br>";
$this->getElement('element',array('pippo'=>Config::read('provaconfig')));
$this->getElement('element/element');
$this->getElement('Test::element/element2');
$this->getElement('Test::element1');
