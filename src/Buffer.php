<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
 
namespace KryuuCommon\Buffer;

use Zend\Crypt\PublicKey\RsaOptions;
use KryuuCommon\Base58\Base58;
use KryuuCommon\Buffer\Exception\WrongResultValueException;



/**
 * Description of Buffer
 *
 * @author spawn
 */
class Buffer {
    
    /**
     * @var String
     */
    private $from = null;
    
    
    public function __construct($from = null) {
        if ($from) {
            $this->from($from);
        }   
        
    }
    
    public function from($from, $type = null) {
        switch($type) {
            case 'base64':
                $this->from = base64_decode($from);
                break;
            case 'base64s':
                $this->from = base64_decode($from, true);
                break;
            case 'base58':
                $base58 = new Base58();
                $this->from = $base58->decode($from);
                break;
            default:
                $this->from = $from;
        }
        return $this;
    }
    
    public function toString($type) {
        switch($type):
            case 'base64':
                return base64_encode($this->from);
            case 'base58':
                return $this->base58($this->from);
            default:
                return $this->from;
        endswitch;
    }
    
    public function toKeypair($type = null) {
        switch($type) {
            case 'base58_ed25519':
            default:
                $keys = $this->toEd25519();
                return [
                    'publicKey' => $this->base58($keys['publicKey']),
                    'secretKey' => $this->base58($keys['secretKey']),
                ];
        }
    }
    
    private function base58($data) {
        $base58 = new Base58();
        return $base58->encode($data);
    }
    
    /**
     * @private
     * Ed25519 keypair in base58 (as BigchainDB expects base58 keys)
     * @type {Object}
     * @param {Buffer} [seed] A seed that will be used as a key derivation function
     * @property {string} publicKey
     * @property {string} privateKey
     */
    private function toEd25519() {
        $keyPair = $this->from ? sodium_crypto_sign_seed_keypair($this->from)
                :sodium_crypto_sign_seed_keypair(sodium_crypto_secretbox_keygen());
        $keyPairHex = unpack('H*', $keyPair);

        if (count($keyPairHex) > 1) {
            throw new WrongResultValueException(
                sprintf('Return value from Unpack returned wrong value; expected'
                    . ' array of size 1 but got array of size %s', 
                    count($keyPairHex)
                )
            );
        }
        
        return [
            'publicKey' => pack('H*', substr($keyPairHex[1], 0, 128)),
            'secretKey' => pack('H*', substr($keyPairHex[1], 128, 160)),
        ];

    }
}
