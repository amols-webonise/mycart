<?php
include_once('./../../bootstrap.php');
require_once('product.php');
require_once('Product_Model.php');
require_once('phpunit/vendor/autoload.php');
 
class ProductWrapperTest extends PHPUnit_Framework_TestCase {
 
    private $pm;
    private $p;

    function setUp(){
        $this->pm = new Product_Model();
        $this->pm->setName('PHPUnit Test Product Name');
        $this->pm->setDescription('PHPUnit Test Product Description');
        $this->pm->setPrice(100);
        $this->pm->setDiscount(1);
        $this->pm->setCategoryId(1);
        $this->pm->setId(0);
        http_response::$print_response = false;
    }
    
    function testProductNameShouldNotBeEmpty(){
        $this->pm->setName('');
        $this->pm->setAction('add');
        $this->p = new product($this->pm);
        $this->p->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }
    
    function testProductDescriptionShouldNotBeEmpty(){
        $this->pm->setDescription('');
        $this->pm->setAction('add');
        $this->p = new product($this->pm);
        $this->p->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testProductPriceGreterThanZero(){
        $this->pm->setPrice(0);
        $this->pm->setAction('add');
        $this->p = new product($this->pm);
        $this->p->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testProductDiscountGreterThanZero(){
        $this->pm->setDiscount(0);
        $this->pm->setAction('add');
        $this->p = new product($this->pm);
        $this->p->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testProductDiscountLessThanHundred(){
        $this->pm->setDiscount(100);
        $this->pm->setAction('add');
        $this->p = new product($this->pm);
        $this->p->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testValidProductIdOnUpdate(){
        $this->pm->setId(100000);
        $this->pm->setAction('update');
        $this->p = new product($this->pm);
        $this->p->update();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }
}