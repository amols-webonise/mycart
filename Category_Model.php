<?php
class Category_Model
{
	protected $_id;
	protected $_name;
	protected $_description;
	protected $_tax;
    protected $_action;
	
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

	

    public function setId($param)
    {
        $this->_id = (int) $param;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
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
	

    public function setDescription($param)
    {
        $this->_description = (string) $param;
        return $this;
    }

    public function getDescription()
    {
        return $this->_description;
    }
	

    public function setTax($param)
    {
        $this->_tax = (float) $param;
        return $this;
    }

    public function getTax()
    {
        return $this->_tax;
    }

    public function setAction($action)
    {
        $this->_action = (string) $action;
        return $this;
    }

    public function getAction()
    {
        return $this->_action;
    }
}
