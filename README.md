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

## Screenshots

<img width="1919" height="1013" alt="Screenshot 2025-12-06 085654" src="https://github.com/user-attachments/assets/965a3dd1-a09a-4571-b981-68dcbce74528" />
<img width="1919" height="1014" alt="Screenshot 2025-12-06 085834" src="https://github.com/user-attachments/assets/2f2c95c3-6aed-4422-9c8a-bddee57aab56" />
<img width="1895" height="1013" alt="Screenshot 2025-12-06 085921" src="https://github.com/user-attachments/assets/3eaf2fff-9552-427c-8dfe-c3ee43e2e0b5" />
<img width="1895" height="1015" alt="Screenshot 2025-12-06 085947" src="https://github.com/user-attachments/assets/4813c29f-332c-4591-8604-8c3d62a75c31" />
<img width="1895" height="852" alt="Screenshot 2025-12-06 090037" src="https://github.com/user-attachments/assets/1c38e5af-c68b-4ed7-8328-e282b4ddc204" />
<img width="1919" height="1014" alt="Screenshot 2025-12-06 090140" src="https://github.com/user-attachments/assets/fe602860-fe4a-4bb3-906c-5be35871bffd" />
<img width="1907" height="998" alt="Screenshot 2025-12-06 090217" src="https://github.com/user-attachments/assets/2205cf35-83ff-4597-adbd-16fe64b344ff" />



