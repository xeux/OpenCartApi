Opencart API
================

Opencart extension based in opencart-webapi (ethernet1), using functions list from API2CART.

Functions
-------

Product Management Functions

* `product.get`: Return full product information (by ID or SKU).
* `product.list`: Return products list with limited information (ID, Name, Price, Thumbnail). Set optional parameter category_id to get products list into the specified category.
* `product.count`: Return store products count. Set optional parameter category_id to get products count into the specified category.

Category Management Functions

* `category.get`: Return full Category information (by ID).
* `category.list`: Get categories list with limited information (ID, Name, Description, Href, Image).
* `category.count`: Return shopping cart categories count.

Customer Management Functions

* `customer.get`: Return full Customer information (by ID, Email or Token).

Roadmap
-------

Product Management Functions
	
* `product.add`: Add new product to the shopping cart.
* `product.update`: Update price and stock for a specific product
* `product.delete`: Remove specified product from the shopping cart (by ID or SKU)
* `product.latest`
* `product.popular`
* `product.bestseller`

Customer Management Functions

* `customer.list // customer.find`: Get customers list with limited information.

Store Management Functions

Order Management Functions

License
-------

This software is distributed under the [GNU GPL V3](http://www.gnu.org/licenses/gpl.html) License.