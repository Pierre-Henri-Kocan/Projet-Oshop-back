# Routes

| URL | HTTP Method | Controller | Method | Title | Content | Comment |
| ------------------------------------- | ----------- | ---------------------- | ---------- | ---------------- | ------------------------- | ------------------------------------------- |
| `/` | `GET` | `MainController` | `home` | oShop Backoffice | Backoffice dashboard | - |
| `/categories` | `GET` | `CategoryController` | `list` | Categories list | Categories list | - |
| `/categories/add` | `GET` | `CategoryController` | `add` | Add category | Form to add a category | - |
| `/categories/[i:id]/edit` | `GET` | `CategoryController` | `edit` | Edit category | Form to edit a category | [i:id] is the category to edit |
| `/categories/[i:id]/delete` | `GET` | `CategoryController` | `delete` | Delete category | Category delete | [i:id] is the category to delete |
| `/brands` | `GET` | `BrandController` | `list` | Brands list | Brands list | - |
| `/brands/add` | `GET` | `BrandController` | `add` | Add brand | Form to add a brand | - |
| `/brands/[i:id]/edit` | `GET` | `BrandController` | `edit` | Edit brand | Form to edit a brand | [i:id] is the brand to edit |
| `/brands/[i:id]/delete` | `GET` | `BrandController` | `delete` | Delete brand | Brand delete | [i:id] is the brand to delete |
| `/products` | `GET` | `ProductController` | `list` | Products list | Products list | - |
| `/products/add` | `GET` | `ProductController` | `add` | Add product | Form to add a product | - |
| `/products/[i:id]/edit` | `GET` | `ProductController` | `edit` | Edit product | Form to edit a product | [i:id] is the product to edit |
| `/products/[i:id]/delete` | `GET` | `ProductController` | `delete` | Delete product | Product delete | [i:id] is the product to delete |
| `/types` | `GET` | `TypeController` | `list` | Types list | Types list | - |
| `/types/add` | `GET` | `TypeController` | `add` | Add type | Form to add a type | - |
| `/types/[i:id]/edit` | `GET` | `TypeController` | `edit` | Edit type | Form to edit a type | [i:id] is the type to edit |
| `/types/[i:id]/delete` | `GET` | `TypeController` | `delete` | Delete type | Type delete | [i:id] is the type to delete |
| `/users` | `GET` | `UserController` | `list` | Users list | Users list | - |
| `/users/add` | `GET` | `UserController` | `add` | Add user | Form to add a user | - |
| `/users/[i:id]/edit` | `GET` | `UserController` | `edit` | Edit user | Form to edit a user | [i:id] is the user to edit |
| `/users/[i:id]/delete` | `GET` | `UserController` | `delete` | Delete user | User delete | User delete[i:id] is the user to delete |
| `/commands` | `GET` | `CommandController` | `list` | Commands list | Commands list | - |
| `/commands/add` | `GET` | `CommandController` | `add` | Add command | Form to add a command | - |
| `/commands/[i:id]/edit` | `GET` | `CommandController` | `edit` | Edit command | Form to edit a command | [i:id] is the command to edit |
| `/commands/[i:id]/delete` | `GET` | `CommandController` | `delete` | Delete command | Command delete | [i:id] is the command to delete |