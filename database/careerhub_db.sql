-- ============================================================
--  CareerHub database  (MySQL 5.7+ / MariaDB 10.3+)
--  Import with phpMyAdmin or:  mysql -u root -p < careerhub_db.sql
--  Default admin login:  admin@example.com  /  admin123
-- ============================================================
CREATE DATABASE IF NOT EXISTS `ecommerce_careerhub_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ecommerce_careerhub_db`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `applications`;
DROP TABLE IF EXISTS `careers`;
DROP TABLE IF EXISTS `gallery`;
DROP TABLE IF EXISTS `admins`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `admins` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_admins_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `gallery` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`      VARCHAR(150) NOT NULL,
  `image`      VARCHAR(255) NOT NULL,
  `thumb`      VARCHAR(255) NULL,
  `status`     TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = visible, 0 = hidden',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gallery_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `careers` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`       VARCHAR(150) NOT NULL,
  `department`  VARCHAR(100) NOT NULL,
  `location`    VARCHAR(100) NOT NULL,
  `job_type`    ENUM('Full Time','Part Time','Contract','Internship') NOT NULL DEFAULT 'Full Time',
  `experience`  VARCHAR(50) NULL,
  `description` TEXT NOT NULL,
  `status`      TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = open, 0 = closed',
  `created_at`  DATETIME NULL,
  `updated_at`  DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_careers_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `applications` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `career_id`  INT UNSIGNED NULL,
  `job_title`  VARCHAR(150) NULL COMMENT 'snapshot of the title at apply time',
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `phone`      VARCHAR(30)  NOT NULL,
  `message`    TEXT NULL,
  `resume`     VARCHAR(255) NOT NULL,
  `status`     ENUM('New','Reviewed','Shortlisted','Rejected') NOT NULL DEFAULT 'New',
  `created_at` DATETIME NULL,
  `updated_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_app_career` (`career_id`),
  KEY `idx_app_status` (`status`),
  KEY `idx_app_created` (`created_at`),
  CONSTRAINT `fk_app_career` FOREIGN KEY (`career_id`) REFERENCES `careers`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ------------------------- sample data -------------------------

INSERT INTO `admins` (`name`,`email`,`password`,`created_at`,`updated_at`) VALUES ('Administrator','admin@example.com','$2y$10$A7Z/5Rm1mL0215GUcQQ00OsZyRBAnv9lU1ByKnYvU5axpFFzz9i12','2026-09-30 09:58:33','2026-09-30 09:58:33');

INSERT INTO `gallery` (`title`,`image`,`thumb`,`status`,`created_at`,`updated_at`) VALUES
  ('Team offsite','sample_01.jpg','t_sample_01.jpg',1,'2026-09-16 09:58:33','2026-09-16 09:58:33'),
  ('Studio morning','sample_02.jpg','t_sample_02.jpg',1,'2026-09-17 09:58:33','2026-09-17 09:58:33'),
  ('Design sprint','sample_03.jpg','t_sample_03.jpg',1,'2026-09-18 09:58:33','2026-09-18 09:58:33'),
  ('Launch day','sample_04.jpg','t_sample_04.jpg',1,'2026-09-19 09:58:33','2026-09-19 09:58:33'),
  ('Whiteboard session','sample_05.jpg','t_sample_05.jpg',1,'2026-09-20 09:58:33','2026-09-20 09:58:33'),
  ('Coffee corner','sample_06.jpg','t_sample_06.jpg',1,'2026-09-21 09:58:33','2026-09-21 09:58:33'),
  ('Hack night','sample_07.jpg','t_sample_07.jpg',1,'2026-09-22 09:58:33','2026-09-22 09:58:33'),
  ('Client workshop','sample_08.jpg','t_sample_08.jpg',1,'2026-09-23 09:58:33','2026-09-23 09:58:33'),
  ('Annual retreat','sample_09.jpg','t_sample_09.jpg',1,'2026-09-24 09:58:33','2026-09-24 09:58:33'),
  ('New office tour','sample_10.jpg','t_sample_10.jpg',1,'2026-09-25 09:58:33','2026-09-25 09:58:33'),
  ('Product demo','sample_11.jpg','t_sample_11.jpg',1,'2026-09-26 09:58:33','2026-09-26 09:58:33'),
  ('Community meetup','sample_12.jpg','t_sample_12.jpg',1,'2026-09-27 09:58:33','2026-09-27 09:58:33'),
  ('Mentorship circle','sample_13.jpg','t_sample_13.jpg',1,'2026-09-28 09:58:33','2026-09-28 09:58:33'),
  ('Celebration lunch','sample_14.jpg','t_sample_14.jpg',1,'2026-09-29 09:58:33','2026-09-29 09:58:33');

