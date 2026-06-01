# JWT Authentication REST API

This is a complete REST API built using Core PHP, following the MVC architecture. It features a secure Dual-Token (JWT Access + HttpOnly Refresh) authentication architecture, password hashing, AES-256-CBC database encryption for sensitive data, CSRF protection, and middleware route protection with strict data isolation.

## Requirements

- PHP 7.4 or higher
- MySQL Database
- Apache/Nginx (with `mod_rewrite` enabled for `.htaccess`)
- PDO Extension

## Setup Instructions

1. **Database Setup**
   Import the `database.sql` script into your MySQL server to create the `jwt_auth` database, along with the `users`, `refresh_tokens`, and `patients` tables.

2. **Environment Configuration**
   Open the `.env` file in the root directory and update your database credentials if necessary:
   ```ini
   DB_HOST=localhost
   DB_NAME=jwt_auth
   DB_USER=root
   DB_PASS=

   JWT_SECRET=your_super_secret_key
   JWT_EXPIRY=900
   ENCRYPTION_KEY=your_secure_32_byte_encryption_key_here
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
- **Description:** Authenticates a user and enforces a single active session (revoking previous sessions). Returns a short-lived JWT access token in the JSON response and a long-lived opaque refresh token via an `HttpOnly` secure cookie.
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
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_in": 1716480000,
    "csrf_token": "a1b2c3d4e5f6..."
  }
  ```
  *Note: A `Set-Cookie: refresh_token=...; HttpOnly; SameSite=Strict` and a `PHPSESSID` session cookie are also sent.*

#### Refresh Token
- **Endpoint:** `POST /api/token/refresh`
- **Description:** Uses the `HttpOnly` refresh token cookie to issue a new JWT access token and rotates the refresh token.
- **Headers:**
  - *No auth headers needed, but the browser must send cookies automatically.*
- **Response (200 OK):**
  ```json
  {
    "message": "Token refreshed successfully",
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "expires_in": 1716480900
  }
  ```
  *Note: A new `Set-Cookie` header will be included with the rotated refresh token.*

---

### 2. Patient Module (Protected Routes)

**Important:** All routes in this section require a valid JWT access token. You must include it in the request headers:
- `Authorization: Bearer <your_access_token>`

**CSRF Protection:** State-changing requests (`POST`, `PUT`, `DELETE`) also require the CSRF token received during login, along with the `PHPSESSID` cookie. Include the token in the headers:
- `X-CSRF-Token: <your_csrf_token>`

**Data Isolation:** The API enforces strict ownership. A user can only view, create, update, or delete patients that are tied to their own `user_id`. Any attempt to access another user's patient will return a `401 Unauthorized access` error.

#### Get All Patients
- **Endpoint:** `GET /api/patients`
- **Description:** Retrieves a list of all patients owned by the authenticated user.
- **Headers:**
  - `Authorization: Bearer <access_token>`
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
        "diagnosis": "Hypertension",
        "created_at": "2024-05-23 10:00:00",
        "updated_at": "2024-05-23 10:00:00"
      }
    ]
  }
  ```

#### Get Patient by ID
- **Endpoint:** `GET /api/patients/{id}`
- **Description:** Retrieves the details of a single patient owned by the authenticated user.
- **Headers:**
  - `Authorization: Bearer <access_token>`
- **Response (200 OK):**
  ```json
  {
    "data": {
      "id": 1,
      "name": "Jane Smith",
      "age": 30,
      "gender": "Female",
      "phone": "555-1234",
      "address": "123 Main St",
      "diagnosis": "Hypertension",
      "created_at": "2024-05-23 10:00:00",
      "updated_at": "2024-05-23 10:00:00"
    }
  }
  ```

#### Create Patient
- **Endpoint:** `POST /api/patients`
- **Description:** Adds a new patient to the database under the authenticated user's account.
- **Headers:**
  - `Content-Type: application/json`
  - `Authorization: Bearer <access_token>`
  - `X-CSRF-Token: <csrf_token>`
- **Body:**
  ```json
  {
    "name": "Jane Smith",
    "age": 30,
    "gender": "Female",
    "phone": "555-1234",
    "address": "123 Main St",
    "diagnosis": "Hypertension"
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
      "diagnosis": "Hypertension",
      "id": 1
    }
  }
  ```

#### Update Patient
- **Endpoint:** `PUT /api/patients/{id}`
- **Description:** Updates specific details of an existing patient owned by the user.
- **Headers:**
  - `Content-Type: application/json`
  - `Authorization: Bearer <access_token>`
  - `X-CSRF-Token: <csrf_token>`
- **Body:**
  ```json
  {
    "age": 31,
    "phone": "555-9999",
    "diagnosis": "Resolved"
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
- **Description:** Deletes a patient from the database if owned by the authenticated user.
- **Headers:**
  - `Authorization: Bearer <access_token>`
  - `X-CSRF-Token: <csrf_token>`
- **Response (200 OK):**
  ```json
  {
    "message": "Patient deleted successfully"
  }
  ```

## Common Error Responses

- `400 Bad Request`: When required fields are missing or JSON is invalid.
- `401 Unauthorized access`: When the JWT token is missing, invalid, expired, or when a user attempts to access a resource (like a patient) they do not own.
- `403 Forbidden`: When the CSRF token is missing or invalid on state-changing requests.
- `404 Not Found`: When the requested route does not exist.
- `409 Conflict`: When trying to register an email that already exists.
