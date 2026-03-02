@echo off
mysqldump -u root siscatalogo --no-data --default-character-set=utf8mb4 > d:\laragon\www\siscatalogo\database\schema.sql
mysqldump -u root siscatalogo roles permissions role_permissions users company_profile home_settings --no-create-info --default-character-set=utf8mb4 > d:\laragon\www\siscatalogo\database\essential_data.sql
mysqldump -u root siscatalogo categories products product_images portfolio portfolio_gallery home_slider --no-create-info --default-character-set=utf8mb4 > d:\laragon\www\siscatalogo\database\demo_data.sql
