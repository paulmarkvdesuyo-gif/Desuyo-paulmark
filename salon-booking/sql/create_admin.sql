USE salon_booking;
INSERT INTO users (name, email, password, role)
VALUES ('admin', 'lovelycornea695@gmail.com', '{PASSWORD_HASH}', 'admin');
-- Replace {PASSWORD_HASH} with result of password_hash('yourpassword', PASSWORD_DEFAULT)