INSERT INTO `careers` (`title`,`department`,`location`,`job_type`,`experience`,`description`,`status`,`created_at`,`updated_at`) VALUES
  ('Senior PHP Developer','Engineering','Chennai','Full Time','3-5 years','We are looking for a senior PHP developer to build and maintain web applications using CodeIgniter and MySQL.\n\nResponsibilities:\n- Design clean, maintainable back-end code\n- Review pull requests and mentor teammates\n- Optimise slow SQL queries\n\nRequirements:\n- 3+ years of PHP experience\n- Solid MySQL and REST API knowledge\n- Comfortable with Git',1,'2026-08-31 09:58:33','2026-08-31 09:58:33'),
  ('UI/UX Designer','Design','Remote','Full Time','2-4 years','Join our design team to craft responsive, accessible interfaces for web products.\n\nRequirements:\n- Portfolio showing end-to-end product work\n- Figma proficiency\n- Understanding of Bootstrap and design systems',1,'2026-09-03 09:58:33','2026-09-03 09:58:33'),
  ('Digital Marketing Executive','Marketing','Chennai','Full Time','1-3 years','Plan and run SEO, email and social campaigns; report on results every month.\n\nRequirements:\n- Hands-on with Google Analytics and Search Console\n- Strong written English',1,'2026-09-06 09:58:33','2026-09-06 09:58:33'),
  ('Front-end Intern','Engineering','Chennai','Internship','0-1 year','A 6-month internship for students who enjoy HTML, CSS and JavaScript. You will work on real client pages with a mentor.',1,'2026-09-09 09:58:33','2026-09-09 09:58:33'),
  ('Customer Support Associate','Operations','Coimbatore','Part Time','0-2 years','Answer customer emails and chats, log issues and share feedback with the product team.',1,'2026-09-12 09:58:33','2026-09-12 09:58:33'),
  ('QA Engineer','Engineering','Bengaluru','Contract','2-3 years','Write test plans and automate regression tests for our web platform. 6-month contract with possible extension.',0,'2026-09-15 09:58:33','2026-09-15 09:58:33');

