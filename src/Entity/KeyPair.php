<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace KryuuCommon\Buffer\Entity;

/**
 * Description of KeyPair
 *
 * @author spawn
 */
class KeyPair {
    
    private $private = null;
    
    private $public = null;
    
    function getPrivate() {
        return $this->private;
    }

    function getPublic() {
        return $this->public;
    }

    function setPrivate($private) {
        $this->private = $private;
        return $this;
    }

    function setPublic($public) {
        $this->public = $public;
        return $this;
    }
    
}
