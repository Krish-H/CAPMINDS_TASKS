# Patient REST API

The Patient REST API is a lightweight, backend web service built with PHP and MySQL (using `mysqli`). It provides a complete set of RESTful endpoints to manage patient records, allowing client applications to perform Create, Read, Update, and Delete (CRUD) operations on patient data.

All API responses are returned in JSON format.

## Prerequisites

- PHP 7.4 or higher
- MySQL Database
- Web Server (Apache/Nginx) with URL rewriting enabled (for `.htaccess`)

## Installation & Setup

1. **Database Setup**:
   Create a database (e.g., `patient_db`) and run the necessary SQL to create the `patients` table. The table should have the following columns: `id`, `name`, `age`, `gender`, `phone`, and `created_at`.
   
2. **Configuration**:
   Update the database connection settings in `api/config/database.php` to match your local database credentials (host, username, password, database name).

3. **Running the API**:
   Host the project directory on your local server (e.g., `http://localhost/patient-api`). The `.htaccess` file will automatically route requests starting with `/api` to the internal router (`api/index.php`).

## API Endpoints

The API handles requests at the base path: `/api/patients`

### 1. Retrieve All Patients
- **URL**: `/api/patients`
- **Method**: `GET`
- **Description**: Retrieves a list of all patients (default limit is 20).
- **Response** (200 OK):
  ```json
  {
    "status": true,
    "message": "Patients retrieved successfully",
    "data": [
      {
        "id": 1,
        "name": "John Doe",
        "age": 30,
        "gender": "Male",
        "phone": "1234567890",
        "created_at": "2026-05-21 10:00:00"
      }
    ]
  }
  ```

### 2. Retrieve a Single Patient
- **URL**: `/api/patients/{id}`
- **Method**: `GET`
- **Description**: Retrieves details of a specific patient by their ID.
- **Response** (200 OK):
  ```json
  {
    "status": true,
    "message": "Patient retrieved successfully",
    "data": {
      "id": 1,
      "name": "John Doe",
      "age": 30,
      "gender": "Male",
      "phone": "1234567890",
      "created_at": "2026-05-21 10:00:00"
    }
  }
  ```

### 3. Create a New Patient
- **URL**: `/api/patients`
- **Method**: `POST`
- **Description**: Creates a new patient record.
- **Request Body** (JSON):
  ```json
  {
    "name": "Jane Doe",
    "age": 28,
    "gender": "Female",
    "phone": "0987654321"
  }
  ```
- **Response** (201 Created):
  ```json
  {
    "status": true,
    "message": "Patient created successfully"
  }
  ```

### 4. Update an Existing Patient
- **URL**: `/api/patients/{id}`
- **Method**: `PUT`
- **Description**: Updates the details of an existing patient by their ID. You only need to send the fields you want to update.
- **Request Body** (JSON):
  ```json
  {
    "phone": "1112223333"
  }
  ```
- **Response** (200 OK):
  ```json
  {
    "status": true,
    "message": "Patient updated successfully"
  }
  ```

### 5. Delete a Patient
- **URL**: `/api/patients/{id}`
- **Method**: `DELETE`
- **Description**: Deletes a specific patient by their ID.
- **Response** (200 OK):
  ```json
  {
    "status": true,
    "message": "Patient deleted successfully"
  }
  ```

## Error Handling

In case of errors (e.g., missing data, non-existent endpoints, or server errors), the API returns appropriate HTTP status codes (e.g., 400, 404, 405, 500) and a consistent JSON response format indicating failure:

```json
{
  "status": false,
  "message": "Error description here"
}
```
