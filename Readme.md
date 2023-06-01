Database = do the connection 

Team  - team_name, league


Player table - A player can be on loan in another team so might belong to more than one team. 

ManyToOne Relationship

Team. 
team_logo, team_name, league
player_name, player_image, 


symfony console doctrine:fixtures:load

composer require symfony/asset



 I have two tables of teams and players. The teams table has team_name, team_logo, league, and moneybalance while players table has team_id, and player_name. I have created a ManyToOne Relationship whereby one team can have many players i would like to Make a page where we can buy a player for a certain amount between two teams in symfony the moneybalance should decrease from that particular team and the player should change from one team to another 

  I have two tables of teams and players. The teams table has team_name, team_logo, league, and release_year while players table has team_id, and player_name. I have created a ManyToOne Relationship whereby one team can have many players i would like to Make a page where i can fetch name of players from the database and displaye it in symfony


 Fetch Player names. - Done 
 Buy and Sell Players 
 display team money balance after a sale




## Internet Projects Limited

## Installation

1. Clone the repository
2. Install dependencies : composer install
3. Set up the database:

- Create a new MySQL database for the project.
- Copy the `.env` file and update the database configuration:
- Update the `DATABASE_URL` in the `.env.local` file with your MySQL database credentials.
- Run the database migrations:
- php bin/console doctrine:migrations:migrate

4. Build the assets:
- npm install
- npm run build

5. Start the Symfony development server:
- symfony server:start

## Usage
Access the application in your web browser at `http://localhost:8000/teams` 
- Explore the functionality and features of the application.


## Testing

To run the tests for this project, execute the following command:




 