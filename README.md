# 🎉 Event Management System | Cloud Computing Project

A scalable and secure cloud-hosted Event Management System developed using AWS services and PHP.  
This project empowers college administrators to create and manage events digitally, while students can register seamlessly through the web interface.

---

## 🚀 Project Overview

The Event Management System modernizes and streamlines the traditional event registration process in educational institutions.

Key highlights:
- Cloud-based deployment with high availability
- Simple UI for event registration
- Secure admin access for event management

---

## 🧩 Features

| Feature | Description |
|--------|-------------|
| Admin Login | Event creation and management |
| Event Listings | Students can browse events |
| Online Registration | Store participant records |
| Cloud Storage | Event attachments stored in S3 |
| Cloud Database | RDS-backed storage for scalability |
| Auto Scaling | Handles increased workload |
| Load Balancing | Distributes traffic across servers |
| Secure Access | IAM-based resource control |

---

## 🛠 Tech Stack

| Layer | Technology |
|------|------------|
| Frontend | HTML, CSS, Bootstrap |
| Backend | PHP |
| Database | MySQL on AWS RDS |
| Compute | AWS EC2 (Ubuntu) |
| Storage | AWS S3 |
| Access Control | AWS IAM |

---

## 📂 Project Structure

/var/www/html/
│
├── index.php # Event listing page (students)
├── admin.php # Admin login and dashboard
├── register.php # Event registration logic
├── config.php # AWS + DB configuration
└── vendor/ # AWS SDK via Composer

---

## 🗄 Database Schema

```sql
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

```



