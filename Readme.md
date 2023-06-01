## Project Images
- stored in project_images folder

## Internet Projects Limited

## Installation

1. Clone the repository
2. Install dependencies : composer install
3. Set up the database:

- Create a new MySQL database for the project.
- Copy the `.env` file and update the database configuration:
- Update the `DATABASE_URL` in the `.env.local` file with your MySQL database credentials.(Username and Password)
- Run the database migrations:
- php bin/console doctrine:migrations:migrate

4. Build the assets:
- npm install
- npm run dev

5. Start the Symfony development server:
- symfony server:start

## Usage
Access the application in your web browser at `http://localhost:8000/teams` 
- Explore the functionality and features of the application.







 