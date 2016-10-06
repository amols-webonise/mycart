-------------------------------------------------------------------------
Api List
-------------------------------------------------------------------------
Add Category
POST http://mycart.com/category/add
Param List: name, description, tax

Update Category
POST http://mycart.com/category/update
Param List: name, description, tax, id

Delete Category
POST http://mycart.com/category/delete
Param List: id

List Categories
GET http://mycart.com/category/getAll
Param List: not required

Add Product
POST http://mycart.com/product/add?
Param List: name=test product 1&description=test p&price=50&discount=5&category_id=8

Update Product
POST http://mycart.com/product/update
Param List: name=test product 1&description=dfhgdfg dfhg p&price=50&discount=5&id=11

Delete Product
DELETE http://mycart.com/product/delete
Param List: id=11

List Products
GET http://mycart.com/product/getAll
Param List: param not required

Create cart
POST http://mycart.com/cart/add
Param List: cartid=0&name=test cart name&product_id=5&qty=2

Delete cart
DELETE http://mycart.com/cart/delete
Param List: cartid=3

Update Cart (Add Product or Delete Product)
POST http://mycart.com/cart/add
Param List: cartid=4&name=test cart name&product_id=6&qty=2

Show Cart (Includes - Cart Name, Products, Total, Total Discount, Total with Discount, Total Tax, Total With Tax, Grand Total)
GET http://mycart.com/cart/showcart
Param List: cartid=4

Get Cart Total
GET http://mycart.com/cart/carttotal
Param List: cartid=4

Get Cart total discount
GET http://mycart.com/cart/carttotaldiscount
Param List: 

Get Cart total tax
GET http://mycart.com/cart/carttotaltax
Param List: cartid=4