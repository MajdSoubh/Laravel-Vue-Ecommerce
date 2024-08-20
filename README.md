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
<img src="screenshots/dashboard.PNG" alt="Dashboard" width="50%" >
<img src="screenshots/edit_survey.PNG" alt="Edit Survey" width="50%" >
<img src="screenshots/surveys.PNG" alt="Surveys" width="50%" >
<img src="screenshots/submission.PNG" alt="Submissions" width="50%" >
</p>
