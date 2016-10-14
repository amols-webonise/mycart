<?php
class Cart_Model
{
	protected $_cartid;
	protected $_name;
	protected $_product_id;
	protected $_qty;

    public function __construct(array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid category property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (('mapper' == $name) || !method_exists($this, $method)) {
            throw new Exception('Invalid category property');
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }


    public function setCartId($param)
    {
        $this->_cartid = (int) $param;
        return $this;
    }

    public function getCartId()
    {
        return $this->_cartid;
    }
	

    public function setName($param)
    {
        $this->_name = (string) $param;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }
	
    
    public function setProductId($param)
    {
        $this->_product_id = (string) $param;
        return $this;
    }

    public function getProductId()
    {
        return $this->_product_id;
    }
	

    public function setQty($param)
    {
        $this->_qty = (int) $param;
        return $this;
    }

    public function getQty()
    {
        return $this->_qty;
    }
}
