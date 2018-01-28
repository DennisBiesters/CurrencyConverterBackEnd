CurrencyConverterBackEnd
=================

A Symfony project created on January 25, 2018, 5:28 pm.


composer update

[Enter Enter Enter...]
database_host (127.0.0.1):
database_port (null):
database_name (symfony):
database_user (root):
database_password (null):
mailer_transport (smtp):
mailer_host (127.0.0.1):
mailer_user (null):
mailer_password (null):
secret (ThisTokenIsNotSoSecretChangeIt):
jwt_private_key_path ('%kernel.root_dir%/../var/jwt/private.pem'):
jwt_public_key_path ('%kernel.root_dir%/../var/jwt/public.pem'):
jwt_key_pass_phrase (welkom):
jwt_token_ttl (3600):

Kopieer private.pem en public.pem naar CurrencyConverterBackEnd/var/jwt

Of zelf genereren
openssl genrsa -out var/jwt/private.pem -aes256 4096
openssl rsa -pubout -in var/jwt/private.pem -out var/jwt/public.pem
pass phrase = welkom

php bin/console server:run (connection refused?)
Zelf getest in xampp