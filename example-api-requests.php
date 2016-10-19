-------------------------------------------------------------------------
Api List for live server
-------------------------------------------------------------------------
CATEGORY :
POST
http://mycart-empmngmntsystm.rhcloud.com/category/add
(name, description, tax)

PATCH
http://mycart-empmngmntsystm.rhcloud.com/category/5
(name, description, tax)

Delete
http://mycart-empmngmntsystm.rhcloud.com/category/5

GET (GET BY ID)
http://mycart-empmngmntsystm.rhcloud.com/category/5

GET (GET ALL)
http://mycart-empmngmntsystm.rhcloud.com/category/

PRODUCT :
POST
http://mycart-empmngmntsystm.rhcloud.com/product/add
(name, description, price, discount, category_id)

PATCH
http://mycart-empmngmntsystm.rhcloud.com/product/8
(name, description, price, discount, category_id)

DELETE
http://mycart-empmngmntsystm.rhcloud.com/product/8

GET (GET BY ID)
http://mycart-empmngmntsystm.rhcloud.com/product/8

GET (GET ALL)
http://mycart-empmngmntsystm.rhcloud.com/product/


CART :
POST
http://mycart-empmngmntsystm.rhcloud.com/cart/add
(name, product_id, qty)

PATCH
http://mycart-empmngmntsystm.rhcloud.com/cart/update/([0-9-/]+?)/
(name, product_id, qty)

PATCH
http://mycart-empmngmntsystm.rhcloud.com/cart/update/([0-9-/]+?)/lineitem/([0-9-/]+?)/qty/([0-9-/]+?)/
http://mycart-empmngmntsystm.rhcloud.com/cart/update/10/lineitem/4/qty/1


DELETE CART
http://mycart-empmngmntsystm.rhcloud.com/cart/7/

DELETE CART LINE ITEM
http://mycart-empmngmntsystm.rhcloud.com/cart/delete/4/lineitem/7

GET (GET BY ID)
http://mycart-empmngmntsystm.rhcloud.com/cart/8

GET (GET ALL)
http://mycart-empmngmntsystm.rhcloud.com/cart/


-------------------------------------------------------------------------
Api List for local machine
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