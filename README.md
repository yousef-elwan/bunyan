# 🏠 Real Estate Marketplace Project

A comprehensive real estate platform built with the Laravel framework, designed to facilitate the buying, selling, and renting of properties.

---

## ✨ Key Features

- **Advanced Search:** A powerful filtering system to search for properties by city, price, type, and other specifications.
- **Listing Management:** A user dashboard for agents to add, edit, and delete their property listings.
- **Agent Profiles:** Dedicated pages for real estate agents to display their listings and contact information.
- **Multi-language Support:** Full support for both Arabic and English.
- **Interactive Maps:** Display property locations accurately on a map.
- **Responsive Design:** Excellent user experience across all devices.

---

## 🛠️ Technology Stack

- **Backend Framework:** Laravel 11
- **Frontend:** Blade, Livewire, Vite.js
- **Database:** MySQL / PostgreSQL
- **Core Packages:**
    - `livewire/livewire`: For building dynamic interfaces.
    - `astrotomic/laravel-translatable`: For managing multi-language content.
    - `spatie/laravel-activitylog`: For logging user activities.
    - `intervention/image`: For image processing.

---

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

1.  **Clone the Repository:**
    ```bash
    git clone [https://github.com/yousef-elwan/your-repo-name.git](https://github.com/yousef-elwan/your-repo-name.git)
    cd your-repo-name
    ```

2.  **Install Composer Dependencies:**
    ```bash
    composer install
    ```

3.  **Install NPM Dependencies:**
    ```bash
    npm install
    ```

4.  **Setup Environment File:**
    - Copy the `.env.example` file to a new file named `.env`.
    - Fill in your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
    ```bash
    cp .env.example .env
    ```

5.  **Generate App Key:**
    ```bash
    php artisan key:generate
    ```

6.  **Run Database Migrations:**
    ```bash
    php artisan migrate
    ```

7.  **Link Storage:**
    ```bash
    php artisan storage:link
    ```

8.  **Run Development Server:**
    - Open a new terminal and run the Vite server.
    ```bash
    npm run dev
    ```
    - In your first terminal, run the Laravel server.
    ```bash
    php artisan serve
    ```

The project should now be running on `http://127.0.0.1:8000`.

---

## 👨‍💻 Author

- **Name:** Yousef Elwan
- **GitHub:** [@yousef-elwan](https://github.com/yousef-elwan)
- **Email:** elwanyousef1@gmail.com

---

## 📄 License

This project is licensed under the MIT License. See the `LICENSE` file for more details.
