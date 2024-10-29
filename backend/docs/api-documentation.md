# API Documentation

Base URL: `http://localhost:9000/api`

## Authentication

### 1. Login/Register
Send verification code to user.

- **URL**: `/login`
- **Method**: `POST`
- **Body**:
```json
{
    "phone": "1234567890",
    "email": "test@example.com"
}
```

Example:
```bash
http POST http://localhost:9001/api/login phone=1234567890 email=test@example.com
```

### 2. Verify Code
Verify the code and get access token.

- **URL**: `/login/verify`
- **Method**: `POST`
- **Body**:
```json
{
    "phone": "1234567890",
    "verification_code": "770338"
}
```

Example:
```bash
http POST http://localhost:9001/api/login/verify phone=1234567890 verification_code=770338
```

## Endpoints

### 1. List All Drivers
Retrieve a list of all drivers.

- **URL**: `/drivers`
- **Method**: `GET`
- **Auth Required**: Yes

#### Success Response
- **Code**: `200 OK`
- **Content Example**:
```json
[
    {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "1234567890",
        "license_number": "DL123456",
        "status": "active",
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z"
    }
]
```

### 2. Create Driver
Create a new driver.

- **URL**: `/drivers`
- **Method**: `POST`
- **Auth Required**: Yes

#### Request Body
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "1234567890",
    "license_number": "DL123456",
    "status": "active"
}
```

#### Required Fields
- `name`: string, max 255 characters
- `email`: valid email address, unique
- `phone`: string, unique
- `license_number`: string, unique
- `status`: string, either "active" or "inactive"

#### Success Response
- **Code**: `201 Created`
- **Content Example**:
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "1234567890",
    "license_number": "DL123456",
    "status": "active",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### Error Response
- **Code**: `422 Unprocessable Entity`
- **Content Example**:
```json
{
    "errors": {
        "email": ["The email has already been taken."],
        "phone": ["The phone has already been taken."]
    }
}
```

### 3. Get Single Driver
Retrieve details of a specific driver.

- **URL**: `/drivers/{id}`
- **Method**: `GET`
- **Auth Required**: Yes
- **URL Parameters**: `id=[integer]` driver ID

#### Success Response
- **Code**: `200 OK`
- **Content Example**:
```json
{
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "1234567890",
    "license_number": "DL123456",
    "status": "active",
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
}
```

#### Error Response
- **Code**: `404 Not Found`
- **Content**: `{"message": "No query results for model [App\\Models\\Driver] {id}"}`

### 4. Update Driver
Update an existing driver's information.

- **URL**: `/drivers/{id}`
- **Method**: `PUT`
- **Auth Required**: Yes
- **URL Parameters**: `id=[integer]` driver ID

#### Request Body
```json
{
    "name": "John Doe Updated",
    "email": "john.updated@example.com",
    "phone": "9876543210",
    "license_number": "DL654321",
    "status": "inactive"
}
```

#### Optional Fields
All fields are optional for updates:
- `name`: string, max 255 characters
- `email`: valid email address, unique
- `phone`: string, unique
- `license_number`: string, unique
- `status`: string, either "active" or "inactive"

#### Success Response
- **Code**: `200 OK`
- **Content**: Updated driver object

#### Error Response
- **Code**: `422 Unprocessable Entity`
- **Content Example**:
```json
{
    "errors": {
        "email": ["The email has already been taken."]
    }
}
```

### 5. Delete Driver
Delete a specific driver.

- **URL**: `/drivers/{id}`
- **Method**: `DELETE`
- **Auth Required**: Yes
- **URL Parameters**: `id=[integer]` driver ID

#### Success Response
- **Code**: `204 No Content`
- **Content**: None

#### Error Response
- **Code**: `404 Not Found`
- **Content**: `{"message": "No query results for model [App\\Models\\Driver] {id}"}`

## Error Codes
- `200`: Success
- `201`: Created
- `204`: No Content
- `401`: Unauthorized
- `404`: Not Found
- `422`: Validation Error
- `500`: Server Error

## Testing the API
You can test these endpoints using tools like Postman or curl.

Example curl command:
```bash
Authorization: Bearer <your-token>
```