<?php
include_once('./../../bootstrap.php');
require_once('category.php');
require_once('Category_Model.php');
require_once('phpunit/vendor/autoload.php');
 
class CategoryWrapperTest extends PHPUnit_Framework_TestCase {
 
    private $cm;
    private $c;

    function setUp(){
        $this->cm = new Category_Model();
        $this->cm->setName('PHPUnit Test Category Name');
        $this->cm->setDescription('PHPUnit Test Category Description');
        $this->cm->setTax(2);
        $this->cm->setId(0);
        http_response::$print_response = false;
    }
    
    function testCategoryNameShouldNotBeEmpty(){
        $this->cm->setName('');
        $this->cm->setAction('add');
        $this->c = new category($this->cm);
        $this->c->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testCategoryDescriptionShouldNotBeEmpty(){
        $this->cm->setDescription('');
        $this->cm->setAction('add');
        $this->c = new category($this->cm);
        $this->c->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testCategoryTaxGreterThanZero(){
        $this->cm->setTax(0);
        $this->cm->setAction('add');
        $this->c = new category($this->cm);
        $this->c->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testCategoryTaxLessThanZero(){
        $this->cm->setTax(-1);
        $this->cm->setAction('add');
        $this->c = new category($this->cm);
        $this->c->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testCategoryTaxLessThanHundred(){
        $this->cm->setTax(100);
        $this->cm->setAction('add');
        $this->c = new category($this->cm);
        $this->c->add();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testValidateExistingCategory(){
        $this->cm->setId(100000);
        $this->cm->setAction('update');
        $this->c = new category($this->cm);
        $this->c->update();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testTryToDeleteInvalidCategory(){
        $this->cm->setId(100000);
        $this->cm->setAction('delete');
        $this->c = new category($this->cm);
        $this->c->delete();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }

    function testTryToGetInvalidCategory(){
        $this->cm->setId(100000);
        $this->cm->setAction('getById');
        $this->c = new category($this->cm);
        $this->c->getById();
        $this->assertEquals('ERROR', http_response::getResponseStatus());
    }
}
