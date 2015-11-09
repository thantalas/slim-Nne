<?php
/**
 * Admin menu manager
 *
 * Nne  : Ninety Nine Enemies Project (http://thnet.komunikando.org)
 *
 * Copyright (c) Ninety Nine Enemies Project, (http://thnet.komunikando.org)
 * Licensed under The MIT License
 * For license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 * 
 * Based on:
 * SlimStarter, https://github.com/xsanisty/SlimStarter
 * slim-facades, https://github.com/itsgoingd/slim-facades
 * 
 * @copyright	Copyright (c) Ninety Nine Enemies, (http://thnet.komunikando.org)
 * @link		http://thnet.komunikando.org Ninety Nine Enemies Project
 * @package		Nne\Helpers
 * @since		Nne (tm) v 1
 * @license		http://www.opensource.org/licenses/mit-license.php MIT License
 * @project		Ninety Nine Enemies Project
 * @encoding	utf-8
 * @author		Giorgio Tonelli <th.thantalas@gmail.com>, <http://thnet.komunikando.org>
 * @creation	08/nov/2015
 */
namespace Nne\Core\Menu;

use \Illuminate\Support\Collection;

class MenuItem{
    protected $hasChildren;
    protected $children;
    protected $hasParent;
    protected $parent;
    protected $options;
    protected $attributes;
    protected $linkAttributes;
    protected $prependedString;
    protected $appendedString;
    protected $name;
    protected $active;

    public function __construct($name, $options){
        $this->options = array(
            'label' => isset($options['label']) ? $options['label'] : '',
            'url'   => isset($options['url']) ? $options['url'] : '',
            'icon'  => isset($options['icon']) ? $options['icon'] : '',
        );

        $this->hasChildren      = false;
        $this->hasParent        = false;
        $this->active           = false;
        $this->children         = new MenuCollection();
        $this->attributes       = array();
        $this->linkAttributes   = array();
        $this->name             = $name;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setLinkAttribute($key, $value){
        $this->linkAttributes[$key] = $value;
    }

    public function getLinkAttribute($key = null){
        if(is_null($key)){
            return $this->linkAttributes;
        }

        return isset($this->linkAttributes[$key]) ? $this->linkAttributes[$key] : null;
    }

    public function getLinkStringAttribute(){
        $string = '';
        foreach ($this->linkAttributes as $key => $value) {
            $string.= "$key=\"$value\" ";
        }

        return $string;
    }

    public function setAttribute($key, $value){
        $this->attributes[$key] = $value;
    }

    public function getAttribute($key = null){
        if(is_null($key)){
            return $this->attributes;
        }

        return isset($this->attributes[$key]) ? $this->attributes[$key] : null;
    }

    public function getStringAttribute(){
        $string = '';
        foreach ($this->attributes as $key => $value) {
            $string.= "$key=\"$value\" ";
        }

        return $string;
    }

    public function getLabel(){
        return isset($this->options['label']) ? $this->options['label'] : null;
    }

    public function getUrl(){
        return isset($this->options['url']) ? $this->options['url'] : null;
    }

    public function getIcon(){
        return isset($this->options['icon']) ? $this->options['icon'] : null;
    }

    public function hasChildren(){
        return $this->hasChildren;
    }

    public function addChildren(\Nne\Core\Menu\MenuItem $menu){
        $this->hasChildren = true;
        $this->children->push($menu);
        $menu->setParent($this);
    }

    public function getChildren(){
        return $this->children;
    }

    public function appendString($string){
        $this->appendedString = $string;
    }

    public function getAppendedString(){
        return $this->appendedString;
    }

    public function prependString($string){
        $this->prependedString = $string;
    }

    public function getPrependedString(){
        return $this->prependedString;
    }

    public function setParent(\Nne\Core\Menu\MenuItem $parent){
        $this->parent = $parent;
        $this->hasParent = true;
    }

    public function getParent(){
        return $this->parent;
    }

    public function hasParent(){
        return $this->hasParent;
    }

    public function setActive($status = true){
        //echo 'Set '.$this->name.' as '.($status ? 'active' : 'passive').'<br>';

        if(false == $status){
            foreach($this->children as $child){
                $status = $status || $child->isActive();
            }
        }

        $this->active = $status;

        if($this->hasParent){
            //echo 'Set '.$this->name.'\'s parent as '.($status ? 'active' : 'passive').'<br>';
            $this->parent->setActive($status);
        }

    }

    public function isActive(){
        return $this->active;
    }
}