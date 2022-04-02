cd system
rename .env.production .env
composer install
cd ../front-vue
npm install
cd ../
rm run.bat
pause