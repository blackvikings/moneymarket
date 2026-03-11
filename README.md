# Money Market

This is a Laravel-based web application for managing a money market.

## Project Overview

The application seems to manage various entities related to a financial market, including:
*   Leads
*   Users (Employees, etc.)
*   Firms
*   Banks and Bank Branches
*   Insurance (and its types)
*   RTO Agents and Challans
*   Vehicles

## Features

Based on the project structure, the application likely includes the following features:
*   Lead Management
*   User Management
*   Bank and Branch Management
*   Insurance Management
*   RTO related functionalities

## Setup and Installation

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd money-market
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    npm install
    ```

3.  **Environment Configuration:**
    *   Copy the `.env.example` file to `.env`:
        ```bash
        cp .env.example .env
        ```
    *   Generate an application key:
        ```bash
        php artisan key:generate
        ```
    *   Configure your database and other environment variables in the `.env` file.

4.  **Database Migration:**
    Run the database migrations to create the necessary tables:
    ```bash
    php artisan migrate
    ```

5.  **Start the development server:**
    ```bash
    php artisan serve
    ```
