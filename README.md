### Beer application BEERAPP.
#### Req:
- Composer 2
- PHP 8


#### Some hints:
Edit .env.local with your credentials, create db:  
`DATABASE_URL="mysql://user:pass@127.0.0.1:3306/db_name"`  
`php bin/console doctrine:database:create`  

If want add import command to crontab (eg. in unix CLI):  
`crontab -l | { cat; echo "0 0 0 0 0 php /usr/src/app/bin/console app:import:beers"; } | crontab -`
 
Local tests environment:  
create .env.test.local based on .env.test | add:  
`DATABASE_URL="mysql://user:pass@127.0.0.1:3306/db_name"`  

phpunit.xml.dist add:  
 `<phpunit>   
     <!-- ... -->  
     <extensions>  
         <extension class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>  
     </extensions>
     <!-- ... -->  
 <phpunit> `

After local db configuration, get schema:  
`php bin/console doctrine:database:create -e test --no-interaction`  
`php bin/console doctrine:migrations:migrate -e test --no-interaction`