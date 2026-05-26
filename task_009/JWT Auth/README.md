# JWT Authentication REST API

This is a complete REST API built using Core PHP, following the MVC architecture. It features JWT-based authentication, password hashing, and middleware route protection.

## Requirements

- PHP 7.4 or higher
- MySQL Database
- Apache/Nginx (with `mod_rewrite` enabled for `.htaccess`)
- PDO Extension

## Setup Instructions

1. **Database Setup**
   Import the `database.sql` script into your MySQL server to create the `jwt_auth` database, along with the `users` and `patients` tables.

2. **Environment Configuration**
   Open the `.env` file in the root directory and update your database credentials if necessary:
   ```ini
   DB_HOST=localhost
   DB_NAME=jwt_auth
   DB_USER=root
   DB_PASS=

   JWT_SECRET=your_super_secret_key
   JWT_EXPIRY=3600
   ```


3. **Start the Server**
   If you are using WAMP/XAMPP, place the project in the `www` or `htdocs` directory. Ensure the apache `rewrite_module` is enabled.

---

## API Documentation

The API accepts and returns **JSON** formatted data. Ensure you send the `Content-Type: application/json` header for POST, PUT, and PATCH requests.

### Base URL
Assuming your project is hosted on localhost under the folder `JWT Auth`:
```
http://localhost/JWT Auth/public/index.php
```

### 1. Authentication Module (Public)

#### Register User
- **Endpoint:** `POST /api/register`
- **Description:** Creates a new user account.
- **Headers:** 
  - `Content-Type: application/json`
- **Body:**
  ```json
  {
    "name": "John Doe",
    "email": "john@example.com",
    "password": "securepassword123"
  }
  ```
- **Response (201 Created):**
  ```json
  {
    "message": "User registered successfully",
    "user_id": 1
  }
  ```

#### Login User
- **Endpoint:** `POST /api/login`
- **Description:** Authenticates a user and returns a JWT token.
- **Headers:**
  - `Content-Type: application/json`
- **Body:**
  ```json
  {
    "email": "john@example.com",
    "password": "securepassword123"
  }
  ```
- **Response (200 OK):**
  ```json
  {
    "message": "Login successful",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_in": 1716480000
  }
  ```

---

### 2. Patient Module (Protected Routes)

**Important:** All routes in this section require a valid JWT token. You must include it in the request headers:
- `Authorization: Bearer <your_token>`

#### Get All Patients
- **Endpoint:** `GET /api/patients`
- **Description:** Retrieves a list of all patients.
- **Headers:**
  - `Authorization: Bearer <token>`
- **Response (200 OK):**
  ```json
  {
    "data": [
      {
        "id": 1,
        "name": "Jane Smith",
        "age": 30,
        "gender": "Female",
        "phone": "555-1234",
        "address": "123 Main St",
        "created_at": "2024-05-23 10:00:00",
        "updated_at": "2024-05-23 10:00:00"
      }
    ]
  }
  ```

#### Create Patient
- **Endpoint:** `POST /api/patients`
- **Description:** Adds a new patient to the database.
- **Headers:**
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- **Body:**
  ```json
  {
    "name": "Jane Smith",
    "age": 30,
    "gender": "Female",
    "phone": "555-1234",
    "address": "123 Main St"
  }
  ```
- **Response (201 Created):**
  ```json
  {
    "message": "Patient created successfully",
    "data": {
      "name": "Jane Smith",
      "age": 30,
      "gender": "Female",
      "phone": "555-1234",
      "address": "123 Main St",
      "id": 1
    }
  }
  ```

#### Update Patient
- **Endpoint:** `PUT /api/patients/{id}`
- **Description:** Updates specific details of an existing patient by their ID.
- **Headers:**
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`
- **Body:**
  ```json
  {
    "age": 31,
    "phone": "555-9999"
  }
  ```
- **Response (200 OK):**
  ```json
  {
    "message": "Patient updated successfully"
  }
  ```

#### Delete Patient
- **Endpoint:** `DELETE /api/patients/{id}`
- **Description:** Deletes a patient from the database by their ID.
- **Headers:**
  - `Authorization: Bearer <token>`
- **Response (200 OK):**
  ```json
  {
    "message": "Patient deleted successfully"
  }
  ```

## Common Error Responses

- `400 Bad Request`: When required fields are missing or JSON is invalid.
- `401 Unauthorized`: When the JWT token is missing, invalid, or expired.
- `404 Not Found`: When the requested route or resource (e.g., patient ID) does not exist.
- `409 Conflict`: When trying to register an email that already exists.