INSERT INTO `applications` (`career_id`,`job_title`,`name`,`email`,`phone`,`message`,`resume`,`status`,`created_at`,`updated_at`) VALUES
  (1,'Senior PHP Developer','Aarav Kumar','aarav.kumar@example.com','+91 9850710220','Hello, I would like to apply for the Senior PHP Developer role. I have relevant experience and can join within 30 days.','sample_resume_01.pdf','New','2026-09-30 09:58:33','2026-09-30 09:58:33'),
  (2,'UI/UX Designer','Diya Menon','diya.menon@example.com','+91 9830309186','Hello, I would like to apply for the UI/UX Designer role. I have relevant experience and can join within 30 days.','sample_resume_02.pdf','New','2026-09-30 06:58:33','2026-09-30 06:58:33'),
  (3,'Digital Marketing Executive','Karthik Raja','karthik.raja@example.com','+91 9891504283','Hello, I would like to apply for the Digital Marketing Executive role. I have relevant experience and can join within 30 days.','sample_resume_03.pdf','Reviewed','2026-09-30 03:58:33','2026-09-30 03:58:33'),
  (4,'Front-end Intern','Meera Iyer','meera.iyer@example.com','+91 9841694511','Hello, I would like to apply for the Front-end Intern role. I have relevant experience and can join within 30 days.','sample_resume_04.pdf','Shortlisted','2026-09-29 00:58:33','2026-09-29 00:58:33'),
  (5,'Customer Support Associate','Rohan Das','rohan.das@example.com','+91 9853996545','Hello, I would like to apply for the Customer Support Associate role. I have relevant experience and can join within 30 days.','sample_resume_05.pdf','Rejected','2026-09-28 21:58:33','2026-09-28 21:58:33'),
  (1,'Senior PHP Developer','Sneha Pillai','sneha.pillai@example.com','+91 9852889111','Hello, I would like to apply for the Senior PHP Developer role. I have relevant experience and can join within 30 days.','sample_resume_06.pdf','New','2026-09-27 18:58:33','2026-09-27 18:58:33'),
  (2,'UI/UX Designer','Vikram Nair','vikram.nair@example.com','+91 9871845005','Hello, I would like to apply for the UI/UX Designer role. I have relevant experience and can join within 30 days.','sample_resume_07.pdf','Reviewed','2026-09-27 15:58:33','2026-09-27 15:58:33'),
  (3,'Digital Marketing Executive','Ananya Reddy','ananya.reddy@example.com','+91 9858567817','Hello, I would like to apply for the Digital Marketing Executive role. I have relevant experience and can join within 30 days.','sample_resume_08.pdf','New','2026-09-26 12:58:33','2026-09-26 12:58:33'),
  (4,'Front-end Intern','Arjun Mehta','arjun.mehta@example.com','+91 9889955780','Hello, I would like to apply for the Front-end Intern role. I have relevant experience and can join within 30 days.','sample_resume_09.pdf','New','2026-09-26 09:58:33','2026-09-26 09:58:33'),
  (5,'Customer Support Associate','Priya Sharma','priya.sharma@example.com','+91 9820605196','Hello, I would like to apply for the Customer Support Associate role. I have relevant experience and can join within 30 days.','sample_resume_10.pdf','Reviewed','2026-09-25 06:58:33','2026-09-25 06:58:33'),
  (1,'Senior PHP Developer','Suresh Babu','suresh.babu@example.com','+91 9878704012','Hello, I would like to apply for the Senior PHP Developer role. I have relevant experience and can join within 30 days.','sample_resume_11.pdf','Shortlisted','2026-09-25 03:58:33','2026-09-25 03:58:33'),
  (2,'UI/UX Designer','Lakshmi Narayanan','lakshmi.narayanan@example.com','+91 9836482740','Hello, I would like to apply for the UI/UX Designer role. I have relevant experience and can join within 30 days.','sample_resume_12.pdf','Rejected','2026-09-24 00:58:33','2026-09-24 00:58:33'),
  (3,'Digital Marketing Executive','Imran Sheikh','imran.sheikh@example.com','+91 9862571125','Hello, I would like to apply for the Digital Marketing Executive role. I have relevant experience and can join within 30 days.','sample_resume_13.pdf','New','2026-09-23 21:58:33','2026-09-23 21:58:33'),
  (4,'Front-end Intern','Nisha George','nisha.george@example.com','+91 9831466432','Hello, I would like to apply for the Front-end Intern role. I have relevant experience and can join within 30 days.','sample_resume_14.pdf','Reviewed','2026-09-22 18:58:33','2026-09-22 18:58:33'),
  (5,'Customer Support Associate','Farhan Ali','farhan.ali@example.com','+91 9843193052','Hello, I would like to apply for the Customer Support Associate role. I have relevant experience and can join within 30 days.','sample_resume_15.pdf','New','2026-09-22 15:58:33','2026-09-22 15:58:33');
