## E-commerce

The online store application enables administrators to add products and view reports, while allowing users to purchase products. It is built using Vue and Vuex for the front end, styled with Tailwind CSS, and uses Laravel for the back end.
## Installation

#### Laravel

```shell
   # Enter the directory
   cd backend


   # Install dependencies
   composer install

   # Create .env file with your configurations
   cp .env.example .env

   # Generate new key
   php artisan key:generate

   # Upload the database and seeding
   php artisan migrate --seed

   # Run server
   php artisan serve

```

#### Vue

```shell
   # Enter the directory
   cd frontend

   # Install npm dependencies
   npm install

   # Create .env file with your configurations
   # Set your VITE_API_BASE_URL (laravel server url)
   # Set your VITE_BASE_URL (current frontend server url)
   cp .env.example .env

   # Run vite server
   npm run dev
```

## Screen Shots

<p align="center">
<img src="screenshots/Dashboard.PNG" alt="Dashboard" width="50%" >
<img src="screenshots/Home.PNG" alt="Store Homepage" >
<img src="screenshots/Cart.PNG" alt="Cart" width="50%" >
<img src="screenshots/Checkout.PNG" alt="Checkout" width="50%" >
<img src="screenshots/Edit-Product.PNG" alt="Edit Product" width="50%" >
<img src="screenshots/Orders.PNG" alt="Orders" width="50%" >
<img src="screenshots/Reports.PNG" alt="Reports" width="50%" >
</p>
