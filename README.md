# Fire Blanket Inventory Tracker

A Laravel-based inventory tracking system for Fire Blanket packs. This application allows users to:

- Track inventory of Fire Blanket packs in different sizes (1, 2, 4, 6, 8, 12, 20, 40, 60, 80, 100)
- Add, edit, and delete orders for each pack size
- Filter and view orders by month and year

## Setup Instructions

1. Clone the repository
2. Install dependencies
   ```bash
   composer install
   npm install
   ```
3. Copy `.env.example` to `.env` and configure your database
   ```bash
   cp .env.example .env
   ```
4. Generate application key
   ```bash
   php artisan key:generate
   ```
5. Run migrations and seed the database
   ```bash
   php artisan migrate --seed
   ```
6. Compile assets
   ```bash
   npm run dev
   ```
7. Start the server
   ```bash
   php artisan serve
   ```

## Usage

1. Register or login to the application
2. Navigate to the Inventory section from the sidebar
3. Use the month and year filters to view orders for specific time periods
4. Add new orders by clicking the "Add Order" button for a specific pack
5. Edit or delete existing orders using the respective buttons

## Features

- **Monthly Filtering**: Sort and view orders by month and year
- **Pack Management**: Track different pack sizes
- **Order Management**: Add, edit, and delete orders for each pack
- **User Authentication**: Secure access to inventory data

## Technologies Used

- Laravel
- Livewire
- Tailwind CSS
- Flux UI Components
