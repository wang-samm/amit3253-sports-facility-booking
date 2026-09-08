# Sports Facility Booking PHP Application

Plain PHP 8 + MySQL application for the AMIT3253 assignment.

## Data model

- `facilities`: sport/category, description, location, fee and photo
- `courts`: specific bookable area belonging to a facility
- `time_slots`: fixed one-hour booking periods
- `bookings`: student, court, date, slot, purpose, participants, fee and status
- `closures`: admin-controlled full-day or individual-slot closures
- `users`, `testimonials`, `contact_messages`: supporting functions

The create operation locks the selected court row and checks bookings and closures inside one transaction. Concurrent requests for the same court/date/slot are serialized, preventing double booking.

## Local requirements

- PHP 8.1 or later with `mysqli`
- MySQL 8 or MariaDB
- Composer (only required for S3 integration)

## Local run

```bash
mysql -u root -p < schema.sql
composer install
DB_HOST=localhost DB_USER=root DB_PASS=yourpassword DB_NAME=sports_facility_db php -S localhost:8080
```

Open `http://localhost:8080`.

In AWS, Apache receives the database and S3 environment values from EC2 user data. Do not hardcode credentials in `config.php`.

