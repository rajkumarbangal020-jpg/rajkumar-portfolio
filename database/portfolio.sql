CREATE DATABASE IF NOT EXISTS rajkumar_portfolio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rajkumar_portfolio;

CREATE TABLE IF NOT EXISTS admins (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(50) NOT NULL UNIQUE,
 email VARCHAR(120) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS personal_information (
 id INT PRIMARY KEY,
 full_name VARCHAR(120), title VARCHAR(255), tagline VARCHAR(255), bio TEXT,
 location VARCHAR(180), role VARCHAR(120), availability VARCHAR(120), preferred_locations VARCHAR(255),
 email VARCHAR(150), phone VARCHAR(50), linkedin_url VARCHAR(255), github_url VARCHAR(255), resume_path VARCHAR(255)
);

INSERT INTO personal_information (id,full_name,title,tagline,bio,location,role,availability,preferred_locations,email,phone,linkedin_url,github_url,resume_path)
VALUES (1,'Rajkumar Bangal','Junior PHP Developer | CodeIgniter Developer | Full Stack Web Developer','I build secure, responsive and scalable web applications.','PHP and CodeIgniter developer focused on business applications, admin panels, APIs and database-driven systems.','Kolkata, West Bengal, India','Junior PHP Developer','Open to Opportunities','Kolkata / Bengaluru / Hyderabad / Pune / Mumbai','','','','https://github.com/rajkumarbangal020-jpg','assets/cv/rajkumar-bangal-cv.pdf')
ON DUPLICATE KEY UPDATE full_name=VALUES(full_name);

CREATE TABLE IF NOT EXISTS skills (
 id INT AUTO_INCREMENT PRIMARY KEY, category VARCHAR(80), name VARCHAR(120), icon_class VARCHAR(100), proficiency_level VARCHAR(50), sort_order INT DEFAULT 0, is_active TINYINT(1) DEFAULT 1
);

CREATE TABLE IF NOT EXISTS experiences (
 id INT AUTO_INCREMENT PRIMARY KEY, company VARCHAR(150), role VARCHAR(180), duration VARCHAR(120), is_current TINYINT(1) DEFAULT 0, responsibilities JSON NULL, sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS education (
 id INT AUTO_INCREMENT PRIMARY KEY, degree VARCHAR(180), institution VARCHAR(180), field_of_study VARCHAR(180), start_year VARCHAR(20), end_year VARCHAR(20), status VARCHAR(50), grade_marks VARCHAR(100), sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS certifications (
 id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(180), issuing_organization VARCHAR(180), issue_year VARCHAR(20), credential_url VARCHAR(255), certificate_file VARCHAR(255), sort_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS projects (
 id INT AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(180) NOT NULL, slug VARCHAR(180), short_description TEXT, full_description TEXT,
 problem_solved TEXT, challenges_solutions TEXT, my_responsibilities TEXT, features JSON NULL,
 technologies TEXT, thumbnail VARCHAR(255), live_url VARCHAR(255), github_url VARCHAR(255), category VARCHAR(100),
 sort_order INT DEFAULT 0, is_featured TINYINT(1) DEFAULT 1, is_active TINYINT(1) DEFAULT 1,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS portfolio_enquiries (
 id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120), email VARCHAR(150), phone VARCHAR(50), subject VARCHAR(180), message TEXT, is_read TINYINT(1) DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO projects (title,slug,short_description,full_description,technologies,thumbnail,category,sort_order,is_featured,is_active) VALUES
('School Management System','school-management-system','Role-based school management platform with attendance, QR student IDs and subscription plans.','A comprehensive educational management system for school operations, student profiles, attendance and reporting.','PHP, CodeIgniter, MySQL, Bootstrap 5, JavaScript, jQuery','uploads/projects/school_management.png','PHP & CodeIgniter',1,1,1),
('Employee Attendance & PWA System','employee-attendance-pwa','Employee attendance application with GPS, selfie check-in and PWA functionality.','A progressive web app for employee attendance with location validation and camera capture.','PHP, CodeIgniter, MySQL, JavaScript, Bootstrap 5, PWA','uploads/projects/employee_attendance.png','Web Apps & PWA',2,1,1),
('Restaurant Billing & Management System','restaurant-billing-management','Multi-restaurant billing and management platform.','A restaurant billing, menu, subscription and reporting system.','PHP, CodeIgniter, MySQL, Bootstrap 5, JavaScript','uploads/projects/restaurant_billing.png','PHP & CodeIgniter',3,1,1),
('Referral Wallet & Payout System','referral-wallet-payout','Referral reward, wallet and payout management system.','A referral tracking, wallet ledger, bank/UPI verification and payout workflow platform.','PHP, MySQL, JavaScript, Bootstrap 5, jQuery, AJAX','uploads/projects/referral_wallet.png','Management Systems',4,1,1),
('AI Voice Calling System – Risha','ai-voice-calling-risha','Multilingual AI voice calling and automation system.','An AI calling system supporting Bengali, Hindi and English with call queue, callback and DNC workflows.','Python, PHP, CodeIgniter, MySQL, Deepgram API, WebSockets, REST APIs','uploads/projects/ai_voice_calling.png','Full Stack & APIs',5,1,1),
('Catering & Event Management Website','catering-event-management','Dynamic catering and event management website with admin panel.','A responsive PHP website with menus, galleries, reviews and enquiry management.','PHP, MySQL, Bootstrap 5, JavaScript, jQuery','uploads/projects/catering_event.png','Full Stack & APIs',6,1,1);
