<?php
include_once('./../../bootstrap.php');
require_once('cart.php');
require_once('Cart_Model.php');
require_once('phpunit/vendor/autoload.php');
 
class CategoryWrapperTest extends PHPUnit_Framework_TestCase {
 
    private $cm;
    private $c;

    function setUp(){
        $this->cm = new Cart_Model();
        $this->cm->setCartId(1);
        $this->cm->setName('PHPUnit Test Cart Name');
        $this->cm->setProductId(1);
        $this->cm->setQty(2);
        http_response::$print_response = false;
    }
    
    function testCartNameShouldNotBeEmpty(){
        $this->cm->setName('');
        $this->cm->setCartId(0);
        $this->cm->setAction('add');
        $this->c = new cart($this->cm);
        $this->c->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testCartQtyShouldNotBeEmpty(){
        $this->cm->setQty('');
        $this->cm->setCartId(0);
        $this->cm->setAction('add');
        $this->c = new cart($this->cm);
        $this->c->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testCartQtyShouldNotBeZero(){
        $this->cm->setQty(0);
        $this->cm->setCartId(0);
        $this->cm->setAction('add');
        $this->c = new cart($this->cm);
        $this->c->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testCartQtyShouldNotBeNegative(){
        $this->cm->setQty(-1);
        $this->cm->setCartId(0);
        $this->cm->setAction('add');
        $this->c = new cart($this->cm);
        $this->c->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testCartIdIsExistInDb(){
        $this->cm->setCartId(100000);
        $this->cm->setAction('update');
        $this->c = new cart($this->cm);
        $this->c->update();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testProductIdIsExistInDb(){
        $this->cm->setProductId(100000);
        $this->cm->setAction('update');
        $this->c = new cart($this->cm);
        $this->c->update();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testCartQtyShouldNotBeZeroOnUpdate(){
        $this->cm->setQty(0);
        $this->cm->setCartId(0);
        $this->cm->setAction('update');
        $this->c = new cart($this->cm);
        $this->c->update();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }
}