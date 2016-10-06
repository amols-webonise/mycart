<?php
class Category_Model
{
	protected $_id;
	protected $_name;
	protected $_description;
	protected $_tax;
	
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

	
	/* get / set _category_id */
    public function setId($param)
    {
        $this->_id = (int) $param;
        return $this;
    }

    public function getId()
    {
        return $this->_id;
    }
	
	/* get / set _name */
    public function setName($param)
    {
        $this->_name = (string) $param;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }
	
	/* get / set _is_active */
    public function setDescription($param)
    {
        $this->_description = (string) $param;
        return $this;
    }

    public function getDescription()
    {
        return $this->_description;
    }
	
	/* get / set _is_deleted */
    public function setTax($param)
    {
        $this->_tax = (float) $param;
        return $this;
    }

    public function getTax()
    {
        return $this->_tax;
    }
}
