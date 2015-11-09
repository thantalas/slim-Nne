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

class MenuCollection extends Collection{
    protected $active;
    protected $name;

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function setActiveMenu($menu){
        $this->active   = $menu;

        foreach($this->items as $item){
            $this->seekAndActivate($item, $menu);
        }
    }

    protected function seekAndActivate(\Nne\Core\Menu\MenuItem $item, $menu){
        if($item->getName() == $menu){
            $item->setActive(true);
        }else if($item->hasChildren()){
            foreach($item->getChildren() as $child){
                $this->seekAndActivate($child, $menu);
            }
        }else{
            $item->setActive(false);
        }
    }

    public function getActiveMenu(){
        return $this->active;
    }

    /**
     * Add new item to menuCollection
     * @param Nne\Core\Menu\MenuItem $item
     * @param String $menu
     */
    public function addItem($name, \Nne\Core\Menu\MenuItem $item){
        $this->items[$name] = $item;
    }

    public function getItem($name){
        return isset($this->items[$name]) ? $this->items[$name] : null;
    }

    /**
     * MenuItem factory
     * @param  String $label
     * @param  String $url
     * @return MenuItem
     */
    public function createItem($name, $option){
        return new MenuItem($name, $option);
    }
}
