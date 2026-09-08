CREATE DATABASE IF NOT EXISTS sports_facility_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sports_facility_db;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE, password_hash VARCHAR(255) NOT NULL,
  id_number VARCHAR(30), faculty VARCHAR(160), date_of_birth DATE,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE facilities (
  id INT AUTO_INCREMENT PRIMARY KEY, facility_name VARCHAR(100) NOT NULL,
  description TEXT NOT NULL, location VARCHAR(160) NOT NULL,
  price_per_hour DECIMAL(10,2) NOT NULL DEFAULT 0,
  image_url VARCHAR(500), is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE courts (
  id INT AUTO_INCREMENT PRIMARY KEY, facility_id INT NOT NULL,
  court_name VARCHAR(100) NOT NULL, is_active TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY uq_facility_court (facility_id,court_name),
  FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE time_slots (
  id INT AUTO_INCREMENT PRIMARY KEY, start_time TIME NOT NULL, end_time TIME NOT NULL,
  UNIQUE KEY uq_slot (start_time,end_time)
) ENGINE=InnoDB;

CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, court_id INT NOT NULL,
  time_slot_id INT NOT NULL, booking_date DATE NOT NULL, purpose VARCHAR(180) NOT NULL,
  participants INT NOT NULL DEFAULT 1, total_price DECIMAL(10,2) NOT NULL,
  status ENUM('confirmed','cancelled','completed') NOT NULL DEFAULT 'confirmed',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_booking_slot (court_id,booking_date,time_slot_id,status),
  KEY idx_booking_user (user_id), KEY idx_booking_date (booking_date),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (court_id) REFERENCES courts(id),
  FOREIGN KEY (time_slot_id) REFERENCES time_slots(id)
) ENGINE=InnoDB;

CREATE TABLE closures (
  id INT AUTO_INCREMENT PRIMARY KEY, court_id INT NOT NULL, closure_date DATE NOT NULL,
  time_slot_id INT NULL, reason VARCHAR(180) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY idx_closure_date (closure_date),
  FOREIGN KEY (court_id) REFERENCES courts(id) ON DELETE CASCADE,
  FOREIGN KEY (time_slot_id) REFERENCES time_slots(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, facility_id INT NOT NULL,
  comment TEXT NOT NULL, rating TINYINT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE,
  CONSTRAINT chk_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL, subject VARCHAR(180) NOT NULL, message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Both demo accounts use password: password
INSERT INTO users (name,email,password_hash,id_number,faculty,date_of_birth,is_admin) VALUES
('System Administrator','admin@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','ADMIN001','Faculty of Computing and Information Technology','2000-01-01',1),
('Demo Student','student@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','24WMR00001','Faculty of Computing and Information Technology','2004-01-01',0);

INSERT INTO facilities (facility_name,description,location,price_per_hour) VALUES
('Badminton','Indoor badminton courts with non-slip flooring and spectator seating.','Sports Complex - Level 1',8.00),
('Futsal','Covered futsal courts for training and friendly matches.','Sports Complex - Ground Floor',20.00),
('Basketball','Full-size indoor court with scoreboards and changing rooms.','Sports Complex - Main Hall',18.00),
('Table Tennis','Indoor area with tournament-standard tables.','Student Centre - Level 2',6.00),
('Tennis','Outdoor hard courts with evening floodlights.','East Campus Sports Zone',15.00),
('Swimming Pool','25-metre pool with changing and shower facilities.','Aquatic Centre',10.00);

INSERT INTO courts (facility_id,court_name) VALUES
(1,'Court A'),(1,'Court B'),(1,'Court C'),(2,'Futsal Court 1'),(2,'Futsal Court 2'),
(3,'Main Court'),(4,'Table 1'),(4,'Table 2'),(4,'Table 3'),(5,'Court 1'),(5,'Court 2'),
(6,'Lane Group A'),(6,'Lane Group B');

INSERT INTO time_slots (start_time,end_time) VALUES
('08:00:00','09:00:00'),('09:00:00','10:00:00'),('10:00:00','11:00:00'),
('11:00:00','12:00:00'),('12:00:00','13:00:00'),('13:00:00','14:00:00'),
('14:00:00','15:00:00'),('15:00:00','16:00:00'),('16:00:00','17:00:00'),
('17:00:00','18:00:00'),('18:00:00','19:00:00'),('19:00:00','20:00:00'),
('20:00:00','21:00:00'),('21:00:00','22:00:00');
