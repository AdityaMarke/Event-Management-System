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


<img width="1919" height="1019" alt="Screenshot 2025-12-06 090910" src="https://github.com/user-attachments/assets/7c64e5b4-f196-47c2-bae4-2bd2600520bb" />
<img width="1919" height="1023" alt="Screenshot 2025-12-06 090948" src="https://github.com/user-attachments/assets/12644267-15b1-498d-b539-8772315aa40c" />
<img width="1903" height="1023" alt="Screenshot 2025-12-06 091021" src="https://github.com/user-attachments/assets/15df0d6d-cf63-4580-9ae0-9c93468b3c83" />
<img width="1919" height="1020" alt="Screenshot 2025-12-06 091045" src="https://github.com/user-attachments/assets/41aa109e-f489-4d88-bb8d-c997fac1325d" />
<img width="1919" height="1019" alt="Screenshot 2025-12-06 091110" src="https://github.com/user-attachments/assets/7cef3a08-9303-41a8-8fae-f295e4e96813" />
<img width="1919" height="1018" alt="Screenshot 2025-12-06 091137" src="https://github.com/user-attachments/assets/b72fca43-62bd-4d4c-b3ef-a58dbb159130" />
<img width="1919" height="1013" alt="Screenshot 2025-12-06 085654" src="https://github.com/user-attachments/assets/965a3dd1-a09a-4571-b981-68dcbce74528" />
<img width="1919" height="1014" alt="Screenshot 2025-12-06 085834" src="https://github.com/user-attachments/assets/2f2c95c3-6aed-4422-9c8a-bddee57aab56" />
<img width="1895" height="1013" alt="Screenshot 2025-12-06 085921" src="https://github.com/user-attachments/assets/3eaf2fff-9552-427c-8dfe-c3ee43e2e0b5" />
<img width="1895" height="1015" alt="Screenshot 2025-12-06 085947" src="https://github.com/user-attachments/assets/4813c29f-332c-4591-8604-8c3d62a75c31" />
<img width="1895" height="852" alt="Screenshot 2025-12-06 090037" src="https://github.com/user-attachments/assets/1c38e5af-c68b-4ed7-8328-e282b4ddc204" />
<img width="1919" height="1014" alt="Screenshot 2025-12-06 090140" src="https://github.com/user-attachments/assets/fe602860-fe4a-4bb3-906c-5be35871bffd" />
<img width="1907" height="998" alt="Screenshot 2025-12-06 090217" src="https://github.com/user-attachments/assets/2205cf35-83ff-4597-adbd-16fe64b344ff" />



