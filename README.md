# 🎉 Event Management System | Cloud Computing Project

A scalable, cloud-hosted Event Management System developed using AWS services and PHP.  
This project enables college administrators to create and manage events, while students can register for events through a user-friendly web application.

---

## 🚀 Project Overview

The Event Management System is designed to digitize the event registration workflow in educational institutes.  
It provides:

- Secure admin login for event creation and management
- Student event browsing and registration
- Cloud-based deployment ensuring high availability and reliability

The system leverages AWS Cloud infrastructure to achieve scalability, cost efficiency, and improved performance during peak event registrations.

---

## 🧩 Features

| Feature | Description |
|--------|-------------|
| Admin Login | Secure authentication for event creation |
| Event Management | Admin can create events with attachments |
| Student Registration | Students can register for events |
| Cloud Database | Store event & registration records using RDS |
| Cloud Storage | Event attachments stored in S3 securely |
| Auto Scaling | Handles load during peak registration times |
| Load Balancing | Distributes requests across EC2 instances |
| Role-Based Access | IAM roles ensure secure resource usage |

---

## 🛠 AWS Services Used

- **Amazon EC2** — Hosts PHP–Apache web application
- **Amazon RDS (MySQL)** — Stores events & registration data
- **Amazon S3** — Stores event-related files & assets
- **Amazon IAM** — Secure access management with roles
- **Auto Scaling Group** — Automatically adjusts capacity
- **Application Load Balancer** — Distributes incoming traffic

---

## 💻 Tech Stack

| Layer | Technology |
|------|------------|
| Frontend | HTML, CSS, Bootstrap |
| Backend | PHP |
| Database | MySQL on AWS RDS |
| OS / Hosting | Ubuntu on EC2 |
| Storage | AWS S3 |

---

## 🏗️ System Architecture

User
↓
Application Load Balancer
↓
Auto Scaling Group → Multiple EC2 Instances (PHP App)
↓ ↓
Amazon RDS (MySQL) ── Amazon S3 (Event Files)

yaml
Copy code

---

## 📂 Project Structure

/var/www/html/
│
├── index.php # Homepage: View events & navigation
├── admin.php # Admin operations & login
├── register.php # Student registration handling
├── config.php # AWS + DB configuration
└── vendor/ # AWS PHP SDK (Composer)

pgsql
Copy code

---

## 🗄️ Database Schema

CREATE TABLE events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  event_name VARCHAR(100),
  event_date DATE,
  s3_key VARCHAR(255),
  file_url VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE registrations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(100),
  email VARCHAR(100),
  event_id INT,
  registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(id)
);

## ▶️ Installation & Deployment
1️⃣ Clone the repo to EC2 Web Root:
bash
Copy code
cd /var/www/html
git clone <YOUR_REPOSITORY_URL> .
2️⃣ Install Dependencies (AWS SDK)
bash
Copy code
composer require aws/aws-sdk-php
3️⃣ Update configurations
Modify config.php with:

RDS DB endpoint, name, user & password

S3 bucket name & region

4️⃣ Restart Apache
bash
Copy code
sudo systemctl restart apache2
5️⃣ Access via Browser
cpp
Copy code
http://<Load-Balancer-DNS>/
🔐 Security
IAM roles eliminate hard-coded credentials

RDS secured with inbound rules for EC2 only

S3 private access with presigned URLs

🚧 Future Enhancements
Email confirmation on registration

Admin analytics dashboard

AWS Cognito support for multi-user roles

Downloadable registration reports

📌 Author
Aditya Marke
B.Tech — Computer Engineering
Pimpri Chinchwad College of Engineering

